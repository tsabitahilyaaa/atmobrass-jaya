@extends('layouts.app')

@section('title', 'Checkout — CV Atmobrass Jaya')

@section('content')
<section class="py-10 sm:py-16 max-w-5xl mx-auto px-4 sm:px-6">
    <h1 class="font-display font-bold text-2xl sm:text-3xl mb-8">Checkout</h1>

    <form method="POST" action="{{ route('checkout.process') }}">
        @csrf
        <div class="grid lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-dark-100 border border-dark-300 rounded-xl p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <h3 class="font-display font-semibold text-lg"><i class="fas fa-map-marker-alt text-gold mr-2"></i>Alamat Pengiriman</h3>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('profile.addresses.create') }}" class="btn-gold px-4 py-3 rounded-full text-xs sm:text-sm">Tambah Alamat</a>
                            <a href="{{ route('profile.addresses') }}" class="btn-outline px-4 py-3 rounded-full text-xs sm:text-sm">Kelola Alamat</a>
                        </div>
                    </div>

                    @if($addresses->isEmpty())
                        <div class="bg-dark-200 border border-dashed border-dark-300 rounded-2xl p-6 text-center">
                            <p class="text-sm text-muted mb-3">Belum ada alamat tersimpan.</p>
                            <a href="{{ route('profile.addresses.create') }}" class="btn-gold px-5 py-3 rounded-full text-sm">Tambah Alamat</a>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($addresses as $address)
                                <label class="block cursor-pointer">
                                    <input type="radio" name="address_id" value="{{ $address->id }}" class="peer sr-only" {{ old('address_id', $defaultAddress?->id) == $address->id ? 'checked' : '' }}>
                                    <div class="bg-dark-200 border border-dark-300 rounded-2xl p-4 transition-all duration-200 peer-checked:border-gold peer-checked:ring-1 peer-checked:ring-gold hover:border-gold/70">
                                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                            <div>
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="font-semibold text-sm">{{ $address->recipient_name }}</p>
                                                    @if($address->is_default)
                                                        <span class="text-gold text-[10px] uppercase tracking-[0.24em]">Utama</span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-muted mt-1">{{ $address->phone }}</p>
                                                <p class="text-sm text-muted mt-2 leading-relaxed">{{ $address->address }}, {{ $address->city }}, {{ $address->province }}, {{ $address->postal_code }}</p>
                                            </div>
                                            <div class="text-xs text-muted">Pilih alamat ini</div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    @error('address_id')
                        <p class="text-sm text-red-400 mt-3">{{ $message }}</p>
                    @enderror

                    <div class="mt-4">
                        <label class="text-xs text-muted mb-1 block">Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="input-dark w-full px-4 py-3 rounded-lg text-sm" placeholder="email@domain.com">
                    </div>
                </div>

                <div class="bg-dark-100 border border-dark-300 rounded-xl p-6">
                    <h3 class="font-display font-semibold text-lg mb-4"><i class="fas fa-qrcode text-gold mr-2"></i>Pembayaran QRIS</h3>
                    <p class="text-sm text-muted mb-4">Scan QRIS berikut, lalu masukkan nominal pembayaran sesuai total pesanan.</p>
                    <div class="rounded-xl overflow-hidden border border-dark-300 mb-4">
                        <img src="{{ $qrisImage }}" alt="QRIS Pembayaran" class="w-full object-cover">
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs text-muted mb-1 block">Total Pesanan</label>
                            <div class="input-dark w-full px-4 py-3 rounded-lg text-sm bg-dark-200">Rp {{ number_format($total, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <label class="text-xs text-muted mb-1 block">Nominal Bayar</label>
                            <input type="number" name="payment_amount" value="{{ old('payment_amount') ?? $total }}" required min="{{ $total }}" class="input-dark w-full px-4 py-3 rounded-lg text-sm" placeholder="Masukkan nominal pembayaran">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-dark-100 border border-dark-300 rounded-xl p-6 h-fit sticky top-24">
                <h3 class="font-display font-semibold text-lg mb-4">Ringkasan Pesanan</h3>
                <div class="space-y-3 mb-4 max-h-60 overflow-y-auto">
                    @foreach($cartItems as $item)
                    <div class="flex gap-3 items-center">
                        <img src="{{ $item->image }}" class="w-12 h-12 rounded-lg object-cover">
                        <div class="flex-1 min-w-0"><p class="text-xs truncate">{{ $item->name }}</p><p class="text-xs text-muted">{{ $item->qty }}x</p></div>
                        <span class="text-xs font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="border-t border-dark-300 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-muted">Subtotal</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-muted">Ongkir</span><span class="text-green-400">Gratis</span></div>
                    <div class="border-t border-dark-300 pt-2 flex justify-between"><span class="font-semibold">Total</span><span class="text-gold font-bold text-lg">Rp {{ number_format($total, 0, ',', '.') }}</span></div>
                </div>
                <button type="submit" class="btn-gold w-full py-3 rounded-lg text-sm mt-6"><i class="fas fa-lock mr-2"></i>Konfirmasi Pembelian</button>
            </div>
        </div>
    </form>
</section>
@endsection