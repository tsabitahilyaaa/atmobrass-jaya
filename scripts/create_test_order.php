<?php
$project = dirname(__DIR__);
require $project.'/vendor/autoload.php';
$app = require_once $project.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

$product = Product::first();
if (!$product) {
    echo "No product found\n";
    exit(1);
}

$total = $product->price * 1;

$order = Order::create([
    'user_id' => null,
    'order_number' => 'TEST' . time() . rand(100,999),
    'status' => 'pending',
    'total_amount' => $total,
    'shipping_address' => 'Alamat test',
    'notes' => 'Order test via script',
    'ordered_at' => now(),
]);

OrderItem::create([
    'order_id' => $order->id,
    'product_id' => $product->id,
    'product_name' => $product->name,
    'product_price' => $product->price,
    'product_image' => $product->image ?? null,
    'quantity' => 1,
    'subtotal' => $total,
]);

echo "Created order: " . $order->order_number . " (id=" . $order->id . ")\n";
