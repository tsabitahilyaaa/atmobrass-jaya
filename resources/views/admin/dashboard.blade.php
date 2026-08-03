@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('content')
<h1 class="font-display font-bold text-2xl mb-6">Dashboard</h1>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-5"><div class="flex items-center justify-between mb-3"><span class="text-xs text-muted">Total Pendapatan</span><i class="fas fa-money-bill-wave text-green-400"></i></div><p class="font-bold text-lg">Rp {{ number_format($totalRevenue,0,',','.') }}</p></div>
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-5"><div class="flex items-center justify-between mb-3"><span class="text-xs text-muted">Total Pesanan</span><i class="fas fa-receipt text-blue-400"></i></div><p class="font-bold text-lg">{{ $totalOrders }}</p></div>
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-5"><div class="flex items-center justify-between mb-3"><span class="text-xs text-muted">Total Produk</span><i class="fas fa-boxes-stacked text-gold"></i></div><p class="font-bold text-lg">{{ $totalProducts }}</p></div>
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-5"><div class="flex items-center justify-between mb-3"><span class="text-xs text-muted">Total Pengguna</span><i class="fas fa-users text-purple-400"></i></div><p class="font-bold text-lg">{{ $totalUsers }}</p></div>
</div>
<div class="grid lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-6"><h3 class="font-semibold text-sm mb-4">Penjualan Bulanan</h3><div style="position:relative;height:220px"><canvas id="chart-monthly"></canvas></div></div>
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-6"><h3 class="font-semibold text-sm mb-4">Penjualan Tahunan</h3><div style="position:relative;height:220px"><canvas id="chart-yearly"></canvas></div></div>
</div>
<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-6"><h3 class="font-semibold text-sm mb-4">Distribusi Kategori</h3><div style="position:relative;height:220px"><canvas id="chart-category"></canvas></div></div>
    <div class="bg-dark-100 border border-dark-300 rounded-xl p-6">
        <h3 class="font-semibold text-sm mb-4">Prediksi Produksi XGBoost</h3>
        <div class="flex items-center gap-4 mb-4"><div class="w-14 h-14 rounded-full bg-gold/10 flex items-center justify-center"><i class="fas fa-brain text-gold text-xl"></i></div><div><p class="text-xs text-muted">Estimasi total produksi bulan berikutnya</p><p class="text-gold font-bold text-xl">{{ number_format($predictedQuantity ?? 0,0,',','.') }} pcs</p></div></div>
        @if(!empty($predictionError))
            <div class="text-sm text-red-300 mb-4">{{ $predictionError }}</div>
        @endif
        <div style="position:relative;height:170px"><canvas id="chart-prediction"></canvas></div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var gold = 'rgba(200,169,81,1)';
    var goldBg = 'rgba(200,169,81,0.15)';
    var gridC = 'rgba(255,255,255,0.05)';
    var tickC = 'rgba(255,255,255,0.4)';
    var fmt = function(v){return v>=1e6?(v/1e6).toFixed(0)+'jt':v>=1e3?(v/1e3).toFixed(0)+'rb':v;};
    var base = {responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{color:gridC},ticks:{color:tickC,font:{size:11}}},y:{grid:{color:gridC},ticks:{color:tickC,font:{size:11},callback:fmt}}}};

    new Chart(document.getElementById('chart-monthly'),{type:'bar',data:{labels:{{ json_encode($monthlyLabels) }},datasets:[{data:{{ json_encode($monthlyValues) }},backgroundColor:goldBg,borderColor:gold,borderWidth:1,borderRadius:4}]},options:base});
    new Chart(document.getElementById('chart-yearly'),{type:'line',data:{labels:{{ json_encode($yearlyLabels) }},datasets:[{data:{{ json_encode($yearlyValues) }},borderColor:gold,backgroundColor:goldBg,fill:true,tension:.4,pointBackgroundColor:gold,pointRadius:5}]},options:base});
    new Chart(document.getElementById('chart-category'),{type:'doughnut',data:{labels:{{ json_encode($catLabels) }},datasets:[{data:{{ json_encode($catValues) }},backgroundColor:['rgba(200,169,81,0.8)','rgba(168,162,158,0.8)','rgba(232,212,139,0.8)','rgba(154,123,44,0.8)'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{color:tickC,padding:15,font:{size:11}}}}}});

    var av = {{ json_encode($monthlyQuantityValues) }}.concat([{{ $predictedQuantity ?? 0 }}]);
    var al = {{ json_encode($monthlyLabels) }}.concat(['Prediksi']);
    var pc = al.map(function(_,i){return i===al.length-1?'#ef4444':gold;});
    var ps = al.map(function(_,i){return i===al.length-1?7:3;});
    new Chart(document.getElementById('chart-prediction'),{type:'line',data:{labels:al,datasets:[{data:av,borderColor:gold,backgroundColor:goldBg,fill:true,tension:.3,pointBackgroundColor:pc,pointRadius:ps}]},options:base});
});
</script>
@endpush