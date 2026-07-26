<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getCartItems(): array
    {
        $items = [];
        $total = 0;
        $count = 0;

        if (auth()->check()) {
            $cart = auth()->user()->cart()->with('items.product')->first();

            if ($cart) {
                foreach ($cart->items as $cartItem) {
                    $product = $cartItem->product;

                    if (! $product || ! $product->is_active) {
                        continue;
                    }

                    $quantity = max(1, (int) $cartItem->quantity);
                    $subtotal = (float) $product->price * $quantity;

                    $items[] = (object) [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => (float) $product->price,
                        'formatted_price' => $product->formatted_price,
                        'image' => $product->image,
                        'stock' => (int) $product->stock,
                        'qty' => $quantity,
                        'subtotal' => $subtotal,
                        'formatted_subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
                    ];

                    $total += $subtotal;
                    $count += $quantity;
                }
            }
        } else {
            foreach (session()->get('cart', []) as $productId => $quantity) {
                $product = Product::find($productId);

                if (! $product || ! $product->is_active) {
                    continue;
                }

                $quantity = max(1, (int) $quantity);
                $subtotal = (float) $product->price * $quantity;

                $items[] = (object) [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'formatted_price' => $product->formatted_price,
                    'image' => $product->image,
                    'stock' => (int) $product->stock,
                    'qty' => $quantity,
                    'subtotal' => $subtotal,
                    'formatted_subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
                ];

                $total += $subtotal;
                $count += $quantity;
            }
        }

        return [
            'items' => $items,
            'total' => $total,
            'count' => $count,
            'formatted_total' => 'Rp ' . number_format($total, 0, ',', '.'),
        ];
    }

    protected function getCartItemCount(): int
    {
        return $this->getCartItems()['count'];
    }

    protected function fetchContentRecommendations(string $productName, int $limit = 4)
    {
        $recommended = collect();
        $apiBase = config('app.python_api_url', 'http://127.0.0.1:5000');

        try {
            $response = Http::timeout(5)->get($apiBase . '/api/recommend_by_name', [
                'name' => $productName,
                'n' => $limit,
            ]);

            if ($response->successful()) {
                foreach ($response->json('data', []) as $rec) {
                    $product = Product::where('name', $rec['nama_produk'] ?? '')->first();

                    if ($product) {
                        $recommended->push($product);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Keep recommendation empty if the Python API is unavailable.
        }

        return $recommended;
    }
}
