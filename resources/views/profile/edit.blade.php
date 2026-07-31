@extends('layouts.app')

@section('title', 'Edit Profil — CV Atmobrass Jaya')

@section('content')
<section class="py-10 sm:py-16 max-w-5xl mx-auto px-4 sm:px-6">
    <div class="mb-10">
        <p class="text-xs text-muted uppercase tracking-[0.24em] mb-2">Profil Saya</p>
        <h1 class="font-display font-bold text-3xl">Edit Profil</h1>
        <p class="text-sm text-muted mt-2">Perbarui nama, email, telepon, dan alamat utama Anda.</p>
    </div>

    <div class="bg-dark-100 border border-dark-300 rounded-3xl p-6">
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input-dark w-full px-4 py-3 rounded-2xl text-sm" />
                    @error('name') <p class="text-sm text-red-400 mt-2">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input-dark w-full px-4 py-3 rounded-2xl text-sm" />
                    @error('email') <p class="text-sm text-red-400 mt-2">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Nomor Telepon</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="input-dark w-full px-4 py-3 rounded-2xl text-sm" placeholder="08xxxxxxxxxx" />
                    @error('phone') <p class="text-sm text-red-400 mt-2">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-2">
                    <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Alamat Utama</label>
                    <textarea name="address" rows="3" class="input-dark w-full px-4 py-3 rounded-2xl text-sm resize-none" placeholder="Jl. ...">{{ old('address', optional($defaultAddress)->address) }}</textarea>
                    @error('address') <p class="text-sm text-red-400 mt-2">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Kota</label>
                    <input type="text" name="city" value="{{ old('city', optional($defaultAddress)->city) }}" class="input-dark w-full px-4 py-3 rounded-2xl text-sm" />
                    @error('city') <p class="text-sm text-red-400 mt-2">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Provinsi</label>
                    <input type="text" name="province" value="{{ old('province', optional($defaultAddress)->province) }}" class="input-dark w-full px-4 py-3 rounded-2xl text-sm" />
                    @error('province') <p class="text-sm text-red-400 mt-2">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs text-muted uppercase tracking-[0.24em] mb-2 block">Kode Pos</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', optional($defaultAddress)->postal_code) }}" class="input-dark w-full px-4 py-3 rounded-2xl text-sm" />
                    @error('postal_code') <p class="text-sm text-red-400 mt-2">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                <button type="submit" class="btn-gold px-6 py-3 rounded-full">Simpan Perubahan</button>
                <a href="{{ route('profile') }}" class="btn-outline px-6 py-3 rounded-full">Kembali</a>
            </div>
        </form>
    </div>
</section>
@endsection
