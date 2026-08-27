<?php

/*
|--------------------------------------------------------------------------
| Data Pendukung Katalog
|--------------------------------------------------------------------------
|
| Kategori, penulis, penerbit, dan kelas. Keempatnya berpola sama: satu
| halaman daftar dengan tambah/ubah/hapus lewat modal, dan penghapusan
| ditolak selama masih ada buku yang memakainya.
|
| Dimuat di dalam grup admin pada routes/web.php (prefix `admin`, nama
| `admin.`, middleware `auth` + `role:admin,super_admin`).
|
*/

use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\PenerbitController;
use App\Http\Controllers\Admin\PenulisController;
use Illuminate\Support\Facades\Route;

Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori');
Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
Route::post('/kategori/update/{id}', [KategoriController::class, 'update'])->name('kategori.update')->whereNumber('id');
Route::post('/kategori/delete/{id}', [KategoriController::class, 'destroy'])->name('kategori.delete')->whereNumber('id');

Route::get('/penulis', [PenulisController::class, 'index'])->name('penulis');
Route::post('/penulis', [PenulisController::class, 'store'])->name('penulis.store');
Route::post('/penulis/update/{id}', [PenulisController::class, 'update'])->name('penulis.update')->whereNumber('id');
Route::post('/penulis/delete/{id}', [PenulisController::class, 'destroy'])->name('penulis.delete')->whereNumber('id');

Route::get('/penerbit', [PenerbitController::class, 'index'])->name('penerbit');
Route::post('/penerbit', [PenerbitController::class, 'store'])->name('penerbit.store');
Route::post('/penerbit/update/{id}', [PenerbitController::class, 'update'])->name('penerbit.update')->whereNumber('id');
Route::post('/penerbit/delete/{id}', [PenerbitController::class, 'destroy'])->name('penerbit.delete')->whereNumber('id');

Route::get('/kelas', [KelasController::class, 'index'])->name('kelas');
Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
Route::post('/kelas/update/{id}', [KelasController::class, 'update'])->name('kelas.update')->whereNumber('id');
Route::post('/kelas/delete/{id}', [KelasController::class, 'destroy'])->name('kelas.delete')->whereNumber('id');
