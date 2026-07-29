@extends('layouts.admin')

@section('title', 'Pembayaran QRIS — Admin')

@section('content')
<h1 class="font-display font-bold text-2xl mb-6">Pembayaran QRIS</h1>

<div class="grid gap-6 lg:grid-cols-2">
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-6">
        <h2 class="font-semibold mb-4">QRIS saat ini</h2>
        <div class="rounded-xl overflow-hidden border border-dark-300">
            <img src="{{ $qrisImage }}" alt="QRIS Pembayaran" class="w-full object-cover">
        </div>
        <p class="text-sm text-muted mt-3">Letakkan satu QRIS saja di folder <code>public/images/pembayaran</code>. File akan ditimpa ketika admin upload ulang.</p>
    </div>

    <div class="bg-dark-100 border border-dark-300 rounded-xl p-6">
        <h2 class="font-semibold mb-4">Upload QRIS baru</h2>
        <form method="POST" action="{{ route('admin.payment.save') }}" enctype="multipart/form-data">
            @csrf
            <label class="text-xs text-muted mb-2 block">File QRIS</label>
            <input type="file" name="qris_image" accept="image/png,image/jpeg" class="input-dark w-full text-sm py-2 rounded-lg" required>
            @error('qris_image')
                <p class="text-xs text-red-400 mt-2">{{ $message }}</p>
            @enderror
            <button type="submit" class="btn-gold mt-4">Simpan QRIS</button>
        </form>
    </div>
</div>
@endsection
