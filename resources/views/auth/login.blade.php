@extends('layouts.app')

@section('title', 'Login — CV Atmobrass Jaya')

@section('content')
<section class="py-20 max-w-md mx-auto px-4 fade-in">
    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-full gold-bg flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user text-dark text-2xl"></i>
        </div>
        <h1 class="font-display font-bold text-2xl">Masuk ke Akun</h1>
        <p class="text-sm text-muted mt-2">Masuk untuk melacak pesanan dan berbelanja</p>
    </div>

    <form method="POST" action="{{ route('login.post') }}" class="bg-dark-100 border border-dark-300 rounded-xl p-6 sm:p-8 space-y-4">
        @csrf

        <div>
            <label class="text-xs text-muted mb-1 block">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="input-dark w-full px-4 py-3 rounded-lg text-sm" placeholder="email@contoh.com">
        </div>
        <div>
            <label class="text-xs text-muted mb-1 block">Password</label>
            <input type="password" name="password" required class="input-dark w-full px-4 py-3 rounded-lg text-sm" placeholder="Masukkan password">
        </div>
        <button type="submit" class="btn-gold w-full py-3 rounded-lg text-sm">Masuk</button>
        <p class="text-center text-sm text-muted">Belum punya akun? <a href="{{ route('register') }}" class="text-gold hover:underline">Daftar Sekarang</a></p>
    </form>
</section>
@endsection