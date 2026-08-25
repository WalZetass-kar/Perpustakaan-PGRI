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
 * Menjaga alur navigasi Data Buku: Rak -> Laci -> Buku, termasuk jalur khusus
 * bagi buku yang kehilangan lacinya karena laci dihapus.
 */
class DataBukuNavigasiTest extends TestCase
{
    use RefreshDatabase;

    private function login(): User
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $user = User::create([
            'name' => 'Petugas Uji', 'email' => 'uji@uji.test',
            'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]);

        $this->actingAs($user);

        return $user;
    }

    private function buatRak(): Rak
    {
        return Rak::create([
            'kode_rak' => 'RAK-UJI-01',
            'nama_rak' => 'Rak Pengujian',
            'lokasi'   => 'Lantai 1',
            'status'   => 'aktif',
        ]);
    }

    private function buatBuku(Rak $rak, ?RakLaci $laci, string $judul): Buku
    {
        return Buku::create([
            'judul'              => $judul,
            'isbn'               => 'ISBN-' . uniqid(),
            'rak_id'             => $rak->id,
            'rak_laci_id'        => $laci?->id,
            'tahun_terbit'       => 2024,
            'total_quantity'     => 3,
            'available_quantity' => 3,
            'status'             => 'tersedia',
        ]);
    }

    public function test_halaman_rak_menampilkan_daftar_laci_bukan_buku(): void
    {
        $this->login();
        $rak  = $this->buatRak();
        $laci = RakLaci::create(['rak_id' => $rak->id, 'nomor_laci' => 1, 'nama_laci' => 'Laci Satu']);
        $this->buatBuku($rak, $laci, 'Buku Rahasia');

        $res = $this->get(route('admin.data-buku.rak', $rak->id));

        $res->assertOk();
        $res->assertSee('Laci Satu');
        $res->assertSee('Lihat Buku di Laci Ini');
        // Judul buku tidak boleh bocor di halaman daftar laci
        $res->assertDontSee('Buku Rahasia');
    }

    public function test_laci_kosong_tampil_tapi_tidak_bisa_dibuka(): void
    {
        $this->login();
        $rak = $this->buatRak();
        RakLaci::create(['rak_id' => $rak->id, 'nomor_laci' => 2, 'nama_laci' => 'Laci Kosong']);

        $res = $this->get(route('admin.data-buku.rak', $rak->id));

        $res->assertOk();
        $res->assertSee('Laci Kosong');
        $res->assertSee('Belum Ada Buku');
    }

    public function test_halaman_laci_menampilkan_buku_di_laci_itu_saja(): void
    {
        $this->login();
        $rak   = $this->buatRak();
        $laciA = RakLaci::create(['rak_id' => $rak->id, 'nomor_laci' => 1, 'nama_laci' => 'Laci A']);
        $laciB = RakLaci::create(['rak_id' => $rak->id, 'nomor_laci' => 2, 'nama_laci' => 'Laci B']);
        $this->buatBuku($rak, $laciA, 'Buku Di Laci A');
        $this->buatBuku($rak, $laciB, 'Buku Di Laci B');

        $res = $this->get(route('admin.data-buku.laci', [$rak->id, $laciA->id]));

        $res->assertOk();
        $res->assertSee('Buku Di Laci A');
        $res->assertDontSee('Buku Di Laci B');
    }

    public function test_buku_tanpa_laci_punya_jalur_sendiri(): void
    {
        $this->login();
        $rak = $this->buatRak();
        $this->buatBuku($rak, null, 'Buku Terlantar');

        $daftar = $this->get(route('admin.data-buku.rak', $rak->id));
        $daftar->assertOk();
        $daftar->assertSee('Belum Ditempatkan di Laci');

        $isi = $this->get(route('admin.data-buku.tanpa-laci', $rak->id));
        $isi->assertOk();
        $isi->assertSee('Buku Terlantar');
    }

    public function test_kartu_tanpa_laci_disembunyikan_bila_semua_buku_punya_laci(): void
    {
        $this->login();
        $rak  = $this->buatRak();
        $laci = RakLaci::create(['rak_id' => $rak->id, 'nomor_laci' => 1, 'nama_laci' => 'Laci Satu']);
        $this->buatBuku($rak, $laci, 'Buku Tertata');

        $res = $this->get(route('admin.data-buku.rak', $rak->id));

        $res->assertOk();
        $res->assertDontSee('Belum Ditempatkan di Laci');
    }

    public function test_laci_milik_rak_lain_ditolak(): void
    {
        $this->login();
        $rakA = $this->buatRak();
        $rakB = Rak::create(['kode_rak' => 'RAK-UJI-02', 'nama_rak' => 'Rak Lain', 'status' => 'aktif']);
        $laciB = RakLaci::create(['rak_id' => $rakB->id, 'nomor_laci' => 1, 'nama_laci' => 'Laci Rak B']);

        $this->get(route('admin.data-buku.laci', [$rakA->id, $laciB->id]))->assertNotFound();
    }
}
