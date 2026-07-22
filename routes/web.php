<?php

use App\Http\Controllers\DailyReportPrintController;
use App\Http\Controllers\InternalAuthController;
use App\Http\Controllers\NasabahAuthController;
use App\Http\Controllers\NasabahPortalController;
use App\Http\Controllers\NasabahQrCodeController;
use App\Http\Controllers\TransactionReceiptController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::redirect('/login', '/masuk')->name('login');
Route::get('/masuk', [InternalAuthController::class, 'create'])->name('internal.login');
Route::get('/nasabah/login', [NasabahAuthController::class, 'create'])->name('nasabah.login');

Route::middleware('guest:web')->group(function (): void {
    Route::post('/masuk', [InternalAuthController::class, 'store'])->name('internal.login.store');
});

Route::middleware('guest:nasabah')->group(function (): void {
    Route::post('/nasabah/login', [NasabahAuthController::class, 'store'])->name('nasabah.login.store');
});

Route::middleware('nasabah.auth')->group(function (): void {
    Route::get('/nasabah', NasabahPortalController::class)->name('nasabah.dashboard');
    Route::post('/nasabah/logout', [NasabahAuthController::class, 'destroy'])->name('nasabah.logout');
});

Route::middleware('auth:web')->group(function (): void {
    Route::get('/transaksi/{transaction}/struk', TransactionReceiptController::class)
        ->name('transactions.receipt');
    Route::get('/laporan-harian/{report}/cetak', DailyReportPrintController::class)
        ->name('daily-reports.print');
    Route::get('/admin/nasabah/{nasabah}/qr-code', NasabahQrCodeController::class)
        ->name('admin.nasabah.qr');
});
