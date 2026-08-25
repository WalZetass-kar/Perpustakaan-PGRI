<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AdminController;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/katalog', [PublicController::class, 'katalog'])->name('katalog');
Route::get('/buku/{id}', [PublicController::class, 'detailBuku'])->name('buku.detail')->whereNumber('id');
Route::get('/api/buku/search-suggestions', [PublicController::class, 'searchSuggestions'])->name('buku.search-suggestions')->middleware('throttle:60,1');
Route::post('/katalog/ajukan-peminjaman', [PublicController::class, 'ajukanPeminjaman'])->name('katalog.ajukan')->middleware('throttle:10,1');

$adminLoginPath = trim(env('ADMIN_LOGIN_PATH', 'akses-perpustakaan'), '/');
Route::get('/' . $adminLoginPath, [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/' . $adminLoginPath, [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');

Route::get('/login', function () {
    return redirect()->route('home');
});
Route::post('/login', function () {
    return redirect()->route('home');
});
Route::get('/admin/login', function () {
    return redirect()->route('home');
})->name('admin.login.form');
Route::get('/register', function () {
    return redirect()->route('home');
})->name('register');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware('role:admin,super_admin')->group(function () {
        // `/admin` polos: arahkan ke dashboard. Karena berada di dalam grup
        // auth, pengunjung yang belum login akan tertahan middleware dan
        // dilempar ke halaman login, bukan mendapat 404.
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        })->name('index');

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/data-buku', [AdminController::class, 'dataBukuIndex'])->name('data-buku');
        Route::get('/data-buku/rak/{rakId}', [AdminController::class, 'dataBukuByRak'])->name('data-buku.rak')->whereNumber('rakId');
        Route::get('/data-buku/rak/{rakId}/tanpa-laci', [AdminController::class, 'dataBukuTanpaLaci'])->name('data-buku.tanpa-laci')->whereNumber('rakId');
        Route::get('/data-buku/rak/{rakId}/laci/{laciId}', [AdminController::class, 'dataBukuByLaci'])->name('data-buku.laci')->whereNumber('rakId')->whereNumber('laciId');
        Route::get('/temukan-buku', [AdminController::class, 'temukanBukuIndex'])->name('temukan-buku');

        Route::get('/buku', [AdminController::class, 'bukuIndex'])->name('buku');
        Route::get('/buku/export/excel', [AdminController::class, 'bukuExportExcel'])->name('buku.export.excel');
        Route::get('/buku/export/pdf', [AdminController::class, 'bukuExportPdf'])->name('buku.export.pdf');
        Route::post('/buku', [AdminController::class, 'bukuStore'])->name('buku.store');
        Route::post('/buku/update/{id}', [AdminController::class, 'bukuUpdate'])->name('buku.update')->whereNumber('id');
        Route::post('/buku/delete/{id}', [AdminController::class, 'bukuDestroy'])->name('buku.delete')->whereNumber('id');

        Route::get('/kategori', [AdminController::class, 'kategoriIndex'])->name('kategori');
        Route::post('/kategori', [AdminController::class, 'kategoriStore'])->name('kategori.store');
        Route::post('/kategori/update/{id}', [AdminController::class, 'kategoriUpdate'])->name('kategori.update')->whereNumber('id');
        Route::post('/kategori/delete/{id}', [AdminController::class, 'kategoriDestroy'])->name('kategori.delete')->whereNumber('id');

        Route::get('/penulis', [AdminController::class, 'penulisIndex'])->name('penulis');
        Route::post('/penulis', [AdminController::class, 'penulisStore'])->name('penulis.store');
        Route::post('/penulis/update/{id}', [AdminController::class, 'penulisUpdate'])->name('penulis.update')->whereNumber('id');
        Route::post('/penulis/delete/{id}', [AdminController::class, 'penulisDestroy'])->name('penulis.delete')->whereNumber('id');

        Route::get('/penerbit', [AdminController::class, 'penerbitIndex'])->name('penerbit');
        Route::post('/penerbit', [AdminController::class, 'penerbitStore'])->name('penerbit.store');
        Route::post('/penerbit/update/{id}', [AdminController::class, 'penerbitUpdate'])->name('penerbit.update')->whereNumber('id');
        Route::post('/penerbit/delete/{id}', [AdminController::class, 'penerbitDestroy'])->name('penerbit.delete')->whereNumber('id');

        Route::get('/kelas', [AdminController::class, 'kelasIndex'])->name('kelas');
        Route::post('/kelas', [AdminController::class, 'kelasStore'])->name('kelas.store');
        Route::post('/kelas/update/{id}', [AdminController::class, 'kelasUpdate'])->name('kelas.update')->whereNumber('id');
        Route::post('/kelas/delete/{id}', [AdminController::class, 'kelasDestroy'])->name('kelas.delete')->whereNumber('id');

        Route::get('/rak', [AdminController::class, 'rakIndex'])->name('rak');
        Route::post('/rak', [AdminController::class, 'rakStore'])->name('rak.store');
        Route::post('/rak/update/{id}', [AdminController::class, 'rakUpdate'])->name('rak.update')->whereNumber('id');
        Route::post('/rak/delete/{id}', [AdminController::class, 'rakDestroy'])->name('rak.delete')->whereNumber('id');
        Route::post('/rak/{rakId}/laci', [AdminController::class, 'laciStore'])->name('rak.laci.store')->whereNumber('rakId');
        Route::post('/rak/laci/update/{id}', [AdminController::class, 'laciUpdate'])->name('rak.laci.update')->whereNumber('id');
        Route::post('/rak/laci/delete/{id}', [AdminController::class, 'laciDestroy'])->name('rak.laci.delete')->whereNumber('id');
        Route::get('/rak/{rakId}/lacis', [AdminController::class, 'getLacisByRak'])->name('rak.lacis')->whereNumber('rakId');

        Route::get('/peminjaman', [AdminController::class, 'peminjamanIndex'])->name('peminjaman');
        Route::get('/peminjaman/export/excel', [AdminController::class, 'peminjamanExportExcel'])->name('peminjaman.export.excel');
        Route::get('/peminjaman/export/pdf', [AdminController::class, 'peminjamanExportPdf'])->name('peminjaman.export.pdf');
        Route::post('/peminjaman', [AdminController::class, 'peminjamanStore'])->name('peminjaman.store');
        Route::post('/peminjaman/kembali/{id}', [AdminController::class, 'peminjamanKembali'])->name('peminjaman.kembali')->whereNumber('id');
        Route::get('/peminjaman/request', [AdminController::class, 'peminjamanRequestIndex'])->name('peminjaman.request');
        Route::post('/peminjaman/request/{id}/approve', [AdminController::class, 'peminjamanRequestApprove'])->name('peminjaman.request.approve')->whereNumber('id');
        Route::post('/peminjaman/request/{id}/reject', [AdminController::class, 'peminjamanRequestReject'])->name('peminjaman.request.reject')->whereNumber('id');
        Route::get('/riwayat', [AdminController::class, 'riwayatIndex'])->name('riwayat');

        Route::get('/anggota', [AdminController::class, 'anggotaIndex'])->name('anggota');
        Route::post('/anggota', [AdminController::class, 'anggotaStore'])->name('anggota.store');
        Route::post('/anggota/update/{id}', [AdminController::class, 'anggotaUpdate'])->name('anggota.update')->whereNumber('id');
        Route::post('/anggota/reset-password/{id}', [AdminController::class, 'anggotaResetPassword'])->name('anggota.reset-password')->whereNumber('id');
        Route::post('/anggota/toggle-status/{id}', [AdminController::class, 'anggotaToggleStatus'])->name('anggota.toggle-status')->whereNumber('id');
        Route::post('/anggota/delete/{id}', [AdminController::class, 'anggotaDestroy'])->name('anggota.delete')->whereNumber('id');

        // Otoritas penuh dipegang Super Administrator. Petugas ber-role `admin`
        // tidak boleh menyentuh profil/keamanan akun maupun pengaturan sistem,
        // termasuk lewat URL yang diketik langsung — menyembunyikan menunya di
        // sidebar saja tidak menutup apa pun.
        Route::middleware('role:super_admin')->group(function () {
            Route::get('/profil', [AdminController::class, 'profilIndex'])->name('profil');
            Route::post('/profil/ubah-password', [AdminController::class, 'ubahPassword'])->name('profil.ubah-password');

            Route::get('/pengaturan', [AdminController::class, 'pengaturanIndex'])->name('pengaturan');
            Route::post('/pengaturan', [AdminController::class, 'pengaturanUpdate'])->name('pengaturan.update');
            Route::get('/pengaturan/backup-database', [AdminController::class, 'backupDatabase'])->name('pengaturan.backup');
        });
        Route::get('/audit-log', [AdminController::class, 'auditLogIndex'])->name('audit-log');
    });
});
