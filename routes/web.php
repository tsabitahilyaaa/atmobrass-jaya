<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;

// Halaman publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang', [HomeController::class, 'about'])->name('about');
Route::get('/kontak', [ContactController::class, 'index'])->name('contact');
Route::post('/kontak', [ContactController::class, 'send'])->name('contact.send');

// Produk
Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('products.show');

// Halaman prediksi (memanggil API Python)
Route::get('/prediksi', [ProduksiController::class, 'index'])->name('prediksi.index');

// Onboarding Preferensi
Route::get('/preferensi', [PreferenceController::class, 'index'])->name('preferences.index');
Route::post('/preferensi', [PreferenceController::class, 'store'])->name('preferences.store');
Route::post('/preferensi/skip', [PreferenceController::class, 'skip'])->name('preferences.skip');

Route::post('/pesan-cepat', [OrderController::class, 'quickOrder'])->name('order.quick');

// =========================
// AUTH CUSTOMER
// =========================

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang/tambah', [CartController::class, 'add'])->name('cart.add');
Route::post('/keranjang/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/keranjang/hapus', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/keranjang/naik', [CartController::class, 'increase'])->name('cart.increase');
Route::post('/keranjang/turun', [CartController::class, 'decrease'])->name('cart.decrease');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/riwayat-pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profil/preferensi', [PreferenceController::class, 'profile'])->name('profile.preferences');
    Route::post('/profil/preferensi/reset', [PreferenceController::class, 'reset'])->name('preferences.reset');
    Route::get('/profil/pesanan', [ProfileController::class, 'index'])->name('profile.orders');
    Route::get('/profil/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profil/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::put('/profil/password', [ProfileController::class, 'passwordUpdate'])->name('profile.password.update');

    Route::get('/profil/alamat', [AddressController::class, 'index'])->name('profile.addresses');
    Route::get('/profil/alamat/tambah', [AddressController::class, 'create'])->name('profile.addresses.create');
    Route::post('/profil/alamat', [AddressController::class, 'store'])->name('profile.addresses.store');
    Route::get('/profil/alamat/{address}/edit', [AddressController::class, 'edit'])->name('profile.addresses.edit');
    Route::put('/profil/alamat/{address}', [AddressController::class, 'update'])->name('profile.addresses.update');
    Route::delete('/profil/alamat/{address}', [AddressController::class, 'destroy'])->name('profile.addresses.destroy');
    Route::put('/profil/alamat/{address}/utama', [AddressController::class, 'setDefault'])->name('profile.addresses.default');

    Route::get('/profil/pesanan/{order}', [ProfileController::class, 'show'])->name('profile.orders.show');
    Route::post('/profil/pesanan/{order}/beli-lagi', [ProfileController::class, 'reorder'])->name('profile.orders.reorder');});

// =========================
// AUTH ADMIN
// =========================

Route::get('/portal/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/portal/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/portal/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Admin (dilindungi middleware)
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/produk', [AdminProductController::class, 'index'])->name('admin.products.index');
    Route::get('/produk/create', [AdminProductController::class, 'create'])->name('admin.products.create');
    Route::post('/produk', [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::get('/produk/{id}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/produk/{id}', [AdminProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/produk/{id}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::get('/pengguna', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::delete('/pengguna/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/pesan', [AdminMessageController::class, 'index'])->name('admin.messages.index');
    Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::put('/pesanan/{id}', [AdminOrderController::class, 'update'])->name('admin.orders.update');
    Route::delete('/pesanan/{id}', [AdminOrderController::class, 'destroy'])->name('admin.orders.destroy');
    Route::get('/pesanan/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::get('/pembayaran', [AdminController::class, 'payment'])->name('admin.payment');
    Route::post('/pembayaran', [AdminController::class, 'savePayment'])->name('admin.payment.save');
    Route::get('/lstm', [AdminController::class, 'lstm'])->name('admin.lstm');
    Route::post('/lstm/reload', [AdminController::class, 'reloadLstm'])->name('admin.lstm.reload');
    Route::get('/history', [AdminController::class, 'history'])->name('admin.history');
});