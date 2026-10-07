<?php

use App\Http\Controllers\UjianController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UjianController::class, 'index'])->name('upload');

// UBAH BARIS INI: Gunakan Route::match agar bisa menerima request GET dan POST
Route::match(['get', 'post'], '/pilih-jadwal', [UjianController::class, 'preview'])->name('pilih.jadwal');

Route::post('/proses-cetak', [UjianController::class, 'prosesCetak'])->name('proses.cetak');