@extends('layouts.admin')

@section('title', 'Manajemen Pesanan — Admin')

@section('content')
<h1 class="font-display font-bold text-2xl mb-6">Manajemen Pesanan</h1>

<div class="flex flex-wrap gap-2 mb-6">
    <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 rounded-full text-xs font-medium transition-all {{ !request('status') ? 'btn-gold' : 'bg-dark-100 border border-dark-300 text-muted hover:border-gold-dark' }}">Semua</a>
    @foreach(['pending','dibayar','dikirim','selesai'] as $s)
        <a href="{{ route('admin.orders.index', ['status' => $s]) }}" class="px-4 py-2 rounded-full text-xs font-medium transition-all {{ request('status') === $s ? 'btn-gold' : 'bg-dark-100 border border-dark-300 text-muted hover:border-gold-dark' }}">{{ ucfirst($s) }}</a>
    @endforeach
</div>

@if($orders->count() === 0)
    <p class="text-muted text-sm">Belum ada pesanan.</p>
@else
    <div class="space-y-4">
        @foreach($orders as $order)
        <a href="{{ route('admin.orders.show', $order->id) }}" class="block bg-dark-100 border border-dark-300 rounded-xl p-5 hover:shadow-lg transition-shadow">
            <div class="flex flex-wrap justify-between items-start gap-3 mb-3">
                <div>
                    <p class="text-xs text-muted">{{ $order->order_number }} — {{ $order->formatted_date }}</p>
                    <p class="text-sm font-semibold mt-1">{{ $order->shipping_name }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('admin.orders.update', $order->id) }}" class="inline">
                        @csrf @method('PUT')
                        <select name="status" onchange="this.form.submit()" class="input-dark px-3 py-1.5 rounded-lg text-xs">
                            @foreach(['pending','dibayar','dikirim','selesai'] as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </form>
                    <span class="text-gold font-bold text-sm">{{ $order->formatted_total }}</span>
                    <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}" class="inline" onsubmit="return confirm('Hapus pesanan ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-muted hover:text-red-400 transition-colors"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mb-3">
                @foreach($order->items as $item)
                    <span class="text-xs bg-dark-200 px-3 py-1 rounded-full">{{ $item->product_name }} x{{ $item->quantity }}</span>
                @endforeach
            </div>
            <div class="text-xs text-muted flex flex-wrap gap-4">
                <span><i class="fas fa-map-marker-alt mr-1 text-gold"></i>{{ $order->shipping_address }}, {{ $order->shipping_city }}</span>
                <span><i class="fas fa-phone mr-1 text-gold"></i>{{ $order->shipping_phone }}</span>
                <span><i class="fas fa-credit-card mr-1 text-gold"></i>{{ strtoupper($order->payment_method) }}</span>
            </div>
        </a>
        @endforeach
    </div>

    <div class="mt-6 flex justify-center">{{ $orders->links() }}</div>
@endif
@endsection