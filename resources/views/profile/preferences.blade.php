@extends('layouts.app')

@section('title', 'Preferensi Saya — CV Atmobrass Jaya')

@section('content')
<section class="py-10 sm:py-16 max-w-4xl mx-auto px-4 sm:px-6">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs text-muted uppercase tracking-[0.28em] mb-2">Profil Saya</p>
            <h1 class="font-display font-bold text-2xl sm:text-3xl">Preferensi Saya</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('preferences.index') }}" class="btn-gold px-6 py-3 rounded-lg text-sm inline-block">Ubah Preferensi</a>
            <form method="POST" action="{{ route('preferences.reset') }}" onsubmit="return confirm('Reset seluruh preferensi Anda?')" class="inline-block">
                @csrf
                <button type="submit" class="btn-outline px-6 py-3 rounded-lg text-sm inline-block">Reset Preferensi</button>
            </form>
        </div>
    </div>

    <div class="bg-dark-100 border border-dark-300 rounded-2xl p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-xs text-muted uppercase tracking-[0.28em]">Status</p>
                <h2 class="font-display font-semibold text-xl">Preferensi aktif</h2>
            </div>
        </div>

        @if($preferences->isEmpty())
            <div class="rounded-xl border border-dark-300 bg-dark-200 p-5 text-sm text-muted">
                Anda belum memilih preferensi. Pilih preferensi untuk mendapatkan rekomendasi produk yang lebih sesuai.
            </div>
        @else
            <div class="flex flex-wrap gap-3">
                @foreach($preferences as $preference)
                    <span class="inline-flex items-center rounded-full border border-gold/30 bg-gold/10 px-4 py-2 text-sm text-gold">{{ $preference }}</span>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
