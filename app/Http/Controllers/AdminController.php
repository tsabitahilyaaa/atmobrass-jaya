<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\ContactMessage;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $validOrderStatuses = ['paid', 'processing', 'shipped', 'completed'];

        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalUsers = User::where('role', 'customer')->count();

        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $incomingMessagesThisWeek = ContactMessage::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $newUsersThisWeek = User::where('role', 'customer')->whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $newCustomerOrdersThisWeek = Order::whereHas('user', function ($query) use ($weekStart, $weekEnd) {
            $query->where('role', 'customer')
                  ->whereBetween('created_at', [$weekStart, $weekEnd]);
        })->count();

        $predictionLabels = [];
        $predictionQuantityValues = [];
        for ($i = 2; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $predictionLabels[] = $date->locale('id_ID')->translatedFormat('M');
            $predictionQuantityValues[] = OrderItem::whereHas('order', function ($query) use ($date, $validOrderStatuses) {
                    $query->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->whereIn('status', $validOrderStatuses);
                })->sum('quantity');
        }

        // XGBoost prediction total
        $predictedQuantity = 0;
        $predictedMonth = Carbon::now()->addMonth()->locale('id_ID')->translatedFormat('F Y');
        $predictedItemsCount = 0;
        $predictionStatus = 'warning';
        $predictionMessage = 'Prediksi XGBoost belum tersedia.';
        $apiBase = config('ml.python_api_url', 'http://127.0.0.1:5000');

        try {
            $response = Http::timeout(30)->get("{$apiBase}/api/predict");
            if ($response->successful()) {
                $data = $response->json();
                $rawPredictions = $data['data'] ?? [];
                $predictedMonth = $data['prediksi_bulan'] ?? $predictedMonth;

                if (!empty($rawPredictions)) {
                    $predictedQuantity = array_sum(array_column($rawPredictions, 'prediksi_pcs'));
                    $predictedItemsCount = count($rawPredictions);
                    $predictionStatus = 'success';
                    $predictionMessage = "Prediksi produksi untuk {$predictedMonth}.";
                } else {
                    $predictionMessage = 'Prediksi XGBoost tidak tersedia saat ini.';
                }
            } else {
                $predictionStatus = 'error';
                $predictionMessage = 'Gagal memuat prediksi XGBoost. Status: ' . $response->status();
            }
        } catch (\Exception $e) {
            $predictionStatus = 'error';
            $predictionMessage = 'Gagal menghubungi server ML: ' . $e->getMessage();
        }

        return view('admin.dashboard', compact(
            'totalOrders', 'totalProducts', 'totalUsers',
            'incomingMessagesThisWeek', 'newUsersThisWeek', 'newCustomerOrdersThisWeek',
            'predictedQuantity', 'predictedMonth', 'predictedItemsCount',
            'predictionStatus', 'predictionMessage'
        ));
    }

    public function xgboost()
    {
        $predictions = collect();
        $history = null;
        $error = null;
        $historyError = null;
        $forecast = null;
        $forecastError = null;
        $apiBase = config('ml.python_api_url', 'http://127.0.0.1:5000');

        $predictedMonth = null;
        try {
            $response = Http::timeout(30)->get("{$apiBase}/api/predict");
                if ($response->successful()) {
                $data = $response->json();
                $rawPredictions = $data['data'] ?? [];
                foreach ($rawPredictions as $item) {
                    $predictions->push((object) [
                        'id_produk' => $item['id_produk'] ?? null,
                        'nama_barang' => $item['nama_barang'] ?? null,
                        'prediksi_pcs' => $item['prediksi_pcs'] ?? null,
                    ]);
                }
                $predictedMonth = $data['prediksi_bulan'] ?? null;

                if (empty($rawPredictions)) {
                    $error = 'Prediksi belum tersedia atau model belum menghasilkan output. Silakan cek backend Python.';
                }
                } else {
                    $error = 'Gagal memuat prediksi XGBoost. Status: ' . $response->status();
                }
        } catch (\Exception $e) {
            $error = 'Gagal menghubungi server ML: ' . $e->getMessage();
        }

        if (empty($predictedMonth)) {
            $predictedMonth = Carbon::now()->addMonth()->locale('id_ID')->translatedFormat('F Y');
        }

        // Fetch history data
        try {
            $historyResponse = Http::timeout(30)->get("{$apiBase}/api/history");
            if ($historyResponse->successful()) {
                $history = $historyResponse->json();
            } else {
                $historyError = 'Gagal memuat riwayat penjualan. Status: ' . $historyResponse->status();
            }
        } catch (\Exception $e) {
            $historyError = 'Gagal menghubungi server ML: ' . $e->getMessage();
        }

        // Fetch multi-step forecast
        try {
            $forecastResponse = Http::timeout(60)->get("{$apiBase}/api/forecast", ['steps' => 5]);
            if ($forecastResponse->successful()) {
                $forecast = $forecastResponse->json('data');
            } else {
                $forecastError = 'Gagal memuat forecast. Status: ' . $forecastResponse->status();
            }
        } catch (\Exception $e) {
            $forecastError = 'Gagal menghubungi server forecast ML: ' . $e->getMessage();
        }

        return view('admin.xgboost', compact(
            'predictions', 'error', 'history', 'historyError', 'predictedMonth',
            'forecast', 'forecastError'
        ));
    }

    public function history()
    {
        $history = null;
        $productsData = null;
        $historyError = null;
        $evaluation = null;
        $evaluationError = null;
        $error = null;
        $apiBase = config('ml.python_api_url', 'http://127.0.0.1:5000');

        try {
            $historyResponse = Http::timeout(10)->get("{$apiBase}/api/history");
            if ($historyResponse->successful()) {
                $history = $historyResponse->json();
            } else {
                $historyError = 'Gagal memuat riwayat penjualan. Status: ' . $historyResponse->status();
            }
        } catch (\Exception $e) {
            $historyError = 'Gagal menghubungi server ML: ' . $e->getMessage();
        }

        // Fetch evaluation results for 2025 backtest
        try {
            $evaluationResponse = Http::timeout(120)->get("{$apiBase}/api/evaluate");
            if ($evaluationResponse->successful()) {
                $evaluation = $evaluationResponse->json('data');
            } else {
                $evaluationError = 'Gagal memuat evaluasi model. Status: ' . $evaluationResponse->status();
            }
        } catch (\Exception $e) {
            $evaluationError = 'Gagal menghubungi server evaluasi ML: ' . $e->getMessage();
        }

        // Fetch products timeseries
        try {
            $resp = Http::timeout(10)->get("{$apiBase}/api/history/products");
            if ($resp->successful()) {
                $productsData = $resp->json();
            } else {
                $error = 'Gagal memuat data produk. Status: ' . $resp->status();
            }
        } catch (\Exception $e) {
            $error = 'Gagal menghubungi server ML: ' . $e->getMessage();
        }

        return view('admin.history', compact('history', 'productsData', 'historyError', 'error', 'evaluation', 'evaluationError'));
    }

    public function payment()
    {
        $qrisImage = $this->qrisImageUrl();
        return view('admin.payment', compact('qrisImage'));
    }

    public function savePayment(Request $request)
    {
        $request->validate([
            'qris_image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $folder = public_path('images/pembayaran');
        if (! file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        foreach (glob($folder . '/qris.*') as $file) {
            @unlink($file);
        }

        $request->file('qris_image')->move($folder, 'qris.' . $request->file('qris_image')->extension());

        return redirect()->route('admin.payment')->with('success', 'QRIS berhasil diperbarui.');
    }

    public function reloadXgboost(Request $request)
    {
        $apiBase = config('ml.python_api_url', 'http://127.0.0.1:5000');
        $error = null;

        try {
            $response = Http::timeout(10)->post("{$apiBase}/api/reload");
            if ($response->successful()) {
                return redirect()->route('admin.xgboost')->with('status', 'Reload dataset dan model berhasil.');
            }

            $error = 'Gagal memuat ulang ML server. Status: ' . $response->status();
        } catch (\Exception $e) {
            $error = 'Gagal menghubungi server ML: ' . $e->getMessage();
        }

        return redirect()->route('admin.xgboost')->with('error', $error);
    }

    private function predictNext($data)
    {
        $n = count($data);
        if ($n < 2) return isset($data[$n - 1]) ? $data[$n - 1] : 0;

        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumX += $i;
            $sumY += $data[$i];
            $sumXY += $i * $data[$i];
            $sumX2 += $i * $i;
        }

        $denominator = $n * $sumX2 - $sumX * $sumX;

        if ($denominator == 0) {
            return isset($data[$n - 1]) ? $data[$n - 1] : 0;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / $denominator;
        $intercept = ($sumY - $slope * $sumX) / $n;

        $result = $slope * $n + $intercept;

        return max(0, (int) round($result));
    }
}