<?php

/*
|--------------------------------------------------------------------------
| Halaman Pengunjung (OPAC)
|--------------------------------------------------------------------------
|
| Rute yang terbuka tanpa login: beranda, katalog, detail buku, dan pengajuan
| peminjaman dari sisi siswa. Karena bisa diakses siapa saja, rute yang memicu
| kerja server dibatasi lajunya lewat middleware `throttle`.
|
| Perhatikan argumen ketiga pada setiap `throttle` — itu BUKAN hiasan. Tanpa
| argumen itu Laravel menghitung seluruh rute ber-throttle memakai satu kunci
| yang sama (`domain|ip`, tanpa nama rutenya), sehingga jatah rute yang longgar
| ikut menghabiskan jatah rute yang ketat: enam permintaan saran pencarian saja
| sudah cukup membuat halaman login petugas menolak dengan 429. Argumen ketiga
| memberi tiap rute penghitungnya sendiri.
|
*/

use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/katalog', [PublicController::class, 'katalog'])->name('katalog');
Route::get('/buku/{id}', [PublicController::class, 'detailBuku'])->name('buku.detail')->whereNumber('id');
Route::get('/api/buku/search-suggestions', [PublicController::class, 'searchSuggestions'])->name('buku.search-suggestions')->middleware('throttle:60,1,saran-publik');
Route::post('/katalog/ajukan-peminjaman', [PublicController::class, 'ajukanPeminjaman'])->name('katalog.ajukan')->middleware('throttle:10,1,ajukan-pinjam');

// Dipanggil berulang oleh halaman katalog selama siswa menunggu keputusan
// petugas. Hanya melayani pengajuan yang tercatat di sesi peramban pengaju,
// jadi id di alamatnya tidak berguna bagi orang lain. Batas lajunya dilonggarkan
// karena satu penantian wajar memakan puluhan pemeriksaan.
Route::get('/katalog/pengajuan/{id}/status', [PublicController::class, 'statusPengajuan'])
    ->name('katalog.pengajuan.status')
    ->whereNumber('id')
    ->middleware('throttle:90,1,status-pengajuan');
