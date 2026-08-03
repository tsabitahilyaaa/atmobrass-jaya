import os
import argparse
import pandas as pd
from ml_utils import build_monthly_pivot, build_product_features, train_model

SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
DATA_PATH = os.path.join(SCRIPT_DIR, 'dataset_atmobrass.csv')
DEFAULT_MODEL_PATH = os.path.join(SCRIPT_DIR, 'xgboost_penjualan.json')


def load_dataset(path):
    df = pd.read_csv(path)
    df['Tanggal_Pemesanan'] = pd.to_datetime(df['Tanggal_Pemesanan'])
    return df


def parse_args():
    parser = argparse.ArgumentParser(description='Train XGBoost model for Atmobrass sales forecasting')
    parser.add_argument('--epochs', type=int, default=80, help='Training epochs')
    parser.add_argument('--output', type=str, default=DEFAULT_MODEL_PATH, help='Output XGBoost model path')
    return parser.parse_args()


if __name__ == '__main__':
    args = parse_args()
    print('Training configuration:')
    print(f'  epochs: {args.epochs}')
    print(f'  output: {args.output}')

    df = load_dataset(DATA_PATH)
    pivot_sales = build_monthly_pivot(df)
    static_features, nama_produk, encoder, scaler_price = build_product_features(df)

    print(f'Jumlah produk   : {pivot_sales.shape[1]}')
    print(f'Jumlah bulan    : {pivot_sales.shape[0]}')

    train_model(
        pivot_sales,
        static_features,
        model_path=args.output,
    )
    print(f'XGBoost model saved to: {args.output}')
