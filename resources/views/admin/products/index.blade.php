@extends('layouts.admin')

@section('title', 'Manajemen Produk — Admin')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <div>
        <h1 class="font-display font-bold text-2xl">Manajemen Produk</h1>
        <p class="text-sm text-muted mt-1">Kelola produk Anda: tambah, edit, atau hapus produk yang tampil di website.</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn-gold px-5 py-2 rounded-lg text-sm inline-flex items-center gap-2">
        <i class="fas fa-plus"></i>
        Tambah Produk
    </a>
</div>
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="flex flex-wrap items-center gap-3">
        <span class="text-sm text-muted">Urutkan stok:</span>
        <a href="{{ route('admin.products.index', ['sort' => 'stock_asc']) }}" class="inline-flex items-center gap-2 rounded-lg border border-dark-300 bg-dark-200 px-4 py-2 text-sm font-semibold text-white transition hover:border-gold hover:text-gold {{ $sort === 'stock_asc' ? 'ring-2 ring-gold' : '' }}">
            <i class="fas fa-sort-amount-down"></i>
            Terendah ke Tertinggi
        </a>
        @if($sort === 'stock_asc')
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-dark-300 bg-dark-200 px-4 py-2 text-sm font-semibold text-white transition hover:border-gold hover:text-gold">
                <i class="fas fa-rotate-left"></i>
                Reset
            </a>
        @endif
    </div>
</div>

<div class="bg-dark-100 border border-dark-300 rounded-2xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[#161616]">
                <tr class="text-left text-xs text-muted uppercase tracking-wider">
                    <th class="p-4">Produk</th>
                    <th class="p-4 hidden sm:table-cell">Kategori</th>
                    <th class="p-4">Harga</th>
                    <th class="p-4">Stok</th>
                    <th class="p-4 hidden md:table-cell">Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="border-b border-dark-300 hover:bg-dark-200 transition-colors">
                        <td class="p-4 align-top">
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-xl overflow-hidden bg-dark-200 border border-dark-300">
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-sm truncate max-w-[220px]">{{ $product->name }}</p>
                                    <p class="text-xs text-muted truncate max-w-[220px]">{{ Str::limit($product->description, 60) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 hidden sm:table-cell align-top">
                            <span class="text-xs uppercase tracking-wide text-gold">{{ $product->category->name }}</span>
                        </td>
                        <td class="p-4 align-top font-semibold">{{ $product->formatted_price }}</td>
                        <td class="p-4 align-top">
                            <span class="text-sm font-medium {{ $product->stock <= 5 ? 'text-red-400' : 'text-green-400' }}">{{ $product->stock }}</span>
                        </td>
                        <td class="p-4 hidden md:table-cell align-top">
                            @if($product->is_active)
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-500/10 text-green-300 text-xs font-semibold">Aktif</span>
                            @else
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-500/10 text-red-300 text-xs font-semibold">Tidak aktif</span>
                            @endif
                        </td>
                        <td class="p-4 text-right align-top">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="text-muted hover:text-gold transition-colors mr-3" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" class="inline" onsubmit="return confirm('Hapus produk ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-muted hover:text-red-400 transition-colors" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-muted">Belum ada produk. Tambahkan produk baru untuk mulai menampilkan katalog.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 flex justify-center">{{ $products->links() }}</div>
@endsection
