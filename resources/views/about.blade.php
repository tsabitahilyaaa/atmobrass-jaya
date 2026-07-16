@extends('layouts.app')

@section('title', 'Tentang Kami — CV Atmobrass Jaya')

@section('content')
<section class="py-16 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-12 anim-scroll">
        <p class="text-gold text-sm font-semibold tracking-widest uppercase mb-3">Tentang Kami</p>
        <h1 class="font-display font-bold text-3xl sm:text-4xl">CV Atmobrass Jaya</h1>
    </div>

    <div class="grid lg:grid-cols-2 gap-10 items-center mb-16 anim-scroll">
        <div>
            <div class="w-16 h-0.5 bg-gradient-to-r from-gold to-transparent mb-4"></div>
            <h2 class="font-display font-bold text-xl sm:text-2xl mb-4">Mengenal Lebih Dekat Atmobrass Jaya</h2>
            <p class="text-sm text-muted leading-relaxed mb-4">CV Atmobrass Jaya beroperasi dari Ngulakan, Sumberejo, Kec. Jaken, Kabupaten Pati, Jawa Tengah. Berawal dari bengkel kecil yang berfokus pada pengolahan logam kuningan, kini kami telah berkembang menjadi perusahaan penyedia produk logam premium terpercaya yang melayani seluruh Indonesia.</p>
            <p class="text-sm text-muted leading-relaxed mb-4">Dengan pengalaman lebih dari 5 tahun, kami menggabungkan keahlian tradisional pengolahan logam dengan teknologi modern untuk menghasilkan produk-produk berkualitas tinggi yang memenuhi standar industri nasional maupun internasional.</p>
            <p class="text-sm text-muted leading-relaxed">Produk kami meliputi komponen kuningan (brass), aluminium, aksesoris furniture premium, serta lampu dekoratif. Kami telah dipercaya oleh ratusan pelanggan dari berbagai sektor — mulai dari kontraktor dan desainer interior hingga pelaku usaha hospitality — dan juga pernah melayani beberapa perusahaan mitra dari luar negeri.</p>
        </div>
        <div class="rounded-xl overflow-hidden border border-dark-300 aspect-[4/3] bg-dark-200">
            <img src="https://picsum.photos/seed/atmobrassfactory/800/600" alt="Pabrik Atmobrass" class="w-full h-full object-cover">
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-8 mb-16 anim-scroll">
        <div class="bg-dark-100 border border-dark-300 rounded-xl p-8">
            <div class="w-12 h-12 rounded-full bg-gold/10 flex items-center justify-center mb-4"><i class="fas fa-eye text-gold text-lg"></i></div>
            <h3 class="font-display font-bold text-lg mb-3">Visi</h3>
            <p class="text-sm text-muted leading-relaxed">Menjadi perusahaan penyedia produk logam premium terdepan di Indonesia yang dikenal akan kualitas, inovasi, dan kepercayaan dalam setiap produk yang dihasilkan.</p>
        </div>
        <div class="bg-dark-100 border border-dark-300 rounded-xl p-8">
            <div class="w-12 h-12 rounded-full bg-gold/10 flex items-center justify-center mb-4"><i class="fas fa-bullseye text-gold text-lg"></i></div>
            <h3 class="font-display font-bold text-lg mb-3">Misi</h3>
            <ul class="text-sm text-muted leading-relaxed space-y-2">
                <li class="flex gap-2"><i class="fas fa-check text-gold text-xs mt-1"></i>Menghasilkan produk logam dengan standar kualitas tertinggi</li>
                <li class="flex gap-2"><i class="fas fa-check text-gold text-xs mt-1"></i>Mengembangkan inovasi desain yang mengikuti tren global</li>
                <li class="flex gap-2"><i class="fas fa-check text-gold text-xs mt-1"></i>Memberikan pelayanan terbaik dan konsultasi profesional</li>
                <li class="flex gap-2"><i class="fas fa-check text-gold text-xs mt-1"></i>Membangun hubungan jangka panjang dengan pelanggan</li>
            </ul>
        </div>
    </div>

    <div class="anim-scroll">
        <div class="text-center mb-10">
            <p class="text-gold text-sm font-semibold tracking-widest uppercase mb-3">Keunggulan Kami</p>
            <h2 class="font-display font-bold text-2xl sm:text-3xl">Apa yang Membedakan Kami</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $strengths = [
                    ['fa-gem', 'Material Premium', 'Hanya menggunakan bahan baku logam berkualitas tinggi dari pemasok terpercaya.'],
                    ['fa-cogs', 'Proses Presisi', 'Mesin CNC dan teknik casting modern memastikan presisi setiap produk.'],
                    ['fa-palette', 'Finishing Berkualitas', 'Beragam pilihan finishing — polished, brushed, antique, powder coating.'],
                    ['fa-certificate', 'Bersertifikat', 'Produk kami memenuhi standar SNI dan telah diuji di laboratorium terakreditasi.'],
                    ['fa-clock', 'Tepat Waktu', 'Komitmen pengiriman tepat waktu dengan sistem produksi yang terstruktur.'],
                    ['fa-handshake', 'Garansi Produk', 'Setiap produk dilengkapi garansi kualitas untuk kepuasan pelanggan.'],
                ];
            @endphp
            @foreach($strengths as $s)
            <div class="bg-dark-100 border border-dark-300 rounded-xl p-6 hover:border-gold-dark transition-colors group">
                <div class="w-12 h-12 rounded-lg bg-dark-200 flex items-center justify-center mb-4 group-hover:bg-gold/10 transition-colors"><i class="fas {{ $s[0] }} text-gold"></i></div>
                <h4 class="font-semibold text-sm mb-2">{{ $s[1] }}</h4>
                <p class="text-xs text-muted leading-relaxed">{{ $s[2] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection