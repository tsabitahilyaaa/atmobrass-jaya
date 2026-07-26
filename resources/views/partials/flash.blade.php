@php
    $cartLink = session('cart_link');
@endphp

@php
    $cartLink = session('cart_link');
    $flashId = 'flash-' . uniqid();
@endphp

@session('success')
<div id="{{ $flashId }}" class="fixed top-20 right-4 z-50 max-w-sm w-[calc(100%-2rem)]">
    <div class="bg-[#C8A951] border border-[#B99248] text-black px-4 py-3 rounded-lg shadow-2xl flex items-start gap-3">
        <i class="fas fa-check-circle text-black mt-0.5"></i>
        <div class="flex-1">
            <p class="text-sm font-medium">{{ $value }}</p>
            @if($cartLink)
                <a href="{{ $cartLink }}" class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-black/90 hover:text-black">
                    <i class="fas fa-shopping-cart"></i>
                    Lihat Keranjang
                </a>
            @endif
        </div>
    </div>
</div>
<script>
    (function(){
        const el = document.getElementById('{{ $flashId }}');
        if (!el) return;
        // auto-hide after 3.5 seconds with fade-out
        setTimeout(() => {
            el.style.transition = 'opacity 400ms ease, transform 400ms ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-8px)';
            setTimeout(() => el.remove(), 450);
        }, 3500);
    })();
</script>
@endsession

@session('error')
@php $errId = 'flash-err-' . uniqid(); @endphp
<div id="{{ $errId }}" class="fixed top-20 right-4 z-50 max-w-sm w-[calc(100%-2rem)]">
    <div class="bg-[#1B1714] border border-red-600 text-red-300 px-4 py-3 rounded-lg shadow-2xl flex items-start gap-3">
        <i class="fas fa-exclamation-circle mt-0.5"></i>
        <span class="text-sm">{{ $value }}</span>
    </div>
</div>
<script>
    (function(){
        const el = document.getElementById('{{ $errId }}');
        if (!el) return;
        setTimeout(() => {
            el.style.transition = 'opacity 400ms ease, transform 400ms ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-8px)';
            setTimeout(() => el.remove(), 450);
        }, 3500);
    })();
</script>
@endsession