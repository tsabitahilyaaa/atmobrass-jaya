@php
    $cartLink = session('cart_link');
    $toastMessage = null;
    $toastTitle = 'Informasi';
    $toastType = 'info';

    if (session('success') || session('status')) {
        $toastMessage = session('success') ?: session('status');
        $toastTitle = 'Berhasil';
        $toastType = 'success';
    } elseif (session('error')) {
        $toastMessage = session('error');
        $toastTitle = 'Perhatian';
        $toastType = 'error';
    } elseif (session('warning')) {
        $toastMessage = session('warning');
        $toastTitle = 'Peringatan';
        $toastType = 'warning';
    } elseif (session('info')) {
        $toastMessage = session('info');
        $toastTitle = 'Informasi';
        $toastType = 'info';
    } elseif ($errors->any()) {
        $toastMessage = $errors->first();
        $toastTitle = 'Validasi gagal';
        $toastType = 'error';
    }
@endphp

@if($toastMessage)
    @php $toastId = 'atmobrass-toast-' . uniqid(); @endphp
    <div id="{{ $toastId }}" class="fixed top-6 right-4 sm:right-6 z-[220] pointer-events-none" style="width: min(420px, calc(100vw - 32px));">
        <div class="toast-surface toast-enter pointer-events-auto" role="status" aria-live="polite">
            <div class="toast-icon" aria-hidden="true">
                @if($toastType === 'success')
                    <i class="fas fa-check"></i>
                @elseif($toastType === 'error')
                    <i class="fas fa-xmark"></i>
                @elseif($toastType === 'warning')
                    <i class="fas fa-exclamation"></i>
                @else
                    <i class="fas fa-circle-info"></i>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="toast-title">{{ $toastTitle }}</p>
                        <p class="toast-message">{{ $toastMessage }}</p>
                    </div>
                    <button type="button" class="toast-close" data-close-toast aria-label="Tutup notifikasi">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
                @if($cartLink)
                    <a href="{{ $cartLink }}" class="toast-link">
                        <i class="fas fa-shopping-cart"></i>
                        Lihat Keranjang
                    </a>
                @endif
            </div>
        </div>
    </div>

    <script>
        (function () {
            const toast = document.getElementById('{{ $toastId }}');
            if (!toast) return;

            const surface = toast.querySelector('.toast-surface');
            const closeButton = toast.querySelector('[data-close-toast]');
            let timeoutId = null;
            const duration = 3600;

            const hideToast = () => {
                if (!surface) return;
                surface.classList.remove('toast-enter');
                surface.classList.add('toast-leaving');
                setTimeout(() => toast.remove(), 280);
            };

            const startTimer = () => {
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }
                timeoutId = setTimeout(hideToast, duration);
            };

            const pauseTimer = () => {
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }
            };

            closeButton?.addEventListener('click', hideToast);
            surface?.addEventListener('mouseenter', pauseTimer);
            surface?.addEventListener('mouseleave', startTimer);

            startTimer();
        })();
    </script>
@endif
