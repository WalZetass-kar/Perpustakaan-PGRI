<?php

/*
|--------------------------------------------------------------------------
| Rak & Laci
|--------------------------------------------------------------------------
|
| Denah penyimpanan fisik perpustakaan. Satu rak berisi beberapa laci, dan
| setiap buku menempel pada salah satu laci -- inilah yang membuat halaman
| Temukan Buku bisa menunjukkan letak sebuah judul.
|
| `rak.lacis` melayani permintaan AJAX dari form buku: saat rak dipilih,
| daftar lacinya diambil tanpa memuat ulang halaman.
|
| Dimuat di dalam grup admin pada routes/web.php (prefix `admin`, nama
| `admin.`, middleware `auth` + `role:admin,super_admin`).
|
*/

use App\Http\Controllers\Admin\LaciController;
use App\Http\Controllers\Admin\RakController;
use Illuminate\Support\Facades\Route;

Route::get('/rak', [RakController::class, 'index'])->name('rak');
Route::post('/rak', [RakController::class, 'store'])->name('rak.store');
Route::post('/rak/update/{id}', [RakController::class, 'update'])->name('rak.update')->whereNumber('id');
Route::post('/rak/delete/{id}', [RakController::class, 'destroy'])->name('rak.delete')->whereNumber('id');
Route::post('/rak/{rakId}/laci', [LaciController::class, 'store'])->name('rak.laci.store')->whereNumber('rakId');
Route::post('/rak/laci/update/{id}', [LaciController::class, 'update'])->name('rak.laci.update')->whereNumber('id');
Route::post('/rak/laci/delete/{id}', [LaciController::class, 'destroy'])->name('rak.laci.delete')->whereNumber('id');
Route::get('/rak/{rakId}/lacis', [LaciController::class, 'byRak'])->name('rak.lacis')->whereNumber('rakId');
