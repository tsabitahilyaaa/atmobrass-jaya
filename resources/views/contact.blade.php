@extends('layouts.app')

@section('title', 'Kontak — CV Atmobrass Jaya')

@section('content')
<section class="py-10 sm:py-16 max-w-7xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-12 anim-scroll">
        <p class="text-gold text-sm font-semibold tracking-widest uppercase mb-3">Hubungi Kami</p>
        <h1 class="font-display font-bold text-3xl sm:text-4xl">Kontak</h1>
    </div>

    <div class="grid lg:grid-cols-2 gap-10 mb-12">
        <div class="anim-scroll">
            <div class="space-y-6 mb-8">
                <div class="flex gap-4 items-start">
                    <div class="w-12 h-12 rounded-lg bg-gold/10 flex items-center justify-center flex-shrink-0"><i class="fas fa-map-marker-alt text-gold"></i></div>
                    <div><h4 class="font-semibold text-sm mb-1">Alamat</h4><p class="text-sm text-muted">Jl. Industri Raya No. 45, Kelurahan Rungkut, Kec. Rungkut, Surabaya 60293</p></div>
                </div>
                <div class="flex gap-4 items-start">
                    <div class="w-12 h-12 rounded-lg bg-gold/10 flex items-center justify-center flex-shrink-0"><i class="fas fa-phone text-gold"></i></div>
                    <div><h4 class="font-semibold text-sm mb-1">Telepon</h4><p class="text-sm text-muted">+62 31-9876-5432 / +62 812-3456-7890</p></div>
                </div>
                <div class="flex gap-4 items-start">
                    <div class="w-12 h-12 rounded-lg bg-gold/10 flex items-center justify-center flex-shrink-0"><i class="fas fa-envelope text-gold"></i></div>
                    <div><h4 class="font-semibold text-sm mb-1">Email</h4><p class="text-sm text-muted">info@atmobrassjaya.com / sales@atmobrassjaya.com</p></div>
                </div>
                <div class="flex gap-4 items-start">
                    <div class="w-12 h-12 rounded-lg bg-gold/10 flex items-center justify-center flex-shrink-0"><i class="fas fa-clock text-gold"></i></div>
                    <div><h4 class="font-semibold text-sm mb-1">Jam Operasional</h4><p class="text-sm text-muted">Senin - Sabtu: 08.00 - 17.00 WIB</p></div>
                </div>
            </div>
            <div class="rounded-xl overflow-hidden border border-dark-300 aspect-[4/3]">
                <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=112.72%2C-7.35%2C112.78%2C-7.31&layer=mapnik" class="w-full h-full" loading="lazy" style="filter:invert(90%) hue-rotate(180deg) brightness(0.9) contrast(0.9)"></iframe>
            </div>
        </div>

        <div class="anim-scroll">
            <div class="bg-dark-100 border border-dark-300 rounded-xl p-6 sm:p-8">
                <h3 class="font-display font-semibold text-lg mb-6">Kirim Pesan</h3>
                <form method="POST" action="{{ route('contact.send') }}" class="space-y-4">
                    @csrf
                    <div><label class="text-xs text-muted mb-1 block">Nama</label><input type="text" name="name" value="{{ old('name') }}" required class="input-dark w-full px-4 py-3 rounded-lg text-sm" placeholder="Nama Anda"></div>
                    <div><label class="text-xs text-muted mb-1 block">Email</label><input type="email" name="email" value="{{ old('email') }}" required class="input-dark w-full px-4 py-3 rounded-lg text-sm" placeholder="email@contoh.com"></div>
                    <div><label class="text-xs text-muted mb-1 block">Subjek</label><input type="text" name="subject" value="{{ old('subject') }}" required class="input-dark w-full px-4 py-3 rounded-lg text-sm" placeholder="Subjek pesan"></div>
                    <div><label class="text-xs text-muted mb-1 block">Pesan</label><textarea name="message" rows="5" required class="input-dark w-full px-4 py-3 rounded-lg text-sm resize-none" placeholder="Tulis pesan Anda...">{{ old('message') }}</textarea></div>
                    <button type="submit" class="btn-gold w-full py-3 rounded-lg text-sm"><i class="fas fa-paper-plane mr-2"></i>Kirim Pesan</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection