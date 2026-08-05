<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{


    public function index(Request $request)
    {
        $categories = Category::withCount('products')->get();
        $products = Product::with('category')->where('is_active', true)->latest()->take(12)->get();
        $featured = Product::where('is_active', true)
            ->whereIn('id', [2, 7, 13, 15])
            ->get();
        $featuredProducts = $featured;

        $preferences = $this->resolvePreferences();
        $recommended = $this->buildRecommendedProducts($preferences);
        $recommendedProducts = $recommended;
        $showPreferenceModal = !auth()->check() && !session('preference_modal_dismissed', false) && empty(session('guest_preferences'));
        $showPreferenceBanner = auth()->check() && empty($preferences) && ! $request->boolean('dismiss_preference_banner');

        return view('home', compact(
            'categories',
            'products',
            'featured',
            'featuredProducts',
            'recommended',
            'recommendedProducts',
            'showPreferenceModal',
            'showPreferenceBanner'
        ));
    }

    public function about()
    {
        return view('about');
    }

    protected function resolvePreferences(): array
    {
        if (auth()->check()) {
            return auth()->user()->preferences()->pluck('preference')->all();
        }

        return session('guest_preferences', []);
    }

    protected function buildRecommendedProducts(array $preferences)
    {
        if (empty($preferences)) {
                return collect();
            }

            $preferenceText = collect($preferences)
                ->implode(' ');

            if (empty($preferenceText)) {
                return collect();
            }

        $apiBase = config('app.python_api_url', 'http://127.0.0.1:5000');

        try {
            $resp = Http::timeout(5)->get($apiBase . '/api/recommend_by_preferences', [
                'preference' => $preferenceText,
                'n' => 4,
            ]);

            if ($resp->successful()) {
                $recommended = collect();
                foreach ($resp->json('data', []) as $rec) {
                    $p = Product::where('name', $rec['nama_produk'] ?? '')->first();
                    if ($p) {
                        $recommended->push($p);
                    }
                }
                if ($recommended->isNotEmpty()) {
                    return $recommended;
                }
            }
        } catch (\Exception $e) {
            // API gagal/timeout, lanjut ke fallback di bawah
        }

        return Product::where('is_active', true)
            ->latest()
            ->take(4)
            ->get();
            }
}