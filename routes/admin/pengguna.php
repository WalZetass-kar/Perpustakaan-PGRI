<?php

/*
|--------------------------------------------------------------------------
| Akun Pengelola & Profil (khusus Super Administrator)
|--------------------------------------------------------------------------
|
| Otoritas penuh dipegang Super Administrator. Petugas ber-role `admin`
| tidak boleh menyentuh profil/keamanan akun maupun pengaturan sistem,
| termasuk lewat URL yang diketik langsung — menyembunyikan menunya di
| sidebar saja tidak menutup apa pun.
|
| Dimuat di dalam grup admin pada routes/web.php, lalu dipersempit lagi
| oleh `role:super_admin` di bawah ini.
|
*/

use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\ProfilController;
use Illuminate\Support\Facades\Route;

Route::middleware('role:super_admin')->group(function () {
    // Daftar akun pengelola memuat nama dan email seluruh petugas.
    // Menunya sudah disembunyikan dari petugas biasa di sidebar, tapi
    // rutenya sendiri semula masih terbuka lewat URL yang diketik
    // langsung — persis celah yang sudah ditutup untuk profil dan
    // pengaturan. Aksi ubah/hapusnya memang sudah menolak sendiri;
    // yang bocor adalah halaman daftarnya.
    Route::get('/anggota', [AnggotaController::class, 'index'])->name('anggota');
    Route::post('/anggota', [AnggotaController::class, 'store'])->name('anggota.store');
    Route::post('/anggota/update/{id}', [AnggotaController::class, 'update'])->name('anggota.update')->whereNumber('id');
    Route::post('/anggota/reset-password/{id}', [AnggotaController::class, 'resetPassword'])->name('anggota.reset-password')->whereNumber('id');
    Route::post('/anggota/toggle-status/{id}', [AnggotaController::class, 'toggleStatus'])->name('anggota.toggle-status')->whereNumber('id');
    Route::post('/anggota/delete/{id}', [AnggotaController::class, 'destroy'])->name('anggota.delete')->whereNumber('id');

    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('/profil/ubah-password', [ProfilController::class, 'ubahPassword'])->name('profil.ubah-password');
});
