<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login untuk melihat pesanan.');
        }

        $query = auth()->user()->orders()->with('items');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->latest()->paginate(10)->withQueryString();
        $activeStatus = $request->input('status', 'all');

        return view('orders.index', compact('orders', 'activeStatus'));
    }
}