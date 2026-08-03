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
});

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
    Route::get('/xgboost', [AdminController::class, 'xgboost'])->name('admin.xgboost');
    Route::post('/xgboost/reload', [AdminController::class, 'reloadXgboost'])->name('admin.xgboost.reload');
    Route::get('/history', [AdminController::class, 'history'])->name('admin.history');
});