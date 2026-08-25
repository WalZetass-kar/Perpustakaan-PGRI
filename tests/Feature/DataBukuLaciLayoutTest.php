<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Rak;
use App\Models\RakLaci;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Halaman isi laci (Data Buku -> Rak -> Laci) menampilkan kartu buku yang
 * bentuknya sama dengan mode Grid halaman Koleksi Buku, jadi perlakuan
 * mobile-nya disamakan: dua kolom kiri-kanan sejak layar terkecil, dengan
 * penyesuaian yang sama supaya tidak sesak.
 *
 * Aturannya ditulis sebagai media query, bukan varian Tailwind, karena berkas
 * Tailwind proyek ini sudah di-purge sehingga kelas yang belum terpakai di mana
 * pun tidak ikut ter-generate.
 */
class DataBukuLaciLayoutTest extends TestCase
{
    use RefreshDatabase;

    private function bukaHalamanLaci(): string
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $this->actingAs(User::create([
            'name' => 'Petugas Uji', 'email' => 'uji@uji.test',
            'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]));

        $rak = Rak::create([
            'kode_rak' => 'RAK-UJI-01', 'nama_rak' => 'Rak Pengujian',
            'lokasi' => 'Lantai 1', 'status' => 'aktif',
        ]);
        $laci = RakLaci::create(['rak_id' => $rak->id, 'nomor_laci' => 1, 'nama_laci' => 'Laci Satu']);

        Buku::create([
            'judul' => 'Bumi Manusia', 'isbn' => 'ISBN-'.uniqid(),
            'rak_id' => $rak->id, 'rak_laci_id' => $laci->id, 'tahun_terbit' => 2024,
            'total_quantity' => 3, 'available_quantity' => 3, 'status' => 'tersedia',
        ]);

        return $this->get(route('admin.data-buku.laci', [$rak->id, $laci->id]))
            ->assertOk()
            ->getContent();
    }

    public function test_kartu_buku_dua_kolom_sejak_layar_terkecil(): void
    {
        $html = $this->bukaHalamanLaci();

        $this->assertStringContainsString(
            'kartu-laci-grid grid grid-cols-2',
            $html,
            'Kartu buku di dalam laci harus dua kolom sejak layar terkecil, '
            .'bukan grid-cols-1 yang baru jadi dua kolom di 640px.'
        );
    }

    /**
     * Sampul di-resize berdasarkan lebar saja, jadi tinggi tetap membuatnya
     * terpangkas di sisi kiri-kanan saat kartunya menyempit.
     */
    public function test_sampul_mengikuti_rasio_bukan_tinggi_tetap(): void
    {
        $html = $this->bukaHalamanLaci();

        $this->assertMatchesRegularExpression(
            '/\.kartu-laci-sampul\s*\{[^}]*aspect-ratio:\s*\d+\s*\/\s*\d+;/',
            $html,
            'Sampul harus dikunci ke rasio supaya tingginya ikut lebar kartu.'
        );
    }

    /**
     * Badge kategori panjang seperti "Rekayasa Perangkat Lunak" melebihi lebar
     * sampul di kartu selebar ~150px dan tumpah keluar kartu.
     */
    public function test_badge_di_atas_sampul_dibatasi_lebarnya(): void
    {
        $html = $this->bukaHalamanLaci();

        $this->assertMatchesRegularExpression(
            '/\.kartu-laci-label-kategori\s*\{[^}]*max-width:/',
            $html,
            'Badge kategori harus dibatasi supaya tidak tumpah keluar kartu.'
        );
        $this->assertMatchesRegularExpression(
            '/\.kartu-laci-label-stok\s*>\s*span\s*\{[^}]*white-space:\s*nowrap;/',
            $html,
            'Badge stok tidak boleh pecah dua baris.'
        );
    }

    public function test_tautan_detail_tidak_pecah_dua_baris(): void
    {
        $html = $this->bukaHalamanLaci();

        $this->assertMatchesRegularExpression(
            '/\.kartu-laci-kaki\s*>\s*span:last-child\s*\{[^}]*white-space:\s*nowrap;/',
            $html,
            'Tautan "Detail" harus dikunci satu baris; kode rak yang mengalah.'
        );
    }

    /**
     * Desktop tidak boleh ikut berubah: seluruh penyesuaian wajib terkurung di
     * dalam media query, dan kelas Tailwind untuk layar besar tetap utuh.
     */
    public function test_desktop_tidak_ikut_berubah(): void
    {
        $html = $this->bukaHalamanLaci();

        $this->assertStringContainsString('md:grid-cols-3 lg:grid-cols-4', $html);

        $posisiMedia = strpos($html, '@media (max-width: 639px)');
        $this->assertNotFalse($posisiMedia, 'Media query mobile tidak ditemukan.');

        $sebelumMedia = substr($html, 0, $posisiMedia);
        $this->assertStringNotContainsString(
            '.kartu-laci-sampul {',
            $sebelumMedia,
            'Aturan kartu bocor ke luar media query dan akan mengubah desktop.'
        );
    }
}
