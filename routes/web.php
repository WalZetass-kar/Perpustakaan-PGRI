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

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/data-buku', [AdminController::class, 'dataBukuIndex'])->name('data-buku');
        Route::get('/temukan-buku', [AdminController::class, 'temukanBukuIndex'])->name('temukan-buku');

        Route::get('/buku', [AdminController::class, 'bukuIndex'])->name('buku');
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

        Route::get('/rak', [AdminController::class, 'rakIndex'])->name('rak');
        Route::post('/rak', [AdminController::class, 'rakStore'])->name('rak.store');
        Route::post('/rak/update/{id}', [AdminController::class, 'rakUpdate'])->name('rak.update')->whereNumber('id');
        Route::post('/rak/delete/{id}', [AdminController::class, 'rakDestroy'])->name('rak.delete')->whereNumber('id');
        Route::post('/rak/{rakId}/laci', [AdminController::class, 'laciStore'])->name('rak.laci.store')->whereNumber('rakId');
        Route::post('/rak/laci/update/{id}', [AdminController::class, 'laciUpdate'])->name('rak.laci.update')->whereNumber('id');
        Route::post('/rak/laci/delete/{id}', [AdminController::class, 'laciDestroy'])->name('rak.laci.delete')->whereNumber('id');
        Route::get('/rak/{rakId}/lacis', [AdminController::class, 'getLacisByRak'])->name('rak.lacis')->whereNumber('rakId');

        Route::get('/peminjaman', [AdminController::class, 'peminjamanIndex'])->name('peminjaman');
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

        Route::get('/pengaturan', [AdminController::class, 'pengaturanIndex'])->name('pengaturan');
        Route::post('/pengaturan', [AdminController::class, 'pengaturanUpdate'])->name('pengaturan.update');
        Route::get('/audit-log', [AdminController::class, 'auditLogIndex'])->name('audit-log');
    });
});
