@extends('layouts.admin')

@section('title', 'Manajemen Produk — Admin')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <h1 class="font-display font-bold text-2xl">Manajemen Produk</h1>
    <a href="{{ route('admin.products.create') }}" class="btn-gold px-5 py-2 rounded-lg text-sm inline-block"><i class="fas fa-plus mr-2"></i>Tambah Produk</a>
</div>

<div class="bg-dark-100 border border-dark-300 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-dark-300 text-left text-xs text-muted uppercase tracking-wider">
                    <th class="p-4">Produk</th>
                    <th class="p-4 hidden sm:table-cell">Kategori</th>
                    <th class="p-4">Harga</th>
                    <th class="p-4">Stok</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr class="border-b border-dark-300 hover:bg-dark-200 transition-colors">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $product->image }}" class="w-10 h-10 rounded-lg object-cover">
                            <span class="font-medium truncate max-w-[200px]">{{ $product->name }}</span>
                        </div>
                    </td>
                    <td class="p-4 hidden sm:table-cell">
                        <span class="text-xs text-gold uppercase">{{ $product->category->name }}</span>
                    </td>
                    <td class="p-4 font-semibold">{{ $product->formatted_price }}</td>
                    <td class="p-4">
                        <span class="{{ $product->stock < 10 ? 'text-red-400' : 'text-green-400' }}">{{ $product->stock }}</span>
                    </td>
                    <td class="p-4 text-right">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="text-muted hover:text-gold mr-3 transition-colors"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" class="inline" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-muted hover:text-red-400 transition-colors"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 flex justify-center">{{ $products->links() }}</div>
@endsection