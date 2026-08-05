@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('content')
<h1 class="font-display font-bold text-2xl mb-6">Dashboard</h1>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-5"><div class="flex items-center justify-between mb-3"><span class="text-xs text-muted">Total Pesanan</span><i class="fas fa-receipt text-blue-400"></i></div><p class="font-bold text-lg">{{ $totalOrders }}</p></div>
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-5"><div class="flex items-center justify-between mb-3"><span class="text-xs text-muted">Total Produk</span><i class="fas fa-boxes-stacked text-gold"></i></div><p class="font-bold text-lg">{{ $totalProducts }}</p></div>
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-5"><div class="flex items-center justify-between mb-3"><span class="text-xs text-muted">Total Pengguna</span><i class="fas fa-users text-purple-400"></i></div><p class="font-bold text-lg">{{ $totalUsers }}</p></div>
</div>
<div class="grid lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-muted">Pesan Masuk Minggu Ini</span>
            <i class="fas fa-envelope-open-text text-cyan-400"></i>
        </div>
        <p class="font-bold text-3xl">{{ $incomingMessagesThisWeek }}</p>
        <p class="text-xs text-muted mt-2">Total pesan kontak masuk sejak awal minggu.</p>
    </div>
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-muted">Pengguna Baru Minggu Ini</span>
            <i class="fas fa-user-plus text-green-400"></i>
        </div>
        <p class="font-bold text-3xl">{{ $newUsersThisWeek }}</p>
        <p class="text-xs text-muted mt-2">Pengguna dengan role customer yang terdaftar minggu ini.</p>
    </div>
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-muted">Pesanan Customer Baru</span>
            <i class="fas fa-shopping-bag text-yellow-400"></i>
        </div>
        <p class="font-bold text-3xl">{{ $newCustomerOrdersThisWeek }}</p>
        <p class="text-xs text-muted mt-2">Pesanan dari customer baru selama minggu ini.</p>
    </div>
</div>
<div class="bg-dark-100 border border-dark-300 rounded-xl p-6">
    <h3 class="font-semibold text-sm mb-4">Prediksi Produksi XGBoost</h3>
    <div class="flex items-start gap-4 mb-4">
        <div class="w-14 h-14 rounded-full bg-gold/10 flex items-center justify-center"><i class="fas fa-brain text-gold text-xl"></i></div>
        <div class="min-w-0">
            <p class="text-xs text-muted">Prediksi produksi untuk {{ $predictedMonth }}</p>
            <p class="text-gold font-bold text-xl">{{ number_format($predictedQuantity,0,',','.') }} pcs</p>
            <p class="text-xs {{ $predictionStatus === 'error' ? 'text-red-300' : 'text-muted' }} mt-1">{{ $predictionMessage }}</p>
            @if($predictedItemsCount > 0)
                <p class="text-xs text-muted mt-1">Diprediksi untuk {{ $predictedItemsCount }} produk.</p>
            @endif
        </div>
    </div>
</div>
@endsection