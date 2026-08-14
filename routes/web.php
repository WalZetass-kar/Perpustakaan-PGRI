<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PustakawanController;
use App\Http\Controllers\AdminController;

// Public Routes
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/katalog', [PublicController::class, 'katalog'])->name('katalog');
Route::get('/buku/{id}', [PublicController::class, 'detailBuku'])->name('buku.detail');

// Auth Routes — dengan Rate Limiting (VULN-002: brute force protection)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginSiswa'])->middleware('throttle:5,1');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'registerSiswa'])->name('register.submit')->middleware('throttle:5,1');

Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login.form');
Route::post('/admin/login', [AuthController::class, 'loginAdmin'])->name('admin.login')->middleware('throttle:3,1');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Router Redirect
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role->name ?? 'mahasiswa';
        if ($role === 'admin') return redirect()->route('admin.dashboard');
        if ($role === 'pustakawan') return redirect()->route('pustakawan.dashboard');
        return redirect()->route('mahasiswa.dashboard');
    })->name('dashboard');

    // --- MAHASISWA / SISWA ROUTES --- //
    // VULN-001 FIX: role:mahasiswa middleware prevents admin/pustakawan from accessing student routes
    Route::prefix('mahasiswa')->name('mahasiswa.')->middleware(['role:mahasiswa'])->group(function () {
        Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
        Route::get('/peminjaman', [MahasiswaController::class, 'peminjamanSaya'])->name('peminjaman');
        Route::post('/peminjaman/perpanjang/{id}', [MahasiswaController::class, 'perpanjangPeminjaman'])->name('peminjaman.perpanjang');
        Route::get('/riwayat', [MahasiswaController::class, 'riwayat'])->name('riwayat');
        Route::get('/reservasi', [MahasiswaController::class, 'reservasi'])->name('reservasi');
        Route::post('/reservasi/buat/{bukuId}', [MahasiswaController::class, 'buatReservasi'])->name('reservasi.buat');
        Route::post('/reservasi/batalkan/{id}', [MahasiswaController::class, 'batalkanReservasi'])->name('reservasi.batalkan');
        Route::get('/denda', [MahasiswaController::class, 'denda'])->name('denda');
        Route::get('/kartu', [MahasiswaController::class, 'kartuPerpustakaan'])->name('kartu');
        Route::get('/notifikasi', [MahasiswaController::class, 'notifikasi'])->name('notifikasi');
        Route::get('/profil', [MahasiswaController::class, 'profil'])->name('profil');
        Route::post('/profil', [MahasiswaController::class, 'updateProfil'])->name('profil.update');
    });

    // --- PUSTAKAWAN ROUTES --- //
    // VULN-001 FIX: role:admin,pustakawan — siswa tidak bisa akses
    Route::prefix('pustakawan')->name('pustakawan.')->middleware(['role:admin,pustakawan'])->group(function () {
        Route::get('/dashboard', [PustakawanController::class, 'dashboard'])->name('dashboard');
        Route::get('/peminjaman', [PustakawanController::class, 'peminjamanForm'])->name('peminjaman');
        Route::post('/peminjaman', [PustakawanController::class, 'prosesPeminjaman']);
        Route::get('/pengembalian', [PustakawanController::class, 'pengembalianForm'])->name('pengembalian');
        Route::post('/pengembalian', [PustakawanController::class, 'prosesPengembalian']);
        Route::get('/anggota', [PustakawanController::class, 'anggotaIndex'])->name('anggota');
        Route::get('/buku', [PustakawanController::class, 'bukuIndex'])->name('buku');
        Route::get('/eksemplar', [PustakawanController::class, 'eksemplarIndex'])->name('eksemplar');
        Route::get('/rak', [PustakawanController::class, 'rakIndex'])->name('rak');
        Route::get('/reservasi', [PustakawanController::class, 'reservasiIndex'])->name('reservasi');
        Route::post('/reservasi/proses/{id}', [PustakawanController::class, 'prosesReservasi'])->name('reservasi.proses');
        Route::get('/denda', [PustakawanController::class, 'dendaIndex'])->name('denda');
        Route::post('/denda', [PustakawanController::class, 'dendaStore'])->name('denda.store');
        Route::post('/denda/bayar/{id}', [PustakawanController::class, 'bayarDenda'])->name('denda.bayar');
    });

    // --- ADMIN ROUTES (FULL CRUD) --- //
    // VULN-001 FIX: role:admin — hanya admin yang bisa akses
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Buku CRUD
        Route::get('/buku', [AdminController::class, 'bukuIndex'])->name('buku');
        Route::post('/buku', [AdminController::class, 'bukuStore'])->name('buku.store');
        Route::post('/buku/update/{id}', [AdminController::class, 'bukuUpdate'])->name('buku.update');
        Route::post('/buku/delete/{id}', [AdminController::class, 'bukuDestroy'])->name('buku.delete');

        // Kategori CRUD
        Route::get('/kategori', [AdminController::class, 'kategoriIndex'])->name('kategori');
        Route::post('/kategori', [AdminController::class, 'kategoriStore'])->name('kategori.store');
        Route::post('/kategori/update/{id}', [AdminController::class, 'kategoriUpdate'])->name('kategori.update');
        Route::post('/kategori/delete/{id}', [AdminController::class, 'kategoriDestroy'])->name('kategori.delete');

        // Rak CRUD
        Route::get('/rak', [AdminController::class, 'rakIndex'])->name('rak');
        Route::post('/rak', [AdminController::class, 'rakStore'])->name('rak.store');
        Route::post('/rak/update/{id}', [AdminController::class, 'rakUpdate'])->name('rak.update');
        Route::post('/rak/delete/{id}', [AdminController::class, 'rakDestroy'])->name('rak.delete');

        // Eksemplar CRUD
        Route::get('/eksemplar', [AdminController::class, 'eksemplarIndex'])->name('eksemplar');
        Route::get('/eksemplar/cetak-barcode/{id?}', [AdminController::class, 'cetakBarcodeEksemplar'])->name('eksemplar.cetak_barcode');
        Route::post('/eksemplar', [AdminController::class, 'eksemplarStore'])->name('eksemplar.store');
        Route::post('/eksemplar/update/{id}', [AdminController::class, 'eksemplarUpdate'])->name('eksemplar.update');
        Route::post('/eksemplar/delete/{id}', [AdminController::class, 'eksemplarDestroy'])->name('eksemplar.delete');

        // Anggota & User Management CRUD
        Route::get('/anggota', [AdminController::class, 'anggotaIndex'])->name('anggota');
        Route::post('/anggota', [AdminController::class, 'anggotaStore'])->name('anggota.store');
        Route::post('/anggota/update/{id}', [AdminController::class, 'anggotaUpdate'])->name('anggota.update');
        Route::post('/anggota/delete/{id}', [AdminController::class, 'anggotaDestroy'])->name('anggota.delete');

        // Denda Management
        Route::post('/denda', [AdminController::class, 'dendaStore'])->name('denda.store');
        Route::post('/denda/bayar/{id}', [AdminController::class, 'dendaBayar'])->name('denda.bayar');

        // Audit, Pengaturan & Laporan
        Route::get('/laporan', [AdminController::class, 'laporanIndex'])->name('laporan');
        Route::get('/laporan/cetak', [AdminController::class, 'laporanCetak'])->name('laporan.cetak');
        Route::get('/audit-log', [AdminController::class, 'auditLogIndex'])->name('audit-log');
        Route::get('/pengaturan', [AdminController::class, 'pengaturanIndex'])->name('pengaturan');
        Route::post('/pengaturan', [AdminController::class, 'pengaturanUpdate'])->name('pengaturan.update');
    });
});
