<?php

/**
 * Router untuk `php artisan serve` — dipakai saat satu komputer sekolah
 * dijadikan server jaringan lokal (lihat PANDUAN_SERVER_LOKAL/).
 *
 * Pada pemasangan Apache/cPanel berkas ini tidak dipakai sama sekali: di sana
 * `public/.htaccess` yang mengatur penulisan ulang alamat dan masa simpan
 * cache. Berkas ini menyediakan dua hal yang sama untuk server bawaan PHP,
 * yang tidak mengenal .htaccess:
 *
 *   1. Pembatasan jalur — hanya berkas di dalam public/ (dan folder sampul yang
 *      ditautkan ke sana) yang boleh dilayani.
 *   2. Masa simpan cache di peramban, supaya aset yang tidak berubah tidak
 *      diunduh berulang kali lewat WiFi sekolah.
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

/**
 * Folder yang boleh dilayani, sudah dalam bentuk jalur nyata.
 *
 * `public/storage` adalah tautan simbolik ke storage/app/public — tempat
 * sampul buku disimpan — sehingga jalur nyatanya berada DI LUAR public/.
 * Karena itu ia perlu didaftarkan tersendiri; kalau tidak, seluruh sampul
 * buku ikut tertolak oleh pembatasan di bawah.
 */
$akarDiizinkan = array_values(array_filter([
    realpath(__DIR__ . '/public'),
    realpath(__DIR__ . '/public/storage'),
]));

$berkasDiminta = realpath(__DIR__ . '/public' . $uri);

/**
 * Jalur nyata berkas harus benar-benar berada di dalam salah satu folder yang
 * diizinkan.
 *
 * Tanpa pemeriksaan ini, alamat seperti `/../.env` keluar dari public/ dan
 * server dengan senang hati mengirimkan isinya — termasuk kata sandi basis
 * data dan APP_KEY, yang cukup untuk memalsukan sesi petugas. Server bawaan
 * PHP menyerahkan jalur permintaan apa adanya kepada berkas ini, jadi
 * penyaringannya memang harus dikerjakan di sini.
 */
$diizinkan = false;
if ($berkasDiminta !== false) {
    foreach ($akarDiizinkan as $akar) {
        if ($berkasDiminta === $akar || str_starts_with($berkasDiminta, $akar . DIRECTORY_SEPARATOR)) {
            $diizinkan = true;
            break;
        }
    }
}

if ($uri !== '/' && $diizinkan && is_file($berkasDiminta)) {
    $ekstensi = strtolower(pathinfo($berkasDiminta, PATHINFO_EXTENSION));

    $tipe = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'webp'  => 'image/webp',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'woff2' => 'font/woff2',
        'woff'  => 'font/woff',
        'ttf'   => 'font/ttf',
        'eot'   => 'application/vnd.ms-fontobject',
        'map'   => 'application/json',
        'txt'   => 'text/plain',
    ];

    // Jenis berkas yang tidak dikenali tidak dilayani sebagai berkas statis;
    // biar Laravel yang memutuskan, daripada mengirim isinya begitu saja.
    if (!isset($tipe[$ekstensi])) {
        require_once __DIR__ . '/public/index.php';
        return true;
    }

    $waktuUbah = filemtime($berkasDiminta);
    $ukuran    = filesize($berkasDiminta);
    $etag      = '"' . dechex($waktuUbah) . '-' . dechex($ukuran) . '"';

    /**
     * Masa simpan mengikuti aturan yang sama seperti public/.htaccess.
     *
     * Gambar dan huruf disimpan setahun: nama berkas sampul dibuat acak dan
     * isinya tidak pernah berubah — mengganti sampul menghasilkan nama baru —
     * sedangkan berkas huruf memang tetap. CSS dan JS disimpan lebih singkat
     * karena bisa berubah saat aplikasi diperbarui sementara namanya belum
     * tentu ikut berganti.
     */
    $masaSimpan = in_array($ekstensi, ['css', 'js', 'map'], true)
        ? 60 * 60 * 24 * 7        // satu minggu
        : 60 * 60 * 24 * 365;     // satu tahun

    header('Content-Type: ' . $tipe[$ekstensi]);
    header('Cache-Control: public, max-age=' . $masaSimpan);
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $waktuUbah) . ' GMT');
    header('ETag: ' . $etag);
    header('X-Content-Type-Options: nosniff');

    /**
     * Peramban yang sudah menyimpan berkas ini menanyakan "masih sama?"
     * lewat ETag atau tanggalnya. Menjawab 304 tanpa isi membuat kunjungan
     * berikutnya nyaris tanpa lalu lintas — bagian inilah yang paling terasa
     * di jaringan sekolah, bukan sekadar masa simpannya.
     */
    $etagDikirim = trim($_SERVER['HTTP_IF_NONE_MATCH'] ?? '');
    $sejakDikirim = $_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? null;

    $masihSama = ($etagDikirim !== '' && $etagDikirim === $etag)
        || ($sejakDikirim !== null && @strtotime($sejakDikirim) >= $waktuUbah);

    if ($masihSama) {
        http_response_code(304);
        return true;
    }

    header('Content-Length: ' . $ukuran);
    readfile($berkasDiminta);

    return true;
}

require_once __DIR__ . '/public/index.php';
