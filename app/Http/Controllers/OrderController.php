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

    public function quickOrder(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:2000',
        ]);

        $product = \App\Models\Product::findOrFail($data['product_id']);

        $total = $product->price * $data['quantity'];

        $order = \App\Models\Order::create([
            'user_id' => auth()->check() ? auth()->id() : null,
            'order_number' => 'ORD' . time() . rand(100, 999),
            'status' => 'pending',
            'total_amount' => $total,
            'shipping_address' => $data['address'],
            'notes' => $data['notes'] ?? null,
            'ordered_at' => now(),
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_price' => $product->price,
            'product_image' => $product->image,
            'quantity' => $data['quantity'],
            'subtotal' => $total,
        ]);

        return view('orders.thankyou', compact('order'));
    }
}