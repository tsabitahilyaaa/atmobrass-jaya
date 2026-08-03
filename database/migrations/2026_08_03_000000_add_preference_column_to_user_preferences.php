<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_preferences') && !Schema::hasColumn('user_preferences', 'preference')) {
            Schema::table('user_preferences', function (Blueprint $table) {
                $table->string('preference')->after('user_id');
                $table->unique(['user_id', 'preference']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_preferences') && Schema::hasColumn('user_preferences', 'preference')) {
            Schema::table('user_preferences', function (Blueprint $table) {
                // dropUnique expects index name or column list
                $table->dropUnique(['user_id', 'preference']);
                $table->dropColumn('preference');
            });
        }
    }
};
