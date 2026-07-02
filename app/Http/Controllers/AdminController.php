<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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