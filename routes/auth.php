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
Route::post('/' . $adminLoginPath, [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1,login');

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

// Keluar HARUS lewat POST. Kalau GET ikut dilayani, tag <img src="/logout">
// di halaman mana pun — termasuk situs orang lain — cukup untuk melempar
// petugas keluar dari sesinya tanpa ia menekan apa pun.
//
// Tapi menolaknya mentah-mentah membuat alamat ini melempar 405, dan itu
// bukan keadaan yang mustahil dicapai: peramban bisa mengulang navigasi
// bekas logout sebagai GET saat tombol Kembali/Maju ditekan atau saat sesi
// tab dipulihkan, tanpa petugas mengetik apa pun. Yang muncul kemudian adalah
// layar galat — layar debug lengkap bila APP_DEBUG masih menyala.
//
// Jadi GET diterima tapi tidak melakukan apa-apa selain memulangkan ke
// beranda, mengikuti pengaman yang sama seperti /login dan /register di atas.
// Sesi yang sedang berjalan sengaja dibiarkan utuh.
Route::get('/logout', function () {
    return redirect()->route('home');
});
