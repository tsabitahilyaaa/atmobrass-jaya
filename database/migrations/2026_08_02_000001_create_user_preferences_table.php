<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_preferences')) {
            Schema::create('user_preferences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('preference');
                $table->timestamps();

                $table->unique(['user_id', 'preference']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_preferences')) {
            Schema::dropIfExists('user_preferences');
        }
    }
};
