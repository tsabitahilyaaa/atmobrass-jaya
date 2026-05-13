<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'phone' => '081234567890',
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@email.com',
            'password' => bcrypt('budi123'),
            'role' => 'user',
            'phone' => '081298765432',
        ]);

        User::create([
            'name' => 'Sari Dewi',
            'email' => 'sari@email.com',
            'password' => bcrypt('sari123'),
            'role' => 'user',
            'phone' => '081355566677',
        ]);
    }
}