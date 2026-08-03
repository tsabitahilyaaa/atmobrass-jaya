@extends('layouts.app')

@section('title', 'Alamat Saya — CV Atmobrass Jaya')

@section('content')
<section class="py-10 sm:py-16 max-w-5xl mx-auto px-4 sm:px-6">
    <div class="mb-10">
        <p class="text-xs text-muted uppercase tracking-[0.24em] mb-2">Profil Saya</p>
        <h1 class="font-display font-bold text-3xl">Alamat Saya</h1>
        <p class="text-sm text-muted mt-2">Kelola alamat pengiriman yang digunakan untuk pesanan Anda.</p>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <a href="{{ route('profile') }}" class="text-sm text-muted hover:underline">&larr; Kembali ke Profil</a>
        <a href="{{ route('profile.addresses.create') }}" class="btn-gold px-5 py-3 rounded-full text-sm">Tambah Alamat</a>
    </div>

    <div class="space-y-4">
        @forelse($addresses as $address)
            <div class="bg-dark-100 border border-dark-300 rounded-3xl p-6">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div>
                        <p class="text-base font-semibold">{{ $address->recipient_name }} @if($address->is_default)<span class="text-gold text-xs uppercase tracking-[0.24em] ml-2">Utama</span>@endif</p>
                        <p class="text-sm text-muted mt-1">{{ $address->phone }}</p>
                        <p class="text-sm text-muted mt-2">{{ $address->address }}, {{ $address->city }}, {{ $address->province }}, {{ $address->postal_code }}</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @unless($address->is_default)
                            <form method="POST" action="{{ route('profile.addresses.default', $address) }}">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn-outline px-4 py-3 rounded-full text-sm">Jadikan Utama</button>
                            </form>
                        @endunless
                        <a href="{{ route('profile.addresses.edit', $address) }}" class="btn-gold px-4 py-3 rounded-full text-sm">Edit</a>
                        <form method="POST" action="{{ route('profile.addresses.destroy', $address) }}" onsubmit="return confirm('Hapus alamat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-outline px-4 py-3 rounded-full text-sm">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-dark-100 border border-dark-300 rounded-3xl p-8 text-center">
                <p class="text-sm text-muted">Belum ada alamat tersimpan. Tambahkan alamat untuk mempermudah pengiriman pesanan.</p>
            </div>
        @endforelse
    </div>
</section>
@endsection
