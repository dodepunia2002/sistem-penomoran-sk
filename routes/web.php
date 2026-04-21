<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════════════════════════
//  PUBLIC ROUTES
// ═══════════════════════════════════════════════════════════

Route::get('/', function () {
    return view('landing');
})->name('landing');

// Redirect /dashboard based on role
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('petugas.dashboard');
})->middleware(['auth'])->name('dashboard');

// ═══════════════════════════════════════════════════════════
//  ADMIN ROUTES
// ═══════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'admin'])
            ->name('dashboard');

        // Pemberian Nomor SK
        Route::get('/pemberian-nomor', [PengajuanController::class, 'index'])
            ->name('pemberian-nomor');
        Route::post('/pengajuan/{pengajuan}/terima', [PengajuanController::class, 'terima'])
            ->name('pengajuan.terima')
            ->middleware('throttle:10,1');
        Route::post('/pengajuan/{pengajuan}/tolak', [PengajuanController::class, 'tolak'])
            ->name('pengajuan.tolak')
            ->middleware('throttle:10,1');

        // Riwayat
        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
        Route::get('/riwayat/cetak', [RiwayatController::class, 'cetak'])->name('riwayat.cetak');
        Route::put('/riwayat/{riwayat}', [RiwayatController::class, 'update'])->name('riwayat.update');
        Route::delete('/riwayat/{riwayat}', [RiwayatController::class, 'destroy'])
            ->name('riwayat.destroy');

        // Manajemen User
        Route::get('/manajemen-user', [UserController::class, 'index'])
            ->name('manajemen-user');
        Route::post('/users', [UserController::class, 'store'])
            ->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])
            ->name('users.update');
        Route::post('/users/{user}/reset', [UserController::class, 'resetPassword'])
            ->name('users.reset');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy');
    });

// ═══════════════════════════════════════════════════════════
//  PETUGAS ROUTES
// ═══════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:petugas'])
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'petugas'])
            ->name('dashboard');

        // Input Data Pengajuan
        Route::get('/input-data', [PengajuanController::class, 'create'])
            ->name('input-data');
        Route::post('/pengajuan', [PengajuanController::class, 'store'])
            ->name('pengajuan.store')
            ->middleware('throttle:5,1');

        // Riwayat Pengajuan
        Route::get('/riwayat', [PengajuanController::class, 'index'])
            ->name('riwayat');
        Route::put('/pengajuan/{pengajuan}', [PengajuanController::class, 'update'])
            ->name('pengajuan.update');
        Route::delete('/pengajuan/{pengajuan}', [PengajuanController::class, 'destroy'])
            ->name('pengajuan.destroy');
    });

// ═══════════════════════════════════════════════════════════
//  PROFILE ROUTES
// ═══════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__ . '/auth.php';
