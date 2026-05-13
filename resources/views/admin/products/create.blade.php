@extends('layouts.admin')

@section('title', 'Tambah Produk — Admin')

@section('content')
<div class="max-w-3xl">
    <h1 class="font-display font-bold text-2xl mb-6">Tambah Produk</h1>

    <form method="POST" action="{{ route('admin.products.store') }}" class="bg-dark-100 border border-dark-300 rounded-xl p-6 space-y-4">
        @csrf

        @if($errors->any())
            <div class="bg-red-900/30 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg text-sm space-y-1">
                @foreach($errors->all() as $err)<p>{{ $err }}</p>@endforeach
            </div>
        @endif

        <div>
            <label class="text-xs text-muted mb-1 block">Nama Produk</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="input-dark w-full px-4 py-3 rounded-lg text-sm">
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs text-muted mb-1 block">Kategori</label>
                <select name="category_id" required class="input-dark w-full px-4 py-3 rounded-lg text-sm">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-muted mb-1 block">Harga (Rp)</label>
                <input type="number" name="price" value="{{ old('price') }}" required min="0" class="input-dark w-full px-4 py-3 rounded-lg text-sm">
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs text-muted mb-1 block">Stok</label>
                <input type="number" name="stock" value="{{ old('stock') }}" required min="0" class="input-dark w-full px-4 py-3 rounded-lg text-sm">
            </div>
            <div>
                <label class="text-xs text-muted mb-1 block">URL Gambar</label>
                <input type="url" name="image" value="{{ old('image', 'https://picsum.photos/seed/'.uniqid().'/600/600') }}" required class="input-dark w-full px-4 py-3 rounded-lg text-sm">
            </div>
        </div>

        <div>
            <label class="text-xs text-muted mb-1 block">Deskripsi</label>
            <textarea name="description" rows="4" required class="input-dark w-full px-4 py-3 rounded-lg text-sm resize-none">{{ old('description') }}</textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-gold px-6 py-2.5 rounded-lg text-sm">Simpan</button>
            <a href="{{ route('admin.products.index') }}" class="btn-outline px-6 py-2.5 rounded-lg text-sm">Batal</a>
        </div>
    </form>
</div>
@endsection