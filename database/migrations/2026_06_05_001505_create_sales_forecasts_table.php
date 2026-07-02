<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_forecasts', function (Blueprint $table) {

            $table->id();

            $table->date('forecast_date')
                  ->unique();

            $table->integer('predicted_quantity');

            $table->decimal('predicted_revenue', 15, 2);

            $table->decimal('confidence_score', 5, 2)
                  ->nullable();

            $table->string('model_version')
                  ->nullable();

            $table->timestamp('computed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_forecasts');
    }
};