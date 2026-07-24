from flask import Flask, jsonify, request
from flask_cors import CORS
import pandas as pd
import numpy as np
from sklearn.preprocessing import MinMaxScaler, OneHotEncoder
import os

from tensorflow.keras.models import Model
from tensorflow.keras.layers import Input, LSTM, Dense, Concatenate
from sklearn.metrics.pairwise import cosine_similarity

app = Flask(__name__)

# Enable CORS untuk semua routes
CORS(app, resources={r"/api/*": {"origins": "*"}})

# Gunakan absolute path
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
DATA_PATH = os.path.join(SCRIPT_DIR, 'dataset_atmobrass.csv')
MODEL_PATH = os.path.join(SCRIPT_DIR, 'model_atmobrass.h5')
LOOKBACK = 3

# Global state for reloadable data and model
last_reload = None
data_mtime = None
model_mtime = None
pivot_sales = None
scaler_qty = None
kolom_produk = []
dict_nama_produk = {}
static_features_dict = {}
num_static_features = 0
model = None


def load_data_and_model():
    global pivot_sales, scaler_qty, kolom_produk, dict_nama_produk
    global static_features_dict, num_static_features, model
    global last_reload, data_mtime, model_mtime

    print("[INFO] Memuat ulang dataset dan model...")

    df = pd.read_csv(DATA_PATH)
    df['Tanggal_Pemesanan'] = pd.to_datetime(df['Tanggal_Pemesanan'])
    df['Bulan_Tahun'] = df['Tanggal_Pemesanan'].dt.to_period('M').dt.to_timestamp()

    monthly_sales = df.groupby(['ID_Produk', 'Bulan_Tahun'])['Quantity'].sum().reset_index()
    pivot_sales = monthly_sales.pivot(index='Bulan_Tahun', columns='ID_Produk', values='Quantity').fillna(0)
    pivot_sales = pivot_sales.resample('MS').asfreq().fillna(0)

    scaler_qty = MinMaxScaler()
    scaled_sales = scaler_qty.fit_transform(pivot_sales)

    kolom_produk = pivot_sales.columns.tolist()
    dict_nama_produk = df.drop_duplicates('ID_Produk').set_index('ID_Produk')['Nama_Produk'].to_dict()

    product_features = df[['ID_Produk', 'Kategori', 'Bahan_Material', 'Warna_Finishing', 'Harga_Rupiah']].drop_duplicates('ID_Produk').set_index('ID_Produk')
    encoder = OneHotEncoder(sparse_output=False)
    encoded_cats = encoder.fit_transform(product_features[['Kategori', 'Bahan_Material', 'Warna_Finishing']])

    scaler_harga = MinMaxScaler()
    scaled_harga = scaler_harga.fit_transform(product_features[['Harga_Rupiah']])

    static_features_dict = {}
    for i, prod_id in enumerate(product_features.index):
        static_features_dict[prod_id] = np.concatenate([encoded_cats[i], scaled_harga[i]])

    num_static_features = static_features_dict[product_features.index[0]].shape[0]

    input_ts = Input(shape=(LOOKBACK, 1), name="input_waktu")
    lstm_out = LSTM(32, activation='relu')(input_ts)

    input_static = Input(shape=(num_static_features,), name="input_fitur")
    dense_static = Dense(16, activation='relu')(input_static)

    gabungan = Concatenate()([lstm_out, dense_static])
    dense_gabungan = Dense(16, activation='relu')(gabungan)
    output = Dense(1, activation='linear')(dense_gabungan)

    model = Model(inputs=[input_ts, input_static], outputs=output)
    model.load_weights(MODEL_PATH)

    last_reload = pd.Timestamp.now()
    data_mtime = os.path.getmtime(DATA_PATH)
    model_mtime = os.path.getmtime(MODEL_PATH)
    print(f"[INFO] Reload lengkap: {last_reload}")


def maybe_reload():
    global data_mtime, model_mtime
    try:
        current_data_mtime = os.path.getmtime(DATA_PATH)
        current_model_mtime = os.path.getmtime(MODEL_PATH)
        if data_mtime is None or model_mtime is None:
            load_data_and_model()
            return

        if current_data_mtime != data_mtime or current_model_mtime != model_mtime:
            load_data_and_model()
    except FileNotFoundError as e:
        print(f"[WARN] File tidak ditemukan saat reload: {e}")
    except Exception as e:
        print(f"[WARN] Gagal memeriksa reload: {e}")


load_data_and_model()

@app.route('/api/predict', methods=['GET'])
def predict_produksi():
    try:
        maybe_reload()
        hasil_prediksi = []

        scaled_sales = scaler_qty.transform(pivot_sales)
        data_3_bulan_terakhir = scaled_sales[-LOOKBACK:]

        for j, prod_id in enumerate(kolom_produk):
            input_waktu_pred = data_3_bulan_terakhir[:, j].reshape(1, LOOKBACK, 1)
            input_fitur_pred = static_features_dict[prod_id].reshape(1, -1)

            pred_skala = model.predict({'input_waktu': input_waktu_pred, 'input_fitur': input_fitur_pred}, verbose=0)

            dummy_array = np.zeros((1, len(kolom_produk)))
            dummy_array[0, j] = pred_skala[0][0]
            prediksi_asli = scaler_qty.inverse_transform(dummy_array)[0, j]

            jumlah_produksi = int(np.ceil(prediksi_asli))
            jumlah_produksi = max(0, jumlah_produksi)

            hasil_prediksi.append({
                'id_produk': prod_id,
                'nama_barang': dict_nama_produk[prod_id],
                'prediksi_pcs': jumlah_produksi
            })

        return jsonify({
            'status': 'success',
            'pesan': 'Prediksi produksi bulan depan berhasil digenerate.',
            'data': hasil_prediksi
        }), 200

    except Exception as e:
        return jsonify({
            'status': 'error',
            'pesan': f"Terjadi kesalahan pada server AI: {str(e)}"
        }), 500


@app.route('/api/reload', methods=['POST'])
def reload_data():
    try:
        load_data_and_model()
        return jsonify({
            'status': 'success',
            'pesan': 'Dataset dan model berhasil dimuat ulang.',
            'last_reload': str(last_reload)
        }), 200
    except Exception as e:
        return jsonify({
            'status': 'error',
            'pesan': f"Tidak dapat memuat ulang: {str(e)}"
        }), 500


@app.route('/api/recommend_by_name', methods=['GET'])
def recommend_by_name():
    try:
        maybe_reload()
        name = request.args.get('name')
        n = int(request.args.get('n', 4))

        if not name:
            return jsonify({'status': 'error', 'pesan': 'Parameter name diperlukan.'}), 400

        target_id = None
        for pid, pname in dict_nama_produk.items():
            if str(pname).strip().lower() == str(name).strip().lower():
                target_id = pid
                break

        if target_id is None:
            return jsonify({'status': 'error', 'pesan': 'Produk tidak ditemukan di dataset ML.'}), 404

        product_ids = list(static_features_dict.keys())
        features = np.vstack([static_features_dict[pid] for pid in product_ids])
        target_vec = static_features_dict[target_id].reshape(1, -1)

        sims = cosine_similarity(target_vec, features)[0]
        paired = list(zip(product_ids, sims))
        paired = [p for p in paired if p[0] != target_id]
        paired.sort(key=lambda x: x[1], reverse=True)
        topn = paired[:n]

        results = []
        for pid, score in topn:
            results.append({
                'id_produk': pid,
                'nama_produk': dict_nama_produk.get(pid, ''),
                'score': float(round(float(score), 4))
            })

        return jsonify({'status': 'success', 'data': results}), 200
    except Exception as e:
        return jsonify({'status': 'error', 'pesan': str(e)}), 500


@app.route('/api/history', methods=['GET'])
def get_sales_history():
    try:
        maybe_reload()
        
        # Baca dataset
        df = pd.read_csv(DATA_PATH)
        df['Tanggal_Pemesanan'] = pd.to_datetime(df['Tanggal_Pemesanan'])
        df['Tahun'] = df['Tanggal_Pemesanan'].dt.year
        df['Bulan'] = df['Tanggal_Pemesanan'].dt.month
        
        # Dapatkan daftar tahun (unik dan terurut descending)
        available_years = sorted(df['Tahun'].unique().tolist(), reverse=True)
        
        # Parameter tahun dari query (default tahun terbaru)
        selected_year = int(request.args.get('year', available_years[0]))
        
        # Filter data untuk tahun yang dipilih
        df_year = df[df['Tahun'] == selected_year].copy()
        
        # Nama-nama bulan Indonesia
        nama_bulan = {
            1: 'Januari', 2: 'Februari', 3: 'Maret', 4: 'April',
            5: 'Mei', 6: 'Juni', 7: 'Juli', 8: 'Agustus',
            9: 'September', 10: 'Oktober', 11: 'November', 12: 'Desember'
        }
        
        # Buat struktur data per bulan
        monthly_sales = []
        total_tahun = 0
        highest_month = None
        lowest_month = None
        highest_value = 0
        lowest_value = None
        bulan_list = []  # List bulan dengan data
        
        for bulan in range(1, 13):
            df_month = df_year[df_year['Bulan'] == bulan]
            
            if len(df_month) > 0:
                # Total penjualan bulan ini (sum semua quantity)
                total_bulan = int(df_month['Quantity'].sum())
                
                # Aggregate produk by ID_Produk
                products_agg = df_month.groupby('ID_Produk').agg({
                    'Nama_Produk': 'first',
                    'Quantity': 'sum'
                }).reset_index()
                
                products = []
                for _, row in products_agg.iterrows():
                    products.append({
                        'id_produk': str(row['ID_Produk']),
                        'nama_produk': str(row['Nama_Produk']),
                        'quantity': int(row['Quantity'])
                    })
                
                # Update total tahun
                total_tahun += total_bulan
                bulan_list.append(total_bulan)
                
                # Track highest
                if total_bulan > highest_value:
                    highest_value = total_bulan
                    highest_month = nama_bulan[bulan]
                
                # Track lowest
                if lowest_value is None or total_bulan < lowest_value:
                    lowest_value = total_bulan
                    lowest_month = nama_bulan[bulan]
            else:
                # Bulan tanpa data
                total_bulan = 0
                products = []
            
            monthly_sales.append({
                'month': nama_bulan[bulan],
                'month_num': bulan,
                'total': total_bulan,
                'products': products
            })
        
        # Hitung rata-rata (hanya bulan dengan data)
        average = int(total_tahun / len(bulan_list)) if len(bulan_list) > 0 else 0
        
        # Default jika tidak ada data
        if lowest_value is None:
            lowest_value = 0
            lowest_month = '-'
        
        return jsonify({
            'status': 'success',
            'years': available_years,
            'selected_year': selected_year,
            'monthly_sales': monthly_sales,
            'summary': {
                'total': total_tahun,
                'average': average,
                'highest_month': highest_month if highest_month else '-',
                'highest_value': highest_value,
                'lowest_month': lowest_month,
                'lowest_value': lowest_value
            }
        }), 200
    
    except Exception as e:
        print(f"[ERROR] History endpoint: {str(e)}")
        import traceback
        traceback.print_exc()
        return jsonify({
            'status': 'error',
            'pesan': f"Terjadi kesalahan: {str(e)}"
        }), 500


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
