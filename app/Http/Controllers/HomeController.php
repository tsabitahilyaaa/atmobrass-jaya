<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        $featured = Product::whereIn('id', [2, 7, 13, 15])->get();

        return view('home', compact('categories', 'featured'));
    }

    public function about()
    {
        return view('about');
    }
}