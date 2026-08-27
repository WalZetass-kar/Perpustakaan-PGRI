<?php

/*
|--------------------------------------------------------------------------
| Sirkulasi
|--------------------------------------------------------------------------
|
| Tiga tahap perjalanan sebuah peminjaman:
|   - peminjaman.request : pengajuan yang masuk dari katalog, menunggu petugas
|   - peminjaman         : yang sedang berjalan, tempat pengembalian dicatat
|   - riwayat            : seluruh transaksi yang pernah terjadi
|
| Dimuat di dalam grup admin pada routes/web.php (prefix `admin`, nama
| `admin.`, middleware `auth` + `role:admin,super_admin`).
|
*/

use App\Http\Controllers\Admin\PeminjamanController;
use App\Http\Controllers\Admin\PengajuanPeminjamanController;
use Illuminate\Support\Facades\Route;

Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman');
Route::get('/peminjaman/export/excel', [PeminjamanController::class, 'exportExcel'])->name('peminjaman.export.excel');
Route::get('/peminjaman/export/pdf', [PeminjamanController::class, 'exportPdf'])->name('peminjaman.export.pdf');
Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
Route::post('/peminjaman/kembali/{id}', [PeminjamanController::class, 'kembali'])->name('peminjaman.kembali')->whereNumber('id');
Route::get('/peminjaman/request', [PengajuanPeminjamanController::class, 'index'])->name('peminjaman.request');
Route::post('/peminjaman/request/{id}/approve', [PengajuanPeminjamanController::class, 'approve'])->name('peminjaman.request.approve')->whereNumber('id');
Route::post('/peminjaman/request/{id}/reject', [PengajuanPeminjamanController::class, 'reject'])->name('peminjaman.request.reject')->whereNumber('id');
Route::get('/riwayat', [PeminjamanController::class, 'riwayat'])->name('riwayat');
