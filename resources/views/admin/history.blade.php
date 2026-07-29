@extends('layouts.admin')

@section('title', 'Riwayat Penjualan')

@section('content')
<h1 class="font-display font-bold text-2xl mb-6">Riwayat Penjualan</h1>

@if(session('status'))
    <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-6">{{ session('status') }}</div>
@endif

@if(session('error'))
    <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-6">{{ session('error') }}</div>
@endif

<div class="mb-6">
    <div class="inline-flex rounded-lg bg-dark-200 border border-dark-300 overflow-hidden">
        <button id="tabEvaluationBtn" class="px-4 py-2 font-semibold text-sm text-white bg-dark-400 hover:bg-dark-300 transition focus:outline-none">Backtest 2025</button>
        <button id="tabHistoryBtn" class="px-4 py-2 font-semibold text-sm text-white bg-dark-200 hover:bg-dark-300 transition focus:outline-none">Riwayat Penjualan</button>
    </div>
</div>

@if(isset($evaluationError) && $evaluationError)
    <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-6">{{ $evaluationError }}</div>
@endif

<div id="evaluationTab" class="space-y-6">
@if(isset($evaluation) && $evaluation)
<div class="bg-dark-100 border border-dark-300 rounded-xl p-6 mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
        <div>
            <h2 class="font-semibold text-lg text-white mb-1">Backtest Evaluasi 2025</h2>
            <p class="text-sm text-muted">Laporan evaluasi model LSTM dibandingkan data aktual periode 2025.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-dark-200 border border-dark-300 rounded-lg p-4">
            <p class="text-sm text-muted mb-1">MAE</p>
            <p class="text-2xl font-bold text-gold">{{ number_format($evaluation['overall']['mae'], 2, ',', '.') }}</p>
        </div>
        <div class="bg-dark-200 border border-dark-300 rounded-lg p-4">
            <p class="text-sm text-muted mb-1">MSE</p>
            <p class="text-2xl font-bold text-gold">{{ number_format($evaluation['overall']['mse'], 2, ',', '.') }}</p>
        </div>
        <div class="bg-dark-200 border border-dark-300 rounded-lg p-4">
            <p class="text-sm text-muted mb-1">RMSE</p>
            <p class="text-2xl font-bold text-gold">{{ number_format($evaluation['overall']['rmse'], 2, ',', '.') }}</p>
        </div>
        <div class="bg-dark-200 border border-dark-300 rounded-lg p-4">
            <p class="text-sm text-muted mb-1">MAPE</p>
            <p class="text-2xl font-bold text-gold">{{ number_format($evaluation['overall']['mape'], 2, ',', '.') }}%</p>
        </div>
    </div>

    <div class="mb-6 text-sm text-muted">{{ $evaluation['conclusion'] }}</div>

    <div class="overflow-x-auto mb-6">
        <table class="min-w-full text-left border-collapse">
            <thead>
                <tr class="bg-dark-200 text-sm uppercase text-muted">
                    <th class="px-4 py-3 border border-dark-300">Produk</th>
                    <th class="px-4 py-3 border border-dark-300">Aktual Total</th>
                    <th class="px-4 py-3 border border-dark-300">Prediksi Total</th>
                    <th class="px-4 py-3 border border-dark-300">MAE</th>
                    <th class="px-4 py-3 border border-dark-300">MAPE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evaluation['product_summary'] as $item)
                    <tr>
                        <td class="px-4 py-3 border border-dark-300">{{ $item['nama_produk'] }}</td>
                        <td class="px-4 py-3 border border-dark-300">{{ number_format($item['aktual_total'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border border-dark-300">{{ number_format($item['prediksi_total'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border border-dark-300">{{ number_format($item['mae'], 2, ',', '.') }}</td>
                        <td class="px-4 py-3 border border-dark-300">{{ number_format($item['mape'], 2, ',', '.') }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mb-6">
        <h3 class="font-semibold text-lg mb-3">Grafik Aktual vs Prediksi 2025</h3>
        <div style="position:relative;height:360px;">
            <canvas id="evaluationChart"></canvas>
        </div>
    </div>
</div>
@endif
</div>

<div id="historyTab" class="space-y-6 hidden">
@if(isset($productsData) && $productsData && $productsData['status'] === 'success')
<div class="bg-dark-100 border border-dark-300 rounded-xl p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-semibold text-lg text-white mb-1">Riwayat Penjualan per Produk</h2>
            <p class="text-sm text-muted">Pilih sebuah produk untuk melihat sejarah penjualan dari awal dataset hingga akhir.</p>
        </div>
    </div>

    <div class="mb-6">
        <label for="productSelect" class="block text-sm font-medium text-white mb-2">Pilih Produk</label>
        <select id="productSelect" class="px-4 py-2 rounded-lg bg-dark-200 text-white border border-dark-300 focus:outline-none focus:border-gold transition w-full md:w-1/2">
            <!-- Options injected by JS -->
        </select>
    </div>

    <div class="mb-6">
        <h3 class="font-semibold text-white mb-4">Grafik Penjualan Produk (Seluruh Periode)</h3>
        <div style="position:relative;height:480px;">
            <canvas id="productTimeseriesChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-dark-200 border border-dark-300 rounded-lg p-4">
            <p class="text-sm text-muted mb-1">Total Penjualan (periode)</p>
            <p class="text-2xl font-bold text-gold" id="prodTotal">- pcs</p>
        </div>
        <div class="bg-dark-200 border border-dark-300 rounded-lg p-4">
            <p class="text-sm text-muted mb-1">Rata-rata per Bulan</p>
            <p class="text-2xl font-bold text-gold" id="prodAverage">- pcs</p>
        </div>
        <div class="bg-dark-200 border border-dark-300 rounded-lg p-4">
            <p class="text-sm text-muted mb-1">Bulan Tertinggi</p>
            <p class="text-2xl font-bold text-gold" id="prodHighest">- pcs</p>
            <p class="text-xs text-muted" id="prodHighestMonth">-</p>
        </div>
        <div class="bg-dark-200 border border-dark-300 rounded-lg p-4">
            <p class="text-sm text-muted mb-1">Bulan Terendah</p>
            <p class="text-2xl font-bold text-gold" id="prodLowest">- pcs</p>
            <p class="text-xs text-muted" id="prodLowestMonth">-</p>
        </div>
    </div>
</div>
@elseif(isset($error))
    <div class="bg-red-900/20 border border-red-500 text-red-400 p-4 rounded-lg mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}
    </div>
@else
    <div class="bg-red-900/20 border border-red-500 text-red-400 p-4 rounded-lg mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i>Data produk tidak tersedia.
    </div>
@endif
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const productsData = {!! json_encode($productsData ?? null) !!};
        if (!productsData || productsData.status !== 'success') return;

        const labelsRaw = productsData.labels || [];

        // Months labels only (no year) - Jan..Dec
        const monthNames = [
            'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'
        ];

        const labels = monthNames; // X-axis will show months only

        const productSelect = document.getElementById('productSelect');
        productsData.products.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id_produk;
            opt.textContent = p.nama_produk + ' (' + p.id_produk + ')';
            productSelect.appendChild(opt);
        });

        let chart = null;

        function updateSummary(series) {
            const total = series.reduce((a,b)=>a+(b||0),0);
            const monthsWithData = series.filter(v=>v>0).length || series.length;
            const average = Math.round(total / monthsWithData);
            const highest = Math.max(...series);
            const lowest = Math.min(...series);
            const highestIdx = series.indexOf(highest);
            const lowestIdx = series.indexOf(lowest);

            document.getElementById('prodTotal').textContent = new Intl.NumberFormat('id-ID').format(total) + ' pcs';
            document.getElementById('prodAverage').textContent = new Intl.NumberFormat('id-ID').format(average) + ' pcs';
            document.getElementById('prodHighest').textContent = new Intl.NumberFormat('id-ID').format(highest) + ' pcs';
            document.getElementById('prodHighestMonth').textContent = highestIdx >=0 ? labels[highestIdx] : '-';
            document.getElementById('prodLowest').textContent = new Intl.NumberFormat('id-ID').format(lowest) + ' pcs';
            document.getElementById('prodLowestMonth').textContent = lowestIdx >=0 ? labels[lowestIdx] : '-';
        }

        function initChartForProduct(productId) {
            const prod = productsData.products.find(p => p.id_produk == productId);
            if (!prod) return;

            // Build year->array(12) map
            const yearMap = {};
            labelsRaw.forEach((ym, idx) => {
                const parts = ym.split('-');
                const y = parts[0];
                const m = parseInt(parts[1], 10); // 1-12
                if (!yearMap[y]) yearMap[y] = Array(12).fill(null);
                const val = Number(prod.series[idx] || 0);
                yearMap[y][m - 1] = val;
            });

            // Build datasets per year (sorted by year asc)
            const years = Object.keys(yearMap).sort();
            const palette = [
                '#D4AF37','#6EE7B7','#60A5FA','#F472B6','#FBBF24','#34D399','#A78BFA','#F87171'
            ];

            const datasets = years.map((y, i) => ({
                label: y,
                data: yearMap[y],
                borderColor: palette[i % palette.length],
                backgroundColor: (i%2===0) ? 'rgba(212,175,55,0.06)' : 'rgba(96,165,250,0.04)',
                fill: false,
                tension: 0.3,
                pointRadius: 3,
                borderWidth: 2
            }));

            // For summary, compute totals across all months for selected product (sum of series)
            const flatSeries = prod.series.map(v => Number(v || 0));
            updateSummary(flatSeries);

            const ctx = document.getElementById('productTimeseriesChart').getContext('2d');
            if (chart) chart.destroy();

            // Plugin to draw year labels at the end of each line
            const endLabelPlugin = {
                id: 'endLabelPlugin',
                afterDatasetsDraw: (chart) => {
                    const ctx = chart.ctx;
                    chart.data.datasets.forEach((dataset, i) => {
                        const meta = chart.getDatasetMeta(i);
                        const data = dataset.data;
                        // find last non-null point index
                        let lastIndex = -1;
                        for (let j = data.length - 1; j >= 0; j--) {
                            if (data[j] !== null && data[j] !== undefined) { lastIndex = j; break; }
                        }
                        if (lastIndex === -1) return;
                        const point = meta.data[lastIndex];
                        if (!point) return;
                        const x = point.x + 8;
                        const y = point.y;
                        ctx.save();
                        ctx.font = '12px sans-serif';
                        ctx.fillStyle = dataset.borderColor || '#fff';
                        ctx.textAlign = 'left';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(dataset.label, x, y);
                        ctx.restore();
                    });
                }
            };

            chart = new Chart(ctx, {
                type: 'line',
                data: { labels: labels, datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top', labels: { color: '#d1d5db' } },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        x: { ticks: { color: '#d1d5db' }, grid: { color: 'rgba(255,255,255,0.03)' } },
                        y: { beginAtZero: true, ticks: { color: '#d1d5db' }, grid: { color: 'rgba(255,255,255,0.06)' } }
                    }
                },
                plugins: [endLabelPlugin]
            });
        }

        // Initialize first product
        if (productsData.products.length > 0) {
            const firstId = productsData.products[0].id_produk;
            productSelect.value = firstId;
            initChartForProduct(firstId);
        }

        productSelect.addEventListener('change', function() {
            initChartForProduct(this.value);
        });

        @if(isset($evaluation) && $evaluation)
            const evaluationLabels = {!! json_encode($evaluation['months']) !!};
            const evaluationActual = {!! json_encode($evaluation['trend']['actual']) !!};
            const evaluationPredicted = {!! json_encode($evaluation['trend']['predicted']) !!};

            const evaluationCanvas = document.getElementById('evaluationChart');
            if (evaluationCanvas) {
                const ctxEval = evaluationCanvas.getContext('2d');
                new Chart(ctxEval, {
                    type: 'line',
                    data: {
                        labels: evaluationLabels,
                        datasets: [
                            {
                                label: 'Aktual 2025',
                                data: evaluationActual,
                                borderColor: 'rgba(34,197,94,1)',
                                backgroundColor: 'rgba(34,197,94,0.15)',
                                fill: false,
                                tension: 0.3,
                                pointRadius: 3,
                                borderWidth: 2,
                            },
                            {
                                label: 'Prediksi 2025',
                                data: evaluationPredicted,
                                borderColor: 'rgba(59,130,246,1)',
                                backgroundColor: 'rgba(59,130,246,0.15)',
                                fill: false,
                                tension: 0.3,
                                pointRadius: 3,
                                borderWidth: 2,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { labels: { color: '#d1d5db' } },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            x: { ticks: { color: '#d1d5db' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                            y: { beginAtZero: true, ticks: { color: '#d1d5db' }, grid: { color: 'rgba(255,255,255,0.05)' } }
                        }
                    }
                });
            }
        @endif

        const tabEvaluationBtn = document.getElementById('tabEvaluationBtn');
        const tabHistoryBtn = document.getElementById('tabHistoryBtn');
        const evaluationTab = document.getElementById('evaluationTab');
        const historyTab = document.getElementById('historyTab');

        function activateTab(tabName) {
            if (tabName === 'evaluation') {
                evaluationTab.classList.remove('hidden');
                historyTab.classList.add('hidden');
                tabEvaluationBtn.classList.add('bg-dark-400');
                tabEvaluationBtn.classList.remove('bg-dark-200');
                tabHistoryBtn.classList.add('bg-dark-200');
                tabHistoryBtn.classList.remove('bg-dark-400');
            } else {
                evaluationTab.classList.add('hidden');
                historyTab.classList.remove('hidden');
                tabEvaluationBtn.classList.add('bg-dark-200');
                tabEvaluationBtn.classList.remove('bg-dark-400');
                tabHistoryBtn.classList.add('bg-dark-400');
                tabHistoryBtn.classList.remove('bg-dark-200');
            }
        }

        tabEvaluationBtn.addEventListener('click', function () {
            activateTab('evaluation');
        });
        tabHistoryBtn.addEventListener('click', function () {
            activateTab('history');
        });

        activateTab('{{ isset($evaluation) && $evaluation ? 'evaluation' : 'history' }}');
    });
</script>
@endpush
