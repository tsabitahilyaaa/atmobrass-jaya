@extends('layouts.admin')

@section('title', 'Prediksi LSTM')

@section('content')
<h1 class="font-display font-bold text-2xl mb-6">Prediksi Produksi LSTM</h1>

<!-- ============ RIWAYAT PENJUALAN DATASET ============ -->
@if($history && !$historyError)
<div class="bg-dark-100 border border-dark-300 rounded-xl p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-semibold text-lg text-white mb-1">Riwayat Penjualan Dataset</h2>
            <p class="text-sm text-muted">Data historis penjualan yang digunakan untuk melatih model LSTM.</p>
        </div>
    </div>

    <!-- Dropdown Tahun -->
    <div class="mb-6">
        <label for="yearSelect" class="block text-sm font-medium text-white mb-2">Pilih Tahun</label>
        <div class="flex items-center gap-3 w-full">
            <select id="yearSelect" class="px-4 py-2 rounded-lg bg-dark-200 text-white border border-dark-300 focus:outline-none focus:border-gold transition" style="min-width: 150px;">
                @foreach($history['years'] as $year)
                    <option value="{{ $year }}" @if($year == $history['selected_year']) selected @endif>{{ $year }}</option>
                @endforeach
            </select>
            <button id="loadHistoryBtn" type="button" class="inline-flex items-center gap-2 rounded-lg bg-gold px-6 py-2 text-sm font-semibold text-dark transition hover:bg-gold/90 whitespace-nowrap">
                <i class="fas fa-arrow-right"></i>
                Tampilkan Data
            </button>
        </div>
    </div>

    <!-- Grafik Penjualan Bulanan -->
    <div class="mb-8">
        <h3 class="font-semibold text-white mb-4">Grafik Penjualan Bulanan</h3>
        <div style="position:relative;height:300px;">
            <canvas id="historySalesChart"></canvas>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-dark-200 border border-dark-300 rounded-lg p-4">
            <p class="text-sm text-muted mb-1">Total Penjualan</p>
            <p class="text-2xl font-bold text-gold" id="summaryTotal">{{ number_format($history['summary']['total']) }} pcs</p>
        </div>
        <div class="bg-dark-200 border border-dark-300 rounded-lg p-4">
            <p class="text-sm text-muted mb-1">Rata-rata per Bulan</p>
            <p class="text-2xl font-bold text-gold" id="summaryAverage">{{ number_format($history['summary']['average']) }} pcs</p>
        </div>
        <div class="bg-dark-200 border border-dark-300 rounded-lg p-4">
            <p class="text-sm text-muted mb-1">Penjualan Tertinggi</p>
            <p class="text-2xl font-bold text-gold" id="summaryHighest">{{ number_format($history['summary']['highest_value']) }} pcs</p>
            <p class="text-xs text-muted">{{ $history['summary']['highest_month'] }}</p>
        </div>
        <div class="bg-dark-200 border border-dark-300 rounded-lg p-4">
            <p class="text-sm text-muted mb-1">Penjualan Terendah</p>
            <p class="text-2xl font-bold text-gold" id="summaryLowest">{{ number_format($history['summary']['lowest_value']) }} pcs</p>
            <p class="text-xs text-muted">{{ $history['summary']['lowest_month'] }}</p>
        </div>
    </div>

    <!-- Tabel Bulan dengan Detail Produk (Expandable) -->
    <div>
        <h3 class="font-semibold text-white mb-4">Detail Penjualan per Bulan</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left border-collapse">
                <thead>
                    <tr class="bg-dark-200 text-sm uppercase text-muted">
                        <th class="px-4 py-3 border border-dark-300 w-12"></th>
                        <th class="px-4 py-3 border border-dark-300">Bulan</th>
                        <th class="px-4 py-3 border border-dark-300">Total Penjualan (pcs)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history['monthly_sales'] as $month)
                        <tr class="month-row cursor-pointer hover:bg-dark-200 transition" data-month="{{ $month['month_num'] }}">
                            <td class="px-4 py-3 border border-dark-300 text-center">
                                <i class="fas fa-chevron-down expand-icon text-gold"></i>
                            </td>
                            <td class="px-4 py-3 border border-dark-300">{{ $month['month'] }}</td>
                            <td class="px-4 py-3 border border-dark-300 font-semibold text-gold">{{ number_format($month['total']) }} pcs</td>
                        </tr>
                        @if(count($month['products']) > 0)
                            <tr class="month-detail hidden" data-month-detail="{{ $month['month_num'] }}">
                                <td colspan="3" class="px-4 py-4 border border-dark-300">
                                    <div class="pl-4">
                                        <p class="text-sm text-muted mb-3">Produk yang terjual:</p>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach($month['products'] as $product)
                                                <div class="bg-dark-100 border border-dark-300 rounded p-3">
                                                    <p class="text-sm font-medium text-white">{{ $product['nama_produk'] }}</p>
                                                    <p class="text-xs text-muted">ID: {{ $product['id_produk'] }}</p>
                                                    <p class="text-sm text-gold mt-2">{{ number_format($product['quantity']) }} pcs</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@elseif($historyError)
    <div class="bg-red-900/20 border border-red-500 text-red-400 p-4 rounded-lg mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ $historyError }}
    </div>
@endif

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
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="font-semibold text-lg">Grafik Prediksi Produksi LSTM</h2>
            <p class="text-sm text-muted">Visualisasi jumlah prediksi per produk.</p>
        </div>
    </div>
    <div style="position:relative;height:320px;">
        <canvas id="lstmPredictionChart"></canvas>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ===== HISTORY CHART & INTERACTIONS =====
        @if($history && !$historyError)
            const historyData = {!! json_encode($history) !!};
            let currentChart = null;

            function attachRowListeners() {
                document.querySelectorAll('.month-row').forEach(row => {
                    row.addEventListener('click', function() {
                        const monthNum = this.getAttribute('data-month');
                        const detailRow = document.querySelector(`[data-month-detail="${monthNum}"]`);
                        const icon = this.querySelector('.expand-icon');

                        if (detailRow) {
                            detailRow.classList.toggle('hidden');
                            icon.classList.toggle('fa-chevron-down', detailRow.classList.contains('hidden'));
                            icon.classList.toggle('fa-chevron-up', !detailRow.classList.contains('hidden'));
                        }
                    });
                });
            }

            function initHistoryChart() {
                const selected = document.getElementById('yearSelect').value;
                console.log('Loading data for year:', selected);
                
                // Fetch data untuk tahun yang dipilih
                fetch(`{{ config('app.python_api_url', 'http://127.0.0.1:5000') }}/api/history?year=${selected}`)
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Data received:', data);
                        if (data.status === 'success') {
                            updateHistoryUI(data);
                        } else {
                            console.error('API Error:', data.pesan);
                            alert('Error: ' + data.pesan);
                        }
                    })
                    .catch(err => {
                        console.error('Fetch Error:', err);
                        alert('Error fetching data: ' + err.message);
                    });
            }

            function updateHistoryUI(data) {
                // Update summary cards
                document.getElementById('summaryTotal').textContent = 
                    new Intl.NumberFormat('id-ID').format(data.summary.total) + ' pcs';
                document.getElementById('summaryAverage').textContent = 
                    new Intl.NumberFormat('id-ID').format(data.summary.average) + ' pcs';
                document.getElementById('summaryHighest').textContent = 
                    new Intl.NumberFormat('id-ID').format(data.summary.highest_value) + ' pcs';
                document.getElementById('summaryLowest').textContent = 
                    new Intl.NumberFormat('id-ID').format(data.summary.lowest_value) + ' pcs';

                // Update chart
                const months = data.monthly_sales.map(m => m.month);
                const sales = data.monthly_sales.map(m => m.total);

                const ctx = document.getElementById('historySalesChart').getContext('2d');
                if (currentChart) {
                    currentChart.destroy();
                }

                currentChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Total Penjualan (pcs)',
                            data: sales,
                            borderColor: '#D4AF37',
                            backgroundColor: 'rgba(212, 175, 55, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#D4AF37',
                            pointBorderColor: '#1f2937',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                labels: { color: '#d1d5db' }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: '#111827',
                                titleColor: '#D4AF37',
                                bodyColor: '#d1d5db',
                                borderColor: '#D4AF37',
                                borderWidth: 1
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: '#d1d5db', font: { size: 12 } },
                                grid: { display: true, color: 'rgba(255,255,255,0.05)' }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: '#d1d5db', font: { size: 12 } },
                                grid: { color: 'rgba(255,255,255,0.08)' }
                            }
                        }
                    }
                });

                // Update tabel bulan
                updateMonthTable(data.monthly_sales);
            }

            function updateMonthTable(monthlySalesData) {
                const tbody = document.querySelector('table tbody');
                if (!tbody) return;

                // Clear existing rows (except header)
                const rows = tbody.querySelectorAll('tr');
                rows.forEach(row => row.remove());

                // Generate new rows
                monthlySalesData.forEach((month, index) => {
                    // Main month row
                    const monthRow = document.createElement('tr');
                    monthRow.className = 'month-row cursor-pointer hover:bg-dark-200 transition';
                    monthRow.setAttribute('data-month', month.month_num);
                    monthRow.innerHTML = `
                        <td class="px-4 py-3 border border-dark-300 text-center">
                            <i class="fas fa-chevron-down expand-icon text-gold"></i>
                        </td>
                        <td class="px-4 py-3 border border-dark-300">${month.month}</td>
                        <td class="px-4 py-3 border border-dark-300 font-semibold text-gold">${new Intl.NumberFormat('id-ID').format(month.total)} pcs</td>
                    `;
                    tbody.appendChild(monthRow);

                    // Detail row (hidden by default)
                    if (month.products.length > 0) {
                        const detailRow = document.createElement('tr');
                        detailRow.className = 'month-detail hidden';
                        detailRow.setAttribute('data-month-detail', month.month_num);

                        let productsHTML = '<div class="pl-4"><p class="text-sm text-muted mb-3">Produk yang terjual:</p><div class="grid grid-cols-1 md:grid-cols-2 gap-3">';
                        
                        month.products.forEach(prod => {
                            productsHTML += `
                                <div class="bg-dark-100 border border-dark-300 rounded p-3">
                                    <p class="text-sm font-medium text-white">${prod.nama_produk}</p>
                                    <p class="text-xs text-muted">ID: ${prod.id_produk}</p>
                                    <p class="text-sm text-gold mt-2">${new Intl.NumberFormat('id-ID').format(prod.quantity)} pcs</p>
                                </div>
                            `;
                        });
                        
                        productsHTML += '</div></div>';
                        detailRow.innerHTML = `<td colspan="3" class="px-4 py-4 border border-dark-300">${productsHTML}</td>`;
                        tbody.appendChild(detailRow);
                    }
                });

                // Re-attach event listeners ke row yang baru
                attachRowListeners();
            }

            // Event listener untuk tombol "Tampilkan Data"
            const loadBtn = document.getElementById('loadHistoryBtn');
            if (loadBtn) {
                loadBtn.addEventListener('click', function() {
                    console.log('Button clicked');
                    initHistoryChart();
                });
            }

            // Initialize chart on page load
            initHistoryChart();
            attachRowListeners();
        @endif

        // ===== LSTM PREDICTION CHART =====
        @if($predictions->count() > 0)
            const labels = {!! json_encode($predictions->pluck('nama_barang')) !!};
            const dataValues = {!! json_encode($predictions->pluck('prediksi_pcs')) !!};

            const ctx = document.getElementById('lstmPredictionChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Prediksi Produksi (pcs)',
                        data: dataValues,
                        backgroundColor: 'rgba(200, 169, 81, 0.6)',
                        borderColor: 'rgba(200, 169, 81, 1)',
                        borderWidth: 1,
                        borderRadius: 8,
                        maxBarThickness: 50,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#d1d5db', font: { size: 12 } },
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#d1d5db', font: { size: 12 } },
                            grid: { color: 'rgba(255,255,255,0.08)' }
                        }
                    }
                }
            });
        @endif
    });
</script>
@endpush
