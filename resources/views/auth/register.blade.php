@extends('layouts.app')

@section('title', 'Daftar — CV Atmobrass Jaya')

@section('content')
<section class="py-20 max-w-md mx-auto px-4 fade-in">
    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-full gold-bg flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-plus text-dark text-2xl"></i>
        </div>
        <h1 class="font-display font-bold text-2xl">Buat Akun Baru</h1>
        <p class="text-sm text-muted mt-2">Daftar untuk mulai berbelanja</p>
    </div>

    <form method="POST" action="{{ route('register.post') }}" class="bg-dark-100 border border-dark-300 rounded-xl p-6 sm:p-8 space-y-4">
        @csrf

        <div>
            <label class="text-xs text-muted mb-1 block">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="input-dark w-full px-4 py-3 rounded-lg text-sm" placeholder="Nama Anda">
        </div>
        <div>
            <label class="text-xs text-muted mb-1 block">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="input-dark w-full px-4 py-3 rounded-lg text-sm" placeholder="email@contoh.com">
        </div>
        <div>
            <label class="text-xs text-muted mb-1 block">No. Telepon</label>
            <input type="tel" name="phone" value="{{ old('phone') }}" class="input-dark w-full px-4 py-3 rounded-lg text-sm" placeholder="08xxxxxxxxxx">
        </div>
        <div>
            <label class="text-xs text-muted mb-1 block">Password</label>
            <input type="password" name="password" required minlength="6" class="input-dark w-full px-4 py-3 rounded-lg text-sm" placeholder="Minimal 6 karakter">
        </div>
        <div>
            <label class="text-xs text-muted mb-1 block">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" required class="input-dark w-full px-4 py-3 rounded-lg text-sm" placeholder="Ulangi password">
        </div>
        <button type="submit" class="btn-gold w-full py-3 rounded-lg text-sm">Daftar</button>
        <p class="text-center text-sm text-muted">Sudah punya akun? <a href="{{ route('login') }}" class="text-gold hover:underline">Masuk</a></p>
    </form>
</section>
@endsection