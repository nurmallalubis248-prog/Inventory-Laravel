<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminOnly;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class)->except(['show', 'create']);
    Route::resource('suppliers', SupplierController::class)->except(['show', 'create']);

    Route::resource('stock-in', StockInController::class)
        ->parameters(['stock-in' => 'movement'])
        ->except(['show', 'create']);
    Route::resource('stock-out', StockOutController::class)
        ->parameters(['stock-out' => 'movement'])
        ->except(['show', 'create']);

    Route::get('/history', HistoryController::class)->name('history');
    Route::get('/reports', ReportController::class)->name('reports');

    // Khusus Admin
    Route::middleware(AdminOnly::class)->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'create']);
    });
});
