<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalRevenue = Order::where('status', '!=', 'pending')->sum('total_amount');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalUsers = User::where('role', 'customer')->count();

        // Bulanan
        $monthlyLabels = [];
        $monthlyValues = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $date->locale('id_ID')->translatedFormat('M');
            $monthlyValues[] = Order::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->where('status', '!=', 'pending')
                ->sum('total_amount');
        }

        // Tahunan
        $yearlyLabels = [];
        $yearlyValues = [];
        for ($i = 2; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            $yearlyLabels[] = (string) $year;
            $yearlyValues[] = Order::whereYear('created_at', $year)
                ->where('status', '!=', 'pending')
                ->sum('total_amount');
        }

        // Kategori
        $categoryData = Category::withCount('products')->get();
        $catLabels = $categoryData->pluck('name')->toArray();
        $catValues = $categoryData->pluck('products_count')->toArray();

        // Prediksi Linear Regression
        $predicted = $this->predictNext($monthlyValues);

        return view('admin.dashboard', compact(
            'totalRevenue', 'totalOrders', 'totalProducts', 'totalUsers',
            'monthlyLabels', 'monthlyValues',
            'yearlyLabels', 'yearlyValues',
            'catLabels', 'catValues',
            'predicted'
        ));
    }

    public function lstm()
    {
        $predictions = collect();
        $history = null;
        $error = null;
        $historyError = null;
        $apiBase = config('app.python_api_url', 'http://127.0.0.1:5000');

        $predictedMonth = null;
        try {
            $response = Http::timeout(10)->get("{$apiBase}/api/predict");
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
            } else {
                $error = 'Gagal memuat prediksi LSTM. Status: ' . $response->status();
            }
        } catch (\Exception $e) {
            $error = 'Gagal menghubungi server ML: ' . $e->getMessage();
        }

        // Fetch history data
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

        return view('admin.lstm', compact('predictions', 'error', 'history', 'historyError', 'predictedMonth'));
    }

    public function history()
    {
        $history = null;
        $productsData = null;
        $historyError = null;
        $error = null;
        $apiBase = config('app.python_api_url', 'http://127.0.0.1:5000');

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

        return view('admin.history', compact('history', 'productsData', 'historyError', 'error'));
    }

    public function reloadLstm(Request $request)
    {
        $apiBase = config('app.python_api_url', 'http://127.0.0.1:5000');
        $error = null;

        try {
            $response = Http::timeout(10)->post("{$apiBase}/api/reload");
            if ($response->successful()) {
                return redirect()->route('admin.lstm')->with('status', 'Reload dataset dan model berhasil.');
            }

            $error = 'Gagal memuat ulang ML server. Status: ' . $response->status();
        } catch (\Exception $e) {
            $error = 'Gagal menghubungi server ML: ' . $e->getMessage();
        }

        return redirect()->route('admin.lstm')->with('error', $error);
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