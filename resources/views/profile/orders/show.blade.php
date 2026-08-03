@extends('layouts.app')

@section('title', 'Detail Pesanan — CV Atmobrass Jaya')

@section('content')
<section class="py-10 sm:py-16 max-w-6xl mx-auto px-4 sm:px-6">
    <div class="mb-8">
        <a href="{{ route('profile.orders', ['status' => request('status', 'all')]) }}" class="text-sm text-muted hover:underline">&larr; Kembali ke Riwayat Pesanan</a>
        <div class="mt-4">
            <p class="text-xs text-muted uppercase tracking-[0.24em]">Detail Pesanan</p>
            <h1 class="font-display font-bold text-3xl">{{ $order->order_number }}</h1>
            <p class="text-sm text-muted mt-2">{{ $order->formatted_date }} — {{ $statusSteps[$order->status] ?? ucfirst($order->status) }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
        <div class="space-y-6">
            <div class="bg-dark-100 border border-dark-300 rounded-3xl p-6">
                <p class="text-xs text-muted uppercase tracking-[0.24em] mb-4">Informasi Order</p>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <p class="text-sm text-muted">Nomor Order</p>
                        <p class="font-semibold">{{ $order->order_number }}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-sm text-muted">Tanggal Order</p>
                        <p class="font-semibold">{{ $order->formatted_date }}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-sm text-muted">Status</p>
                        <p class="font-semibold">{{ $statusSteps[$order->status] ?? ucfirst($order->status) }}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-sm text-muted">Metode Pembayaran</p>
                        <p class="font-semibold">{{ strtoupper($order->payment_method ?? 'N/A') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-dark-100 border border-dark-300 rounded-3xl p-6">
                <p class="text-xs text-muted uppercase tracking-[0.24em] mb-4">Progress Status</p>
                <div class="space-y-4">
                    @foreach(['pending' => 'Pesanan Dibuat', 'paid' => 'Pembayaran', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'completed' => 'Selesai'] as $statusKey => $statusLabel)
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center border {{ $order->status === $statusKey || in_array($order->status, ['processing', 'shipped', 'completed']) && $statusKey !== 'pending' || ($order->status === 'completed' && $statusKey !== 'pending') ? 'border-gold bg-gold/20 text-gold' : 'border-dark-300 text-muted' }}">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <p class="text-sm {{ $order->status === $statusKey ? 'font-semibold text-white' : 'text-muted' }}">{{ $statusLabel }}</p>
                        </div>
                    @endforeach
                    @if($order->status === 'cancelled')
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center border border-red-400 text-red-400">
                                <i class="fas fa-times text-xs"></i>
                            </div>
                            <p class="text-sm text-red-300">Pesanan dibatalkan</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-dark-100 border border-dark-300 rounded-3xl p-6">
                <p class="text-xs text-muted uppercase tracking-[0.24em] mb-4">Produk</p>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="grid gap-4 sm:grid-cols-[auto_1fr_auto] items-center bg-dark-200 border border-dark-300 rounded-3xl p-4">
                            <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-20 h-20 rounded-3xl object-cover">
                            <div>
                                <p class="font-semibold">{{ $item->product_name }}</p>
                                <p class="text-sm text-muted mt-1">{{ $item->quantity }} x {{ $item->formatted_price }}</p>
                            </div>
                            <p class="text-right font-semibold">{{ $item->formatted_subtotal }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-dark-100 border border-dark-300 rounded-3xl p-6">
                <p class="text-xs text-muted uppercase tracking-[0.24em] mb-4">Alamat Pengiriman</p>
                <div class="space-y-2">
                    <p class="font-semibold">{{ $order->shipping_name }}</p>
                    <p class="text-sm text-muted">{{ $order->shipping_phone }}</p>
                    <p class="text-sm text-muted">{{ $order->shipping_email ?? '' }}</p>
                    <p class="text-sm leading-relaxed">{{ $order->shipping_address }}</p>
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="bg-dark-100 border border-dark-300 rounded-3xl p-6">
                <p class="text-xs text-muted uppercase tracking-[0.24em] mb-4">Ringkasan Pembayaran</p>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span>Subtotal Produk</span><span>{{ $order->items->sum('subtotal') ? 'Rp ' . number_format($order->items->sum('subtotal'), 0, ',', '.') : '-' }}</span></div>
                    <div class="flex justify-between"><span>Ongkos Kirim</span><span>{{ $order->shipping_city ? 'Rp 0' : '-' }}</span></div>
                    <div class="flex justify-between"><span>Biaya Lain</span><span>{{ $order->payment_amount ? 'Rp ' . number_format(($order->payment_amount - $order->items->sum('subtotal')), 0, ',', '.') : '-' }}</span></div>
                    <div class="border-t border-dark-300 pt-3 flex justify-between font-semibold"><span>Total Pembayaran</span><span>{{ $order->formatted_total }}</span></div>
                </div>
            </div>
            <div class="bg-dark-100 border border-dark-300 rounded-3xl p-6">
                <p class="text-xs text-muted uppercase tracking-[0.24em] mb-4">Metode Pembayaran</p>
                <p class="font-semibold">{{ strtoupper($order->payment_method ?? 'N/A') }}</p>
            </div>
            @if($order->status === 'completed')
                <form method="POST" action="{{ route('profile.orders.reorder', $order) }}" class="grid gap-3">
                    @csrf
                    <button type="submit" class="btn-gold w-full py-3 rounded-full">Beli Lagi</button>
                </form>
            @endif
        </aside>
    </div>
</section>
@endsection
