<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $budi = User::where('email', 'budi@email.com')->first();
        $sari = User::where('email', 'sari@email.com')->first();
        $products = Product::all();

        $ordersData = [
            ['user' => $budi, 'items' => [0, 2], 'qtys' => [1, 2], 'status' => 'selesai', 'days' => 90, 'payment' => 'bca'],
            ['user' => $budi, 'items' => [4, 5], 'qtys' => [3, 1], 'status' => 'selesai', 'days' => 75, 'payment' => 'mandiri'],
            ['user' => $sari, 'items' => [12], 'qtys' => [1], 'status' => 'selesai', 'days' => 60, 'payment' => 'bca'],
            ['user' => $budi, 'items' => [1, 8], 'qtys' => [1, 2], 'status' => 'selesai', 'days' => 50, 'payment' => 'bri'],
            ['user' => $sari, 'items' => [9, 10], 'qtys' => [5, 3], 'status' => 'dikirim', 'days' => 35, 'payment' => 'gopay'],
            ['user' => $budi, 'items' => [3, 6], 'qtys' => [1, 1], 'status' => 'dikirim', 'days' => 25, 'payment' => 'ovo'],
            ['user' => $sari, 'items' => [14, 15], 'qtys' => [1, 2], 'status' => 'dibayar', 'days' => 15, 'payment' => 'dana'],
            ['user' => $budi, 'items' => [7, 11], 'qtys' => [1, 4], 'status' => 'dibayar', 'days' => 10, 'payment' => 'bca'],
            ['user' => $sari, 'items' => [0, 13], 'qtys' => [2, 1], 'status' => 'pending', 'days' => 3, 'payment' => 'mandiri'],
            ['user' => $budi, 'items' => [15], 'qtys' => [1], 'status' => 'pending', 'days' => 1, 'payment' => 'gopay'],
        ];

        foreach ($ordersData as $data) {
            $total = 0;
            $orderItems = [];

            foreach ($data['items'] as $idx => $productIdx) {
                $product = $products[$productIdx];
                $qty = $data['qtys'][$idx];
                $total += $product->price * $qty;
                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'product_image' => $product->image,
                    'quantity' => $qty,
                ];
            }

            $order = Order::create([
                'user_id' => $data['user']->id,
                'order_number' => 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'total' => $total,
                'status' => $data['status'],
                'payment_method' => $data['payment'],
                'shipping_name' => $data['user']->name,
                'shipping_phone' => $data['user']->phone,
                'shipping_city' => 'Surabaya',
                'shipping_address' => 'Jl. Raya Darmo No. ' . rand(10, 200),
                'shipping_postal' => '602' . rand(10, 99),
                'created_at' => now()->subDays($data['days']),
                'updated_at' => now()->subDays($data['days']),
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }
        }
    }
}