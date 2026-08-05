@php
    $preferenceOptions = [
        'Pintu Rumah',
        'Lemari & Kabinet',
        'Furniture',
        'Kantor & Bangunan Komersial',
        'Dekorasi Interior',
        'Renovasi & Proyek Bangunan',
    ];

    $selectedPreferences = old('preferences', $selectedPreferences ?? []);
@endphp

<div id="preference-modal-overlay"
class="fixed inset-0 z-[100] flex justify-center overflow-y-auto bg-black/70 backdrop-blur-md"
style="padding-top:70px;padding-bottom:10px;">

    <div class="w-full max-w-5xl rounded-3xl border border-dark-300 bg-dark-100 p-7 sm:p-8 shadow-[0_35px_70px_rgba(0,0,0,.65)]"
        style="animation:preferenceModalFade .35s ease-out">

        <p class="text-xs uppercase tracking-[0.35em] text-gold mb-3">
            Langkah 1 dari 1
        </p>

        <h2 class="font-display font-bold text-3xl text-white mb-3">
            Temukan Produk yang Sesuai untuk Anda
        </h2>

        <p class="text-muted leading-7 mb-8">
            Pilih maksimal <span class="text-gold font-semibold">3 kebutuhan</span>
            agar kami dapat memberikan rekomendasi produk yang paling relevan.
        </p>

        <div class="rounded-2xl border border-dark-300 bg-dark-200 p-6">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

                <div>

                    <h3 class="font-display font-semibold text-xl text-white">
                        Apa yang sedang Anda cari?
                    </h3>

                    <p id="preference-counter"
                        class="mt-2 text-sm text-muted font-medium">
                        Belum ada pilihan
                    </p>

                </div>

                <div
                    class="rounded-full border border-dark-300 bg-dark-100 px-4 py-2 text-sm text-muted">

                    <span class="text-gold font-semibold">
                        Tips
                    </span>

                    Pilih maksimal 3 kategori.

                </div>

            </div>

            <form
                action="{{ route('preferences.store') }}"
                method="POST"
                class="mt-8">

                @csrf

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

                    @foreach($preferenceOptions as $option)

                        <label
                            class="preference-option cursor-pointer rounded-xl border border-dark-300 bg-dark-100 px-5 py-4 text-center text-sm font-medium text-white transition-all duration-300 hover:border-gold hover:bg-dark-200 hover:-translate-y-1 hover:shadow-lg {{ in_array($option,$selectedPreferences) ? 'border-gold bg-gold/20 text-gold' : '' }}">

                            <input
                                type="checkbox"
                                class="sr-only preference-modal-checkbox"
                                name="preferences[]"
                                value="{{ $option }}"
                                {{ in_array($option,$selectedPreferences) ? 'checked' : '' }}>

                            {{ $option }}

                        </label>

                    @endforeach

                </div>

                <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-3">

                    <button
                        type="button"
                        onclick="document.getElementById('skip-preference-form').submit()"
                        class="btn-outline px-6 py-3 rounded-lg">

                        Lewati

                    </button>

                    <button
                        id="continue-preference-button"
                        type="submit"
                        disabled
                        class="btn-outline px-6 py-3 rounded-lg opacity-60 cursor-not-allowed">

                        Lihat Rekomendasi

                    </button>

                </div>

            </form>

            <form
                id="skip-preference-form"
                action="{{ route('preferences.skip') }}"
                method="POST"
                class="hidden">

                @csrf

            </form>

        </div>

    </div>

</div>

<style>

@keyframes preferenceModalFade{

    from{

        opacity:0;
        transform:translateY(15px) scale(.97);

    }

    to{

        opacity:1;
        transform:translateY(0) scale(1);

    }

}

.preference-option{

    transition:all .25s ease;

}

.preference-option:hover{

    border-color:#d4af37 !important;
    box-shadow:0 10px 25px rgba(212,175,55,.15);

}

.preference-option.selected{

    background:rgba(212,175,55,.15) !important;
    border-color:#d4af37 !important;
    color:#d4af37 !important;
    box-shadow:0 12px 28px rgba(212,175,55,.18);

}

</style>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const overlay = document.getElementById('preference-modal-overlay');
    const checkboxes = document.querySelectorAll('.preference-modal-checkbox');
    const counter = document.getElementById('preference-counter');
    const continueButton = document.getElementById('continue-preference-button');

    if (!overlay) return;

    // Lock scroll
    document.body.style.overflow = 'hidden';

    // Tampilkan modal
    setTimeout(() => {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
    }, 200);

    function updateState() {

        const selected = [...checkboxes].filter(cb => cb.checked);

        // Counter
        if (selected.length === 0) {

            counter.innerHTML = "Belum ada pilihan";

        } else {

            counter.innerHTML =
                "<span class='text-gold font-semibold'>" +
                selected.length +
                "</span> dari 3 kebutuhan dipilih";

        }

        // Maksimal 3
        checkboxes.forEach(cb => {

            if (!cb.checked && selected.length >= 3) {

                cb.disabled = true;

                cb.closest('.preference-option')
                    .classList.add('opacity-50');

            } else {

                cb.disabled = false;

                cb.closest('.preference-option')
                    .classList.remove('opacity-50');

            }

        });

        // Highlight pilihan
        checkboxes.forEach(cb => {

            const label = cb.closest('.preference-option');

            if (cb.checked) {

                label.classList.add(
                    'selected',
                    'border-gold',
                    'text-gold'
                );

                label.classList.remove(
                    'bg-dark-100',
                    'text-white'
                );

            } else {

                label.classList.remove(
                    'selected',
                    'border-gold',
                    'text-gold'
                );

                label.classList.add(
                    'bg-dark-100',
                    'text-white'
                );

            }

        });

        // Tombol
        continueButton.disabled = selected.length === 0;

        if (continueButton.disabled) {

            continueButton.classList.remove(
                'btn-gold',
                'text-black'
            );

            continueButton.classList.add(
                'btn-outline',
                'opacity-60',
                'cursor-not-allowed'
            );

        } else {

            continueButton.classList.remove(
                'btn-outline',
                'opacity-60',
                'cursor-not-allowed'
            );

            continueButton.classList.add(
                'btn-gold',
                'text-black'
            );

        }

    }

    checkboxes.forEach(cb => {

        cb.addEventListener('change', updateState);

    });

    updateState();

});

</script>