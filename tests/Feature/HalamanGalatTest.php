<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Halaman galat yang dilihat pengguna harus berbahasa Indonesia dan seragam
 * dengan tampilan sistem.
 *
 * Laravel hanya membawa halaman bawaan untuk sebagian status, dan yang
 * dibawanya berbahasa Inggris — "Page Expired", "Too Many Requests". Sisanya,
 * seperti 405, jatuh ke halaman kerangka yang tampilannya asing sama sekali.
 * Ketiganya bukan status langka: 429 muncul saat pembatasan laju bekerja, 419
 * saat halaman terbuka terlalu lama, dan 405 saat peramban mengulang navigasi
 * bekas kiriman formulir sebagai GET.
 *
 * Seluruh pemeriksaan mematikan APP_DEBUG lebih dulu, karena halaman inilah
 * yang benar-benar tampil di server sekolah — bukan layar debug.
 */
class HalamanGalatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.debug' => false]);
    }

    public function test_halaman_tidak_ditemukan(): void
    {
        $this->get('/alamat-yang-tidak-pernah-ada')
            ->assertStatus(404)
            ->assertSee('Halaman Tidak Ditemukan')
            ->assertSee('Kembali ke Beranda');
    }

    public function test_cara_akses_tidak_sesuai(): void
    {
        $this->get('/katalog/ajukan-peminjaman')
            ->assertStatus(405)
            ->assertSee('Cara Akses Tidak Sesuai')
            ->assertDontSee('Method Not Allowed');
    }

    public function test_terlalu_banyak_permintaan(): void
    {
        // Batas pengajuan 10/menit; yang ke-11 harus disambut halaman ini.
        for ($i = 0; $i < 11; $i++) {
            $respons = $this->post(route('katalog.ajukan'), []);
        }

        $respons->assertStatus(429)
            ->assertSee('Terlalu Banyak Permintaan')
            ->assertDontSee('Too Many Requests');
    }

    public function test_seluruh_halaman_galat_berbahasa_indonesia(): void
    {
        foreach (['403', '404', '405', '419', '429', '500'] as $kode) {
            $berkas = resource_path("views/errors/{$kode}.blade.php");

            $this->assertFileExists($berkas, "Halaman galat {$kode} milik proyek belum ada.");
            $this->assertStringContainsString('lang="id"', file_get_contents($berkas));
        }
    }
}
