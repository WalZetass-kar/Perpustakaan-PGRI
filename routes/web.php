<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AdminController;

// Public OPAC Catalog Routes
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/katalog', [PublicController::class, 'katalog'])->name('katalog');
Route::get('/buku/{id}', [PublicController::class, 'detailBuku'])->name('buku.detail');

// Authentication Routes (Custom Admin Secret URL: /aksesperpuspgri)
Route::get('/aksesperpuspgri', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/aksesperpuspgri', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');

// Redirect common exposed endpoints to home for stealth security
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

// Authenticated Admin Routes (Seluruh pengguna login adalah Admin Perpustakaan)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        // 1. Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // 2. Manage Buku: Koleksi Buku
        Route::get('/buku', [AdminController::class, 'bukuIndex'])->name('buku');
        Route::post('/buku', [AdminController::class, 'bukuStore'])->name('buku.store');
        Route::post('/buku/update/{id}', [AdminController::class, 'bukuUpdate'])->name('buku.update');
        Route::post('/buku/delete/{id}', [AdminController::class, 'bukuDestroy'])->name('buku.delete');

        // 3. Manage Buku: Kategori
        Route::get('/kategori', [AdminController::class, 'kategoriIndex'])->name('kategori');
        Route::post('/kategori', [AdminController::class, 'kategoriStore'])->name('kategori.store');
        Route::post('/kategori/update/{id}', [AdminController::class, 'kategoriUpdate'])->name('kategori.update');
        Route::post('/kategori/delete/{id}', [AdminController::class, 'kategoriDestroy'])->name('kategori.delete');

        // 4. Manage Buku: Penulis (Master Data)
        Route::get('/penulis', [AdminController::class, 'penulisIndex'])->name('penulis');
        Route::post('/penulis', [AdminController::class, 'penulisStore'])->name('penulis.store');
        Route::post('/penulis/update/{id}', [AdminController::class, 'penulisUpdate'])->name('penulis.update');
        Route::post('/penulis/delete/{id}', [AdminController::class, 'penulisDestroy'])->name('penulis.delete');

        // 5. Manage Buku: Penerbit (Master Data)
        Route::get('/penerbit', [AdminController::class, 'penerbitIndex'])->name('penerbit');
        Route::post('/penerbit', [AdminController::class, 'penerbitStore'])->name('penerbit.store');
        Route::post('/penerbit/update/{id}', [AdminController::class, 'penerbitUpdate'])->name('penerbit.update');
        Route::post('/penerbit/delete/{id}', [AdminController::class, 'penerbitDestroy'])->name('penerbit.delete');

        // 6. Manage Buku: Rak Perpustakaan
        Route::get('/rak', [AdminController::class, 'rakIndex'])->name('rak');
        Route::post('/rak', [AdminController::class, 'rakStore'])->name('rak.store');
        Route::post('/rak/update/{id}', [AdminController::class, 'rakUpdate'])->name('rak.update');
        Route::post('/rak/delete/{id}', [AdminController::class, 'rakDestroy'])->name('rak.delete');

        // 7. Sirkulasi Peminjaman (Same-day loan & return)
        Route::get('/peminjaman', [AdminController::class, 'peminjamanIndex'])->name('peminjaman');
        Route::post('/peminjaman', [AdminController::class, 'peminjamanStore'])->name('peminjaman.store');
        Route::post('/peminjaman/kembali/{id}', [AdminController::class, 'peminjamanKembali'])->name('peminjaman.kembali');
        Route::get('/riwayat', [AdminController::class, 'riwayatIndex'])->name('riwayat');

        // 8. Setting: Pengguna / Anggota
        Route::get('/anggota', [AdminController::class, 'anggotaIndex'])->name('anggota');
        Route::post('/anggota', [AdminController::class, 'anggotaStore'])->name('anggota.store');
        Route::post('/anggota/update/{id}', [AdminController::class, 'anggotaUpdate'])->name('anggota.update');
        Route::post('/anggota/delete/{id}', [AdminController::class, 'anggotaDestroy'])->name('anggota.delete');

        // 9. Setting: Pengaturan Sistem & Audit Log
        Route::get('/pengaturan', [AdminController::class, 'pengaturanIndex'])->name('pengaturan');
        Route::post('/pengaturan', [AdminController::class, 'pengaturanUpdate'])->name('pengaturan.update');
        Route::get('/audit-log', [AdminController::class, 'auditLogIndex'])->name('audit-log');
    });
});
