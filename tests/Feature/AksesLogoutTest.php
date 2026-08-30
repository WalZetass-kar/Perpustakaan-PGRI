<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Alamat keluar hanya melayani POST, dan itu bukan kerewelan: kalau GET ikut
 * dilayani, tag <img src="/logout"> di halaman mana pun sudah cukup untuk
 * melempar petugas keluar dari sesinya tanpa ia menekan apa pun.
 *
 * Menolaknya mentah-mentah pun tidak cukup. Peramban bisa mengulang navigasi
 * bekas logout sebagai GET — saat tombol Kembali/Maju ditekan, atau saat sesi
 * tab dipulihkan — tanpa petugas mengetik apa pun, dan yang muncul kemudian
 * adalah layar galat 405; layar debug lengkap bila APP_DEBUG masih menyala.
 * Jadi GET dipulangkan diam-diam ke beranda, seperti /login dan /register.
 */
class AksesLogoutTest extends TestCase
{
    use RefreshDatabase;

    private function buatPetugas(): User
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);

        return User::create([
            'name' => 'Petugas Uji', 'email' => 'keluar@uji.test', 'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]);
    }

    public function test_membuka_alamat_keluar_lewat_get_tidak_menimbulkan_galat(): void
    {
        $this->get('/logout')
            ->assertRedirect(route('home'))
            ->assertSessionHasNoErrors();
    }

    public function test_get_tidak_boleh_melempar_petugas_keluar(): void
    {
        $this->actingAs($this->buatPetugas());

        $this->get('/logout')->assertRedirect(route('home'));

        // Inilah inti penjagaannya: sesi harus tetap hidup, supaya alamat ini
        // tidak bisa dipakai memaksa petugas keluar dari luar sistem.
        $this->assertTrue(Auth::check(), 'Sesi petugas tidak boleh berakhir hanya karena alamat keluar dibuka.');
    }

    public function test_keluar_lewat_post_tetap_bekerja(): void
    {
        $this->actingAs($this->buatPetugas());

        $this->post(route('logout'))->assertRedirect(route('home'));

        $this->assertFalse(Auth::check(), 'Tombol Keluar harus benar-benar mengakhiri sesi.');
    }

    public function test_halaman_405_tersedia_untuk_rute_post_lain(): void
    {
        // Pengajuan peminjaman juga hanya melayani POST dan terbuka untuk umum.
        // Tanpa halaman 405 milik proyek, yang muncul adalah halaman bawaan
        // yang tampilannya berbeda sendiri dari 403/404/500.
        // Di server sekolah APP_DEBUG mati, dan di situlah halaman ini dipakai.
        config(['app.debug' => false]);

        $respons = $this->get('/katalog/ajukan-peminjaman');

        $respons->assertStatus(405);
        $respons->assertSee('405');
        $respons->assertSee('Cara Akses Tidak Sesuai');
        $respons->assertSee('Kembali ke Beranda');
    }
}
