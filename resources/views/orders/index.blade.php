@extends('layouts.app')

@section('title', 'Pesanan Saya — CV Atmobrass Jaya')

@section('content')
<section class="py-10 sm:py-16 max-w-5xl mx-auto px-4 sm:px-6">
    <h1 class="font-display font-bold text-2xl sm:text-3xl mb-8">Pesanan Saya</h1>

    <div class="flex flex-wrap gap-2 mb-8">
        @php
            $statuses = ['all' => 'Semua', 'pending' => 'Pending', 'dibayar' => 'Dibayar', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai'];
        @endphp
        @foreach($statuses as $val => $label)
            <a href="{{ route('orders.index', ['status' => $val]) }}" class="px-4 py-2 rounded-full text-xs font-medium transition-all {{ $activeStatus === $val ? 'btn-gold' : 'bg-dark-100 border border-dark-300 text-muted hover:border-gold-dark' }}">{{ $label }}</a>
        @endforeach
    </div>

    @if($orders->count() === 0)
        <div class="text-center py-16">
            <i class="fas fa-receipt text-4xl text-dark-400 mb-4 block"></i>
            <p class="text-muted">Belum ada pesanan</p>
        </div>
    @else
        @foreach($orders as $order)
        <div class="bg-dark-100 border border-dark-300 rounded-xl p-5 sm:p-6 mb-4">
            <div class="flex flex-wrap justify-between items-start gap-3 mb-4">
                <div>
                    <p class="text-xs text-muted mb-1">{{ $order->order_number }}</p>
                    <p class="text-sm font-semibold">{{ $order->formatted_date }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold uppercase tracking-wide status-{{ $order->status }}">{{ $order->status }}</span>
                    <span class="text-gold font-bold text-sm">{{ $order->formatted_total }}</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mb-3">
                @foreach($order->items as $item)
                <div class="flex items-center gap-2 bg-dark-200 rounded-lg px-3 py-2">
                    <img src="{{ $item->product_image }}" class="w-10 h-10 rounded object-cover">
                    <div>
                        <p class="text-xs font-medium">{{ $item->product_name }}</p>
                        <p class="text-xs text-muted">{{ $item->quantity }}x {{ $item->formatted_price }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-3 pt-3 border-t border-dark-300 flex flex-wrap gap-4 text-xs text-muted">
                <span><i class="fas fa-user mr-1 text-gold"></i>{{ $order->shipping_name }}</span>
                <span><i class="fas fa-map-marker-alt mr-1 text-gold"></i>{{ $order->shipping_city }}</span>
                <span><i class="fas fa-credit-card mr-1 text-gold"></i>{{ strtoupper($order->payment_method) }}</span>
            </div>
        </div>
        @endforeach

        <div class="mt-8 flex justify-center">
            {{ $orders->links() }}
        </div>
    @endif
</section>
@endsection