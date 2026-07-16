<footer class="border-t border-dark-300 bg-dark-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-10">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-full gold-bg flex items-center justify-center">
                        <i class="fas fa-gem text-dark text-sm"></i>
                    </div>
                    <span class="font-display font-bold text-lg gold-gradient">Atmobrass</span>
                </div>
                <p class="text-sm text-muted leading-relaxed">CV Atmobrass Jaya — Pemasok produk logam premium berkualitas tinggi untuk kebutuhan industri, furniture, dan dekorasi.</p>
            </div>

            <div>
                <h4 class="font-display font-semibold text-sm mb-4 text-gold">Navigasi</h4>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('home') }}" class="text-sm text-muted hover:text-gold transition-colors">Beranda</a>
                    <a href="{{ route('about') }}" class="text-sm text-muted hover:text-gold transition-colors">Tentang Kami</a>
                    <a href="{{ route('products.index') }}" class="text-sm text-muted hover:text-gold transition-colors">Produk</a>
                    <a href="{{ route('contact') }}" class="text-sm text-muted hover:text-gold transition-colors">Kontak</a>
                </div>
            </div>

            <div>
                <h4 class="font-display font-semibold text-sm mb-4 text-gold">Kategori</h4>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('products.index', ['category' => 'aksesori-plat']) }}" class="text-sm text-muted hover:text-gold transition-colors">Aksesori & Plat</a>
                    <a href="{{ route('products.index', ['category' => 'engsel']) }}" class="text-sm text-muted hover:text-gold transition-colors">Engsel</a>
                    <a href="{{ route('products.index', ['category' => 'pemegang-tombol']) }}" class="text-sm text-muted hover:text-gold transition-colors">Pemegang & Tombol</a>
                    <a href="{{ route('products.index', ['category' => 'roda-kaki-perabot']) }}" class="text-sm text-muted hover:text-gold transition-colors">Roda & Kaki Perabot</a>
                </div>
            </div>

            <div>
                <h4 class="font-display font-semibold text-sm mb-4 text-gold">Kontak</h4>
                <div class="flex flex-col gap-3 text-sm text-muted">
                    <p><i class="fas fa-map-marker-alt text-gold mr-2"></i>Ngulakan, Sumberejo, Kec. Jaken, Kabupaten Pati, Jawa Tengah</p>
                    <p><i class="fas fa-phone text-gold mr-2"></i>+62 852-2926-9792</p>
                    <p><i class="fas fa-envelope text-gold mr-2"></i>atmobrassjaya.cv@gmail.com</p>
                </div>
                <div class="flex gap-3 mt-4">
                    <a href="https://wa.me/6285229269792" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full border border-dark-300 flex items-center justify-center text-muted hover:border-gold hover:text-gold transition-colors"><i class="fab fa-whatsapp text-sm"></i></a>
                </div>
            </div>
        </div>
        <div class="mt-10 pt-6 border-t border-dark-300 text-center text-xs text-muted">
            &copy; {{ date('Y') }} CV Atmobrass Jaya. Seluruh hak cipta dilindungi.
        </div>
    </div>
</footer>