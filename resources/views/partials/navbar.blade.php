<header style="position:fixed;top:0;left:0;right:0;z-index:50;background:#0F0F0F;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between" style="height:64px;">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ asset('images/logo/logo.png') }}" alt="Atmobrass" class="h-10 w-auto" />
            <span class="font-display font-bold text-lg gold-gradient">Atmobrass</span>
        </a>
        <nav class="hidden lg:flex items-center gap-8">
            <a href="{{ route('home') }}" class="text-sm font-medium hover:text-gold" style="color:{{ Request::is('/') ? '#C8A951' : '#9A9590' }}">Beranda</a>
            <a href="{{ route('about') }}" class="text-sm font-medium hover:text-gold" style="color:#9A9590">Tentang Kami</a>
            <a href="{{ route('products.index') }}" class="text-sm font-medium hover:text-gold" style="color:#9A9590">Produk</a>
            <a href="{{ route('contact') }}" class="text-sm font-medium hover:text-gold" style="color:#9A9590">Kontak</a>
        </nav>
        <div class="flex items-center gap-3">
            @php
                $cartCount = 0;
                if (auth()->check()) {
                    $cart = auth()->user()->cart()->with('items')->first();
                    if ($cart) {
                        $cartCount = $cart->items->sum('quantity');
                    }
                } else {
                    $cartCount = collect(session('cart', []))->sum();
                }
            @endphp
            <a href="{{ route('cart.index') }}" class="relative p-2 hover:text-gold" style="color:#9A9590">
                <i class="fas fa-shopping-cart text-lg"></i>
                @if($cartCount > 0)
                    <span class="absolute -top-1 -right-1 bg-gold text-black text-[10px] font-bold min-w-5 h-5 rounded-full flex items-center justify-center px-1">{{ $cartCount }}</span>
                @endif
            </a>
            @if(auth()->check())
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="p-2 text-gold"><i class="fas fa-user-shield text-lg"></i></a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline">@csrf<button type="submit" class="p-2 hover:text-red-400" style="color:#9A9590"><i class="fas fa-sign-out-alt text-lg"></i></button></form>
                <span class="text-xs font-semibold text-gold hidden sm:inline">{{ auth()->user()->name }}</span>
            @else
                <a href="{{ route('login') }}" class="p-2 hover:text-gold" style="color:#9A9590"><i class="fas fa-user text-lg"></i></a>
            @endif
            <button id="mobile-menu-btn" class="lg:hidden p-2 hover:text-gold" style="color:#9A9590"><i class="fas fa-bars text-lg"></i></button>
        </div>
    </div>
    <div id="mobile-menu" class="hidden lg:hidden" style="border-top:1px solid #2A2A2A;background:#1A1A1A;">
        <div class="px-4 py-4 flex flex-col gap-3">
            <a href="{{ route('home') }}" class="text-sm hover:text-gold py-2" style="color:#9A9590">Beranda</a>
            <a href="{{ route('about') }}" class="text-sm hover:text-gold py-2" style="color:#9A9590">Tentang Kami</a>
            <a href="{{ route('products.index') }}" class="text-sm hover:text-gold py-2" style="color:#9A9590">Produk</a>
            <a href="{{ route('contact') }}" class="text-sm hover:text-gold py-2" style="color:#9A9590">Kontak</a>
            @if(auth()->check())
                <form method="POST" action="{{ route('logout') }}" class="inline">@csrf<button class="text-sm text-red-400 py-2">Logout</button></form>
            @else
                <a href="{{ route('login') }}" class="text-sm text-gold py-2">Login / Daftar</a>
            @endif
        </div>
    </div>
</header>