<?php

/*
|--------------------------------------------------------------------------
| Pengaturan Sistem, Cadangan Data, dan Jejak Audit
|--------------------------------------------------------------------------
|
| Pengaturan dan pencadangan hanya untuk Super Administrator, dengan alasan
| yang sama seperti pada berkas pengguna.php: menyembunyikan menu di sidebar
| tidak menutup URL yang diketik langsung.
|
| Jejak audit sengaja berada DI LUAR grup itu — petugas biasa boleh melihat
| catatan aktivitas, karena isinya justru menjadi pertanggungjawaban bersama.
|
| Dimuat di dalam grup admin pada routes/web.php (prefix `admin`, nama
| `admin.`, middleware `auth` + `role:admin,super_admin`).
|
*/

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\PengaturanController;
use Illuminate\Support\Facades\Route;

Route::middleware('role:super_admin')->group(function () {
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
    Route::post('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    Route::get('/pengaturan/backup-database', [BackupController::class, 'sql'])->name('pengaturan.backup');
    Route::get('/pengaturan/backup-lengkap', [BackupController::class, 'lengkap'])->name('pengaturan.backup-lengkap');
});

Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log');
