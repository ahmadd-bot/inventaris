<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\DetailBarangController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index']) 
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::resource('user', UserController::class);
    Route::resource('kategori', KategoriController::class);
    Route::resource('lokasi', LokasiController::class);
    Route::get('/barang/laporan', [BarangController::class, 'cetakLaporan'])->name('barang.laporan');
    Route::resource('barang', BarangController::class);
    
    // ===== ROUTES PEMINJAMAN - CUSTOM ROUTES HARUS SEBELUM RESOURCE =====
    Route::get('peminjaman/create-not-per-unit', [PeminjamanController::class, 'createNotPerUnit'])
        ->name('peminjaman.createNotPerUnit');
    Route::post('peminjaman/store-not-per-unit', [PeminjamanController::class, 'storeNotPerUnit'])
        ->name('peminjaman.storeNotPerUnit');
    
    // Resource route SETELAH custom routes
    Route::get('/peminjaman/cetak-laporan', [PeminjamanController::class, 'cetakLaporan'])->name('peminjaman.cetakLaporan');
    Route::resource('peminjaman', PeminjamanController::class);
    Route::post('peminjaman/{peminjaman}/kembalikan', [PeminjamanController::class, 'kembalikan'])
        ->name('peminjaman.kembalikan');
    
    // Routes Kelola Unit Barang
    Route::get('barang/{barang}/units', [DetailBarangController::class, 'index'])
        ->name('barang.units.index');
    Route::post('barang/{barang}/units', [DetailBarangController::class, 'store'])
        ->name('barang.units.store');
    Route::post('barang/{barang}/units/bulk', [DetailBarangController::class, 'storeBulk'])
        ->name('barang.units.storeBulk');
    Route::put('barang/{barang}/units/{detailBarang}', [DetailBarangController::class, 'update'])
        ->name('barang.units.update');
    Route::delete('barang/{barang}/units/{detailBarang}', [DetailBarangController::class, 'destroy'])
        ->name('barang.units.destroy');
    Route::get('barang/{barang}/units/{detailBarang}/show', [DetailBarangController::class, 'show'])
        ->name('barang.units.show');
});

require __DIR__.'/auth.php';