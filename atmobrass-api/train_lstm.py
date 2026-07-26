import os
import argparse

import numpy as np
import pandas as pd
from sklearn.preprocessing import MinMaxScaler, OneHotEncoder
from tensorflow.keras.callbacks import EarlyStopping, ModelCheckpoint, ReduceLROnPlateau
from tensorflow.keras.layers import Input, LSTM, Dense, Concatenate, Dropout
from tensorflow.keras.models import Model
from tensorflow.keras.optimizers import Adam

SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
DATA_PATH = os.path.join(SCRIPT_DIR, 'dataset_atmobrass.csv')
DEFAULT_MODEL_PATH = os.path.join(SCRIPT_DIR, 'model_atmobrass.h5')


def load_dataset(path):
    df = pd.read_csv(path)
    df['Tanggal_Pemesanan'] = pd.to_datetime(df['Tanggal_Pemesanan'])
    return df


def build_monthly_pivot(df):
    df['Bulan_Tahun'] = df['Tanggal_Pemesanan'].dt.to_period('M').dt.to_timestamp()
    monthly_sales = (
        df.groupby(['ID_Produk', 'Bulan_Tahun'])['Quantity']
        .sum()
        .reset_index()
    )
    pivot = monthly_sales.pivot(index='Bulan_Tahun', columns='ID_Produk', values='Quantity').fillna(0)
    pivot = pivot.resample('MS').asfreq().fillna(0)
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

    encoded = encoder.fit_transform(product_features[['Kategori', 'Bahan_Material', 'Warna_Finishing']])
    scaler_price = MinMaxScaler()
    scaled_price = scaler_price.fit_transform(product_features[['Harga_Rupiah']])

    static_features = {}
    for i, prod_id in enumerate(product_features.index):
        static_features[prod_id] = np.concatenate([encoded[i], scaled_price[i]])

    return static_features, product_features['Nama_Produk'].to_dict(), encoder, scaler_price


def build_sequences(pivot_sales, static_features, lookback):
    scaler_qty = MinMaxScaler()
    scaled = scaler_qty.fit_transform(pivot_sales)
    product_ids = pivot_sales.columns.tolist()
    n_months = scaled.shape[0]

    X_time = []
    X_static = []
    y = []
    product_index = []

    for j, prod_id in enumerate(product_ids):
        for start in range(0, n_months - lookback):
            seq = scaled[start:start + lookback, j].reshape(lookback, 1)
            target = scaled[start + lookback, j]
            X_time.append(seq)
            X_static.append(static_features[prod_id])
            y.append(target)
            product_index.append(prod_id)

    X_time = np.array(X_time, dtype=np.float32)
    X_static = np.array(X_static, dtype=np.float32)
    y = np.array(y, dtype=np.float32).reshape(-1, 1)

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


def train_model(
    lookback=60,
    epochs=80,
    batch_size=32,
    val_split=0.15,
    model_path=DEFAULT_MODEL_PATH,
):
    df = load_dataset(DATA_PATH)
    pivot_sales = build_monthly_pivot(df)
    static_features, nama_produk, encoder, scaler_price = build_product_features(df)

    X_time, X_static, y, scaler_qty, product_ids, product_index = build_sequences(
        pivot_sales, static_features, lookback
    )

    print(f'Jumlah produk   : {len(product_ids)}')
    print(f'Jumlah bulan    : {pivot_sales.shape[0]}')
    print(f'Samples         : {X_time.shape[0]}')
    print(f'Lookback window : {lookback}')

    model = build_model(lookback, X_static.shape[1])
    model.summary()

    callbacks = [
        EarlyStopping(monitor='val_loss', patience=10, restore_best_weights=True, verbose=1),
        ReduceLROnPlateau(monitor='val_loss', factor=0.5, patience=5, verbose=1, min_lr=1e-6),
        ModelCheckpoint(model_path, monitor='val_loss', save_best_only=True, verbose=1),
    ]

    history = model.fit(
        {'input_waktu': X_time, 'input_fitur': X_static},
        y,
        validation_split=val_split,
        epochs=epochs,
        batch_size=batch_size,
        callbacks=callbacks,
        verbose=2,
    )

    print(f'Best model saved to: {model_path}')
    return model, scaler_qty, encoder, scaler_price, nama_produk


def parse_args():
    parser = argparse.ArgumentParser(description='Train LSTM model for Atmobrass sales forecasting')
    parser.add_argument('--lookback', type=int, default=60, help='Number of months to use as input sequence')
    parser.add_argument('--epochs', type=int, default=80, help='Training epochs')
    parser.add_argument('--batch_size', type=int, default=32, help='Batch size')
    parser.add_argument('--val_split', type=float, default=0.15, help='Validation split')
    parser.add_argument('--output', type=str, default=DEFAULT_MODEL_PATH, help='Output H5 model path')
    return parser.parse_args()


if __name__ == '__main__':
    args = parse_args()
    MODEL_PATH = args.output
    print('Training configuration:')
    print(f'  lookback: {args.lookback}')
    print(f'  epochs: {args.epochs}')
    print(f'  batch_size: {args.batch_size}')
    print(f'  val_split: {args.val_split}')
    print(f'  output: {args.output}')

    train_model(
        lookback=args.lookback,
        epochs=args.epochs,
        batch_size=args.batch_size,
        val_split=args.val_split,
        model_path=args.output,
    )
