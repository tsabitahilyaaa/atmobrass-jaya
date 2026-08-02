@extends('layouts.admin')

@section('title', 'Detail Pesanan — Admin')

@section('content')

@php
$statusList = [
    'pending' => 'Pending',
    'paid' => 'Dibayar',
    'processing' => 'Diproses',
    'shipped' => 'Dikirim',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan',
];
@endphp

<h1 class="font-display font-bold text-2xl mb-6">
    Detail Pesanan
</h1>

<div class="mb-6">
    <a href="{{ route('admin.orders.index') }}"
       class="text-sm text-muted hover:underline">
        &larr; Kembali ke daftar pesanan
    </a>
</div>

<div class="bg-dark-100 border border-dark-300 rounded-2xl p-8">

    {{-- Header --}}

    <div class="flex items-center justify-between mb-8">

        <div>

            <h1 class="font-display font-bold text-2xl">
                Detail Pesanan
            </h1>

            <p class="text-sm text-muted mt-1">
                Informasi lengkap pesanan pelanggan
            </p>

        </div>

        <span
            class="px-4 py-2 rounded-full
            @if($order->status=='pending') bg-yellow-500/20 text-yellow-400
            @elseif($order->status=='paid') bg-green-500/20 text-green-400
            @elseif($order->status=='processing') bg-blue-500/20 text-blue-400
            @elseif($order->status=='shipped') bg-indigo-500/20 text-indigo-400
            @elseif($order->status=='completed') bg-emerald-500/20 text-emerald-400
            @else bg-red-500/20 text-red-400
            @endif
            text-sm font-semibold">

            {{ strtoupper($order->status) }}

        </span>

    </div>


    {{-- Customer + Address --}}

    <div class="grid lg:grid-cols-2 gap-6 mb-8">

        {{-- Customer --}}

        <div class="bg-dark-200 border border-dark-300 rounded-2xl p-6">

            <h3 class="font-semibold text-lg mb-5">

                <i class="fas fa-user text-gold mr-2"></i>

                Identitas Customer

            </h3>

            <div class="space-y-4">

                <div>

                    <p class="text-xs uppercase tracking-wider text-muted">
                        Nama
                    </p>

                    <p class="mt-1 font-medium">
                        {{ $order->shipping_name }}
                    </p>

                </div>

                <div>

                    <p class="text-xs uppercase tracking-wider text-muted">
                        Email
                    </p>

                    <p class="mt-1">
                        {{ $order->shipping_email ?: '-' }}
                    </p>

                </div>

                <div>

                    <p class="text-xs uppercase tracking-wider text-muted">
                        Nomor Telepon
                    </p>

                    <p class="mt-1">
                        {{ $order->shipping_phone }}
                    </p>

                </div>

            </div>

        </div>


        {{-- Address --}}

        <div class="bg-dark-200 border border-dark-300 rounded-2xl p-6">

            <h3 class="font-semibold text-lg mb-5">

                <i class="fas fa-map-marker-alt text-gold mr-2"></i>

                Alamat Pengiriman

            </h3>

            <div class="space-y-4">

                <div>

                    <p class="text-xs uppercase tracking-wider text-muted">
                        Kota
                    </p>

                    <p class="mt-1">
                        {{ $order->shipping_city }}
                    </p>

                </div>

                <div>

                    <p class="text-xs uppercase tracking-wider text-muted">
                        Kode Pos
                    </p>

                    <p class="mt-1">
                        {{ $order->shipping_postal }}
                    </p>

                </div>

                <div>

                    <p class="text-xs uppercase tracking-wider text-muted">
                        Alamat Lengkap
                    </p>

                    <p class="mt-1 leading-7">

                        {{ $order->shipping_address }}

                    </p>

                </div>

            </div>

        </div>

    </div>

    {{-- ===================== --}}
    {{-- DETAIL PESANAN --}}
    {{-- ===================== --}}

    <div class="mb-10">

        <div class="flex items-center justify-between mb-6">

            <div>

                <h3 class="text-lg font-semibold">

                    <i class="fas fa-box-open text-gold mr-2"></i>

                    Detail Pesanan

                </h3>

                <p class="text-sm text-muted mt-1">

                    Order #{{ $order->order_number }}

                </p>

            </div>

            <div class="text-right">

                <p class="text-xs text-muted uppercase tracking-wider">

                    Tanggal Pesanan

                </p>

                <p class="font-medium">

                    {{ $order->ordered_at->format('d M Y H:i') }}

                </p>

            </div>

        </div>


        {{-- List Produk --}}

        <div class="space-y-4">

            @foreach($order->items as $item)

            <div class="bg-dark-200 border border-dark-300 rounded-2xl p-5">

                <div class="flex items-center gap-5">

                    <img
                        src="{{ $item->product_image }}"
                        class="w-20 h-20 rounded-xl object-cover border border-dark-300">

                    <div class="flex-1">

                        <h4 class="font-semibold text-base">

                            {{ $item->product_name }}

                        </h4>

                        <div class="grid grid-cols-3 gap-6 mt-4 text-sm">

                            <div>

                                <p class="text-muted">
                                    Harga
                                </p>

                                <p class="font-semibold mt-1">

                                    Rp {{ number_format($item->product_price,0,',','.') }}

                                </p>

                            </div>

                            <div>

                                <p class="text-muted">
                                    Jumlah
                                </p>

                                <p class="font-semibold mt-1">

                                    {{ $item->quantity }}

                                </p>

                            </div>

                            <div>

                                <p class="text-muted">
                                    Subtotal
                                </p>

                                <p class="font-bold text-gold mt-1">

                                    Rp {{ number_format($item->subtotal,0,',','.') }}

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            @endforeach

        </div>


        {{-- Ringkasan Total --}}

        <div class="flex justify-end mt-6">

            <div class="bg-dark-200 border border-dark-300 rounded-2xl p-6 w-full md:w-96">

                <div class="flex justify-between mb-3">

                    <span class="text-muted">

                        Total Item

                    </span>

                    <span>

                        {{ $order->items->sum('quantity') }}

                    </span>

                </div>

                <div class="border-t border-dark-300 my-4"></div>

                <div class="flex justify-between items-center">

                    <span class="font-semibold">

                        Total Pesanan

                    </span>

                    <span class="text-2xl font-bold text-gold">

                        Rp {{ number_format($order->total_amount,0,',','.') }}

                    </span>

                </div>

            </div>

        </div>

    </div>

    {{-- ===================== --}}
    {{-- PEMBAYARAN --}}
    {{-- ===================== --}}

    <div class="mt-10">

        <h3 class="font-semibold text-lg mb-6">

            <i class="fas fa-credit-card text-gold mr-2"></i>

            Pembayaran

        </h3>

        <div class="bg-dark-200 border border-dark-300 rounded-2xl p-6">

            <div class="grid lg:grid-cols-2 gap-8">

                {{-- KIRI --}}

                <div>

                    <div class="space-y-4 mb-6">

                        <div class="flex justify-between">

                            <span class="text-muted">
                                Metode Pembayaran
                            </span>

                            <span class="font-medium">
                                {{ strtoupper($order->payment_method) }}
                            </span>

                        </div>

                        <div class="flex justify-between">

                            <span class="text-muted">
                                Nominal
                            </span>

                            <span class="font-semibold text-gold">

                                Rp {{ number_format($order->payment_amount,0,',','.') }}

                            </span>

                        </div>

                        <div class="flex justify-between items-center">

                            <span class="text-muted">
                                Status Pembayaran
                            </span>

                            @if($order->payment_status=='pending')

                                <span class="px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-400 text-xs">

                                    Menunggu Verifikasi

                                </span>

                            @elseif($order->payment_status=='verified')

                                <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-400 text-xs">

                                    Terverifikasi

                                </span>

                            @else

                                <span class="px-3 py-1 rounded-full bg-red-500/20 text-red-400 text-xs">

                                    Ditolak

                                </span>

                            @endif

                        </div>

                    </div>

                    <p class="font-medium mb-3">

                        Bukti Pembayaran

                    </p>

                    @if($order->payment_proof)

                        <a href="{{ asset('storage/'.$order->payment_proof) }}"
                        target="_blank">

                            <img
                                src="{{ asset('storage/'.$order->payment_proof) }}"
                                class="w-56 rounded-xl border border-dark-300 hover:scale-105 transition">

                        </a>

                    @else

                        <div class="w-56 h-56 rounded-xl border border-dashed border-dark-300 flex items-center justify-center text-muted text-sm">

                            Tidak ada bukti pembayaran

                        </div>

                    @endif

                </div>

                {{-- KANAN --}}

                <div class="flex flex-col">

                    <div class="bg-dark-100 rounded-xl border border-dark-300 p-5">

                        <p class="text-sm text-muted mb-2">

                            Status Pesanan

                        </p>

                        <form method="POST"
                            action="{{ route('admin.orders.update',$order->id) }}">

                            @csrf
                            @method('PUT')

                            <select
                                name="status"
                                class="input-dark w-full rounded-xl px-4 py-3">

                                @foreach($statusList as $value=>$label)

                                    <option
                                        value="{{ $value }}"
                                        {{ $order->status==$value?'selected':'' }}>

                                        {{ $label }}

                                    </option>

                                @endforeach

                            </select>

                            <button
                                class="btn-gold w-full mt-4">

                                <i class="fas fa-save mr-2"></i>

                                Perbarui Status

                            </button>

                        </form>

                    </div>


                    @if($order->payment_status=='pending')

                    <div class="grid gap-3 mt-6">

                        <form
                            method="POST"
                            action="{{ route('admin.orders.verify',$order->id) }}">

                            @csrf
                            @method('PATCH')

                            <button
                                class="btn-gold w-full py-3 rounded-xl">

                                <i class="fas fa-check mr-2"></i>

                                Verifikasi Pembayaran

                            </button>

                        </form>

                        <form
                            method="POST"
                            action="{{ route('admin.orders.reject',$order->id) }}">

                            @csrf
                            @method('PATCH')

                            <button
                                class="w-full py-3 rounded-xl bg-red-600 hover:bg-red-700 transition">

                                <i class="fas fa-times mr-2"></i>

                                Tolak Pembayaran

                            </button>

                        </form>

                    </div>

                    @endif

                </div>

            </div>

        </div>

</div>

{{-- ===================== --}}
{{-- FOOTER --}}
{{-- ===================== --}}

<div class="border-t border-dark-300 mt-10 pt-8">

    <div class="flex flex-col md:flex-row justify-between items-center gap-4">

        <div class="flex flex-wrap gap-3">

            <a href="mailto:{{ $order->shipping_email }}"
               class="px-5 py-3 rounded-xl border border-dark-300 hover:border-gold transition flex items-center gap-2">

                <i class="fas fa-envelope text-gold"></i>

                Email Customer

            </a>

            <form method="POST"
                  action="{{ route('admin.orders.destroy',$order->id) }}"
                  onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">

                @csrf
                @method('DELETE')

                <button
                    class="px-5 py-3 rounded-xl bg-red-600 hover:bg-red-700 transition flex items-center gap-2">

                    <i class="fas fa-trash"></i>

                    Hapus Pesanan

                </button>

            </form>

        </div>

    </div>

</div>

@endsection