<?php

namespace Tests\Feature;

use App\Models\{Buku,Kategori,Rak,Role,User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Navigasi antar halaman sebelumnya memakai tampilan bawaan Laravel yang
 * bersandar pada kelas Tailwind. tailwind.min.css di public/ adalah hasil
 * purge, jadi kelas itu tidak ada dan tombolnya tampil bertumpuk -- plus
 * keterangannya masih "Showing 1 to 12 of 15 results".
 */
class PaginasiLayoutTest extends TestCase
{
    use RefreshDatabase;

    private function isiKoleksi(int $jumlah): void
    {
        $rak = Rak::create(['kode_rak'=>'R','nama_rak'=>'R','lokasi'=>'L','status'=>'aktif']);
        $kategori = Kategori::create(['nama'=>'Sastra','slug'=>'sastra','status'=>'aktif']);

        for ($i = 1; $i <= $jumlah; $i++) {
            Buku::create(['judul'=>"Buku $i",'isbn'=>"ISBN-$i",'rak_id'=>$rak->id,'kategori_id'=>$kategori->id,
                'tahun_terbit'=>2024,'total_quantity'=>2,'available_quantity'=>2,'status'=>'tersedia']);
        }
    }

    private function login(): void
    {
        $role = Role::firstOrCreate(['name'=>'super_admin'],['display_name'=>'Super Administrator']);
        $this->actingAs(User::create(['name'=>'Petugas Uji','email'=>'uji@uji.test',
            'password'=>Hash::make('rahasia123'),'role_id'=>$role->id,'status'=>'active']));
    }

    public function test_katalog_opac_memakai_paginasi_seragam(): void
    {
        $this->isiKoleksi(40);

        $html = $this->get(route('katalog'))->assertOk()->getContent();

        $this->assertStringContainsString('class="paginasi"', $html);
        $this->assertStringContainsString('paginasi__btn--aktif', $html);
        $this->assertStringContainsString('dari <b>40</b> buku', $html);
        $this->assertStringNotContainsString('results', $html, 'Teks bawaan Laravel yang berbahasa Inggris masih terpakai.');
    }

    public function test_temukan_buku_memakai_paginasi_seragam(): void
    {
        $this->isiKoleksi(40);
        $this->login();

        $html = $this->get(route('admin.temukan-buku'))->assertOk()->getContent();

        $this->assertStringContainsString('class="paginasi"', $html);
        $this->assertStringContainsString('dari <b>40</b> buku', $html);
        $this->assertStringNotContainsString('results', $html);
    }

    /**
     * Blok <style>-nya dibungkus @once, jadi tidak boleh berlipat kalau satu
     * halaman kelak memuat dua paginator.
     */
    public function test_gaya_paginasi_hanya_disisipkan_sekali(): void
    {
        $this->isiKoleksi(40);

        $html = $this->get(route('katalog'))->getContent();

        $this->assertSame(1, substr_count($html, '.paginasi__btn {'));
    }
}
