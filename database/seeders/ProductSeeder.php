<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $handle = Category::where('slug', 'gagang-pintu-handle')->firstOrFail()->id;
        $railing = Category::where('slug', 'handrail-railing')->firstOrFail()->id;
        $lampu = Category::where('slug', 'lampu-chandelier')->firstOrFail()->id;
        $sanitary = Category::where('slug', 'sanitary')->firstOrFail()->id;
        $ornamen = Category::where('slug', 'ornamen-dekorasi')->firstOrFail()->id;
        $aluminium = Category::where('slug', 'aluminium-arsitektural')->firstOrFail()->id;

        $products = [
            [
                'category_id' => $railing,
                'name' => 'Handrail Kuningan Premium',
                'slug' => 'handrail-kuningan-premium',
                'description' => 'Handrail kuningan berkualitas tinggi dengan finishing polished. Cocok untuk tangga interior maupun eksterior rumah dan gedung premium.',
                'price' => 2850000,
                'stock' => 15,
                'image' => 'https://picsum.photos/seed/brasshr/600/600',
                'is_active' => true
            ],
            [
                'category_id' => $lampu,
                'name' => 'Chandelier Kuningan 8-Arm',
                'slug' => 'chandelier-kuningan-8-arm',
                'description' => 'Chandelier mewah dengan 8 lengan kuningan polished dan crystal drops elegan.',
                'price' => 8500000,
                'stock' => 5,
                'image' => 'https://picsum.photos/seed/brassch/600/600',
                'is_active' => true
            ],
            [
                'category_id' => $handle    ,
                'name' => 'Door Handle Kuningan Antique',
                'slug' => 'door-handle-kuningan-antique',
                'description' => 'Handle pintu kuningan anti karat dengan finishing antique elegan.',
                'price' => 450000,
                'stock' => 30,
                'image' => 'https://picsum.photos/seed/brassdh/600/600',
                'is_active' => true
            ],
            [
                'category_id' => $sanitary,
                'name' => 'Shower Set Kuningan Complete',
                'slug' => 'shower-set-kuningan-complete',
                'description' => 'Set shower lengkap dari kuningan premium anti karat dengan finishing chrome.',
                'price' => 3200000,
                'stock' => 8,
                'image' => 'https://picsum.photos/seed/brasssh/600/600',
                'is_active' => true
            ],

            [
                'category_id' => $aluminium,
                'name' => 'Panel Dinding Aluminium Perforated',
                'slug' => 'panel-dinding-aluminium-perforated',
                'description' => 'Panel aluminium modern untuk fasad dan interior dengan desain perforated.',
                'price' => 1200000,
                'stock' => 20,
                'image' => 'https://picsum.photos/seed/alumpan/600/600',
                'is_active' => true
            ],
            [
                'category_id' => $railing,
                'name' => 'Railing Balkon Aluminium',
                'slug' => 'railing-balkon-aluminium',
                'description' => 'Railing aluminium minimalis kuat dan tahan cuaca ekstrem.',
                'price' => 1850000,
                'stock' => 12,
                'image' => 'https://picsum.photos/seed/alumrl/600/600',
                'is_active' => true
            ],
            [
                'category_id' => $aluminium,
                'name' => 'Pintu Aluminium Sliding',
                'slug' => 'pintu-aluminium-sliding',
                'description' => 'Pintu geser aluminium dengan kaca tempered 8mm dan sistem rail smooth.',
                'price' => 5200000,
                'stock' => 6,
                'image' => 'https://picsum.photos/seed/alumdr/600/600',
                'is_active' => true
            ],
            [
                'category_id' => $aluminium,
                'name' => 'Kusen Aluminium Powder Coated',
                'slug' => 'kusen-aluminium-powder-coated',
                'description' => 'Kusen aluminium tahan karat dengan finishing powder coating premium.',
                'price' => 950000,
                'stock' => 25,
                'image' => 'https://picsum.photos/seed/alumks/600/600',
                'is_active' => true
            ],

            [
                'category_id' => $handle,
                'name' => 'Knob Furniture Kuningan Round',
                'slug' => 'knob-furniture-kuningan-round',
                'description' => 'Knob furniture kuningan solid dengan desain klasik elegan.',
                'price' => 185000,
                'stock' => 50,
                'image' => 'https://picsum.photos/seed/accknob/600/600',
                'is_active' => true
            ],
            [
                'category_id' => $handle,
                'name' => 'Handle Laci Premium T-Bar',
                'slug' => 'handle-laci-premium-t-bar',
                'description' => 'Handle laci ergonomis berbahan kuningan untuk kitchen set dan lemari.',
                'price' => 275000,
                'stock' => 40,
                'image' => 'https://picsum.photos/seed/acctlb/600/600',
                'is_active' => true
            ],
            [
                'category_id' => $ornamen,
                'name' => 'Engsel Decoratif Brass',
                'slug' => 'engsel-decoratif-brass',
                'description' => 'Engsel dekoratif dengan detail ukiran dan ball bearing system.',
                'price' => 350000,
                'stock' => 35,
                'image' => 'https://picsum.photos/seed/acceng/600/600',
                'is_active' => true
            ],
            [
                'category_id' => $ornamen,
                'name' => 'Ring Gantung Industrial',
                'slug' => 'ring-gantung-industrial',
                'description' => 'Ring gantung industrial matte black untuk dekorasi interior modern.',
                'price' => 155000,
                'stock' => 60,
                'image' => 'https://picsum.photos/seed/accring/600/600',
                'is_active' => true
            ],

            [
                'category_id' => $lampu,
                'name' => 'Lampu Gantung Kristal Brass',
                'slug' => 'lampu-gantung-kristal-brass',
                'description' => 'Lampu gantung mewah dengan kristal dan rangka kuningan elegan.',
                'price' => 12500000,
                'stock' => 3,
                'image' => 'https://picsum.photos/seed/lampcr/600/600',
                'is_active' => true
            ],
            [
                'category_id' => $lampu,
                'name' => 'Lampu Meja Brass Architect',
                'slug' => 'lampu-meja-brass-architect',
                'description' => 'Lampu meja adjustable dengan desain architect klasik.',
                'price' => 1850000,
                'stock' => 10,
                'image' => 'https://picsum.photos/seed/lampar/600/600',
                'is_active' => true
            ],
            [
                'category_id' => $lampu,
                'name' => 'Lampu Dinding Industrial Cage',
                'slug' => 'lampu-dinding-industrial-cage',
                'description' => 'Lampu dinding industrial cage cocok untuk cafe dan interior modern.',
                'price' => 950000,
                'stock' => 18,
                'image' => 'https://picsum.photos/seed/lampcage/600/600',
                'is_active' => true
            ],
            [
                'category_id' => $lampu,
                'name' => 'Lampu Lantai Vintage Brass',
                'slug' => 'lampu-lantai-vintage-brass',
                'description' => 'Lampu lantai vintage dengan shade kain dan stand kuningan elegan.',
                'price' => 2350000,
                'stock' => 7,
                'image' => 'https://picsum.photos/seed/lampflr/600/600',
                'is_active' => true
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}