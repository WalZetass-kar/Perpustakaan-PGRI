<?php

/*
|--------------------------------------------------------------------------
| Daftar Rute Aplikasi
|--------------------------------------------------------------------------
|
| Berkas ini sengaja dibiarkan pendek: isinya hanya kerangka — siapa yang
| boleh masuk ke mana — sedangkan daftar alamatnya sendiri dipecah per
| bagian di berkas-berkas berikut.
|
|   routes/publik.php        halaman pengunjung (OPAC)
|   routes/auth.php          masuk & keluar petugas
|   routes/admin/            seluruh halaman petugas, satu berkas per bagian
|
| Berkas di dalam routes/admin/ dimuat dari dalam grup admin di bawah,
| sehingga rute di dalamnya otomatis mewarisi prefix `admin`, awalan nama
| `admin.`, serta middleware `auth` dan `role:admin,super_admin`. Jadi
| menambah satu rute admin baru cukup di berkas bagiannya, tanpa perlu
| menuliskan lagi prefix maupun middleware-nya.
|
*/

use Illuminate\Support\Facades\Route;

require __DIR__ . '/publik.php';
require __DIR__ . '/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware('role:admin,super_admin')->group(function () {
        require __DIR__ . '/admin/dashboard.php';
        require __DIR__ . '/admin/buku.php';
        require __DIR__ . '/admin/master-data.php';
        require __DIR__ . '/admin/rak.php';
        require __DIR__ . '/admin/peminjaman.php';
        require __DIR__ . '/admin/pengguna.php';
        require __DIR__ . '/admin/sistem.php';
    });
});
