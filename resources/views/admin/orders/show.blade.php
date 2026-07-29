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

<h1 class="font-display font-bold text-2xl mb-6">Detail Pesanan</h1>

<div class="mb-6">
    <a href="{{ route('admin.orders.index') }}"
       class="text-sm text-muted hover:underline">
        &larr; Kembali ke daftar pesanan
    </a>
</div>

<div class="bg-dark-100 border border-dark-300 rounded-xl p-6">

    <div class="flex justify-between items-start mb-4">

        <div>
            <p class="text-xs text-muted">
                {{ $order->order_number }} —
                {{ $order->ordered_at->format('d M Y H:i') }}
            </p>

            <h2 class="text-lg font-semibold mt-1">
                {{ $order->shipping_name }}
            </h2>

            <p class="text-sm text-muted">
                {{ $order->shipping_phone }}
                @if($order->shipping_email)
                    — {{ $order->shipping_email }}
                @endif
            </p>
        </div>

        <div class="text-right">

            <p class="text-sm text-muted mb-2">
                Status
            </p>

            <form method="POST"
                  action="{{ route('admin.orders.update', $order->id) }}">

                @csrf
                @method('PUT')

                <select
                    name="status"
                    class="input-dark px-3 py-1.5 rounded-lg text-sm">

                    @foreach($statusList as $value => $label)
                        <option
                            value="{{ $value }}"
                            {{ $order->status == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach

                </select>

                <div class="mt-2 text-right">
                    <button type="submit" class="btn-gold">
                        Perbarui
                    </button>
                </div>

            </form>

        </div>

    </div>


    <div class="mb-4">
        <h3 class="font-medium">Alamat Pengiriman</h3>
        <p class="text-sm text-muted">
            {{ $order->shipping_address }}
        </p>

    <div class="mb-4 grid gap-4 sm:grid-cols-2">

        <div>
            <h3 class="font-medium">Alamat Pengiriman</h3>

            <p class="text-sm text-muted">
                {{ $order->shipping_address }}
            </p>
        </div>

        <div>
            <h3 class="font-medium">Pembayaran</h3>

            <p class="text-sm text-muted">
                {{ strtoupper($order->payment_method) }}
            </p>

            @if($order->payment_amount)
                <p class="text-sm text-white font-semibold">
                    Rp {{ number_format($order->payment_amount, 0, ',', '.') }}
                </p>
            @endif

        </div>

    </div>

    </div>

    <div class="mb-4">

        <h3 class="font-medium mb-3">
            Detail Item
        </h3>

        <table class="w-full text-left text-sm">

            <thead>
                <tr class="text-xs text-muted">
                    <th>Produk</th>
                    <th class="text-right">Harga</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>

            <tbody>

                @foreach($order->items as $item)

                <tr class="border-t border-dark-200">

                    <td class="py-3">
                        {{ $item->product_name }}
                    </td>

                    <td class="py-3 text-right">
                        {{ number_format($item->price, 0, ',', '.') }}
                    </td>

                    <td class="py-3 text-center">
                        {{ $item->quantity }}
                    </td>

                    <td class="py-3 text-right">
                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                    </td>

                </tr>

                @endforeach

            </tbody>

            <tfoot>

                <tr class="border-t border-dark-300">

                    <td colspan="3"
                        class="py-3 text-right font-bold">
                        Total
                    </td>

                    <td class="py-3 text-right font-bold">
                        {{ number_format($order->total_amount, 0, ',', '.') }}
                    </td>

                </tr>

            </tfoot>

        </table>

    </div>

    @if($order->notes)

    <div class="mb-4">
        <h3 class="font-medium">Catatan</h3>

        <p class="text-sm text-muted">
            {{ $order->notes }}
        </p>
    </div>

    @endif

    <div class="flex gap-3 mt-4">

        <form method="POST"
              action="{{ route('admin.orders.destroy', $order->id) }}"
              onsubmit="return confirm('Hapus pesanan ini?')">

            @csrf
            @method('DELETE')

            <button type="submit" class="btn-danger">
                Hapus
            </button>

        </form>

        <a href="mailto:{{ config('mail.from.address') }}?subject=Order%20{{ $order->order_number }}"
           class="btn-dark">
            Email Pelanggan
        </a>

    </div>

</div>

@endsection