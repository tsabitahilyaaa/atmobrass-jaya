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
                <div class="mb-6 rounded-xl border border-dark-300 bg-dark-100 p-4">
                    <form method="POST" action="{{ route('cart.add') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}" />
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-muted">Jumlah</span>
                            <div class="flex items-center border border-dark-300 rounded-lg overflow-hidden">
                                <button type="button" class="quantity-btn w-10 h-10 hover:bg-dark-200 transition" data-action="minus">-</button>
                                <input id="quantity-input" type="number" name="qty" min="1" max="{{ $product->stock }}" value="1" class="w-16 h-10 text-center bg-transparent outline-none" />
                                <button type="button" class="quantity-btn w-10 h-10 hover:bg-dark-200 transition" data-action="plus">+</button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between border-t border-dark-300 pt-4">
                            <span class="text-sm text-muted">Subtotal</span>
                            <span id="subtotal-display" class="text-gold font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        </div>
                        <button type="submit" class="btn-gold w-full py-3 rounded-lg">Tambah ke Keranjang</button>
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

    @if(isset($recommendedContent) && $recommendedContent->count() > 0)
    <div class="mt-12">
        <h2 class="font-display font-bold text-xl sm:text-2xl mb-6">Rekomendasi Mirip (Content‑Based)</h2>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($recommendedContent as $rp)
                @include('partials.product-card', ['product' => $rp])
            @endforeach
        </div>
    </div>
    @endif
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('quantity-input');
        const subtotal = document.getElementById('subtotal-display');
        const price = {{ $product->price }};
        const maxStock = {{ $product->stock }};

        function syncSubtotal() {
            let value = parseInt(input.value || 1, 10);
            if (Number.isNaN(value) || value < 1) value = 1;
            if (value > maxStock) value = maxStock;
            input.value = value;
            subtotal.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(price * value);
        }

        document.querySelectorAll('.quantity-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                let value = parseInt(input.value || 1, 10);
                if (button.dataset.action === 'plus') value += 1;
                if (button.dataset.action === 'minus') value -= 1;
                if (value < 1) value = 1;
                if (value > maxStock) value = maxStock;
                input.value = value;
                syncSubtotal();
            });
        });

        input.addEventListener('input', syncSubtotal);
        syncSubtotal();
    });
</script>
@endsection