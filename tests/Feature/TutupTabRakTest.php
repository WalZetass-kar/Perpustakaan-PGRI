<?php

namespace Tests\Feature;

use App\Models\Rak;
use App\Models\RakLaci;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Halaman pemilihan laci dibuka di TAB BARU dari menu Data Buku
 * (target="_blank"), sehingga tombol "Tutup Tab" adalah satu-satunya jalan
 * menutupnya dari dalam aplikasi.
 *
 * Tombol itu sempat ber-"hidden sm:flex" sehingga lenyap di bawah 640px, dan
 * petugas yang memakai HP terjebak di tab tanpa jalan keluar.
 */
class TutupTabRakTest extends TestCase
{
    use RefreshDatabase;

    private function bukaHalamanPemilihanLaci(): string
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
        RakLaci::create(['rak_id' => $rak->id, 'nomor_laci' => 1, 'nama_laci' => 'Laci Satu']);

        return $this->get(route('admin.data-buku.rak', $rak->id))->assertOk()->getContent();
    }

    public function test_tombol_tutup_tab_ada(): void
    {
        $html = $this->bukaHalamanPemilihanLaci();

        $this->assertStringContainsString('window.close()', $html);
        $this->assertStringContainsString('Tutup Tab', $html);
    }

    /**
     * Inti perbaikannya: tombol itu tidak boleh lagi disembunyikan di layar
     * kecil. "hidden sm:flex" berarti display:none di bawah 640px.
     */
    public function test_tombol_tutup_tab_tidak_disembunyikan_di_hp(): void
    {
        $html = $this->bukaHalamanPemilihanLaci();

        // Ambil tag <button> yang memuat window.close()
        preg_match('/<button[^>]*window\.close\(\)[^>]*>/', $html, $m);
        $this->assertNotEmpty($m, 'Tombol Tutup Tab tidak ditemukan.');

        $this->assertStringNotContainsString(
            'hidden sm:flex',
            $m[0],
            'Tombol Tutup Tab masih disembunyikan di HP, padahal halaman ini '
            .'dibuka di tab baru sehingga tanpa tombol itu petugas terjebak.'
        );
        $this->assertStringContainsString('flex', $m[0]);
    }

    /**
     * Desktop tidak boleh berubah: di >=640px lebarnya kembali mengikuti isi,
     * tidak melebar penuh seperti di HP.
     */
    public function test_lebar_tombol_kembali_normal_di_desktop(): void
    {
        $html = $this->bukaHalamanPemilihanLaci();

        preg_match('/<button[^>]*window\.close\(\)[^>]*>/', $html, $m);
        $this->assertNotEmpty($m);

        $this->assertStringContainsString('w-full', $m[0], 'Di HP tombol harus selebar penuh.');
        $this->assertStringContainsString('sm:w-auto', $m[0], 'Di desktop lebarnya harus kembali mengikuti isi.');
    }
}
