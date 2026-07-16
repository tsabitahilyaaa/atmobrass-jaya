<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Aksesori & Plat',
                'slug' => 'aksesori-plat',
                'description' => 'Aksesori dan plat dekoratif untuk furnitur dan kerajinan metal.',
                'icon' => 'fa-square',
            ],
            [
                'name' => 'Engsel',
                'slug' => 'engsel',
                'description' => 'Engsel berkualitas tinggi untuk pintu, lemari, dan furnitur.',
                'icon' => 'fa-hammer',
            ],
            [
                'name' => 'Pemegang & Tombol',
                'slug' => 'pemegang-tombol',
                'description' => 'Handle, knob, dan tombol untuk berbagai aplikasi pintu dan laci.',
                'icon' => 'fa-hand-point-up',
            ],
            [
                'name' => 'Roda & Kaki Perabot',
                'slug' => 'roda-kaki-perabot',
                'description' => 'Roda, kaki, dan komponen pendukung untuk perabot bergerak.',
                'icon' => 'fa-cog',
            ],
        ];

        $activeSlugs = [];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );

            $activeSlugs[] = $category['slug'];
        }

        Category::whereNotIn('slug', $activeSlugs)->delete();
    }
}