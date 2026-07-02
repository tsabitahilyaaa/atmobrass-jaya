<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        foreach ($users as $user) {

            // bikin 1–3 order per user
            for ($i = 0; $i < rand(1, 3); $i++) {

                $orderNumber = 'ORD-' . strtoupper(Str::random(10));

                $order = Order::create([
                    'user_id' => $user->id,
                    'order_number' => $orderNumber,
                    'status' => $this->randomStatus(),
                    'total_amount' => 0, // sementara
                    'shipping_address' => 'Jl. Contoh No. ' . rand(1, 100),
                    'notes' => rand(0, 1) ? 'Tolong kirim cepat ya' : null,
                    'ordered_at' => now()->subDays(rand(1, 30))
                ]);

                $totalAmount = 0;

                // tiap order isi 1–4 product
                $orderProducts = $products->random(rand(1, 4));

                foreach ($orderProducts as $product) {

                    $qty = rand(1, 3);
                    $subtotal = $product->price * $qty;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_price' => $product->price,
                        'product_image' => $product->image,
                        'quantity' => $qty,
                        'subtotal' => $subtotal
                    ]);

                    $totalAmount += $subtotal;
                }

                // update total order
                $order->update([
                    'total_amount' => $totalAmount
                ]);
            }
        }
    }

    private function randomStatus(): string
    {
        $statuses = [
            'pending',
            'paid',
            'processing',
            'shipped',
            'completed',
            'cancelled'
        ];

        return $statuses[array_rand($statuses)];
    }
}