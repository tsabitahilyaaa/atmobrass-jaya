<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 🔥 ADMIN (untuk dashboard)
        User::create([
            'name' => 'Admin Toko',
            'email' => 'admin@toko.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 🔥 CUSTOMER DEFAULT
        User::create([
            'name' => 'Bita Customer',
            'email' => 'customer@toko.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        // 🔥 DATA CUSTOMER RANDOM (untuk testing order)
        $customers = [
            ['name' => 'Andi Pratama', 'email' => 'andi@mail.com'],
            ['name' => 'Siti Aisyah', 'email' => 'siti@mail.com'],
            ['name' => 'Rizky Firmansyah', 'email' => 'rizky@mail.com'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@mail.com'],
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@mail.com'],
        ];

        foreach ($customers as $c) {
            User::create([
                'name' => $c['name'],
                'email' => $c['email'],
                'password' => Hash::make('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);
        }
    }
}