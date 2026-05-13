<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $brass = Category::where('slug', 'brass')->first()->id;
        $aluminium = Category::where('slug', 'aluminium')->first()->id;
        $accessories = Category::where('slug', 'accessories')->first()->id;
        $lamp = Category::where('slug', 'lamp')->first()->id;

        $products = [
            ['category_id' => $brass, 'name' => 'Handrail Kuningan Premium', 'slug' => 'handrail-kuningan-premium', 'price' => 2850000, 'stock' => 15, 'image' => 'https://picsum.photos/seed/brasshr/600/600', 'description' => 'Handrail kuningan berkualitas tinggi dengan finishing polished. Cocok untuk tangga interior maupun eksterior rumah dan gedung premium. Bahan kuningan solid dengan ketebalan 2mm yang menjamin kekuatan dan daya tahan.'],
            ['category_id' => $brass, 'name' => 'Chandelier Kuningan 8-Arm', 'slug' => 'chandelier-kuningan-8-arm', 'price' => 8500000, 'stock' => 5, 'image' => 'https://picsum.photos/seed/brassch/600/600', 'description' => 'Chandelier mewah dengan 8 lengan kuningan polished. Desain klasik yang elegan untuk ruang tamu atau ruang makan. Dilengkapi crystal drops yang memantulkan cahaya dengan indah.'],
            ['category_id' => $brass, 'name' => 'Door Handle Kuningan Antique', 'slug' => 'door-handle-kuningan-antique', 'price' => 450000, 'stock' => 30, 'image' => 'https://picsum.photos/seed/brassdh/600/600', 'description' => 'Handle pintu kuningan dengan sentuhan antique. Tahan karat dan cocok untuk pintu utama maupun pintu interior. Finishing brushed brass yang memberikan kesan timeless.'],
            ['category_id' => $brass, 'name' => 'Shower Set Kuningan Complete', 'slug' => 'shower-set-kuningan-complete', 'price' => 3200000, 'stock' => 8, 'image' => 'https://picsum.photos/seed/brasssh/600/600', 'description' => 'Set shower lengkap dari kuningan premium termasuk shower head, hand shower, dan keran. Anti karat tahan lama dengan finishing chrome over brass.'],
            ['category_id' => $aluminium, 'name' => 'Panel Dinding Aluminium Perforated', 'slug' => 'panel-dinding-aluminium-perforated', 'price' => 1200000, 'stock' => 20, 'image' => 'https://picsum.photos/seed/alumpan/600/600', 'description' => 'Panel dinding aluminium dengan pola perforated modern. Ideal untuk cladding fasad atau partisi interior. Tersedia dalam berbagai pola dan ukuran custom.'],
            ['category_id' => $aluminium, 'name' => 'Railing Balkon Aluminium', 'slug' => 'railing-balkon-aluminium', 'price' => 1850000, 'stock' => 12, 'image' => 'https://picsum.photos/seed/alumrl/600/600', 'description' => 'Railing balkon aluminium dengan desain minimalis. Ringan namun kuat, tahan terhadap cuaca ekstrem. Finishing powder coating berkualitas tinggi.'],
            ['category_id' => $aluminium, 'name' => 'Pintu Aluminium Sliding', 'slug' => 'pintu-aluminium-sliding', 'price' => 5200000, 'stock' => 6, 'image' => 'https://picsum.photos/seed/alumdr/600/600', 'description' => 'Pintu geser aluminium dengan kaca tempered 8mm. Sempurna untuk akses ke taman atau balkon. Sistem rail smooth dan anti-jam.'],
            ['category_id' => $aluminium, 'name' => 'Kusen Aluminium Powder Coated', 'slug' => 'kusen-aluminium-powder-coated', 'price' => 950000, 'stock' => 25, 'image' => 'https://picsum.photos/seed/alumks/600/600', 'description' => 'Kusen aluminium dengan finishing powder coating berkualitas. Tersedia berbagai ukuran standar dan custom. Tahan karat hingga 20 tahun.'],
            ['category_id' => $accessories, 'name' => 'Knob Furniture Kuningan Round', 'slug' => 'knob-furniture-kuningan-round', 'price' => 185000, 'stock' => 50, 'image' => 'https://picsum.photos/seed/accknob/600/600', 'description' => 'Knob furniture berbahan kuningan solid dengan bentuk bulat klasik. Finishing polished untuk tampilan mewah pada lemari, laci, atau drawer.'],
            ['category_id' => $accessories, 'name' => 'Handle Laci Premium T-Bar', 'slug' => 'handle-laci-premium-t-bar', 'price' => 275000, 'stock' => 40, 'image' => 'https://picsum.photos/seed/acctlb/600/600', 'description' => 'Handle laci T-bar premium dari kuningan. Cocok untuk laci lemari, meja rias, atau kitchen set. Desain ergonomis dan elegan.'],
            ['category_id' => $accessories, 'name' => 'Engsel Decoratif Brass', 'slug' => 'engsel-decoratif-brass', 'price' => 350000, 'stock' => 35, 'image' => 'https://picsum.photos/seed/acceng/600/600', 'description' => 'Engsel dekoratif dari kuningan dengan detail ukiran. Ideal untuk pintu kayu dan furniture klasik. Ball bearing system untuk kelancaran.'],
            ['category_id' => $accessories, 'name' => 'Ring Gantung Industrial', 'slug' => 'ring-gantung-industrial', 'price' => 155000, 'stock' => 60, 'image' => 'https://picsum.photos/seed/accring/600/600', 'description' => 'Ring gantung industrial style untuk dekorasi interior. Bahan logam solid dengan finishing matte black. Mampu menahan beban hingga 15kg.'],
            ['category_id' => $lamp, 'name' => 'Lampu Gantung Kristal Brass', 'slug' => 'lampu-gantung-kristal-brass', 'price' => 12500000, 'stock' => 3, 'image' => 'https://picsum.photos/seed/lampcr/600/600', 'description' => 'Lampu gantung mewah dengan kristal dan rangka kuningan. Pusat perhatian ruangan dengan cahaya yang memukau. Diameter 80cm, tinggi 65cm.'],
            ['category_id' => $lamp, 'name' => 'Lampu Meja Brass Architect', 'slug' => 'lampu-meja-brass-architect', 'price' => 1850000, 'stock' => 10, 'image' => 'https://picsum.photos/seed/lampar/600/600', 'description' => 'Lampu meja bergaya architect dengan lengan kuningan adjustable. Sempurna untuk meja kerja atau area baca. E27 socket, compatible LED.'],
            ['category_id' => $lamp, 'name' => 'Lampu Dinding Industrial Cage', 'slug' => 'lampu-dinding-industrial-cage', 'price' => 950000, 'stock' => 18, 'image' => 'https://picsum.photos/seed/lampcage/600/600', 'description' => 'Lampu dinding industrial dengan desain cage. Cocok untuk ruang tamu, kafe, atau area koridor. E27 socket, finishing black matte.'],
            ['category_id' => $lamp, 'name' => 'Lampu Lantai Vintage Brass', 'slug' => 'lampu-lantai-vintage-brass', 'price' => 2350000, 'stock' => 7, 'image' => 'https://picsum.photos/seed/lampflr/600/600', 'description' => 'Lampu lantai vintage dengan stand kuningan dan shade kain. Menghadirkan suasana hangat dan elegan. Tinggi 160cm, dimmer ready.'],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }
    }
}