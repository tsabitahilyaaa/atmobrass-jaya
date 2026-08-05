from flask import Flask, jsonify, request
from flask_cors import CORS
import pandas as pd
import numpy as np
from sklearn.preprocessing import MinMaxScaler, OneHotEncoder
import os
import json
import glob
import joblib

from sklearn.metrics.pairwise import cosine_similarity
from ml_utils import (
    build_monthly_pivot,
    build_product_features,
    load_dataset,
    train_model,
    compute_metrics,
    recursive_forecast,
    inverse_transform_prediction,
    pad_history,
    load_model as load_xgb_model,
)

app = Flask(__name__)

# Enable CORS untuk semua routes
CORS(app, resources={r"/api/*": {"origins": "*"}})

# Gunakan absolute path
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
DATA_PATH = os.path.join(SCRIPT_DIR, 'dataset_atmobrass.csv')
MODEL_PATH = os.path.join(SCRIPT_DIR, 'xgboost_penjualan.json')
LOOKBACK = 60
default_lookback = LOOKBACK

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
product_models = {}
product_scalers = {}
product_lookbacks = {}
id_to_kategori = {}

static_meta = {}

# ===========================
# CONTENT BASED FILTERING - pemetaan preferensi kebutuhan ke kategori produk asli
# ===========================
PREFERENCE_KATEGORI_MAP = {
    "Pintu Rumah": ["Engsel", "Pemegang & Tombol"],
    "Lemari & Kabinet": ["Pemegang & Tombol", "Roda & Kaki Perabot"],
    "Furniture": ["Roda & Kaki Perabot", "Aksesori & Plat"],
    "Kantor & Bangunan Komersial": ["Aksesori & Plat", "Engsel"],
    "Dekorasi Interior": ["Aksesori & Plat", "Pemegang & Tombol"],
    "Renovasi & Proyek Bangunan": ["Engsel", "Roda & Kaki Perabot"],
}


def get_model_related_paths():
    paths = []
    if os.path.exists(MODEL_PATH):
        paths.append(MODEL_PATH)

    paths.extend(glob.glob(os.path.join(SCRIPT_DIR, 'xgboost_penjualan*.pkl')))
    paths.extend(glob.glob(os.path.join(SCRIPT_DIR, 'xgboost_penjualan*.json')))

    static_meta_path = os.path.join(SCRIPT_DIR, 'static_meta.pkl')
    if os.path.exists(static_meta_path):
        paths.append(static_meta_path)

    return paths


def get_model_files_mtime():
    paths = get_model_related_paths()
    if not paths:
        return None
    return max(os.path.getmtime(path) for path in paths)


def get_model_lookback(loaded_model):
    if loaded_model is None:
        return LOOKBACK
    try:
        input_shape = loaded_model.inputs[0].shape
        if len(input_shape) >= 2 and input_shape[1] is not None:
            return int(input_shape[1])
    except Exception:
        pass
    return LOOKBACK


def load_data_and_model():
    global pivot_sales, scaler_qty, kolom_produk, dict_nama_produk
    global static_features_dict, num_static_features, model
    global last_reload, data_mtime, model_mtime, default_lookback
    global id_to_kategori

    print("[INFO] Memuat ulang dataset dan model...")

    df = pd.read_csv(DATA_PATH)
    df['Tanggal_Pemesanan'] = pd.to_datetime(df['Tanggal_Pemesanan'])
    df['Bulan_Tahun'] = df['Tanggal_Pemesanan'].dt.to_period('M').dt.to_timestamp()

    monthly_sales = df.groupby(['ID_Produk', 'Bulan_Tahun'])['Quantity'].sum().reset_index()
    pivot_sales = monthly_sales.pivot(index='Bulan_Tahun', columns='ID_Produk', values='Quantity').fillna(0)
    pivot_sales = pivot_sales.resample('MS').asfreq().fillna(0)

    scaler_qty = MinMaxScaler()
    scaler_qty.fit_transform(pivot_sales)

    kolom_produk = pivot_sales.columns.tolist()
    dict_nama_produk = df.drop_duplicates('ID_Produk').set_index('ID_Produk')['Nama_Produk'].to_dict()
    id_to_kategori = df.drop_duplicates('ID_Produk').set_index('ID_Produk')['Kategori'].to_dict()

    static_meta_path = os.path.join(SCRIPT_DIR, 'static_meta.pkl')
    if os.path.exists(static_meta_path):
        static_meta = joblib.load(static_meta_path)
        static_features_dict = static_meta.get('static_vectors', {})
        if 'product_names' in static_meta:
            dict_nama_produk.update(static_meta['product_names'])
    else:
        product_features = df[['ID_Produk', 'Kategori', 'Bahan_Material', 'Warna_Finishing', 'Harga_Rupiah']].drop_duplicates('ID_Produk').set_index('ID_Produk')
        try:
            encoder = OneHotEncoder(sparse_output=False)
        except TypeError:
            encoder = OneHotEncoder(sparse=False)
        encoded_cats = encoder.fit_transform(product_features[['Kategori', 'Bahan_Material', 'Warna_Finishing']])

        scaler_harga = MinMaxScaler()
        scaled_harga = scaler_harga.fit_transform(product_features[['Harga_Rupiah']])

        static_features_dict = {}
        for i, prod_id in enumerate(product_features.index):
            static_features_dict[prod_id] = np.concatenate([encoded_cats[i], scaled_harga[i]])

    num_static_features = next(iter(static_features_dict.values())).shape[0] if static_features_dict else 0

    product_models.clear()
    product_scalers.clear()
    product_lookbacks.clear()

    model, product_encoder = load_xgb_model(MODEL_PATH, os.path.join(SCRIPT_DIR, 'xgboost_penjualan_meta.pkl'))
    if model is not None:
        default_lookback = 12
        print("[INFO] XGBoost model berhasil dimuat.")
    else:
        raise FileNotFoundError(f'Model XGBoost tidak ditemukan di {MODEL_PATH}')

    last_reload = pd.Timestamp.now()
    data_mtime = os.path.getmtime(DATA_PATH)
    model_mtime = get_model_files_mtime()
    print(f"[INFO] Reload lengkap: {last_reload}")


def get_predicted_month_label():
    if pivot_sales is None or pivot_sales.empty:
        return None

    next_month = pivot_sales.index.max() + pd.DateOffset(months=1)
    nama_bulan = {
        1: 'Januari', 2: 'Februari', 3: 'Maret', 4: 'April',
        5: 'Mei', 6: 'Juni', 7: 'Juli', 8: 'Agustus',
        9: 'September', 10: 'Oktober', 11: 'November', 12: 'Desember'
    }
    return f"{nama_bulan[next_month.month]} {next_month.year}"


def maybe_reload():
    global data_mtime, model_mtime
    try:
        current_data_mtime = os.path.getmtime(DATA_PATH)
        current_model_mtime = os.path.getmtime(MODEL_PATH)
        if data_mtime is None or model_mtime is None:
            load_data_and_model()
            return

        current_model_mtime = get_model_files_mtime()
        if data_mtime is None or model_mtime is None:
            load_data_and_model()
            return

        if current_data_mtime != data_mtime or current_model_mtime != model_mtime:
            load_data_and_model()
    except FileNotFoundError as e:
        print(f"[WARN] File tidak ditemukan saat reload: {e}")
    except Exception as e:
        print(f"[WARN] Gagal memeriksa reload: {e}")


def build_month_label(date):
    return date.strftime('%Y-%m')


def build_indonesian_month_label(date):
    nama_bulan = {
        1: 'Januari', 2: 'Februari', 3: 'Maret', 4: 'April',
        5: 'Mei', 6: 'Juni', 7: 'Juli', 8: 'Agustus',
        9: 'September', 10: 'Oktober', 11: 'November', 12: 'Desember'
    }
    return f"{nama_bulan.get(date.month, date.strftime('%B'))} {date.year}"


def get_product_model_and_scaler(prod_id):
    return model, scaler_qty, default_lookback


def evaluate_model_on_2025(epochs=60, batch_size=16):
    df = load_dataset(DATA_PATH)
    pivot = build_monthly_pivot(df)
    train_pivot = pivot.loc['2021-01-01':'2024-12-31']
    test_pivot = pivot.loc['2025-01-01':'2025-12-31']

    if train_pivot.empty or test_pivot.empty:
        raise ValueError('Data untuk evaluasi tidak mencukupi. Pastikan dataset mencapai 2025.')

    static_features, _, _, _ = build_product_features(df)

    need_fallback_model = (model is None) or any(prod_id not in product_models for prod_id in train_pivot.columns.tolist())
    eval_model = None
    eval_scaler = None
    eval_lookback = LOOKBACK

    if need_fallback_model:
        eval_lookback = min(LOOKBACK, len(train_pivot) - 1)
        if eval_lookback < 1:
            raise ValueError('Panjang data pelatihan tidak mencukupi untuk melakukan evaluasi backtest.')
        if eval_lookback != LOOKBACK:
            print(f"[WARN] Lookback disesuaikan menjadi {eval_lookback} untuk evaluasi karena data pelatihan hanya {len(train_pivot)} bulan.")

        eval_model, eval_scaler = train_model(
            train_pivot,
            static_features,
            eval_lookback,
            epochs=epochs,
            batch_size=batch_size,
            val_split=0.1,
        )

    months = [build_month_label(m) for m in test_pivot.index]
    monthly_total_actual = [0.0] * len(months)
    monthly_total_predicted = [0.0] * len(months)
    monthly_abs_error = [0.0] * len(months)

    overall_actual = []
    overall_predicted = []
    product_summary = []
    trend_actual = [0.0] * len(months)
    trend_predicted = [0.0] * len(months)

    for prod_idx, prod_id in enumerate(train_pivot.columns.tolist()):
        if prod_id not in static_features:
            continue

        if model is not None:
            history_scaled = scaler_qty.transform(train_pivot)[:, prod_idx].tolist()
            predictions = recursive_forecast(
                model,
                history_scaled,
                prod_id,
                None,
                1,
                len(months),
            )
        elif eval_model is not None:
            history_scaled = eval_scaler.transform(train_pivot)[:, prod_idx].tolist()
            predictions = recursive_forecast(
                eval_model,
                history_scaled,
                np.asarray(static_features[prod_id], dtype=np.float32),
                eval_scaler,
                prod_idx,
                eval_lookback,
                len(months),
            )
        else:
            raise ValueError(f'Tidak ada model yang tersedia untuk produk {prod_id} saat evaluasi backtest.')

        actuals = test_pivot[prod_id].fillna(0).astype(float).tolist()
        metrics = compute_metrics(actuals, predictions)

        sum_actual = float(sum(actuals))
        sum_predicted = float(sum(predictions))
        sum_error = float(sum(abs(np.asarray(predictions) - np.asarray(actuals))))

        for idx, value in enumerate(actuals):
            trend_actual[idx] += value
            trend_predicted[idx] += predictions[idx]
            monthly_total_actual[idx] += value
            monthly_total_predicted[idx] += predictions[idx]
            monthly_abs_error[idx] += abs(predictions[idx] - value)

        overall_actual.extend(actuals)
        overall_predicted.extend(predictions)

        product_summary.append({
            'id_produk': str(prod_id),
            'nama_produk': dict_nama_produk.get(prod_id, str(prod_id)),
            'aktual_total': sum_actual,
            'prediksi_total': sum_predicted,
            'error_total': sum_error,
            'mae': metrics['mae'],
            'mse': metrics['mse'],
            'rmse': metrics['rmse'],
            'mape': metrics['mape'],
            'actual_series': actuals,
            'predicted_series': predictions,
        })

    total_metrics = compute_metrics(overall_actual, overall_predicted)
    monthly_summary = []
    product_count = len(product_summary) if len(product_summary) > 0 else 1

    for idx, month in enumerate(months):
        monthly_summary.append({
            'month': month,
            'actual': monthly_total_actual[idx],
            'predicted': monthly_total_predicted[idx],
            'error': monthly_abs_error[idx] / product_count,
        })

    conclusion = (
        f"Model menghasilkan MAPE sebesar {total_metrics['mape']:.2f}%, "
        f"sehingga tingkat akurasi model sebesar {max(0.0, 100.0 - total_metrics['mape']):.2f}%.")

    return {
        'months': months,
        'overall': total_metrics,
        'conclusion': conclusion,
        'product_summary': product_summary,
        'monthly_summary': monthly_summary,
        'trend': {
            'actual': trend_actual,
            'predicted': trend_predicted,
        }
    }


def forecast_future_steps(steps=5):
    if pivot_sales is None or (model is None and not product_models):
        raise ValueError('Model atau data belum dimuat. Silakan reload terlebih dahulu.')

    if steps < 1:
        steps = 5
    if steps > 12:
        steps = 12

    last_month = pivot_sales.index.max()
    forecast_months = [
        build_indonesian_month_label(last_month + pd.DateOffset(months=i))
        for i in range(1, steps + 1)
    ]
    start_month = ((last_month.month) % 12) + 1

    scaled_sales = scaler_qty.transform(pivot_sales)
    overall_month_totals = [0.0] * steps
    forecast_products = []

    for prod_idx, prod_id in enumerate(kolom_produk):
        history_scaled = scaled_sales[:, prod_idx].tolist()
        prod_model, prod_scaler, prod_lookback = get_product_model_and_scaler(prod_id)
        predictions = recursive_forecast(
            prod_model,
            history_scaled,
            prod_id,
            None,
            start_month,
            steps,
        )

        monthly_values = []
        for idx, value in enumerate(predictions):
            monthly_values.append({
                'month': forecast_months[idx],
                'prediksi_pcs': int(np.ceil(value)),
                'quantity': float(value),
            })
            overall_month_totals[idx] += float(value)

        forecast_products.append({
            'id_produk': str(prod_id),
            'nama_produk': dict_nama_produk.get(prod_id, str(prod_id)),
            'monthly': monthly_values,
            'total': int(np.ceil(sum(predictions))),
        })

    return {
        'forecast_months': forecast_months,
        'products': forecast_products,
        'overall': {
            'monthly_total': [int(np.ceil(v)) for v in overall_month_totals],
            'total_forecast': int(np.ceil(sum(overall_month_totals))),
        }
    }


load_data_and_model()

@app.route('/api/predict', methods=['GET'])
def predict_produksi():
    try:
        maybe_reload()
        hasil_prediksi = []

        scaled_sales = scaler_qty.transform(pivot_sales)
        max_lookback = max([product_lookbacks.get(pid, default_lookback) for pid in kolom_produk] + [default_lookback])
        recent_sales = scaled_sales[-max_lookback:]

        for j, prod_id in enumerate(kolom_produk):
            prod_model, prod_scaler, prod_lookback = get_product_model_and_scaler(prod_id)
            history_values = recent_sales[:, j]
            if len(history_values) < prod_lookback:
                padded_history = np.concatenate([np.zeros(prod_lookback - len(history_values)), history_values])
            else:
                padded_history = history_values[-prod_lookback:]

            next_month = pivot_sales.index.max() + pd.DateOffset(months=1)
            feature_df = pd.DataFrame([{
                'Lag_1': float(padded_history[-1]) if len(padded_history) > 0 else 0.0,
                'Lag_2': float(padded_history[-2]) if len(padded_history) > 1 else 0.0,
                'Lag_3': float(padded_history[-3]) if len(padded_history) > 2 else 0.0,
                'Lag_6': float(padded_history[-6]) if len(padded_history) > 5 else 0.0,
                'Lag_12': float(padded_history[-12]) if len(padded_history) > 11 else 0.0,
                'MA_3': float(np.mean(padded_history[-3:])) if len(padded_history) >= 3 else float(np.mean(padded_history)),
                'MA_6': float(np.mean(padded_history[-6:])) if len(padded_history) >= 6 else float(np.mean(padded_history)),
                'STD_3': float(np.std(padded_history[-3:])) if len(padded_history) >= 3 else 0.0,
                'Month': int(next_month.month),
                'Quarter': ((int(next_month.month) - 1) // 3) + 1,
                'Produk': 0,
            }], columns=['Lag_1','Lag_2','Lag_3','Lag_6','Lag_12','MA_3','MA_6','STD_3','Month','Quarter','Produk'])
            prediksi_asli = float(prod_model.predict(feature_df)[0])
            jumlah_produksi = int(np.ceil(max(0.0, prediksi_asli)))
            jumlah_produksi = max(0, jumlah_produksi)

            hasil_prediksi.append({
                'id_produk': prod_id,
                'nama_barang': dict_nama_produk[prod_id],
                'prediksi_pcs': jumlah_produksi
            })

        next_month_label = get_predicted_month_label()

        return jsonify({
            'status': 'success',
            'pesan': 'Prediksi produksi bulan depan berhasil digenerate.',
            'prediksi_bulan': next_month_label,
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


@app.route('/api/recommend_by_preferences', methods=['GET'])
def recommend_by_preferences():
    try:
        maybe_reload()

        preference_param = request.args.get('preference', '').strip()
        n = int(request.args.get('n', 4))

        if not preference_param:
            return jsonify({
                'status': 'error',
                'pesan': 'Parameter preference diperlukan.'
            }), 400

        # Boleh lebih dari satu preferensi, dipisah koma
        preference_list = [p.strip() for p in preference_param.split(',') if p.strip()]

        # Terjemahkan preferensi kebutuhan ke kategori produk asli
        kategori_target = set()
        for pref in preference_list:
            kategori_target.update(PREFERENCE_KATEGORI_MAP.get(pref, []))

        if not kategori_target:
            return jsonify({
                'status': 'error',
                'pesan': 'Preferensi tidak dikenali.'
            }), 400

        product_ids = list(static_features_dict.keys())
        features = np.vstack([static_features_dict[pid] for pid in product_ids])

        # Hitung centroid vektor atribut PER KATEGORI target secara terpisah,
        # lalu ambil skor similarity TERTINGGI antar centroid untuk tiap produk.
        # (Menghindari kategori minoritas "kalah" akibat dirata-ratakan jadi satu vektor gabungan.)
        max_scores = np.zeros(len(product_ids))
        found_any_seed = False
        for kat in kategori_target:
            seed_ids_kat = [pid for pid, k in id_to_kategori.items() if k == kat and pid in static_features_dict]
            if not seed_ids_kat:
                continue
            found_any_seed = True
            centroid = np.vstack([static_features_dict[pid] for pid in seed_ids_kat]).mean(axis=0).reshape(1, -1)
            sims = cosine_similarity(centroid, features)[0]
            max_scores = np.maximum(max_scores, sims)

        if not found_any_seed:
            return jsonify({'status': 'error', 'pesan': 'Tidak ada produk yang cocok dengan preferensi tersebut.'}), 404

        paired = list(zip(product_ids, max_scores))
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


@app.route('/api/history/products', methods=['GET'])
def get_products_timeseries():
    try:
        maybe_reload()

        # Use the precomputed pivot_sales (monthly index, columns=ID_Produk)
        idx = pivot_sales.index.to_list()
        # Format labels as 'YYYY-MM' for clarity
        labels = [d.strftime('%Y-%m') for d in idx]

        products = []
        for pid in kolom_produk:
            series = pivot_sales[pid].fillna(0).astype(int).tolist()
            products.append({
                'id_produk': str(pid),
                'nama_produk': dict_nama_produk.get(pid, ''),
                'series': series
            })

        return jsonify({
            'status': 'success',
            'labels': labels,
            'products': products
        }), 200

    except Exception as e:
        print(f"[ERROR] History products endpoint: {str(e)}")
        import traceback
        traceback.print_exc()
        return jsonify({'status': 'error', 'pesan': str(e)}), 500


@app.route('/api/evaluate', methods=['GET'])
def evaluate_model():
    try:
        maybe_reload()
        epochs = int(request.args.get('epochs', 60))
        batch_size = int(request.args.get('batch_size', 16))

        evaluation = evaluate_model_on_2025(epochs=epochs, batch_size=batch_size)
        return jsonify({
            'status': 'success',
            'data': evaluation
        }), 200
    except Exception as e:
        print(f"[ERROR] Evaluate endpoint: {str(e)}")
        import traceback
        traceback.print_exc()
        return jsonify({
            'status': 'error',
            'pesan': f"Gagal melakukan evaluasi model: {str(e)}"
        }), 500


@app.route('/api/forecast', methods=['GET'])
def forecast_model():
    try:
        maybe_reload()
        steps = int(request.args.get('steps', 5))
        forecast = forecast_future_steps(steps=steps)
        return jsonify({
            'status': 'success',
            'data': forecast
        }), 200
    except Exception as e:
        print(f"[ERROR] Forecast endpoint: {str(e)}")
        import traceback
        traceback.print_exc()
        return jsonify({
            'status': 'error',
            'pesan': f"Gagal melakukan forecast: {str(e)}"
        }), 500


import os

if __name__ == '__main__':
    port = int(os.environ.get("PORT", 5000))
    app.run(host='0.0.0.0', port=port)