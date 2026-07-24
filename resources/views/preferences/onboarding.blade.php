@extends('layouts.app')

@section('title', 'Preferensi Produk — CV Atmobrass Jaya')

@section('content')
<section class="min-h-screen bg-[#0F0F0F] py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <div class="rounded-2xl border border-[#222222] bg-[#131313] p-6 shadow-[0_30px_60px_rgba(0,0,0,0.5)] sm:p-10">
            <div class="space-y-6">
                <div class="space-y-3">
                    <p class="text-xs uppercase tracking-[0.35em] text-[#D4AF37]/90">Langkah 1 dari 2</p>
                    <h1 class="font-display text-3xl sm:text-4xl font-bold text-white">Pilih Preferensi Produk Anda</h1>
                    <p class="text-sm leading-7 text-gray-300">Pilih minimal 3 kategori agar kami dapat memberikan rekomendasi produk yang lebih sesuai dengan kebutuhan Anda.</p>
                </div>

                <div class="rounded-[28px] border border-[#242424] bg-[#0F0F0F] p-6 sm:p-8">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-white">Pilih kategori yang menarik bagi Anda</h2>
                            <p id="choice-counter" class="mt-2 text-sm text-gray-400">0 dari minimal 3 kategori dipilih</p>
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-[#2A2A2A] bg-[#111111] px-4 py-2 text-sm text-gray-300">
                            <span class="font-medium text-[#D4AF37]">Tips</span>
                            <span>Pilih lebih banyak kategori untuk rekomendasi yang lebih personal.</span>
                        </div>
                    </div>

                    @if($errors->has('categories'))
                        <div class="mt-6 rounded-2xl border border-red-700 bg-red-900/20 p-4 text-sm text-red-200">
                            {{ $errors->first('categories') }}
                        </div>
                    @endif

                    <form action="{{ route('preferences.store') }}" method="POST" class="mt-8">
                        @csrf
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($categories as $category)
                                <label class="preference-chip cursor-pointer {{ in_array($category->id, old('categories', $selectedCategories)) ? 'selected' : '' }}">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="sr-only preference-checkbox" {{ in_array($category->id, old('categories', $selectedCategories)) ? 'checked' : '' }}>
                                    <span class="inline-flex w-full items-center justify-center rounded-full border border-[#2A2A2A] bg-[#151515] px-4 py-3 text-sm font-medium text-white transition-all duration-300 ease-out">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-8 space-y-3">
                            <button id="continue-button" type="submit" disabled class="w-full rounded-full bg-[#3A3A3A] px-6 py-4 text-base font-semibold text-[#A7A7A7] transition duration-300 ease-out cursor-not-allowed">Lanjut</button>
                            <p class="text-sm text-gray-400">Preferensi Anda akan membantu kami menampilkan rekomendasi produk yang lebih relevan.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var checkboxes = document.querySelectorAll('.preference-checkbox');
        var counter = document.getElementById('choice-counter');
        var continueButton = document.getElementById('continue-button');

        function updateSelectionState() {
            var selected = Array.from(checkboxes).filter(function (checkbox) {
                return checkbox.checked;
            });

            var count = selected.length;
            counter.textContent = count >= 3 ? count + ' kategori dipilih' : count + ' dari minimal 3 kategori dipilih';

            if (count >= 3) {
                continueButton.disabled = false;
                continueButton.classList.remove('bg-[#3A3A3A]', 'text-[#A7A7A7]', 'cursor-not-allowed');
                continueButton.classList.add('bg-[#D4AF37]', 'text-[#0F0F0F]', 'shadow-[0_18px_40px_rgba(212,175,55,0.22)]');
            } else {
                continueButton.disabled = true;
                continueButton.classList.add('bg-[#3A3A3A]', 'text-[#A7A7A7]', 'cursor-not-allowed');
                continueButton.classList.remove('bg-[#D4AF37]', 'text-[#0F0F0F]', 'shadow-[0_18px_40px_rgba(212,175,55,0.22)]');
            }

            checkboxes.forEach(function (checkbox) {
                var chip = checkbox.closest('.preference-chip');
                if (!chip) {
                    return;
                }

                if (checkbox.checked) {
                    chip.classList.add('selected');
                } else {
                    chip.classList.remove('selected');
                }
            });
        }

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', updateSelectionState);
        });

        var preferenceForm = document.querySelector('form');
        if (preferenceForm) {
            preferenceForm.addEventListener('submit', function () {
                if (continueButton.disabled) {
                    return;
                }
                continueButton.disabled = true;
                continueButton.textContent = 'Menyimpan...';
                continueButton.classList.add('opacity-70');
            });
        }

        updateSelectionState();
    });
</script>
@endsection
