<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HistoryStockController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VariantsController;
use App\Models\HistoryStock;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'register'])->name('register');

// Home Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Master Data Routes
Route::prefix('master-data')->name('master-data.')->group(function () {
    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('/categories/show/{id}', [CategoryController::class, 'show'])->name('categories.show');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::post('/categories/delete', [CategoryController::class, 'selectedDestroy'])->name('categories.delete');

    // Products
    Route::get('/products', [ProductsController::class, 'index'])->name('products');
    Route::get('/products/show/{id}', [ProductsController::class, 'show'])->name('products.show');
    Route::get('/products/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductsController::class, 'store'])->name('products.store');
    Route::put('/products/{id}', [ProductsController::class, 'update'])->name('products.update');
    Route::post('/products/delete', [ProductsController::class, 'selectedDestroy'])->name('products.delete');

    // Variant
    Route::get('/product/{id}', [VariantsController::class, 'index'])->name('variants');

    Route::get('/variant/{id}/list', [VariantsController::class, 'variantList'])->name('variants.list');
    Route::get('/variant/show/{id}', [VariantsController::class, 'show'])->name('variants.show');
    Route::post('/variant', [VariantsController::class, 'store'])->name('variants.store');
    Route::put('/variant/{id}', [VariantsController::class, 'update'])->name('variants.update');
    Route::delete('/variant/{id}', [VariantsController::class, 'destroy'])->name('variants.delete');
    
    // Product Stock
    Route::get('/stock', [ProductStockController::class, 'index'])->name('stocks');
    Route::get('/stock/create', [ProductStockController::class, 'create'])->name('stocks.create');
    
    // Stock History
    Route::get('/stock-history/{id}', [HistoryStockController::class, 'index'])->name('stocks.history');

    // Transactions
});

Route::prefix('transactions')->name('transactions.')->group(function () {
    Route::get('/history', [TransactionController::class, 'index'])->name('history');
    Route::get('/', [TransactionController::class, 'create'])->name('create');
    Route::get('/supplier', [TransactionController::class, 'supplier'])->name('supplier');
    Route::get('/detail/{id}', [TransactionController::class, 'detail'])->name('detail');
    Route::post('/', [TransactionController::class, 'store'])->name('store');
    Route::post('/export-excel', [TransactionController::class, 'exportExcel'])->name('exportExcel');
    Route::get('/export-pdf/{id}', [TransactionController::class, 'exportPdf'])->name('exportPdf');
});