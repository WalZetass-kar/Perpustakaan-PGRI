<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AdminController;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/katalog', [PublicController::class, 'katalog'])->name('katalog');
Route::get('/buku/{id}', [PublicController::class, 'detailBuku'])->name('buku.detail');
Route::get('/api/buku/search-suggestions', [PublicController::class, 'searchSuggestions'])->name('buku.search-suggestions');

Route::get('/aksesperpuspgri', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/aksesperpuspgri', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');

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

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/buku', [AdminController::class, 'bukuIndex'])->name('buku');
        Route::post('/buku', [AdminController::class, 'bukuStore'])->name('buku.store');
        Route::post('/buku/update/{id}', [AdminController::class, 'bukuUpdate'])->name('buku.update');
        Route::post('/buku/delete/{id}', [AdminController::class, 'bukuDestroy'])->name('buku.delete');

        Route::get('/kategori', [AdminController::class, 'kategoriIndex'])->name('kategori');
        Route::post('/kategori', [AdminController::class, 'kategoriStore'])->name('kategori.store');
        Route::post('/kategori/update/{id}', [AdminController::class, 'kategoriUpdate'])->name('kategori.update');
        Route::post('/kategori/delete/{id}', [AdminController::class, 'kategoriDestroy'])->name('kategori.delete');

        Route::get('/penulis', [AdminController::class, 'penulisIndex'])->name('penulis');
        Route::post('/penulis', [AdminController::class, 'penulisStore'])->name('penulis.store');
        Route::post('/penulis/update/{id}', [AdminController::class, 'penulisUpdate'])->name('penulis.update');
        Route::post('/penulis/delete/{id}', [AdminController::class, 'penulisDestroy'])->name('penulis.delete');

        Route::get('/penerbit', [AdminController::class, 'penerbitIndex'])->name('penerbit');
        Route::post('/penerbit', [AdminController::class, 'penerbitStore'])->name('penerbit.store');
        Route::post('/penerbit/update/{id}', [AdminController::class, 'penerbitUpdate'])->name('penerbit.update');
        Route::post('/penerbit/delete/{id}', [AdminController::class, 'penerbitDestroy'])->name('penerbit.delete');

        Route::get('/rak', [AdminController::class, 'rakIndex'])->name('rak');
        Route::post('/rak', [AdminController::class, 'rakStore'])->name('rak.store');
        Route::post('/rak/update/{id}', [AdminController::class, 'rakUpdate'])->name('rak.update');
        Route::post('/rak/delete/{id}', [AdminController::class, 'rakDestroy'])->name('rak.delete');
        Route::post('/rak/{rakId}/laci', [AdminController::class, 'laciStore'])->name('rak.laci.store');
        Route::post('/rak/laci/update/{id}', [AdminController::class, 'laciUpdate'])->name('rak.laci.update');
        Route::post('/rak/laci/delete/{id}', [AdminController::class, 'laciDestroy'])->name('rak.laci.delete');
        Route::get('/rak/{rakId}/lacis', [AdminController::class, 'getLacisByRak'])->name('rak.lacis');

        Route::get('/peminjaman', [AdminController::class, 'peminjamanIndex'])->name('peminjaman');
        Route::post('/peminjaman', [AdminController::class, 'peminjamanStore'])->name('peminjaman.store');
        Route::post('/peminjaman/kembali/{id}', [AdminController::class, 'peminjamanKembali'])->name('peminjaman.kembali');
        Route::get('/riwayat', [AdminController::class, 'riwayatIndex'])->name('riwayat');

        Route::get('/anggota', [AdminController::class, 'anggotaIndex'])->name('anggota');
        Route::post('/anggota', [AdminController::class, 'anggotaStore'])->name('anggota.store');
        Route::post('/anggota/update/{id}', [AdminController::class, 'anggotaUpdate'])->name('anggota.update');
        Route::post('/anggota/reset-password/{id}', [AdminController::class, 'anggotaResetPassword'])->name('anggota.reset-password');
        Route::post('/anggota/toggle-status/{id}', [AdminController::class, 'anggotaToggleStatus'])->name('anggota.toggle-status');
        Route::post('/anggota/delete/{id}', [AdminController::class, 'anggotaDestroy'])->name('anggota.delete');

        Route::get('/pengaturan', [AdminController::class, 'pengaturanIndex'])->name('pengaturan');
        Route::post('/pengaturan', [AdminController::class, 'pengaturanUpdate'])->name('pengaturan.update');
        Route::get('/audit-log', [AdminController::class, 'auditLogIndex'])->name('audit-log');
    });
});
