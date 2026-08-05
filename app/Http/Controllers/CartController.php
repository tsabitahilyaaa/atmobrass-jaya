<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartData = $this->getCartItems();
        $cartItems = $cartData['items'];
        $total = $cartData['total'];
        $formattedTotal = $cartData['formatted_total'];

        $recommendedContent = collect();
        if (! empty($cartItems)) {
            $referenceProduct = end($cartItems);
            $recommendedContent = $this->fetchContentRecommendations($referenceProduct->name, 4);
        }

        return view('cart.index', compact('cartItems', 'total', 'formattedTotal', 'recommendedContent'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $requestedQty = max(1, (int) $request->qty);

        if ($product->stock < $requestedQty) {
            return back()->with('error', 'Stok produk ' . $product->name . ' tidak mencukupi.');
        }

        if (auth()->check()) {
            $cart = auth()->user()->cart()->firstOrCreate(['user_id' => auth()->id()]);
            $cartItem = $cart->items()->where('product_id', $product->id)->first();
            $newQuantity = ($cartItem ? $cartItem->quantity : 0) + $requestedQty;

            if ($newQuantity > $product->stock) {
                $newQuantity = $product->stock;
            }

            if ($cartItem) {
                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $newQuantity,
                ]);
            }

            session()->forget('cart');
        } else {
            $cart = session()->get('cart', []);
            $existingQty = (int) ($cart[$product->id] ?? 0);
            $cart[$product->id] = min($existingQty + $requestedQty, $product->stock);
            session()->put('cart', $cart);
        }

        return back()
            ->with('success', 'Produk berhasil ditambahkan ke keranjang.')
            ->with('cart_link', route('cart.index'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'qty' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = min(max(1, (int) $request->qty), $product->stock);

        if (auth()->check()) {
            $cart = auth()->user()->cart()->first();

            if ($cart) {
                $cartItem = $cart->items()->where('product_id', $product->id)->first();

                if ($cartItem) {
                    $cartItem->update(['quantity' => $quantity]);
                }
            }
        } else {
            $cart = session()->get('cart', []);
            $cart[$product->id] = $quantity;
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Jumlah produk berhasil diperbarui.');
    }

    public function remove(Request $request)
    {
        if (auth()->check()) {
            $cart = auth()->user()->cart()->first();

            if ($cart) {
                $cart->items()->where('product_id', $request->product_id)->delete();
            }
        } else {
            $cart = session()->get('cart', []);
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    public function increase(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::find($productId);

        if (! $product) {
            return back();
        }

        if (auth()->check()) {
            $cart = auth()->user()->cart()->first();

            if ($cart) {
                $cartItem = $cart->items()->where('product_id', $productId)->first();

                if ($cartItem && $cartItem->quantity < $product->stock) {
                    $cartItem->update(['quantity' => $cartItem->quantity + 1]);
                }
            }
        } else {
            $cart = session()->get('cart', []);

            if (isset($cart[$productId]) && $cart[$productId] < $product->stock) {
                $cart[$productId]++;
                session()->put('cart', $cart);
            }
        }

        return back();
    }

    public function decrease(Request $request)
    {
        $productId = $request->product_id;

        if (auth()->check()) {
            $cart = auth()->user()->cart()->first();

            if ($cart) {
                $cartItem = $cart->items()->where('product_id', $productId)->first();

                if ($cartItem) {
                    $newQuantity = $cartItem->quantity - 1;

                    if ($newQuantity <= 0) {
                        $cartItem->delete();
                    } else {
                        $cartItem->update(['quantity' => $newQuantity]);
                    }
                }
            }
        } else {
            $cart = session()->get('cart', []);

            if (isset($cart[$productId])) {
                $cart[$productId]--;

                if ($cart[$productId] <= 0) {
                    unset($cart[$productId]);
                }

                session()->put('cart', $cart);
            }
        }

        return back();
    }
}
