<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        $total = 0;
        $cartItems = [];

        foreach ($cart as $id => $qty) {

            $product = Product::find($id);

            if ($product) {

                $subtotal = $product->price * $qty;

                $cartItems[] = (object) [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'formatted_price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                    'image' => $product->image,
                    'stock' => $product->stock,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                    'formatted_subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
                ];

                $total += $subtotal;
            }
        }

        $formattedTotal = 'Rp ' . number_format($total, 0, ',', '.');

        return view('cart.index', compact(
            'cartItems',
            'total',
            'formattedTotal'
        ));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id] += $request->qty;
        } else {
            $cart[$product->id] = $request->qty;
        }

        session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'qty' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id] = $request->qty;
        }

        session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Jumlah produk berhasil diperbarui.');
    }

    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
        }

        session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Produk berhasil dihapus dari keranjang.');
    }
    public function increase(Request $request)
    {
        $productId = $request->product_id;

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {

            $product = Product::find($productId);

            if ($cart[$productId] < $product->stock) {
                $cart[$productId]++;
            }

            session()->put('cart', $cart);
        }

        return back();
    }

    public function decrease(Request $request)
    {
        $productId = $request->product_id;

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {

            $cart[$productId]--;

            if ($cart[$productId] <= 0) {
                unset($cart[$productId]);
            }

            session()->put('cart', $cart);
        }

        return back();
    }
}