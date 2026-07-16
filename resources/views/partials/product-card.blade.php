@props(['product'])

<div class="card-product rounded-xl overflow-hidden group" onclick="window.location='{{ route('products.show', $product->slug) }}'">
    <div class="relative aspect-square overflow-hidden bg-dark-200">
        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
        @if($product->stock < 10 && $product->stock > 0)
            <span class="absolute top-3 left-3 bg-red-500/90 text-white text-xs px-2 py-1 rounded-md font-medium">Stok Terbatas</span>
        @elseif($product->stock === 0)
            <span class="absolute top-3 left-3 bg-dark/80 text-red-400 text-xs px-2 py-1 rounded-md font-medium">Habis</span>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4">
            <span class="text-xs font-medium text-white bg-gold/90 px-4 py-2 rounded-lg">Lihat Detail</span>
        </div>
    </div>
    <div class="p-4">
        <p class="text-xs text-gold mb-1 uppercase tracking-wider">{{ $product->category->name }}</p>
        <h3 class="font-semibold text-sm mb-2 line-clamp-2 leading-snug">{{ $product->name }}</h3>
        <p class="text-gold font-bold text-sm">{{ $product->formatted_price }}</p>
    </div>
</div>