<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * Menampilkan halaman login admin
     */
    public function showLoginForm()
    {
        // Jika sudah login sebagai admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Jika sudah login sebagai customer
        if (Auth::check() && Auth::user()->role === 'customer') {
            return redirect()->route('home')
                ->with('error', 'Silakan logout terlebih dahulu untuk mengakses portal administrator.');
        }

        return view('admin.auth.login');
    }

    /**
     * Proses login admin
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->filled('remember'))) {

            return back()->withErrors([
                'email' => 'Email atau password salah.'
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // Cek apakah benar admin
        if (Auth::user()->role !== 'admin') {

            Auth::logout();

            return back()->withErrors([
                'email' => 'Akun ini bukan administrator.'
            ])->onlyInput('email');
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Selamat datang Administrator.');
    }

    /**
     * Logout admin
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}