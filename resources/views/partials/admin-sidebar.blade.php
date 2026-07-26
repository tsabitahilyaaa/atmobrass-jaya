@php
 $r = Request::path();
 $dash = $r === 'admin';
 $prods = str_contains($r, 'admin/produk');
 $lstm = str_contains($r, 'admin/lstm');
 $history = str_contains($r, 'admin/history');
 $msgs = str_contains($r, 'admin/pesan');
 $ords = str_contains($r, 'admin/pesanan');
 $usrs = str_contains($r, 'admin/pengguna');
@endphp
<aside class="hidden md:flex fixed top-0 left-0 w-64 flex-col bg-dark-100 border-r border-dark-300" style="height:100vh;z-index:30;">
    <div class="p-6 border-b border-dark-300">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full overflow-hidden bg-dark-200 border border-dark-300 flex items-center justify-center">
                <img src="{{ asset('images/logo/logo.png') }}" alt="Atmobrass Jaya" class="w-full h-full object-cover" />
            </div>
            <span class="font-display font-bold gold-gradient">Admin Panel</span>
        </div>
    </div>
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm {{ $dash ? 'bg-gold/10 text-gold' : 'text-muted hover:text-gold' }}">
            <i class="fas fa-chart-line w-5"></i>Dashboard
        </a>
        <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm {{ $prods ? 'bg-gold/10 text-gold' : 'text-muted hover:text-gold' }}">
            <i class="fas fa-boxes-stacked w-5"></i>Produk
        </a>
        <a href="{{ route('admin.lstm') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm {{ $lstm ? 'bg-gold/10 text-gold' : 'text-muted hover:text-gold' }}">
            <i class="fas fa-brain w-5"></i>Prediksi LSTM
        </a>
        <a href="{{ route('admin.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm {{ $history ? 'bg-gold/10 text-gold' : 'text-muted hover:text-gold' }}">
            <i class="fas fa-clock-rotate-left w-5"></i>Riwayat Penjualan
        </a>
        <a href="{{ route('admin.messages.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm {{ $msgs ? 'bg-gold/10 text-gold' : 'text-muted hover:text-gold' }}">
            <i class="fas fa-envelope-open-text w-5"></i>Pesan
        </a>
        <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm {{ $ords ? 'bg-gold/10 text-gold' : 'text-muted hover:text-gold' }}">
            <i class="fas fa-shopping-bag w-5"></i>Pesanan
        </a>
        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm {{ $usrs ? 'bg-gold/10 text-gold' : 'text-muted hover:text-gold' }}">
            <i class="fas fa-users w-5"></i>Pengguna
        </a>
    </nav>
    <div class="p-4 border-t border-dark-300">
        <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm text-muted hover:text-gold">
            <i class="fas fa-store w-5"></i>Lihat Toko
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm text-red-400 w-full hover:text-red-300">
                <i class="fas fa-sign-out-alt w-5"></i>Logout
            </button>
        </form>
    </div>
</aside>

<!-- Header Mobile -->
<div class="md:hidden fixed top-0 left-0 right-0 bg-dark-100 border-b border-dark-300 flex items-center justify-between px-4" style="height:56px;z-index:30;">
    <span class="font-display font-bold text-sm gold-gradient">Admin</span>
    <div class="flex gap-1">
        <a href="{{ route('admin.dashboard') }}" class="w-9 h-9 rounded-lg flex items-center justify-center {{ $dash ? 'bg-gold/10 text-gold' : 'text-muted' }}">
            <i class="fas fa-chart-line text-sm"></i>
        </a>
        <a href="{{ route('admin.products.index') }}" class="w-9 h-9 rounded-lg flex items-center justify-center {{ $prods ? 'bg-gold/10 text-gold' : 'text-muted' }}">
            <i class="fas fa-boxes-stacked text-sm"></i>
        </a>
        <a href="{{ route('admin.users.index') }}" class="w-9 h-9 rounded-lg flex items-center justify-center {{ $usrs ? 'bg-gold/10 text-gold' : 'text-muted' }}">
            <i class="fas fa-users text-sm"></i>
        </a>
        <a href="{{ route('home') }}" class="w-9 h-9 rounded-lg flex items-center justify-center text-muted">
            <i class="fas fa-store text-sm"></i>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="w-9 h-9 rounded-lg flex items-center justify-center text-red-400">
                <i class="fas fa-sign-out-alt text-sm"></i>
            </button>
        </form>
    </div>
</div>