<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('total');
            $table->enum('status', ['pending', 'dibayar', 'dikirim', 'selesai'])->default('pending');
            $table->string('payment_method')->default('bca');
            $table->string('shipping_name');
            $table->string('shipping_phone');
            $table->string('shipping_city');
            $table->text('shipping_address');
            $table->string('shipping_postal');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};