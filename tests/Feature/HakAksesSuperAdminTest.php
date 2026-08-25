<?php

namespace Tests\Feature;

use App\Models\Pengaturan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Profil & Keamanan dan Pengaturan Sistem adalah wewenang penuh Super
 * Administrator. Petugas ber-role `admin` tidak boleh menjangkaunya.
 *
 * Menyembunyikan menunya di sidebar saja tidak cukup — sebelum ini
 * pengaturanIndex dan pengaturanUpdate sama sekali tidak punya penjagaan
 * peran, sehingga admin biasa bisa mengubah pengaturan sistem hanya dengan
 * mengetik alamatnya. Test di bawah menjaga lubang itu tetap tertutup.
 */
class HakAksesSuperAdminTest extends TestCase
{
    use RefreshDatabase;

    private function login(string $peran): User
    {
        $role = Role::firstOrCreate(
            ['name' => $peran],
            ['display_name' => ucfirst($peran)]
        );

        $user = User::create([
            'name' => 'Petugas '.$peran, 'email' => $peran.'@uji.test',
            'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]);

        $this->actingAs($user);

        return $user;
    }

    // ------------------------------------------------- admin biasa: ditolak

    public function test_admin_biasa_ditolak_membuka_profil(): void
    {
        $this->login('admin');
        $this->get(route('admin.profil'))->assertForbidden();
    }

    public function test_admin_biasa_ditolak_membuka_pengaturan(): void
    {
        $this->login('admin');
        $this->get(route('admin.pengaturan'))->assertForbidden();
    }

    public function test_admin_biasa_ditolak_mengunduh_backup(): void
    {
        $this->login('admin');
        $this->get(route('admin.pengaturan.backup'))->assertForbidden();
    }

    /**
     * Inti perbaikannya: bukan cuma halamannya yang tertutup, tapi juga
     * pengirimannya. Kalau ini bocor, admin biasa bisa mengganti identitas
     * perpustakaan dan durasi pinjam tanpa pernah membuka halamannya.
     */
    public function test_admin_biasa_tidak_bisa_mengubah_pengaturan_lewat_kiriman_langsung(): void
    {
        Pengaturan::create([
            'key' => 'nama_perpustakaan', 'value' => 'Perpustakaan Asli',
            'label' => 'Nama Perpustakaan', 'tipe' => 'text',
        ]);

        $this->login('admin');

        $this->post(route('admin.pengaturan.update'), [
            'nama_perpustakaan' => 'Diubah Diam-diam',
        ])->assertForbidden();

        $this->assertSame(
            'Perpustakaan Asli',
            Pengaturan::where('key', 'nama_perpustakaan')->value('value'),
            'Pengaturan tidak boleh berubah oleh admin biasa.'
        );
    }

    public function test_admin_biasa_tidak_bisa_mengganti_passwordnya_lewat_kiriman_langsung(): void
    {
        $user = $this->login('admin');

        $this->post(route('admin.profil.ubah-password'), [
            'password'              => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ])->assertForbidden();

        $this->assertTrue(Hash::check('rahasia123', $user->fresh()->password));
    }

    // ------------------------------------------------ admin biasa: menunya

    public function test_menu_profil_dan_pengaturan_tidak_muncul_untuk_admin_biasa(): void
    {
        $this->login('admin');
        $html = $this->get(route('admin.peminjaman'))->assertOk()->getContent();

        $this->assertStringNotContainsString('Profil & Keamanan', $html);
        $this->assertStringNotContainsString('Pengaturan Sistem', $html);
        $this->assertStringNotContainsString('/admin/pengaturan', $html);
        $this->assertStringNotContainsString('/admin/profil', $html);
    }

    // -------------------------------------------------- super admin: boleh

    public function test_super_admin_tetap_bisa_membuka_keduanya(): void
    {
        $this->login('super_admin');

        $this->get(route('admin.profil'))->assertOk();
        $this->get(route('admin.pengaturan'))->assertOk();
    }

    public function test_menu_profil_dan_pengaturan_tetap_muncul_untuk_super_admin(): void
    {
        $this->login('super_admin');
        $html = $this->get(route('admin.peminjaman'))->assertOk()->getContent();

        $this->assertStringContainsString('Profil & Keamanan', $html);
        $this->assertStringContainsString('Pengaturan Sistem', $html);
    }
}
