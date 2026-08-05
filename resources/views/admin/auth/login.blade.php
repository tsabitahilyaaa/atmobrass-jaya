@extends('layouts.app')

@section('title', 'Portal Administrator — CV Atmobrass Jaya')

@section('content')
<section class="min-h-[80vh] flex items-center justify-center px-4 py-20 fade-in">

    <div class="w-full max-w-md">

        <div class="text-center mb-8">

            <div class="w-20 h-20 rounded-full gold-bg flex items-center justify-center mx-auto mb-5 shadow-lg">
                <i class="fas fa-user-shield text-dark text-3xl"></i>
            </div>

            <h1 class="font-display font-bold text-3xl">
                Portal Administrator
            </h1>

            <p class="text-muted mt-3 text-sm">
                CV Atmobrass Jaya
            </p>

            <p class="text-xs text-muted mt-2">
                Halaman ini hanya dapat diakses oleh administrator.
            </p>

        </div>

        <form method="POST"
              action="{{ route('admin.login.post') }}"
              class="bg-dark-100 border border-dark-300 rounded-2xl p-8 shadow-xl space-y-5">

            @csrf

            <div>

                <label class="block text-xs text-muted mb-2">
                    Email Administrator
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="input-dark w-full px-4 py-3 rounded-lg"
                    placeholder="admin@atmobrass.com">

            </div>

            <div>

                <label class="block text-xs text-muted mb-2">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    required
                    class="input-dark w-full px-4 py-3 rounded-lg"
                    placeholder="Masukkan password">

            </div>

            <div class="flex items-center">

                <input
                    id="remember"
                    type="checkbox"
                    name="remember"
                    class="rounded border-dark-300 bg-dark-200 text-gold focus:ring-gold">

                <label for="remember" class="ml-2 text-sm text-muted">
                    Ingat saya
                </label>

            </div>

            <button
                type="submit"
                class="btn-gold w-full py-3 rounded-lg font-semibold transition duration-300">

                <i class="fas fa-lock mr-2"></i>

                Masuk ke Dashboard

            </button>

        </form>

        <div class="mt-6 text-center">

            <a href="{{ route('home') }}"
               class="text-sm text-muted hover:text-gold transition">

                ← Kembali ke Website

            </a>

        </div>

    </div>

</section>
@endsection