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
                    <h3 class="font-display font-semibold text-lg mb-4"><i class="fas fa-map-marker-alt text-gold mr-2"></i>Alamat Pengiriman</h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="text-xs text-muted mb-1 block">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ auth()->user()->name }}" required class="input-dark w-full px-4 py-3 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="text-xs text-muted mb-1 block">Nomor Telepon</label>
                            <input type="tel" name="phone" value="{{ auth()->user()->phone ?? '' }}" required inputmode="numeric" pattern="[0-9]{1,15}" maxlength="15" class="input-dark w-full px-4 py-3 rounded-lg text-sm" placeholder="08xxxxxxxxxx">
                        </div>
                        <div>
                            <label class="text-xs text-muted mb-1 block">Kota</label>
                            <input type="text" name="city" required class="input-dark w-full px-4 py-3 rounded-lg text-sm" placeholder="Kota Anda">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs text-muted mb-1 block">Alamat Lengkap</label>
                            <textarea name="address" rows="3" required class="input-dark w-full px-4 py-3 rounded-lg text-sm resize-none" placeholder="Jl. ..."></textarea>
                        </div>
                        <div>
                            <label class="text-xs text-muted mb-1 block">Kode Pos</label>
                            <input type="text" name="postal" required class="input-dark w-full px-4 py-3 rounded-lg text-sm" placeholder="60xxx">
                        </div>
                    </div>
                </div>

                <div class="bg-dark-100 border border-dark-300 rounded-xl p-6">
                    <h3 class="font-display font-semibold text-lg mb-4"><i class="fas fa-credit-card text-gold mr-2"></i>Metode Pembayaran</h3>
                    <div class="space-y-3">
                        @php
                            $payments = ['bca' => 'Transfer Bank BCA', 'mandiri' => 'Transfer Bank Mandiri', 'bri' => 'Transfer Bank BRI', 'gopay' => 'GoPay', 'ovo' => 'OVO', 'dana' => 'DANA'];
                        @endphp
                        @foreach($payments as $val => $label)
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-dark-300 cursor-pointer hover:border-gold-dark transition-colors has-[:checked]:border-gold has-[:checked]:bg-gold/5">
                            <input type="radio" name="payment" value="{{ $val }}" {{ $loop->first ? 'checked' : '' }} class="accent-[#C8A951]">
                            <span class="text-sm">{{ $label }}</span>
                        </label>
                        @endforeach
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