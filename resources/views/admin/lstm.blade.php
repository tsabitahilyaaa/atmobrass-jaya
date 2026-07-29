@extends('layouts.admin')

@section('title', 'Prediksi LSTM')

@section('content')
<h1 class="font-display font-bold text-2xl mb-3">Prediksi Produksi LSTM</h1>
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <p class="text-sm text-muted">Hasil prediksi untuk bulan berikutnya berdasarkan data terakhir di dataset.</p>
    </div>
    <div class="bg-dark-200 border border-dark-300 rounded-lg px-4 py-3">
        <p class="text-xs text-muted uppercase tracking-wide">Bulan yang diprediksi</p>
        <p class="font-semibold text-gold">{{ $predictedMonth ?? 'Bulan berikutnya' }}</p>
    </div>
</div>

@if(session('status'))
    <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-6">{{ session('status') }}</div>
@endif

@if(!empty($error))
    <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-6">{{ $error }}</div>
@endif

@if(session('error'))
    <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-6">{{ session('error') }}</div>
@endif

<!-- Riwayat penjualan sekarang dipisah ke halaman terpisah. -->
<div class="mb-6">
    <a href="{{ route('admin.history') }}" class="inline-flex items-center gap-2 rounded-lg bg-gold px-4 py-2 text-sm font-semibold text-dark transition hover:bg-gold/90">
        <i class="fas fa-clock-rotate-left"></i>
        Lihat Riwayat Penjualan
    </a>
</div>

<!-- ============ PREDIKSI LSTM ============ -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <p class="text-sm text-muted">Perbarui dataset dan model di backend Python setiap kali file CSV atau model berubah.</p>
    </div>
    <form method="POST" action="{{ route('admin.lstm.reload') }}">
        @csrf
        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-gold px-4 py-2 text-sm font-semibold text-dark transition hover:bg-gold/90">
            <i class="fas fa-sync-alt"></i>
            Reload Model & Data
        </button>
    </form>
</div>

<div class="bg-dark-100 border border-dark-300 rounded-xl p-6 mb-8">
    <p class="text-sm text-muted mb-3">Halaman ini menampilkan hasil prediksi produksi dari model LSTM yang berjalan di backend Python.</p>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left border-collapse">
            <thead>
                <tr class="bg-dark-200 text-sm uppercase text-muted">
                    <th class="px-4 py-3 border border-dark-300">ID Produk</th>
                    <th class="px-4 py-3 border border-dark-300">Nama Produk</th>
                    <th class="px-4 py-3 border border-dark-300">Prediksi (pcs)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($predictions as $item)
                    <tr>
                        <td class="px-4 py-3 border border-dark-300">{{ $item->id_produk }}</td>
                        <td class="px-4 py-3 border border-dark-300">{{ $item->nama_barang }}</td>
                        <td class="px-4 py-3 border border-dark-300">{{ $item->prediksi_pcs }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-5 text-center text-muted">Tidak ada data prediksi yang dapat ditampilkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($predictions->count() > 0)
<div class="bg-dark-100 border border-dark-300 rounded-xl p-6 mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
            <h2 class="font-semibold text-lg">Grafik Prediksi Produksi LSTM</h2>
            <p class="text-sm text-muted">Visualisasi jumlah prediksi per produk.</p>
            <p class="text-sm text-muted mt-1">Prediksi ini dibuat untuk bulan berikutnya setelah data terakhir pada dataset.</p>
        </div>
        <div class="bg-dark-200 border border-dark-300 rounded-lg px-4 py-3">
            <p class="text-xs text-muted uppercase tracking-wide">Bulan yang diprediksi</p>
            <p class="font-semibold text-gold" id="predictedMonthLabel">{{ $predictedMonth ?? 'Bulan berikutnya' }}</p>
        </div>
    </div>
    <div style="position:relative;height:360px;">
        <canvas id="lstmPredictionChart"></canvas>
    </div>
</div>
@endif

@if($forecast)
<div class="bg-dark-100 border border-dark-300 rounded-xl p-6 mb-8">
    <div class="mb-4">
        <h2 class="font-semibold text-lg">Forecast Multi-Step</h2>
        <p class="text-sm text-muted">Prediksi produksi per produk untuk 5 bulan ke depan.</p>
    </div>

    <div class="overflow-x-auto mb-6">
        <table class="min-w-full text-left border-collapse">
            <thead>
                <tr class="bg-dark-200 text-sm uppercase text-muted">
                    <th class="px-4 py-3 border border-dark-300">Produk</th>
                    @foreach($forecast['forecast_months'] as $month)
                        <th class="px-4 py-3 border border-dark-300">{{ $month }}</th>
                    @endforeach
                    <th class="px-4 py-3 border border-dark-300">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($forecast['products'] as $item)
                    <tr>
                        <td class="px-4 py-3 border border-dark-300">{{ $item['nama_produk'] }}</td>
                        @foreach($item['monthly'] as $month)
                            <td class="px-4 py-3 border border-dark-300">{{ number_format($month['prediksi_pcs'], 0, ',', '.') }}</td>
                        @endforeach
                        <td class="px-4 py-3 border border-dark-300">{{ number_format($item['total'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mb-6">
        <h3 class="font-semibold text-lg mb-3">Grafik Forecast Multi-Step</h3>
        <div style="position:relative;height:360px;">
            <canvas id="forecastChart"></canvas>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if($predictions->count() > 0)
            const labels = {!! json_encode($predictions->pluck('nama_barang')) !!};
            const dataValues = {!! json_encode($predictions->pluck('prediksi_pcs')) !!};
            const predictedMonth = {!! json_encode($predictedMonth ?? null) !!} || null;

            const monthLabel = document.getElementById('predictedMonthLabel');
            if (monthLabel) {
                monthLabel.textContent = predictedMonth ? predictedMonth : 'Bulan berikutnya';
            }

            const canvas = document.getElementById('lstmPredictionChart');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Prediksi Produksi (pcs)',
                            data: dataValues,
                            backgroundColor: 'rgba(200, 169, 81, 0.7)',
                            borderColor: 'rgba(200, 169, 81, 1)',
                            borderWidth: 1,
                            borderRadius: 6,
                            maxBarThickness: 38,
                            categoryPercentage: 0.75,
                            barPercentage: 0.75,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                top: 8,
                                right: 16,
                                bottom: 8,
                                left: 8
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: '#111827',
                                titleColor: '#D4AF37',
                                bodyColor: '#d1d5db',
                                borderColor: '#D4AF37',
                                borderWidth: 1,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + new Intl.NumberFormat('id-ID').format(context.parsed.y) + ' pcs';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: '#d1d5db', font: { size: 11 }, maxRotation: 45, minRotation: 0, autoSkip: true },
                                grid: { display: false },
                                title: { display: true, text: 'Produk', color: '#d1d5db', font: { size: 12 } }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: '#d1d5db', font: { size: 12 } },
                                grid: { color: 'rgba(255,255,255,0.08)', drawBorder: false },
                                title: { display: true, text: 'Prediksi (pcs)', color: '#d1d5db', font: { size: 12 } }
                            }
                        }
                    }
                });
            }
        @endif

        @if($forecast)
            const forecastLabels = {!! json_encode($forecast['forecast_months']) !!};
            const forecastTotals = {!! json_encode($forecast['overall']['monthly_total']) !!};

            const forecastCanvas = document.getElementById('forecastChart');
            if (forecastCanvas) {
                const ctx = forecastCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: forecastLabels,
                        datasets: [{
                            label: 'Total Prediksi (pcs)',
                            data: forecastTotals,
                            backgroundColor: 'rgba(251,191,36,0.8)',
                            borderColor: 'rgba(234,179,8,1)',
                            borderWidth: 1,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + new Intl.NumberFormat('id-ID').format(context.parsed.y) + ' pcs';
                                }
                            }}
                        },
                        scales: {
                            x: { ticks: { color: '#d1d5db' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                            y: { beginAtZero: true, ticks: { color: '#d1d5db' }, grid: { color: 'rgba(255,255,255,0.05)' } }
                        }
                    }
                });
            }
        @endif
    });
</script>
@endpush
