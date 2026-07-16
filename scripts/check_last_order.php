<?php
$project = dirname(__DIR__);
require $project.'/vendor/autoload.php';
$app = require_once $project.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;

$order = Order::with('items')->latest()->first();
if (!$order) {
    echo "No orders found\n";
    exit(1);
}

echo "Order #" . $order->order_number . "\n";
echo "Status: " . $order->status . "\n";
echo "Total: " . $order->total_amount . "\n";
echo "Address: " . $order->shipping_address . "\n";
echo "Items:\n";
foreach ($order->items as $item) {
    echo " - " . $item->product_name . " x" . $item->quantity . " (" . $item->subtotal . ")\n";
}
