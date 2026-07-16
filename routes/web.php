<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminUserController;
use Illuminate\Support\Facades\Route;

// Halaman publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang', [HomeController::class, 'about'])->name('about');
Route::get('/kontak', [ContactController::class, 'index'])->name('contact');
Route::post('/kontak', [ContactController::class, 'send'])->name('contact.send');

// Produk
Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('/pesan-cepat', [App\Http\Controllers\OrderController::class, 'quickOrder'])->name('order.quick');

// Autentikasi
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin (dilindungi middleware)
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
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
});