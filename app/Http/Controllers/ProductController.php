<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

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
        $product = Product::where('slug', $slug)->firstOrFail();
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'related'));
    }
}