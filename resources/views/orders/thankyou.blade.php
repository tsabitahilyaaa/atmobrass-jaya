@extends('layouts.app')

@section('title', 'Terima Kasih — Pesanan Diterima')

@section('content')
<section class="py-16 max-w-3xl mx-auto px-4">
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-8 text-center">
        <h1 class="font-display font-bold text-2xl mb-4">Terima Kasih!</h1>
        <p class="text-sm text-muted mb-4">Pesanan Anda telah kami terima dengan nomor pesanan:</p>
        <p class="font-bold text-lg gold-gradient mb-4">{{ $order->order_number }}</p>
        <p class="text-sm text-muted mb-4">Status: {{ ucfirst($order->status) }}</p>
        <a href="{{ route('home') }}" class="btn-gold px-6 py-2 rounded-lg">Kembali ke Beranda</a>
    </div>
</section>
@endsection
