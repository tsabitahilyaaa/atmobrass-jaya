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
                $cartItems[] = (object) [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                    'stock' => $product->stock,
                    'qty' => $qty,
                    'subtotal' => $product->price * $qty,
                ];
                $total += $product->price * $qty;
            }
        }

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        $productId = $request->input('product_id');
        $qty = $request->input('qty');
        $product = Product::find($productId);

        if ($product->stock < $qty) {
            return back()->with('error', 'Stok tidak mencukupi. Tersedia: ' . $product->stock);
        }

        $cart = session()->get('cart', []);
        $cart[$productId] = isset($cart[$productId]) ? $cart[$productId] + $qty : $qty;
        session()->put('cart', $cart);

        return back()->with('success', $product->name . ' ditambahkan ke keranjang.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        $productId = $request->input('product_id');
        $qty = $request->input('qty');
        $product = Product::find($productId);

        if ($product->stock < $qty) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cart = session()->get('cart', []);
        $cart[$productId] = $qty;
        session()->put('cart', $cart);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function remove(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $cart = session()->get('cart', []);
        unset($cart[$request->input('product_id')]);
        session()->put('cart', $cart);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}