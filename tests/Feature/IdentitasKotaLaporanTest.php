<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Pengaturan;
use App\Models\Rak;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Kota sekolah dicetak mendahului tanggal pada blok tanda tangan setiap
 * laporan resmi. Nilainya harus benar-benar berasal dari menu Pengaturan.
 *
 * Sebelumnya isian itu tidak pernah ada di formulir, padahal laporannya
 * membacanya — dan ketika kosong, kedua laporan PDF mencetak nama kota bawaan
 * yang tertanam di kode. Akibatnya setiap sekolah yang memakai sistem ini
 * mencetak kota yang salah pada dokumen bertanda tangan, tanpa satu pun cara
 * memperbaikinya dari dalam aplikasi.
 */
class IdentitasKotaLaporanTest extends TestCase
{
    use RefreshDatabase;

    private function loginSuperAdmin(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $this->actingAs(User::create([
            'name' => 'Kepala Perpustakaan', 'email' => 'kota@uji.test', 'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]));
    }

    private function buatBuku(): void
    {
        $rak = Rak::create(['kode_rak' => 'RAK-KOTA', 'nama_rak' => 'Rak Uji', 'lokasi' => 'L1', 'status' => 'aktif']);
        Buku::create([
            'judul' => 'Buku Laporan', 'isbn' => 'ISBN-KOTA', 'rak_id' => $rak->id, 'tahun_terbit' => 2024,
            'total_quantity' => 1, 'available_quantity' => 1, 'status' => 'tersedia',
        ]);
    }

    public function test_isian_kota_tersedia_di_menu_pengaturan(): void
    {
        $this->loginSuperAdmin();

        $this->get(route('admin.pengaturan'))
            ->assertOk()
            ->assertSee('name="kota"', false);
    }

    public function test_kota_yang_disimpan_muncul_di_laporan(): void
    {
        $this->loginSuperAdmin();
        $this->buatBuku();

        $this->post(route('admin.pengaturan.update'), [
            'nama_perpustakaan'  => 'Perpustakaan Uji',
            'nama_sekolah'       => 'SMK Uji Coba',
            'jam_operasional'    => '07.00 - 15.00',
            'durasi_pinjam_hari' => 7,
            'kota'               => 'Sukabumi',
        ])->assertSessionHasNoErrors();

        $this->assertSame('Sukabumi', Pengaturan::ambil('kota'));

        foreach (['admin.buku.export.pdf', 'admin.peminjaman.export.pdf'] as $rute) {
            $this->get(route($rute))->assertOk()->assertSee('Sukabumi');
        }
    }

    public function test_tidak_ada_nama_kota_yang_tertanam_saat_pengaturan_kosong(): void
    {
        $this->loginSuperAdmin();
        $this->buatBuku();

        // Pengaturan sengaja dibiarkan kosong — laporan harus mencetak
        // tanggalnya saja, bukan menebak kota mana pun.
        foreach (['admin.buku.export.pdf', 'admin.peminjaman.export.pdf'] as $rute) {
            $isi = $this->get(route($rute))->assertOk()->getContent();

            $this->assertStringNotContainsString('Pekanbaru', $isi, "Nama kota tertanam masih tercetak di {$rute}.");
            $this->assertStringContainsString(now()->translatedFormat('d F Y'), $isi);
        }
    }

    public function test_katalog_publik_tidak_menanam_nama_kota(): void
    {
        $isi = $this->get(route('katalog'))->assertOk()->getContent();

        $this->assertStringNotContainsString('Pekanbaru', $isi);
        $this->assertStringNotContainsString('ID-RI', $isi, 'Kode wilayah pun tidak boleh dipatok ke satu provinsi.');
    }
}
