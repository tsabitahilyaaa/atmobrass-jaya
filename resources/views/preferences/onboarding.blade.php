@extends('layouts.app')

@section('title', 'Preferensi Produk — CV Atmobrass Jaya')

@section('content')
<section class="py-12 sm:py-16 max-w-4xl mx-auto px-4 sm:px-6">
    <div class="rounded-2xl border border-dark-300 bg-dark-100 p-6 sm:p-8 shadow-[0_30px_60px_rgba(0,0,0,0.35)]">
        <p class="text-xs uppercase tracking-[0.35em] text-gold mb-3">Langkah 1 dari 1</p>
        <h1 class="font-display font-bold text-2xl sm:text-3xl mb-3">Selamat Datang di Atmobrass</h1>
        <p class="text-sm text-muted leading-7 mb-6">Bantu kami memberikan rekomendasi produk yang sesuai dengan kebutuhan Anda.</p>

        <div class="rounded-2xl border border-dark-300 bg-dark-200 p-5 sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="font-display font-semibold text-lg">Pilih kebutuhan Anda</h2>
                    <p id="preference-counter" class="mt-2 text-sm text-muted">0 dari maksimal 3 kebutuhan dipilih</p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full border border-dark-300 bg-dark-100 px-4 py-2 text-sm text-muted">
                    <span class="font-medium text-gold">Tips</span>
                    <span>Pilih maksimal 3 kebutuhan untuk rekomendasi yang relevan.</span>
                </div>
            </div>

            @if($errors->any())
                <div class="mt-6 rounded-xl border border-red-700 bg-red-950/40 p-4 text-sm text-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('preferences.store') }}" method="POST" class="mt-6">
                @csrf
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach([
                        'Pintu Rumah',
                        'Lemari & Kabinet',
                        'Furniture',
                        'Kantor & Bangunan Komersial',
                        'Dekorasi Interior',
                        'Renovasi & Proyek Bangunan',
                    ] as $option)
                        <label class="cursor-pointer rounded-full border border-dark-300 bg-dark-100 px-4 py-3 text-sm font-medium text-white transition-all duration-300 ease-out {{ in_array($option, old('preferences', $selectedPreferences ?? [])) ? 'border-gold bg-gold/10 text-gold' : '' }}">
                            <input type="checkbox" name="preferences[]" value="{{ $option }}" class="sr-only preference-checkbox" {{ in_array($option, old('preferences', $selectedPreferences ?? [])) ? 'checked' : '' }}>
                            <span class="inline-flex w-full items-center justify-center">{{ $option }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <button type="button" onclick="document.getElementById('skip-preference-form').submit()" class="btn-outline px-6 py-3 rounded-lg text-sm inline-block">Lewati</button>
                    <button id="continue-preference-button" type="submit" disabled class="btn-outline px-6 py-3 rounded-lg text-sm inline-block opacity-60 cursor-not-allowed">Lanjutkan</button>
                </div>
            </form>

            <form id="skip-preference-form" method="POST" action="{{ route('preferences.skip') }}" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var checkboxes = document.querySelectorAll('.preference-checkbox');
        var counter = document.getElementById('preference-counter');
        var continueButton = document.getElementById('continue-preference-button');

        function updateState() {
            var selected = Array.from(checkboxes).filter(function (checkbox) {
                return checkbox.checked;
            });

            counter.textContent = selected.length + ' dari maksimal 3 kebutuhan dipilih';
            continueButton.disabled = selected.length === 0 || selected.length > 3;
            continueButton.classList.toggle('opacity-60', continueButton.disabled);
            continueButton.classList.toggle('cursor-not-allowed', continueButton.disabled);
        }

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                var checked = Array.from(checkboxes).filter(function (item) {
                    return item.checked;
                });

                if (checked.length > 3) {
                    checkbox.checked = false;
                }

                updateState();
            });
        });

        updateState();
    });
</script>
@endsection
