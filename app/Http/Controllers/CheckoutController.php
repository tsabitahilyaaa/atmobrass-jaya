<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class CheckoutController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $cartItems = [];
        $total = 0;

        foreach ($cart as $id => $qty) {
            $product = Product::find($id);
            if ($product) {
                $cartItems[] = (object) [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                    'qty' => $qty,
                    'subtotal' => $product->price * $qty,
                ];
                $total += $product->price * $qty;
            }
        }

        return view('checkout.index', compact('cartItems', 'total'));
    }

    public function process(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'postal' => 'required|string|max:10',
            'payment' => 'required|string',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang kosong.');
        }

        $total = 0;

        foreach ($cart as $id => $qty) {

            $product = Product::find($id);

            if (!$product || $product->stock < $qty) {
                return back()->with(
                    'error',
                    'Stok produk ' .
                    ($product ? $product->name : '') .
                    ' tidak mencukupi.'
                );
            }

            $total += $product->price * $qty;
        }

        $order = Order::create([
            'user_id' => auth()->id(),

            'order_number' =>
                'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8)),

            'status' => 'pending',

            'total_amount' => $total,

            'shipping_address' =>
                $request->name . "\n" .
                $request->phone . "\n" .
                $request->city . "\n" .
                $request->address . "\n" .
                $request->postal,

            'notes' =>
                'Metode pembayaran: ' . $request->payment,

            'ordered_at' => now(),
        ]);

        foreach ($cart as $id => $qty) {

            $product = Product::find($id);

            $subtotal = $product->price * $qty;

            $product->decrement('stock', $qty);

            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'product_image' => $product->image,
                'quantity' => $qty,
                'subtotal' => $subtotal,
            ]);
        }

        session()->forget('cart');

        return redirect()
            ->route('orders.index')
            ->with(
                'success',
                'Pesanan berhasil dibuat! Nomor: ' . $order->order_number
            );
    }
}   