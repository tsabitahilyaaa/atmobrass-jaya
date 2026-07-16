@extends('layouts.app')

@section('title', $product->name . ' — CV Atmobrass Jaya')

@section('content')
<section class="py-10 sm:py-16 max-w-7xl mx-auto px-4 sm:px-6">
    <nav class="text-sm text-muted mb-8">
        <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
        <i class="fas fa-chevron-right text-xs mx-2"></i>
        <a href="{{ route('products.index') }}" class="hover:text-gold transition-colors">Produk</a>
        <i class="fas fa-chevron-right text-xs mx-2"></i>
        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-gold transition-colors">{{ $product->category->name }}</a>
        <i class="fas fa-chevron-right text-xs mx-2"></i>
        <span class="text-gold">{{ $product->name }}</span>
    </nav>

    <div class="grid md:grid-cols-2 gap-8 sm:gap-12 mb-16">
        <div class="rounded-xl overflow-hidden border border-dark-300 aspect-square bg-dark-200">
            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
        </div>

        <div>
            <p class="text-xs text-gold uppercase tracking-widest mb-2">{{ $product->category->name }}</p>
            <h1 class="font-display font-bold text-2xl sm:text-3xl mb-4">{{ $product->name }}</h1>
            <p class="text-gold font-bold text-2xl mb-6">{{ $product->formatted_price }}</p>
            <p class="text-sm text-muted leading-relaxed mb-8">{{ $product->description }}</p>

            <div class="flex items-center gap-3 mb-6">
                @if($product->stock > 0)
                    <span class="text-sm text-green-400"><i class="fas fa-check-circle mr-1"></i>Stok: {{ $product->stock }}</span>
                @else
                    <span class="text-sm text-red-400"><i class="fas fa-times-circle mr-1"></i>Habis</span>
                @endif
            </div>

            @if($product->stock > 0)
                <div class="mb-6">
                    <form method="POST" action="{{ route('order.quick') }}" class="space-y-3 bg-dark-100 border border-dark-300 rounded-lg p-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}" />
                        <div class="grid grid-cols-2 gap-2">
                            <div><label class="text-xs text-muted">Nama</label><input type="text" name="name" required class="input-dark w-full px-3 py-2 rounded-lg text-sm" placeholder="Nama Anda"></div>
                            <div><label class="text-xs text-muted">Email</label><input type="email" name="email" required class="input-dark w-full px-3 py-2 rounded-lg text-sm" placeholder="email@contoh.com"></div>
                        </div>
                        <div><label class="text-xs text-muted">Telepon</label><input type="tel" name="phone" required class="input-dark w-full px-3 py-2 rounded-lg text-sm" placeholder="08xxxxxxxxxx"></div>
                        <div><label class="text-xs text-muted">Alamat Pengiriman</label><input type="text" name="address" required class="input-dark w-full px-3 py-2 rounded-lg text-sm" placeholder="Alamat lengkap"></div>
                        <div class="grid grid-cols-2 gap-2">
                            <div><label class="text-xs text-muted">Jumlah</label><input type="number" name="quantity" min="1" value="1" required class="input-dark w-full px-3 py-2 rounded-lg text-sm"></div>
                            <div><label class="text-xs text-muted">Catatan (opsional)</label><input type="text" name="notes" class="input-dark w-full px-3 py-2 rounded-lg text-sm" placeholder="Ukuran / finishing / catatan"></div>
                        </div>
                        <div class="flex gap-2 items-center">
                            <button type="submit" class="btn-gold px-6 py-2 rounded-lg">Pesan Sekarang</button>
                            <span class="text-sm text-muted">Atau hubungi WA: <a href="https://wa.me/6285229269792" class="text-gold">+62 852-2926-9792</a></span>
                        </div>
                    </form>
                </div>
            @else
                <button disabled class="w-full sm:w-auto py-3 px-8 rounded-lg text-sm bg-dark-300 text-muted cursor-not-allowed">Stok Habis</button>
            @endif
        </div>
    </div>

    @if($related->count() > 0)
    <div>
        <h2 class="font-display font-bold text-xl sm:text-2xl mb-6">Produk Terkait</h2>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($related as $rp)
                @include('partials.product-card', ['product' => $rp])
            @endforeach
        </div>
    </div>
    @endif
</section>
@endsection