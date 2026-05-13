@extends('layouts.app')

@section('title', 'Produk — CV Atmobrass Jaya')

@section('content')
<section class="py-10 sm:py-16 max-w-7xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-10 anim-scroll">
        <p class="text-gold text-sm font-semibold tracking-widest uppercase mb-3">Katalog Produk</p>
        <h1 class="font-display font-bold text-3xl sm:text-4xl mb-6">Produk Kami</h1>
        <form method="GET" action="{{ route('products.index') }}" class="max-w-md mx-auto relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-muted text-sm"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..." class="input-dark w-full pl-11 pr-4 py-3 rounded-lg text-sm">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
        </form>
    </div>

    <div class="flex flex-wrap justify-center gap-2 sm:gap-3 mb-10 anim-scroll">
        <a href="{{ route('products.index') }}" class="px-5 py-2 rounded-full text-sm font-medium transition-all {{ $activeCategory === 'all' ? 'btn-gold' : 'bg-dark-100 border border-dark-300 text-muted hover:border-gold-dark hover:text-gold' }}">Semua</a>
        @foreach($categories as $cat)
            <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="px-5 py-2 rounded-full text-sm font-medium transition-all {{ $activeCategory === $cat->slug ? 'btn-gold' : 'bg-dark-100 border border-dark-300 text-muted hover:border-gold-dark hover:text-gold' }}">{{ $cat->name }}</a>
        @endforeach
    </div>

    @if($products->count() === 0)
        <div class="text-center py-20">
            <i class="fas fa-search text-4xl text-dark-400 mb-4 block"></i>
            <p class="text-muted">Produk tidak ditemukan</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            @foreach($products as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>

        <div class="mt-10 flex justify-center">
            {{ $products->links() }}
        </div>
    @endif
</section>
@endsection