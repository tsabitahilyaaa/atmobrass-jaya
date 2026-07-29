import numpy as np
import pandas as pd
from sklearn.preprocessing import MinMaxScaler, OneHotEncoder
from tensorflow.keras.layers import Input, LSTM, Dense, Concatenate, Dropout
from tensorflow.keras.models import Model
from tensorflow.keras.optimizers import Adam


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


def build_sequences(pivot_sales, static_features, lookback):
    scaler_qty = MinMaxScaler()
    scaled_sales = scaler_qty.fit_transform(pivot_sales)
    product_ids = pivot_sales.columns.tolist()
    n_months = scaled_sales.shape[0]

    X_time = []
    X_static = []
    y = []
    product_index = []

    for j, prod_id in enumerate(product_ids):
        for start in range(0, n_months - lookback):
            X_time.append(scaled_sales[start:start + lookback, j].reshape(lookback, 1))
            X_static.append(static_features[prod_id])
            y.append(scaled_sales[start + lookback, j])
            product_index.append(prod_id)

    if len(X_time) == 0:
        raise ValueError('Tidak ada sampel pelatihan yang tersedia. Periksa nilai lookback dan panjang data.')

    X_time = np.asarray(X_time, dtype=np.float32)
    X_static = np.asarray(X_static, dtype=np.float32)
    y = np.asarray(y, dtype=np.float32).reshape(-1, 1)

    return X_time, X_static, y, scaler_qty, product_ids, product_index


def build_model(lookback, static_dim, hidden_units=64, dropout_rate=0.2):
    time_input = Input(shape=(lookback, 1), name='input_waktu')
    x_time = LSTM(hidden_units, activation='tanh')(time_input)
    x_time = Dropout(dropout_rate)(x_time)

    static_input = Input(shape=(static_dim,), name='input_fitur')
    x_static = Dense(hidden_units // 2, activation='relu')(static_input)
    x_static = Dropout(dropout_rate)(x_static)

    merged = Concatenate()([x_time, x_static])
    x = Dense(hidden_units, activation='relu')(merged)
    x = Dropout(dropout_rate)(x)
    x = Dense(hidden_units // 2, activation='relu')(x)
    output = Dense(1, activation='linear')(x)

    model = Model(inputs=[time_input, static_input], outputs=output)
    model.compile(optimizer=Adam(learning_rate=0.001), loss='mse', metrics=['mae'])
    return model


def train_model(pivot_sales, static_features, lookback, epochs=80, batch_size=32, val_split=0.15):
    X_time, X_static, y, scaler_qty, product_ids, product_index = build_sequences(
        pivot_sales, static_features, lookback
    )

    model = build_model(lookback, X_static.shape[1])

    callbacks = []
    try:
        from tensorflow.keras.callbacks import EarlyStopping, ModelCheckpoint, ReduceLROnPlateau
        callbacks = [
            EarlyStopping(monitor='val_loss', patience=10, restore_best_weights=True, verbose=1),
            ReduceLROnPlateau(monitor='val_loss', factor=0.5, patience=5, verbose=1, min_lr=1e-6),
        ]
    except Exception:
        callbacks = []

    history = model.fit(
        {'input_waktu': X_time, 'input_fitur': X_static},
        y,
        validation_split=val_split,
        epochs=epochs,
        batch_size=batch_size,
        callbacks=callbacks,
        verbose=0,
    )

    return model, scaler_qty


def pad_history(history, lookback):
    history = list(history)
    if len(history) >= lookback:
        return history[-lookback:]
    return [0.0] * (lookback - len(history)) + history


def inverse_transform_prediction(scaler_qty, prod_idx, scaled_value):
    candidate = np.zeros((1, scaler_qty.scale_.shape[0]))
    candidate[0, prod_idx] = scaled_value
    inv = scaler_qty.inverse_transform(candidate)
    return float(inv[0, prod_idx])


def recursive_forecast(model, history_scaled, static_feature, scaler_qty, prod_idx, lookback, steps):
    history = pad_history(history_scaled, lookback)
    predictions = []

    for _ in range(steps):
        sequence = np.asarray(history[-lookback:], dtype=np.float32).reshape(1, lookback, 1)
        pred_scaled = float(model.predict({'input_waktu': sequence, 'input_fitur': static_feature.reshape(1, -1)}, verbose=0)[0][0])
        predicted_quantity = inverse_transform_prediction(scaler_qty, prod_idx, pred_scaled)
        predicted_quantity = max(0.0, predicted_quantity)
        predictions.append(predicted_quantity)
        history.append(pred_scaled)

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

    nonzero_mask = actual_arr != 0
    if np.any(nonzero_mask):
        mape = float(np.mean(np.abs(diffs[nonzero_mask] / actual_arr[nonzero_mask])) * 100)
    else:
        mape = 0.0

    return {
        'mae': mae,
        'mse': mse,
        'rmse': rmse,
        'mape': mape,
    }
