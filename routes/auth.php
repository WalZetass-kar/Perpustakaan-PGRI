<?php

/*
|--------------------------------------------------------------------------
| Masuk & Keluar Petugas
|--------------------------------------------------------------------------
|
| Halaman login petugas sengaja tidak berada di /login yang mudah ditebak.
| Alamatnya diatur lewat ADMIN_LOGIN_PATH pada berkas .env, dan alamat-alamat
| lazim (/login, /admin/login, /register) dialihkan ke beranda supaya tidak
| membocorkan keberadaan pintu masuk petugas.
|
*/

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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
