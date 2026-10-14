<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SweetBakes Inventory Management System
| Pertemuan 5: Laravel Fundamental
|--------------------------------------------------------------------------
*/

// Redirect root to dashboard
Route::redirect('/', '/dashboard');

// Dashboard Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Resource Route: Products (index, create, store, show, edit, update, destroy)
Route::resource('products', ProductController::class);

// Resource Route: Categories
Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

// Resource Route: Suppliers
Route::resource('suppliers', SupplierController::class)->only(['index', 'store', 'update', 'destroy']);

// Stock Movement Routes (Barang Masuk & Keluar)
Route::prefix('stock')->name('stock.')->group(function () {
    Route::get('/', [StockController::class, 'index'])->name('index');
    Route::get('/in', [StockController::class, 'createIn'])->name('in');
    Route::post('/in', [StockController::class, 'storeIn'])->name('storeIn');
    Route::get('/out', [StockController::class, 'createOut'])->name('out');
    Route::post('/out', [StockController::class, 'storeOut'])->name('storeOut');
});
