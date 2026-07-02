<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_histories', function (Blueprint $table) {

            $table->id();

            $table->date('sale_date')->unique();

            $table->integer('total_quantity');

            $table->decimal('total_revenue', 15, 2);

            $table->integer('total_orders');

            $table->timestamp('created_at')
                  ->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_histories');
    }
};