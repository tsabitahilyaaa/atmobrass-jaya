<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing foreign key, make column nullable, then re-add FK with ON DELETE SET NULL
        DB::statement('ALTER TABLE `orders` DROP FOREIGN KEY `orders_user_id_foreign`');
        DB::statement('ALTER TABLE `orders` MODIFY `user_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `orders` ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL');
    }

    public function down(): void
    {
        // Revert: drop FK, make column NOT NULL, re-add FK with cascade on delete
        DB::statement('ALTER TABLE `orders` DROP FOREIGN KEY `orders_user_id_foreign`');
        DB::statement('ALTER TABLE `orders` MODIFY `user_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `orders` ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE');
    }
};
