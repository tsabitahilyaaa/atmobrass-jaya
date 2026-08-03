<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected array $preferenceKeywords = [
        'Pintu Rumah' => ['pintu', 'engsel', 'handle', 'kunci', 'door'],
        'Lemari & Kabinet' => ['lemari', 'kabinet', 'rak', 'laci', 'cabinet', 'wardrobe'],
        'Furniture' => ['furniture', 'meja', 'kursi', 'kursi', 'perabot', 'furnitur'],
        'Kantor & Bangunan Komersial' => ['kantor', 'office', 'bangunan', 'komersial', 'commercial'],
        'Dekorasi Interior' => ['dekor', 'interior', 'lampu', 'ornamen', 'dekoratif'],
        'Renovasi & Proyek Bangunan' => ['renovasi', 'proyek', 'bangunan', 'building', 'project'],
    ];

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

        $products = Product::with('category')->where('is_active', true)->get();
        $scoredProducts = [];

        foreach ($products as $product) {
            $score = 0;
            $haystack = strtolower($product->name . ' ' . $product->description . ' ' . ($product->category?->name ?? ''));

            foreach ($preferences as $preference) {
                foreach ($this->preferenceKeywords[$preference] ?? [] as $keyword) {
                    if (str_contains($haystack, strtolower($keyword))) {
                        $score++;
                    }
                }
            }

            if ($score > 0) {
                $scoredProducts[] = [
                    'product' => $product,
                    'score' => $score,
                ];
            }
        }

        usort($scoredProducts, fn ($a, $b) => $b['score'] <=> $a['score']);

        return collect(array_slice($scoredProducts, 0, 4))->map(fn ($entry) => $entry['product']);
    }
}