<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class PreferenceController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $selectedCategories = [];

        return view('preferences.onboarding', compact('categories', 'selectedCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'required|array|min:3',
            'categories.*' => 'exists:categories,id',
        ]);

        $request->session()->put('preferences', $validated['categories']);

        return redirect()->route('products.index');
    }
}
