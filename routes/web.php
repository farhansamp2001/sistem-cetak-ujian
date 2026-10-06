<?php

use App\Http\Controllers\UjianController;
use Illuminate\Support\Facades\Route;

// // Rute untuk halaman utama (Upload)
// Route::get('/', [UjianController::class, 'index'])->name('upload');

// // // Rute untuk memproses excel dan menampilkan preview
// // // Nama 'preview' inilah yang dicari oleh form di halaman upload
// // Route::post('/preview', [UjianController::class, 'preview'])->name('preview');

// // Rute untuk memproses cetak PDF
// Route::post('/proses-cetak', [UjianController::class, 'prosesCetak'])->name('proses.cetak');


Route::get('/', [UjianController::class, 'index'])->name('upload');
Route::post('/pilih-jadwal', [UjianController::class, 'preview'])->name('pilih.jadwal');
Route::post('/proses-cetak', [UjianController::class, 'prosesCetak'])->name('proses.cetak');