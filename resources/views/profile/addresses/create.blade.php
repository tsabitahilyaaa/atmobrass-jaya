@extends('layouts.app')

@section('title', 'Tambah Alamat — CV Atmobrass Jaya')

@section('content')
<section class="py-10 sm:py-16 max-w-3xl mx-auto px-4 sm:px-6">
    <div class="mb-10">
        <p class="text-xs text-muted uppercase tracking-[0.24em] mb-2">Alamat Saya</p>
        <h1 class="font-display font-bold text-3xl">Tambah Alamat Baru</h1>
        <p class="text-sm text-muted mt-2">Simpan alamat baru untuk pengiriman pesanan Anda.</p>
    </div>

    <div class="bg-dark-100 border border-dark-300 rounded-3xl p-6">
        <form method="POST" action="{{ route('profile.addresses.store') }}">
            @csrf
            <div class="grid gap-6">
                <div>
                    <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Nama Penerima</label>
                    <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" required class="input-dark w-full px-4 py-3 rounded-2xl text-sm" />
                    @error('recipient_name') <p class="text-sm text-red-400 mt-2">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Nomor Telepon</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required class="input-dark w-full px-4 py-3 rounded-2xl text-sm" placeholder="08xxxxxxxxxx" />
                    @error('phone') <p class="text-sm text-red-400 mt-2">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Alamat Lengkap</label>
                    <textarea name="address" rows="3" required class="input-dark w-full px-4 py-3 rounded-2xl text-sm resize-none" placeholder="Jl. ...">{{ old('address') }}</textarea>
                    @error('address') <p class="text-sm text-red-400 mt-2">{{ $message }}</p> @enderror
                </div>
                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Kota</label>
                        <input type="text" name="city" value="{{ old('city') }}" required class="input-dark w-full px-4 py-3 rounded-2xl text-sm" />
                        @error('city') <p class="text-sm text-red-400 mt-2">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Provinsi</label>
                        <input type="text" name="province" value="{{ old('province') }}" required class="input-dark w-full px-4 py-3 rounded-2xl text-sm" />
                        @error('province') <p class="text-sm text-red-400 mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Kode Pos</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}" required class="input-dark w-full px-4 py-3 rounded-2xl text-sm" />
                    @error('postal_code') <p class="text-sm text-red-400 mt-2">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-3">
                    <input id="is_default" type="checkbox" name="is_default" value="1" class="checkbox" {{ old('is_default') ? 'checked' : '' }} />
                    <label for="is_default" class="text-sm text-muted">Jadikan alamat utama</label>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                <button type="submit" class="btn-gold px-6 py-3 rounded-full">Simpan Alamat</button>
                <a href="{{ route('profile.addresses') }}" class="btn-outline px-6 py-3 rounded-full">Batal</a>
            </div>
        </form>
    </div>
</section>
@endsection
