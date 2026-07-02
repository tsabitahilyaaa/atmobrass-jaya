@extends('layouts.app')

@section('title', 'Keranjang — CV Atmobrass Jaya')

@section('content')
<section class="py-10 sm:py-16 max-w-5xl mx-auto px-4 sm:px-6">
    <h1 class="font-display font-bold text-2xl sm:text-3xl mb-8">
        Keranjang Belanja
    </h1>

    @if(count($cartItems) === 0)
        <div class="text-center py-20">
            <i class="fas fa-shopping-bag text-5xl text-dark-400 mb-6 block"></i>

            <h2 class="font-display font-bold text-2xl mb-3">
                Keranjang Kosong
            </h2>

            <p class="text-sm text-muted mb-8">
                Belum ada produk di keranjang belanja Anda
            </p>

            <a href="{{ route('products.index') }}"
               class="btn-gold px-8 py-3 rounded-lg text-sm inline-block">
                Mulai Belanja
            </a>
        </div>
    @else

        @php
            $totalQty = 0;

            foreach($cartItems as $item){
                $totalQty += $item->qty;
            }
        @endphp

        <div class="grid lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-4">

                @foreach($cartItems as $item)

                <div class="bg-dark-100 border border-dark-300 rounded-xl p-4 flex gap-4 items-center">

                    <img
                        src="{{ $item->image }}"
                        alt="{{ $item->name }}"
                        class="w-20 h-20 rounded-lg object-cover flex-shrink-0"
                    >

                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-sm mb-1 truncate">
                            {{ $item->name }}
                        </h3>

                        <p class="text-gold text-sm font-bold">
                            {{ $item->formatted_price }}
                        </p>
                    </div>

                    <div class="flex items-center border border-dark-300 rounded-lg overflow-hidden">

                        <form method="POST"
                            action="{{ route('cart.decrease') }}">
                            @csrf

                            <input type="hidden"
                                name="product_id"
                                value="{{ $item->id }}">

                            <button type="submit"
                                    class="w-10 h-10 hover:bg-dark-200 transition">
                                <i class="fas fa-minus"></i>
                            </button>
                        </form>

                        <div class="px-4 text-center min-w-[50px]">
                            {{ $item->qty }}
                        </div>

                        <form method="POST"
                            action="{{ route('cart.increase') }}">
                            @csrf

                            <input type="hidden"
                                name="product_id"
                                value="{{ $item->id }}">

                            <button type="submit"
                                    class="w-10 h-10 hover:bg-dark-200 transition">
                                <i class="fas fa-plus"></i>
                            </button>
                        </form>

                    </div>

                    <div class="text-right flex-shrink-0 w-28">

                        <p class="text-sm font-bold mb-1">
                            {{ $item->formatted_subtotal }}
                        </p>

                        <form method="POST"
                              action="{{ route('cart.remove') }}">
                            @csrf

                            <input type="hidden"
                                   name="product_id"
                                   value="{{ $item->id }}">

                            <button type="submit"
                                    class="text-xs text-red-400 hover:text-red-300 transition-colors">
                                <i class="fas fa-trash mr-1"></i>
                                Hapus
                            </button>
                        </form>

                    </div>

                </div>

                @endforeach

            </div>

            <div class="bg-dark-100 border border-dark-300 rounded-xl p-6 h-fit sticky top-24">

                <h3 class="font-display font-semibold text-lg mb-4">
                    Ringkasan
                </h3>

                <div class="space-y-3 text-sm mb-6">

                    <div class="flex justify-between">
                        <span class="text-muted">
                            Total ({{ $totalQty }} item)
                        </span>

                        <span class="font-semibold">
                            {{ $formattedTotal }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-muted">
                            Ongkos Kirim
                        </span>

                        <span class="text-green-400 font-medium">
                            Dihitung saat checkout
                        </span>
                    </div>

                </div>

                <div class="border-t border-dark-300 pt-4 mb-6">

                    <div class="flex justify-between">

                        <span class="font-semibold">
                            Total
                        </span>

                        <span class="text-gold font-bold text-lg">
                            {{ $formattedTotal }}
                        </span>

                    </div>

                </div>

                <a href="{{ route('checkout.index') }}"
                   class="btn-gold w-full py-3 rounded-lg text-sm text-center block">
                    Checkout
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>

            </div>

        </div>

    @endif
</section>
@endsection