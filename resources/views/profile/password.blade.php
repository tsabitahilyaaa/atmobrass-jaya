@extends('layouts.app')

@section('title', 'Ubah Password — CV Atmobrass Jaya')

@section('content')
<section class="py-10 sm:py-16 max-w-3xl mx-auto px-4 sm:px-6">
    <div class="mb-10">
        <p class="text-xs text-muted uppercase tracking-[0.24em] mb-2">Profil Saya</p>
        <h1 class="font-display font-bold text-3xl">Ubah Password</h1>
        <p class="text-sm text-muted mt-2">Perbarui password akun Anda dengan aman.</p>
    </div>

    <div class="bg-dark-100 border border-dark-300 rounded-3xl p-6">
        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Password Saat Ini</label>
                    <input type="password" name="current_password" required class="input-dark w-full px-4 py-3 rounded-2xl text-sm" />
                    @error('current_password') <p class="text-sm text-red-400 mt-2">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Password Baru</label>
                    <input type="password" name="password" required class="input-dark w-full px-4 py-3 rounded-2xl text-sm" />
                    @error('password') <p class="text-sm text-red-400 mt-2">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required class="input-dark w-full px-4 py-3 rounded-2xl text-sm" />
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                <button type="submit" class="btn-gold px-6 py-3 rounded-full">Perbarui Password</button>
                <a href="{{ route('profile') }}" class="btn-outline px-6 py-3 rounded-full">Kembali</a>
            </div>
        </form>
    </div>
</section>
@endsection
