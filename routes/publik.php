<?php

/*
|--------------------------------------------------------------------------
| Halaman Pengunjung (OPAC)
|--------------------------------------------------------------------------
|
| Rute yang terbuka tanpa login: beranda, katalog, detail buku, dan pengajuan
| peminjaman dari sisi siswa. Karena bisa diakses siapa saja, dua rute yang
| memicu kerja server dibatasi lajunya lewat middleware `throttle`.
|
*/

use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/katalog', [PublicController::class, 'katalog'])->name('katalog');
Route::get('/buku/{id}', [PublicController::class, 'detailBuku'])->name('buku.detail')->whereNumber('id');
Route::get('/api/buku/search-suggestions', [PublicController::class, 'searchSuggestions'])->name('buku.search-suggestions')->middleware('throttle:60,1');
Route::post('/katalog/ajukan-peminjaman', [PublicController::class, 'ajukanPeminjaman'])->name('katalog.ajukan')->middleware('throttle:10,1');
