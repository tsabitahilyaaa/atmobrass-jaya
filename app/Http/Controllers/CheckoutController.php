<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        if (! auth()->check()) {
            session()->put('url.intended', route('checkout.index'));

            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $cartData = $this->getCartItems();
        $cartItems = $cartData['items'];
        $total = $cartData['total'];

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $recommendedContent = collect();
        if (! empty($cartItems)) {
            $referenceProduct = end($cartItems);
            $recommendedContent = $this->fetchContentRecommendations($referenceProduct->name, 4);
        }

        $qrisImage = $this->qrisImageUrl();

        return view('checkout.index', compact('cartItems', 'total', 'recommendedContent', 'qrisImage'));
    }

    public function process(Request $request)
    {
        if (! auth()->check()) {
            session()->put('url.intended', route('checkout.index'));

            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => ['required','regex:/^[0-9]{1,15}$/', 'max:15'],
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:1000',
            'postal' => 'required|string|max:20',
            'payment_amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:2000',
        ], [
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
        ]);

        $cartData = $this->getCartItems();
        $cartItems = $cartData['items'];

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $total = 0;

        foreach ($cartItems as $item) {
            $product = Product::find($item->id);

            if (! $product || $product->stock < $item->qty) {
                return back()->with('error', 'Stok produk ' . ($product ? $product->name : '') . ' tidak mencukupi.');
            }

            $total += $item->subtotal;
        }

        if ($request->payment_amount < $total) {
            return back()->withInput()->with('error', 'Nominal pembayaran harus sama atau lebih besar dari total pesanan. Total minimum Rp ' . number_format($total, 0, ',', '.'));
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'status' => 'pending',
            'payment_method' => 'qris',
            'payment_amount' => $request->payment_amount,
            'total_amount' => $total,
            'shipping_name' => $request->name,
            'shipping_email' => $request->email,
            'shipping_phone' => $request->phone,
            'shipping_city' => $request->city,
            'shipping_address' => $request->address,
            'notes' => $request->notes,
            'ordered_at' => now(),
        ]);

        foreach ($cartItems as $item) {
            $product = Product::find($item->id);

            if (! $product) {
                continue;
            }

            $product->decrement('stock', $item->qty);

            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'product_image' => $product->image,
                'quantity' => $item->qty,
                'subtotal' => $item->subtotal,
            ]);
        }

        if (auth()->check()) {
            $cart = auth()->user()->cart()->first();

            if ($cart) {
                $cart->items()->delete();
                $cart->delete();
            }
        }

        session()->forget('cart');

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibuat! Nomor: ' . $order->order_number);
    }
}
   