<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%');
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($builder) use ($request) {
                $builder->where('slug', $request->input('category'));
            });
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::all();
        $activeCategory = $request->input('category', 'all');

        return view('products.index', compact('products', 'categories', 'activeCategory'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        // Request content-based recommendations from Python API by product name
        $recommendedContent = collect();
        $apiBase = config('ml.python_api_url', 'http://127.0.0.1:5000');

        try {
            $resp = Http::timeout(5)->get($apiBase . '/api/recommend_by_name', ['name' => $product->name, 'n' => 4]);
            if ($resp->successful()) {
                $json = $resp->json();
                foreach ($json['data'] ?? [] as $rec) {
                    // Try to find matching product in local DB by name
                    $p = Product::where('name', $rec['nama_produk'] ?? '')->first();
                    if ($p) {
                        $recommendedContent->push($p);
                    }
                }
            }
        } catch (\Exception $e) {
            // silent fail; recommendedContent stays empty
        }

        return view('products.show', compact('product', 'related', 'recommendedContent'));
    }
}