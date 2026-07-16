<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $aksesori = Category::where('slug', 'aksesori-plat')->firstOrFail()->id;
        $engsel = Category::where('slug', 'engsel')->firstOrFail()->id;
        $pemegang = Category::where('slug', 'pemegang-tombol')->firstOrFail()->id;
        $roda = Category::where('slug', 'roda-kaki-perabot')->firstOrFail()->id;

        $products = [
            [
                'category_id' => $aksesori,
                'name' => 'Accecoris Katak',
                'slug' => 'accecoris-katak',
                'description' => 'Ornamen dekoratif berbentuk katak berbahan kuningan asli. Sangat cocok untuk memberikan sentuhan antik dan unik pada karya kayu atau furnitur gaya klasik.',
                'price' => 28000,
                'stock' => 15,
                'image' => 'images/product/accecoris_katak.jpeg',
                'is_active' => true,
            ],
            [
                'category_id' => $aksesori,
                'name' => 'Bintang',
                'slug' => 'bintang',
                'description' => 'Aksesori pelat ornamen bermotif bintang dengan material kuningan berkualitas. Ideal untuk mempercantik tampilan kotak perhiasan, laci kecil, atau kerajinan vintage lainnya.',
                'price' => 25000,
                'stock' => 20,
                'image' => 'images/product/bintang.jpeg',
                'is_active' => true,
            ],
            [
                'category_id' => $engsel,
                'name' => 'Engsel Jahit',
                'slug' => 'engsel-jahit',
                'description' => 'Engsel lipat besi yang dirancang kokoh dan presisi. Memberikan pergerakan yang mulus untuk pintu lemari, peti, atau perabot dengan mekanisme buka-tutup berulang.',
                'price' => 32000,
                'stock' => 25,
                'image' => 'images/product/engsel_jahit.jpeg',
                'is_active' => true,
            ],
            [
                'category_id' => $engsel,
                'name' => 'Engsel Kepala Antiq',
                'slug' => 'engsel-kepala-antiq',
                'description' => 'Engsel premium berbahan gangsa (perunggu) dengan detail kepala ukiran antik. Menawarkan kekuatan fungsional sekaligus menonjolkan estetika mewah pada furnitur.',
                'price' => 47000,
                'stock' => 10,
                'image' => 'images/product/engsel_kepala_antiq.jpeg',
                'is_active' => true,
            ],
            [
                'category_id' => $engsel,
                'name' => 'Engsel Tombak',
                'slug' => 'engsel-tombak',
                'description' => 'Engsel klasik dengan desain ujung menyerupai tombak berbahan gangsa antik. Memberikan ketahanan ekstra dan tampilan gagah pada pintu kayu solid atau peti besar.',
                'price' => 47000,
                'stock' => 10,
                'image' => 'images/product/engsel_tombak.jpeg',
                'is_active' => true,
            ],
            [
                'category_id' => $pemegang,
                'name' => 'Handle Arsip Kuningan',
                'slug' => 'handle-arsip-kuningan',
                'description' => 'Tarikan laci khusus kabinet arsip berbahan kuningan solid, dilengkapi slot untuk menyisipkan label nama. Pilihan tepat untuk lemari penyimpanan atau laci meja bergaya retro.',
                'price' => 36000,
                'stock' => 18,
                'image' => 'images/product/handle_arsip_kuningan.jpeg',
                'is_active' => true,
            ],
            [
                'category_id' => $pemegang,
                'name' => 'Handle Kuningan',
                'slug' => 'handle-kuningan',
                'description' => 'Handle serbaguna dari kuningan asli dengan desain elegan dan tahan karat. Mudah digenggam dan mampu memberikan sentuhan premium pada pintu maupun lemari pakaian.',
                'price' => 210000,
                'stock' => 12,
                'image' => 'images/product/handle_kuningan.jpeg',
                'is_active' => true,
            ],
            [
                'category_id' => $pemegang,
                'name' => 'Handle Laci',
                'slug' => 'handle-laci',
                'description' => 'Tarikan laci ergonomis berbahan kuningan yang didesain untuk kenyamanan optimal. Memiliki daya tahan tinggi yang pas diaplikasikan pada berbagai jenis meja rias dan nakas.',
                'price' => 57000,
                'stock' => 10,
                'image' => 'images/product/handle_laci.jpeg',
                'is_active' => true,
            ],
            [
                'category_id' => $pemegang,
                'name' => 'Handle Peti',
                'slug' => 'handle-peti',
                'description' => 'Handle gantung (drop handle) ekstra tebal berbahan kuningan. Dirancang khusus dengan daya tahan beban tinggi untuk memudahkan pengangkatan peti kayu atau kotak berukuran besar.',
                'price' => 90000,
                'stock' => 8,
                'image' => 'images/product/handle_peti.jpeg',
                'is_active' => true,
            ],
            [
                'category_id' => $aksesori,
                'name' => 'Hanger Kapstok',
                'slug' => 'hanger-kapstok',
                'description' => 'Gantungan baju (kapstok) dinding berbahan besi yang tebal dan kuat. Ideal untuk menggantung mantel, topi, maupun tas dengan aman tanpa memakan banyak ruang.',
                'price' => 19000,
                'stock' => 30,
                'image' => 'images/product/hanger_kapstok.jpeg',
                'is_active' => true,
            ],
            [
                'category_id' => $aksesori,
                'name' => 'Kotak Kuningan',
                'slug' => 'kotak-kuningan',
                'description' => 'Pelat pelindung sudut (corner guard) berbahan kuningan dengan profil kotak. Berfungsi ganda untuk melindungi sudut furnitur dari benturan sekaligus menambah nilai estetika.',
                'price' => 48000,
                'stock' => 16,
                'image' => 'images/product/kotak_kuningan.jpeg',
                'is_active' => true,
            ],
            [
                'category_id' => $roda,
                'name' => 'Roda Troli Alumunium',
                'slug' => 'roda-troli-alumunium',
                'description' => 'Roda perabot berbahan aluminium yang ringan namun kokoh. Dirancang untuk memberikan mobilitas yang lancar, stabil, dan aman bagi permukaan lantai ruangan.',
                'price' => 34000,
                'stock' => 14,
                'image' => 'images/product/roda_troli_alumunium.jpeg',
                'is_active' => true,
            ],
            [
                'category_id' => $roda,
                'name' => 'Roda Troli Kuningan',
                'slug' => 'roda-troli-kuningan',
                'description' => 'Roda troli kelas premium dengan konstruksi kuningan solid bergaya industrial-vintage. Menawarkan kapasitas angkut yang tangguh dengan tampilan yang mewah.',
                'price' => 97000,
                'stock' => 9,
                'image' => 'images/product/roda_troli_kuningan.jpeg',
                'is_active' => true,
            ],
        ];

        $activeSlugs = [];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['slug' => $product['slug']],
                $product
            );

            $activeSlugs[] = $product['slug'];
        }

        Product::whereNotIn('slug', $activeSlugs)
            ->update(['is_active' => false]);
    }
}