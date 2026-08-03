@extends('layouts.app')

@section('title', 'Profil Saya — CV Atmobrass Jaya')

@section('content')
<section class="py-10 sm:py-16 max-w-5xl mx-auto px-4 sm:px-6">
    <h1 class="font-display font-bold text-2xl sm:text-3xl mb-8">Profil Customer</h1>

    <div class="bg-dark-100 border border-dark-300 rounded-xl p-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="w-20 h-20 rounded-lg bg-dark-200 flex items-center justify-center text-2xl text-muted">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div class="min-w-0">
                <p class="font-semibold text-sm">{{ $user->name }}</p>
                <p class="text-sm text-muted mt-1">{{ $user->email }}</p>
                <p class="text-sm text-muted mt-1">Customer sejak {{ $user->created_at->format('d F Y') }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('profile.edit') }}" class="btn-outline px-6 py-3 rounded-lg text-sm inline-block">Edit Profil</a>
            <a href="{{ route('profile.password') }}" class="btn-outline px-6 py-3 rounded-lg text-sm inline-block">Ubah Password</a>
            <a href="{{ route('profile.preferences') }}" class="btn-outline px-6 py-3 rounded-lg text-sm inline-block">Preferensi Saya</a>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-[1.25fr_0.75fr] mt-8">
        <div class="space-y-6">
            <div class="bg-dark-100 border border-dark-300 rounded-xl p-5">
                <div class="mb-5">
                    <p class="text-xs text-muted uppercase tracking-[0.28em]">
                        Pesanan Saya
                    </p>
                    <h2 class="font-display font-semibold text-2xl">
                        Status Pesanan
                    </h2>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                    <div class="bg-dark-200 border border-dark-300 rounded-xl h-32 flex flex-col items-center justify-center transition hover:border-gold">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-muted">
                            Belum Dibayar
                        </p>

                        <p class="mt-2 text-2xl font-semibold text-gold">
                            {{ $orderSummary['pending'] }}
                        </p>
                    </div>

                    <div class="bg-dark-200 border border-dark-300 rounded-xl h-32 flex flex-col items-center justify-center transition hover:border-gold">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-muted">
                            Diproses
                        </p>

                        <p class="mt-2 text-2xl font-semibold text-gold">
                            {{ $orderSummary['processing'] }}
                        </p>
                    </div>

                    <div class="bg-dark-200 border border-dark-300 rounded-xl h-32 flex flex-col items-center justify-center transition hover:border-gold">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-muted">
                            Dikirim
                        </p>

                        <p class="mt-2 text-2xl font-semibold text-gold">
                            {{ $orderSummary['shipped'] }}
                        </p>
                    </div>

                    <div class="bg-dark-200 border border-dark-300 rounded-xl h-32 flex flex-col items-center justify-center transition hover:border-gold">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-muted">
                            Selesai
                        </p>

                        <p class="mt-2 text-2xl font-semibold text-gold">
                            {{ $orderSummary['completed'] }}
                        </p>
                    </div>

                </div>
            </div>

            <div class="bg-dark-100 border border-dark-300 rounded-xl p-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs text-muted uppercase tracking-[0.28em]">Riwayat Pesanan</p>
                        <h2 class="font-display font-semibold text-lg">Pesanan Terbaru</h2>
                    </div>
                    <a href="{{ route('orders.index') }}" class="text-sm text-gold hover:text-white">Lihat semua pesanan</a>
                </div>

                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($statusOptions as $value => $label)
                        <a href="{{ route('profile.orders', ['status' => $value]) }}" class="px-3 py-2 rounded-full text-xs font-medium transition-all {{ $activeStatus === $value ? 'btn-gold' : 'bg-dark-200 border border-dark-300 text-muted hover:border-gold' }}">{{ $label }}</a>
                    @endforeach
                </div>

                @if($orders->count() === 0)
                    <div class="text-center py-10">
                        <i class="fas fa-receipt text-4xl text-dark-400 mb-4 block"></i>
                        <p class="text-sm text-muted">Tidak ada pesanan di status ini.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($orders as $order)
                            <div class="bg-dark-100 border border-dark-300 rounded-xl p-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="text-xs text-muted uppercase tracking-[0.24em] mb-1">{{ $order->order_number }}</p>
                                        <h3 class="font-semibold text-base truncate">{{ $order->formatted_date }}</h3>
                                        <p class="text-sm text-muted mt-1">{{ $order->items->count() }} produk</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs uppercase tracking-[0.24em] status-{{ $order->status }}">{{ $statusOptions[$order->status] ?? ucfirst($order->status) }}</span>
                                        <p class="font-semibold text-sm">{{ $order->formatted_total }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    @foreach($order->items->take(3) as $item)
                                        <div class="bg-dark-200 border border-dark-300 rounded-xl p-3 flex items-center gap-3">
                                            <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-16 h-16 rounded-lg object-cover">
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium truncate">{{ $item->product_name }}</p>
                                                <p class="text-xs text-muted">{{ $item->quantity }}x {{ $item->formatted_price }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($order->items->count() > 3)
                                        <div class="bg-dark-200 border border-dark-300 rounded-xl p-3 flex items-center justify-center text-sm text-muted">+ {{ $order->items->count() - 3 }} produk lainnya</div>
                                    @endif
                                </div>

                                <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                    <a href="{{ route('profile.orders.show', $order) }}" class="btn-outline px-6 py-3 rounded-lg text-sm inline-block">Lihat Detail</a>
                                    @if($order->status === 'completed')
                                        <form method="POST" action="{{ route('profile.orders.reorder', $order) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="btn-gold px-6 py-3 rounded-lg text-sm inline-block">Beli Lagi</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-center">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-dark-100 border border-dark-300 rounded-xl p-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs text-muted uppercase tracking-[0.28em]">Alamat Saya</p>
                        <h2 class="font-display font-semibold text-lg">Alamat Pengiriman</h2>
                    </div>
                    <a href="{{ route('profile.addresses.create') }}" class="btn-gold px-6 py-3 rounded-lg text-sm inline-block">Tambah Alamat</a>
                </div>

                <div class="space-y-4">
                    @forelse($addresses as $address)
                        <div class="bg-dark-200 border border-dark-300 rounded-xl p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold">{{ $address->recipient_name }}</p>
                                    <p class="text-xs text-muted mt-1">{{ $address->phone }}</p>
                                </div>
                                @if($address->is_default)
                                    <span class="text-xs uppercase tracking-[0.24em] text-gold">Utama</span>
                                @endif
                            </div>
                            <p class="text-sm text-muted mt-3 leading-relaxed">{{ $address->address }}, {{ $address->city }}, {{ $address->province }}, {{ $address->postal_code }}</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ route('profile.addresses.edit', $address) }}" class="btn-outline px-4 py-3 rounded-lg text-sm inline-block">Edit</a>
                                @unless($address->is_default)
                                    <form method="POST" action="{{ route('profile.addresses.default', $address) }}" class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn-outline px-4 py-3 rounded-lg text-sm inline-block">Jadikan Utama</button>
                                    </form>
                                @endunless
                                <form method="POST" action="{{ route('profile.addresses.destroy', $address) }}" class="inline-block" onsubmit="return confirm('Hapus alamat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-outline px-4 py-3 rounded-lg text-sm inline-block">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="bg-dark-200 border border-dark-300 rounded-xl p-6 text-center text-sm text-muted">
                            Belum ada alamat tersimpan. Tambahkan alamat untuk mempercepat checkout.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
