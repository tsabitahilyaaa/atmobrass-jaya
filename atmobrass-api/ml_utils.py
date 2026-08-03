import os
import numpy as np
import pandas as pd
from sklearn.preprocessing import MinMaxScaler, OneHotEncoder, LabelEncoder
import joblib
import xgboost as xgb

FEATURE_COLUMNS = [
    'Lag_1', 'Lag_2', 'Lag_3', 'Lag_6', 'Lag_12',
    'MA_3', 'MA_6', 'STD_3', 'Month', 'Quarter', 'Produk'
]
DEFAULT_MODEL_PATH = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'xgboost_penjualan.json')
DEFAULT_META_PATH = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'xgboost_penjualan_meta.pkl')


def load_dataset(path):
    df = pd.read_csv(path)
    df['Tanggal_Pemesanan'] = pd.to_datetime(df['Tanggal_Pemesanan'])
    return df


def build_monthly_pivot(df, start_date=None, end_date=None):
    df = df.copy()
    df['Bulan_Tahun'] = df['Tanggal_Pemesanan'].dt.to_period('M').dt.to_timestamp()
    monthly_sales = (
        df.groupby(['ID_Produk', 'Bulan_Tahun'])['Quantity']
        .sum()
        .reset_index()
    )

    pivot = monthly_sales.pivot(index='Bulan_Tahun', columns='ID_Produk', values='Quantity').fillna(0)
    pivot = pivot.resample('MS').asfreq().fillna(0)

    if start_date is not None:
        pivot = pivot.loc[pd.to_datetime(start_date):]
    if end_date is not None:
        pivot = pivot.loc[:pd.to_datetime(end_date)]

    return pivot


def build_product_features(df):
    product_features = (
        df[['ID_Produk', 'Nama_Produk', 'Kategori', 'Bahan_Material', 'Warna_Finishing', 'Harga_Rupiah']]
        .drop_duplicates('ID_Produk')
        .set_index('ID_Produk')
    )

    try:
        encoder = OneHotEncoder(sparse_output=False)
    except TypeError:
        encoder = OneHotEncoder(sparse=False)

    encoded_cats = encoder.fit_transform(product_features[['Kategori', 'Bahan_Material', 'Warna_Finishing']])
    scaler_price = MinMaxScaler()
    scaled_price = scaler_price.fit_transform(product_features[['Harga_Rupiah']])

    static_features = {}
    for i, prod_id in enumerate(product_features.index):
        static_features[prod_id] = np.concatenate([encoded_cats[i], scaled_price[i]])

    return static_features, product_features['Nama_Produk'].to_dict(), encoder, scaler_price


def build_feature_dataset(pivot_sales, product_encoder=None):
    rows = []
    targets = []

    product_ids = list(pivot_sales.columns)
    if product_encoder is None:
        product_encoder = LabelEncoder()
        product_encoder.fit(product_ids)

    for prod_id in product_ids:
        series = pivot_sales[prod_id].astype(float).fillna(0).to_numpy()
        for idx in range(12, len(series)):
            history = series[:idx]
            lag_1 = float(history[-1]) if len(history) >= 1 else 0.0
            lag_2 = float(history[-2]) if len(history) >= 2 else 0.0
            lag_3 = float(history[-3]) if len(history) >= 3 else 0.0
            lag_6 = float(history[-6]) if len(history) >= 6 else 0.0
            lag_12 = float(history[-12]) if len(history) >= 12 else 0.0
            ma_3 = float(np.mean(history[-3:])) if len(history) >= 3 else float(np.mean(history))
            ma_6 = float(np.mean(history[-6:])) if len(history) >= 6 else float(np.mean(history))
            std_3 = float(np.std(history[-3:])) if len(history) >= 3 else 0.0
            month = int(pivot_sales.index[idx].month)
            quarter = ((month - 1) // 3) + 1
            rows.append({
                'Lag_1': lag_1,
                'Lag_2': lag_2,
                'Lag_3': lag_3,
                'Lag_6': lag_6,
                'Lag_12': lag_12,
                'MA_3': ma_3,
                'MA_6': ma_6,
                'STD_3': std_3,
                'Month': month,
                'Quarter': quarter,
                'Produk': int(product_encoder.transform([prod_id])[0]),
            })
            targets.append(float(series[idx]))

    if not rows:
        raise ValueError('Tidak ada sampel pelatihan tersedia untuk XGBoost.')

    features_df = pd.DataFrame(rows, columns=FEATURE_COLUMNS)
    return features_df, np.asarray(targets, dtype=np.float32), product_encoder


def build_model(lookback=None, static_dim=None, hidden_units=64, dropout_rate=0.2):
    model = xgb.XGBRegressor(
        n_estimators=300,
        learning_rate=0.05,
        max_depth=6,
        subsample=0.8,
        colsample_bytree=0.8,
        random_state=42,
        objective='reg:squarederror',
    )
    return model


def train_model(pivot_sales, static_features, lookback=None, epochs=80, batch_size=32, val_split=0.15, model_path=DEFAULT_MODEL_PATH):
    features_df, target_values, product_encoder = build_feature_dataset(pivot_sales)
    model = build_model()
    model.fit(features_df, target_values)

    if model_path:
        model.save_model(model_path)
        meta = {
            'feature_columns': FEATURE_COLUMNS,
            'product_encoder': product_encoder,
        }
        joblib.dump(meta, DEFAULT_META_PATH if model_path == DEFAULT_MODEL_PATH else model_path.replace('.json', '_meta.pkl'))

    return model, product_encoder


def load_model(model_path=DEFAULT_MODEL_PATH, meta_path=DEFAULT_META_PATH):
    if not os.path.exists(model_path):
        return None, None

    model = xgb.XGBRegressor()
    model.load_model(model_path)
    product_encoder = None
    if os.path.exists(meta_path):
        meta = joblib.load(meta_path)
        product_encoder = meta.get('product_encoder', None)
    return model, product_encoder


def pad_history(history, lookback):
    history = list(history)
    if len(history) >= lookback:
        return history[-lookback:]
    return [0.0] * (lookback - len(history)) + history


def inverse_transform_prediction(scaler_qty, prod_idx, scaled_value):
    return float(max(0.0, scaled_value))


def make_feature_row(history, product_id, month_number, product_encoder=None):
    history = [max(0.0, float(value)) for value in history]
    if product_encoder is not None:
        product_code = int(product_encoder.transform([product_id])[0])
    else:
        product_code = 0

    lag_1 = history[-1] if len(history) >= 1 else 0.0
    lag_2 = history[-2] if len(history) >= 2 else 0.0
    lag_3 = history[-3] if len(history) >= 3 else 0.0
    lag_6 = history[-6] if len(history) >= 6 else 0.0
    lag_12 = history[-12] if len(history) >= 12 else 0.0
    ma_3 = float(np.mean(history[-3:])) if len(history) >= 3 else float(np.mean(history))
    ma_6 = float(np.mean(history[-6:])) if len(history) >= 6 else float(np.mean(history))
    std_3 = float(np.std(history[-3:])) if len(history) >= 3 else 0.0
    quarter = ((month_number - 1) // 3) + 1

    return pd.DataFrame([{
        'Lag_1': lag_1,
        'Lag_2': lag_2,
        'Lag_3': lag_3,
        'Lag_6': lag_6,
        'Lag_12': lag_12,
        'MA_3': ma_3,
        'MA_6': ma_6,
        'STD_3': std_3,
        'Month': month_number,
        'Quarter': quarter,
        'Produk': product_code,
    }], columns=FEATURE_COLUMNS)


def recursive_forecast(model, history_scaled, product_id, product_encoder, start_month, steps):
    history = [max(0.0, float(value)) for value in history_scaled]
    predictions = []

    for step in range(steps):
        month_number = ((start_month - 1 + step) % 12) + 1
        feature_row = make_feature_row(history, product_id, month_number, product_encoder)
        pred_scaled = float(model.predict(feature_row)[0])
        predicted_quantity = max(0.0, pred_scaled)
        predictions.append(predicted_quantity)
        history.append(predicted_quantity)

    return predictions


def compute_metrics(actual, predicted):
    actual_arr = np.asarray(actual, dtype=np.float32)
    predicted_arr = np.asarray(predicted, dtype=np.float32)

    if actual_arr.shape != predicted_arr.shape:
        raise ValueError('Ukuran actual dan predicted harus sama untuk perhitungan metrik.')

    diffs = predicted_arr - actual_arr
    mae = float(np.mean(np.abs(diffs)))
    mse = float(np.mean(np.square(diffs)))
    rmse = float(np.sqrt(mse))

    total_actual = float(np.sum(actual_arr))
    total_predicted = float(np.sum(predicted_arr))

    if total_actual != 0.0:
        mape = float(abs(total_predicted - total_actual) / total_actual * 100)
    else:
        mape = 0.0

    return {
        'mae': mae,
        'mse': mse,
        'rmse': rmse,
        'mape': mape,
    }
