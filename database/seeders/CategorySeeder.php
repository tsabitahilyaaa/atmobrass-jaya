<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::insert([

            [
                'name' => 'Gagang Pintu & Handle',
                'slug' => 'gagang-pintu-handle',
                'description' => 'Berbagai gagang pintu, handle laci, dan knob furniture berbahan kuningan premium.',
                'icon' => 'fa-door-open',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Handrail & Railing',
                'slug' => 'handrail-railing',
                'description' => 'Produk handrail dan railing untuk rumah, hotel, dan bangunan komersial.',
                'icon' => 'fa-grip-lines',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Lampu & Chandelier',
                'slug' => 'lampu-chandelier',
                'description' => 'Lampu dekoratif, chandelier, dan pencahayaan premium berbahan kuningan.',
                'icon' => 'fa-lightbulb',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Kamar Mandi & Sanitary',
                'slug' => 'sanitary',
                'description' => 'Perlengkapan kamar mandi dan sanitary berbahan kuningan berkualitas tinggi.',
                'icon' => 'fa-shower',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Ornamen & Dekorasi',
                'slug' => 'ornamen-dekorasi',
                'description' => 'Ornamen, dekorasi, dan aksesoris interior berbahan kuningan.',
                'icon' => 'fa-star',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Aluminium & Arsitektural',
                'slug' => 'aluminium-arsitektural',
                'description' => 'Produk aluminium modern untuk kebutuhan arsitektural dan konstruksi.',
                'icon' => 'fa-building',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}