@extends('layouts.app')

@section('title', 'Checkout — CV Atmobrass Jaya')

@section('content')
<section class="py-10 sm:py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="max-w-5xl mx-auto">

        <h1 class="font-display font-bold text-2xl sm:text-3xl mb-8">
            Checkout
        </h1>

        <form method="POST"
            action="{{ route('checkout.process') }}"
            enctype="multipart/form-data">

            @csrf

            <div class="space-y-6">

                {{-- ALAMAT --}}
                <div class="bg-dark-100 border border-dark-300 rounded-xl p-6">

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">

                        <h3 class="font-display font-semibold text-lg">
                            <i class="fas fa-map-marker-alt text-gold mr-2"></i>
                            Alamat Pengiriman
                        </h3>

                        <div class="flex flex-wrap gap-2">

                            <a href="{{ route('profile.addresses.create') }}"
                                class="btn-gold px-4 py-3 rounded-full text-xs sm:text-sm">
                                Tambah Alamat
                            </a>

                            <a href="{{ route('profile.addresses') }}"
                                class="btn-outline px-4 py-3 rounded-full text-xs sm:text-sm">
                                Kelola Alamat
                            </a>

                        </div>

                    </div>

                    @if($addresses->isEmpty())

                        <div class="bg-dark-200 border border-dashed border-dark-300 rounded-2xl p-6 text-center">

                            <p class="text-sm text-muted mb-3">
                                Belum ada alamat tersimpan.
                            </p>

                            <a href="{{ route('profile.addresses.create') }}"
                                class="btn-gold px-5 py-3 rounded-full text-sm">
                                Tambah Alamat
                            </a>

                        </div>

                    @else

                        <div class="space-y-3">

                            @foreach($addresses as $address)

                            <label class="flex items-start gap-4 bg-dark-200 border border-dark-300 rounded-2xl p-5 cursor-pointer hover:border-gold transition">

                                <input
                                    type="radio"
                                    name="address_id"
                                    value="{{ $address->id }}"
                                    class="mt-1 h-5 w-5"
                                    style="accent-color:#C9A227;"
                                    {{ old('address_id',$defaultAddress?->id)==$address->id ? 'checked' : '' }}>

                                <div class="flex-1">

                                    <div class="flex items-center gap-2 flex-wrap">

                                        <h4 class="font-semibold">
                                            {{ $address->recipient_name }}
                                        </h4>

                                        @if($address->is_default)
                                            <span class="px-2 py-1 rounded-full text-[10px] bg-gold/20 text-gold">
                                                Utama
                                            </span>
                                        @endif

                                    </div>

                                    <p class="text-sm text-muted mt-1">
                                        {{ $address->phone }}
                                    </p>

                                    <p class="text-sm text-muted mt-2">
                                        {{ $address->address }},
                                        {{ $address->city }},
                                        {{ $address->province }},
                                        {{ $address->postal_code }}
                                    </p>

                                </div>

                            </label>

                            @endforeach

                        </div>

                    @endif

                    @error('address_id')
                        <p class="text-red-400 text-sm mt-3">
                            {{ $message }}
                        </p>
                    @enderror

                    <div class="mt-5">

                        <label class="text-xs text-muted block mb-2">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email',auth()->user()->email) }}"
                            class="input-dark w-full px-4 py-3 rounded-xl"
                            placeholder="email@domain.com">

                    </div>

                </div>

                {{-- RINGKASAN --}}
                <div class="bg-dark-100 border border-dark-300 rounded-xl p-6">

                    <h3 class="font-display font-semibold text-lg mb-5">
                        Ringkasan Pesanan
                    </h3>

                    <div class="space-y-4 mb-6">

                        @foreach($cartItems as $item)

                        <div class="flex items-center gap-4">

                            <img
                                src="{{ $item->image }}"
                                class="w-16 h-16 rounded-xl object-cover">

                            <div class="flex-1 min-w-0">

                                <p class="font-medium truncate">
                                    {{ $item->name }}
                                </p>

                                <p class="text-sm text-muted">
                                    {{ $item->qty }} x
                                </p>

                            </div>

                            <span class="font-semibold whitespace-nowrap">
                                Rp {{ number_format($item->subtotal,0,',','.') }}
                            </span>

                        </div>

                        @endforeach

                    </div>

                    <div class="border-t border-dark-300 pt-4 space-y-3">

                        <div class="flex justify-between">

                            <span class="text-muted">
                                Subtotal
                            </span>

                            <span>
                                Rp {{ number_format($total,0,',','.') }}
                            </span>

                        </div>

                        <div class="flex justify-between">

                            <span class="text-muted">
                                Ongkir
                            </span>

                            <span class="text-green-400">
                                Gratis
                            </span>

                        </div>

                        <div class="border-t border-dark-300 pt-3 flex justify-between items-center">

                            <span class="font-semibold">
                                Total
                            </span>

                            <span class="text-gold font-bold text-2xl">
                                Rp {{ number_format($total,0,',','.') }}
                            </span>

                        </div>

                    </div>

                </div>
                                {{-- PEMBAYARAN --}}
                <div class="bg-dark-100 border border-dark-300 rounded-xl p-6">

                    <h3 class="font-display font-semibold text-lg mb-2">
                        <i class="fas fa-qrcode text-gold mr-2"></i>
                        Pembayaran QRIS
                    </h3>

                    <p class="text-sm text-muted mb-6">
                        Scan QRIS di bawah menggunakan aplikasi pembayaran Anda,
                        lalu upload bukti pembayaran.
                    </p>

                    <div class="grid md:grid-cols-2 gap-8">

                        {{-- QRIS --}}
                        <div class="flex justify-center">
                            <div class="bg-white rounded-2xl p-4 shadow-lg">
                                <img
                                    src="{{ $qrisImage }}"
                                    alt="QRIS"
                                    class="w-56 h-56 object-contain">
                            </div>
                        </div>

                        {{-- FORM PEMBAYARAN --}}
                        <div class="space-y-5">

                            <div>

                                <label class="text-xs text-muted mb-2 block">
                                    Total Pembayaran
                                </label>

                                <div class="bg-dark-200 border border-dark-300 rounded-xl px-4 py-3">

                                    <span class="text-gold text-2xl font-bold">
                                        Rp {{ number_format($total,0,',','.') }}
                                    </span>

                                </div>

                            </div>

                            <div>

                                <label class="text-xs text-muted mb-2 block">
                                    Upload Bukti Pembayaran
                                </label>

                                <input
                                    id="payment_proof"
                                    type="file"
                                    name="payment_proof"
                                    accept="image/*"
                                    class="hidden">

                                <label
                                    for="payment_proof"
                                    class="cursor-pointer flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gold bg-dark-200 hover:bg-dark-300 transition py-10">

                                    <i class="fas fa-cloud-upload-alt text-gold text-4xl mb-3"></i>

                                    <span class="font-medium">
                                        Klik untuk upload bukti pembayaran
                                    </span>

                                    <span class="text-xs text-muted mt-2">
                                        JPG / PNG (Maks 5 MB)
                                    </span>

                                </label>

                                <p
                                    id="file-name"
                                    class="text-center text-xs text-muted mt-3">
                                    Belum ada file dipilih
                                </p>

                                @error('payment_proof')
                                    <p class="text-red-400 text-xs mt-2">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            <div>

                                <label class="text-xs text-muted mb-2 block">
                                    Nominal Pembayaran
                                </label>

                                <input
                                    type="number"
                                    name="payment_amount"
                                    value="{{ old('payment_amount',$total) }}"
                                    min="{{ $total }}"
                                    required
                                    class="input-dark w-full px-4 py-3 rounded-xl"
                                    placeholder="Masukkan nominal pembayaran">

                            </div>

                            <button
                                type="submit"
                                class="btn-gold w-full py-4 rounded-xl mt-4">

                                <i class="fas fa-lock mr-2"></i>
                                Konfirmasi Pembelian

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>

</section>

<script>
document.addEventListener('DOMContentLoaded', function(){

    const input = document.getElementById('payment_proof');
    const fileName = document.getElementById('file-name');

    if(input){

        input.addEventListener('change', function(){

            if(this.files.length){

                fileName.innerHTML =
                    '<span class="text-green-400"><i class="fas fa-check-circle mr-1"></i>' +
                    this.files[0].name +
                    '</span>';

            }else{

                fileName.innerHTML = 'Belum ada file dipilih';

            }

        });

    }

});
</script>

@endsection