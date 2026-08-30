<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * `server.php` adalah router yang dipakai `php artisan serve` — yaitu cara
 * sistem ini dijalankan ketika satu komputer sekolah dijadikan server jaringan
 * lokal. Berkas itu melayani berkas statis sendiri, di luar Laravel, sehingga
 * middleware maupun rute tidak bisa melindunginya.
 *
 * Yang dijaga di sini ada dua:
 *
 *   1. Berkas di luar public/ tidak boleh terlayani. Sebelum dijaga, alamat
 *      `/../.env` mengembalikan isi berkas .env lengkap dengan kata sandi
 *      basis data dan APP_KEY — cukup untuk memalsukan sesi petugas — dan itu
 *      terbuka bagi siapa pun yang terhubung ke WiFi sekolah.
 *   2. Aset yang tidak berubah dikirim dengan masa simpan dan penanda versi,
 *      sehingga kunjungan berikutnya cukup dijawab 304 tanpa isi.
 *
 * Pengujiannya menjalankan server sungguhan lalu mengirim permintaan HTTP
 * mentah lewat soket — perlu, karena pustaka HTTP biasa merapikan sendiri
 * jalur `..` sehingga serangannya tidak pernah sampai ke server.
 */
class ServerStatikTest extends TestCase
{
    private static $proses = null;
    private static ?int $port = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$port = random_int(8600, 8999);
        $akar = dirname(__DIR__, 2);

        self::$proses = @proc_open(
            sprintf('exec php -S 127.0.0.1:%d %s', self::$port, escapeshellarg($akar . '/server.php')),
            [1 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']],
            $pipa,
            $akar
        );

        for ($i = 0; $i < 50; $i++) {
            $uji = @fsockopen('127.0.0.1', self::$port, $e, $s, 0.2);
            if ($uji) {
                fclose($uji);
                return;
            }
            usleep(100_000);
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (is_resource(self::$proses)) {
            proc_terminate(self::$proses);
            proc_close(self::$proses);
        }

        parent::tearDownAfterClass();
    }

    /** Kirim permintaan HTTP apa adanya, tanpa perapian jalur oleh pustaka. */
    private function mentah(string $jalur, array $header = []): string
    {
        $soket = @fsockopen('127.0.0.1', self::$port, $e, $s, 3);

        if (!$soket) {
            $this->markTestSkipped('Server uji tidak dapat dijalankan di lingkungan ini.');
        }

        $permintaan = "GET {$jalur} HTTP/1.1\r\nHost: 127.0.0.1\r\nConnection: close\r\n";
        foreach ($header as $nama => $isi) {
            $permintaan .= "{$nama}: {$isi}\r\n";
        }

        fwrite($soket, $permintaan . "\r\n");
        $jawaban = stream_get_contents($soket);
        fclose($soket);

        return $jawaban;
    }

    public function test_berkas_di_luar_public_tidak_terlayani(): void
    {
        $jalurBerbahaya = [
            '/../.env',
            '/..%2f.env',
            '/vendor/../../.env',
            '/../composer.json',
            '/../storage/logs/laravel.log',
            '/../../.env',
        ];

        foreach ($jalurBerbahaya as $jalur) {
            $jawaban = $this->mentah($jalur);

            $this->assertStringNotContainsString('APP_KEY', $jawaban, "Isi .env bocor lewat {$jalur}");
            $this->assertStringNotContainsString('DB_PASSWORD', $jawaban, "Kata sandi basis data bocor lewat {$jalur}");
            $this->assertStringNotContainsString('"autoload"', $jawaban, "Berkas proyek bocor lewat {$jalur}");
        }
    }

    public function test_aset_dikirim_dengan_masa_simpan_dan_penanda_versi(): void
    {
        $jawaban = $this->mentah('/vendor/alpine/alpine.min.js');

        $this->assertStringContainsString('200 OK', $jawaban);
        $this->assertMatchesRegularExpression('/Cache-Control: public, max-age=\d+/i', $jawaban);
        $this->assertMatchesRegularExpression('/ETag: "/i', $jawaban);
        $this->assertStringContainsString('Last-Modified:', $jawaban);
    }

    public function test_kunjungan_berikutnya_dijawab_tanpa_mengirim_ulang_isinya(): void
    {
        $pertama = $this->mentah('/vendor/alpine/alpine.min.js');

        preg_match('/ETag: (".*?")/i', $pertama, $cocok);
        $this->assertNotEmpty($cocok, 'Aset harus membawa ETag agar bisa diperiksa ulang.');

        $kedua = $this->mentah('/vendor/alpine/alpine.min.js', ['If-None-Match' => $cocok[1]]);

        $this->assertStringContainsString('304 Not Modified', $kedua);
        $this->assertStringNotContainsString('window.Alpine', $kedua, 'Isi berkas tidak boleh ikut dikirim pada 304.');
    }

    public function test_halaman_aplikasi_tetap_dilayani_laravel(): void
    {
        $this->assertStringContainsString('200 OK', $this->mentah('/'));
    }
}
