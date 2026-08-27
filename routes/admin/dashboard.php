<?php

/*
|--------------------------------------------------------------------------
| Beranda Area Admin
|--------------------------------------------------------------------------
|
| Dimuat di dalam grup admin pada routes/web.php, jadi prefix `admin`,
| awalan nama `admin.`, dan middleware `auth` + `role:admin,super_admin`
| sudah menempel pada rute di bawah ini.
|
*/

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

// `/admin` polos: arahkan ke dashboard. Karena berada di dalam grup
// auth, pengunjung yang belum login akan tertahan middleware dan
// dilempar ke halaman login, bukan mendapat 404.
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
})->name('index');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
