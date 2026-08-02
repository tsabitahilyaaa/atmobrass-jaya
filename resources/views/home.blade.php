@extends('layouts.app')

@section('title', 'Beranda — CV Atmobrass Jaya')

@section('content')
@if(session('preference_reset_banner'))
<section class="max-w-7xl mx-auto px-4 sm:px-6 pt-8">
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-white">Preferensi Anda telah direset.</p>
            <p class="text-sm text-muted mt-1">Pilih rekomendasi baru kapan saja untuk mendapatkan saran yang lebih sesuai.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('preferences.index') }}" class="btn-gold px-6 py-3 rounded-lg text-sm inline-block">Pilih Sekarang</a>
            <a href="{{ route('home', ['dismiss_preference_banner' => 1]) }}" class="btn-outline px-6 py-3 rounded-lg text-sm inline-block">Nanti Saja</a>
        </div>
    </div>
</section>
@endif

<section class="relative min-h-[90vh] flex items-center hero-pattern overflow-hidden">
    <div class="absolute top-20 right-10 w-64 h-64 rounded-full opacity-10 gold-bg blur-[100px] float-anim"></div>
    <div class="absolute bottom-20 left-10 w-48 h-48 rounded-full opacity-5 gold-bg blur-[80px] float-anim" style="animation-delay:2s"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-20 w-full">
        <div class="max-w-2xl">
            <div class="w-16 h-0.5 bg-gradient-to-r from-gold to-transparent mb-6"></div>
            <h1 class="font-display font-black text-4xl sm:text-5xl lg:text-6xl leading-tight mb-6">
                Premium <span class="gold-gradient">Metal Craft</span> untuk Setiap Ruang
            </h1>
            <p class="text-muted text-base sm:text-lg leading-relaxed mb-8">
                CV Atmobrass Jaya menghadirkan koleksi produk logam berkualitas tinggi — dari kuningan, aluminium, hingga aksesoris furniture dan lampu dekoratif yang memukau.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('products.index') }}" class="btn-gold px-8 py-3 rounded-lg text-sm pulse-gold inline-block">Belanja Sekarang</a>
                <a href="{{ route('about') }}" class="btn-outline px-8 py-3 rounded-lg text-sm inline-block">Tentang Kami</a>
            </div>
        </div>
    </div>
</section>

@if($recommendedProducts->isNotEmpty())
<section class="py-16 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-12 gap-4 anim-scroll">
        <div>
            <p class="text-gold text-sm font-semibold tracking-widest uppercase mb-3">Personalized</p>
            <h2 class="font-display font-bold text-2xl sm:text-3xl">Recommended For You</h2>
        </div>
        <a href="{{ route('products.index') }}" class="btn-outline px-6 py-2 rounded-lg text-sm inline-block">Lihat Semua <i class="fas fa-arrow-right ml-2 text-xs"></i></a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 anim-scroll">
        @foreach($recommendedProducts as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

<section class="py-16 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-12 anim-scroll">
        <p class="text-gold text-sm font-semibold tracking-widest uppercase mb-3">Koleksi Kami</p>
        <h2 class="font-display font-bold text-2xl sm:text-3xl">Kategori Produk</h2>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 anim-scroll">
        @foreach($categories as $cat)
        <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="cursor-pointer group bg-dark-100 border border-dark-300 rounded-xl p-6 sm:p-8 text-center hover:border-gold-dark transition-all duration-300 hover:-translate-y-1">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-dark-200 flex items-center justify-center mx-auto mb-4 group-hover:bg-gold/10 transition-colors">
                <i class="fas {{ $cat->icon }} text-gold text-xl sm:text-2xl"></i>
            </div>
            <h3 class="font-display font-semibold text-sm sm:text-base mb-1">{{ $cat->name }}</h3>
            <p class="text-xs text-muted">{{ $cat->products_count }} produk</p>
        </a>
        @endforeach
    </div>
</section>

<section class="py-16 sm:py-20 bg-dark-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-12 gap-4 anim-scroll">
            <div>
                <p class="text-gold text-sm font-semibold tracking-widest uppercase mb-3">Pilihan Terbaik</p>
                <h2 class="font-display font-bold text-2xl sm:text-3xl">Produk Unggulan</h2>
            </div>
            <a href="{{ route('products.index') }}" class="btn-outline px-6 py-2 rounded-lg text-sm inline-block">Lihat Semua <i class="fas fa-arrow-right ml-2 text-xs"></i></a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 anim-scroll">
            @foreach($featured as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>

<section class="py-16 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6">
    <div class="bg-dark-100 border border-dark-300 rounded-2xl p-8 sm:p-12 flex flex-col lg:flex-row items-center gap-8 anim-scroll">
        <div class="flex-1">
            <p class="text-gold text-sm font-semibold tracking-widest uppercase mb-3">Mengapa Kami</p>
            <h2 class="font-display font-bold text-2xl sm:text-3xl mb-6">Kualitas Logam Premium, Harga Bersaing</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gold/10 flex items-center justify-center flex-shrink-0"><i class="fas fa-medal text-gold text-sm"></i></div>
                    <div><h4 class="text-sm font-semibold mb-1">Kualitas Terjamin</h4><p class="text-xs text-muted">Material logam pilihan dengan standar industri</p></div>
                </div>
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gold/10 flex items-center justify-center flex-shrink-0"><i class="fas fa-truck-fast text-gold text-sm"></i></div>
                    <div><h4 class="text-sm font-semibold mb-1">Pengiriman Cepat</h4><p class="text-xs text-muted">Pengiriman ke seluruh Indonesia</p></div>
                </div>
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gold/10 flex items-center justify-center flex-shrink-0"><i class="fas fa-ruler-combined text-gold text-sm"></i></div>
                    <div><h4 class="text-sm font-semibold mb-1">Custom Order</h4><p class="text-xs text-muted">Menerima pesanan sesuai spesifikasi</p></div>
                </div>
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gold/10 flex items-center justify-center flex-shrink-0"><i class="fas fa-headset text-gold text-sm"></i></div>
                    <div><h4 class="text-sm font-semibold mb-1">Layanan Konsultasi</h4><p class="text-xs text-muted">Tim ahli siap membantu kebutuhan Anda</p></div>
                </div>
            </div>
        </div>
        <div class="flex-shrink-0">
            <a href="{{ route('products.index') }}" class="btn-gold px-10 py-4 rounded-lg text-sm inline-block">Belanja Sekarang <i class="fas fa-arrow-right ml-2"></i></a>
        </div>
    </div>
</section>

<section class="py-16 sm:py-20 bg-dark-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center anim-scroll">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <div><p class="font-display font-bold text-3xl sm:text-4xl gold-gradient mb-1">5+</p><p class="text-sm text-muted">Tahun Pengalaman</p></div>
            <div><p class="font-display font-bold text-3xl sm:text-4xl gold-gradient mb-1">5000+</p><p class="text-sm text-muted">Produk Terjual</p></div>
            <div><p class="font-display font-bold text-3xl sm:text-4xl gold-gradient mb-1">800+</p><p class="text-sm text-muted">Pelanggan Puas</p></div>
            <div><p class="font-display font-bold text-3xl sm:text-4xl gold-gradient mb-1">4</p><p class="text-sm text-muted">Kategori Produk</p></div>
        </div>
    </div>
</section>

@if($showPreferenceModal)
    @include('components.preference-onboarding-modal')
@endif
@endsection