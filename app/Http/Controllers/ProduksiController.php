<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProduksiController extends Controller
{
    public function index()
    {
        $prediksi = [];
        $error = null;
        $apiBase = config('app.python_api_url', 'http://127.0.0.1:5000');

        try {
            $response = Http::timeout(10)->get("{$apiBase}/api/predict");
            if ($response->successful()) {
                $data = $response->json();
                $prediksi = $data['data'] ?? [];
            } else {
                $error = 'Tidak dapat memuat prediksi. Status: ' . $response->status();
            }
        } catch (\Exception $e) {
            $error = 'Tidak dapat menghubungi server prediksi.';
        }

        return view('prediksi.index', compact('prediksi', 'error'));
    }
}
