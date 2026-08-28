<?php

/*
|--------------------------------------------------------------------------
| Koleksi Buku
|--------------------------------------------------------------------------
|
| Tiga cara memandang koleksi yang sama:
|   - Data Buku    : ditelusuri per rak lalu per laci, mengikuti wujud fisiknya
|   - Temukan Buku : pencarian cepat saat petugas mencari satu judul tertentu
|   - Koleksi Buku : tabel utama tempat buku ditambah, diubah, dan diekspor
|
| Dimuat di dalam grup admin pada routes/web.php (prefix `admin`, nama
| `admin.`, middleware `auth` + `role:admin,super_admin`).
|
*/

use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Admin\DataBukuController;
use App\Http\Controllers\Admin\TemukanBukuController;
use Illuminate\Support\Facades\Route;

Route::get('/data-buku', [DataBukuController::class, 'index'])->name('data-buku');
Route::get('/data-buku/rak/{rakId}', [DataBukuController::class, 'rak'])->name('data-buku.rak')->whereNumber('rakId');
Route::get('/data-buku/rak/{rakId}/tanpa-laci', [DataBukuController::class, 'tanpaLaci'])->name('data-buku.tanpa-laci')->whereNumber('rakId');
Route::get('/data-buku/rak/{rakId}/laci/{laciId}', [DataBukuController::class, 'laci'])->name('data-buku.laci')->whereNumber('rakId')->whereNumber('laciId');
Route::get('/temukan-buku', [TemukanBukuController::class, 'index'])->name('temukan-buku');
// Dipanggil sekali per ketukan tombol (sudah di-debounce di sisi browser), jadi tetap dibatasi.
Route::get('/temukan-buku/saran', [TemukanBukuController::class, 'saran'])->name('temukan-buku.saran')->middleware('throttle:60,1');

Route::get('/buku', [BukuController::class, 'index'])->name('buku');
Route::get('/buku/export/excel', [BukuController::class, 'exportExcel'])->name('buku.export.excel');
Route::get('/buku/export/pdf', [BukuController::class, 'exportPdf'])->name('buku.export.pdf');
Route::post('/buku', [BukuController::class, 'store'])->name('buku.store');
Route::post('/buku/update/{id}', [BukuController::class, 'update'])->name('buku.update')->whereNumber('id');
Route::post('/buku/delete/{id}', [BukuController::class, 'destroy'])->name('buku.delete')->whereNumber('id');
