@extends('layouts.app')

@section('title', 'Prediksi Produksi')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Prediksi Produksi</h1>

    @if(isset($error) && $error)
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ $error }}</div>
    @endif

    @if(!empty($prediksi) && count($prediksi) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border">ID Produk</th>
                        <th class="px-4 py-2 border">Nama Barang</th>
                        <th class="px-4 py-2 border">Prediksi (pcs)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prediksi as $p)
                        <tr>
                            <td class="px-4 py-2 border">{{ $p['id_produk'] ?? '' }}</td>
                            <td class="px-4 py-2 border">{{ $p['nama_barang'] ?? '' }}</td>
                            <td class="px-4 py-2 border">{{ $p['prediksi_pcs'] ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-gray-100 text-gray-800 p-3 rounded">Belum ada data prediksi.</div>
    @endif
</div>
@endsection
