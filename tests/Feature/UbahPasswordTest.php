<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Ubah password mandiri di menu Profil.
 *
 * Kolom "Password Saat Ini" sengaja dihapus: pengguna sudah terbukti memegang
 * akun karena sedang login. Test ini memastikan penghapusan itu tidak sekalian
 * melonggarkan hal lain — syarat panjang, konfirmasi, batas peran, dan jejak
 * audit harus tetap berjalan.
 */
class UbahPasswordTest extends TestCase
{
    use RefreshDatabase;

    private function buatUser(string $peran, string $password = 'passwordlama'): User
    {
        $role = Role::firstOrCreate(
            ['name' => $peran],
            ['display_name' => ucfirst($peran)]
        );

        return User::create([
            'name' => 'Petugas Uji', 'email' => $peran.'@uji.test',
            'password' => Hash::make($password),
            'role_id' => $role->id, 'status' => 'active',
        ]);
    }

    public function test_bisa_ganti_password_tanpa_mengisi_password_lama(): void
    {
        $user = $this->buatUser('super_admin');

        $this->actingAs($user)
            ->post(route('admin.profil.ubah-password'), [
                'password'              => 'passwordbaru123',
                'password_confirmation' => 'passwordbaru123',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertTrue(
            Hash::check('passwordbaru123', $user->fresh()->password),
            'Password baru seharusnya sudah tersimpan.'
        );
    }

    public function test_password_lama_tidak_lagi_bisa_dipakai_setelah_diganti(): void
    {
        $user = $this->buatUser('super_admin');

        $this->actingAs($user)->post(route('admin.profil.ubah-password'), [
            'password'              => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $this->assertFalse(Hash::check('passwordlama', $user->fresh()->password));
    }

    public function test_konfirmasi_yang_tidak_cocok_ditolak(): void
    {
        $user = $this->buatUser('super_admin');

        $this->actingAs($user)
            ->post(route('admin.profil.ubah-password'), [
                'password'              => 'passwordbaru123',
                'password_confirmation' => 'salahketik123',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('passwordlama', $user->fresh()->password));
    }

    public function test_password_kurang_dari_delapan_karakter_ditolak(): void
    {
        $user = $this->buatUser('super_admin');

        $this->actingAs($user)
            ->post(route('admin.profil.ubah-password'), [
                'password'              => 'pendek',
                'password_confirmation' => 'pendek',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('passwordlama', $user->fresh()->password));
    }

    public function test_petugas_biasa_tetap_tidak_boleh_ganti_password_sendiri(): void
    {
        $user = $this->buatUser('admin');

        $this->actingAs($user)
            ->post(route('admin.profil.ubah-password'), [
                'password'              => 'passwordbaru123',
                'password_confirmation' => 'passwordbaru123',
            ])
            ->assertForbidden();

        $this->assertTrue(Hash::check('passwordlama', $user->fresh()->password));
    }

    /**
     * Karena password lama tidak lagi diminta, jejak audit menjadi satu-satunya
     * cara menelusuri siapa yang mengganti password dan dari mana.
     */
    public function test_penggantian_password_tercatat_di_audit_log(): void
    {
        $user = $this->buatUser('super_admin');

        $this->actingAs($user)->post(route('admin.profil.ubah-password'), [
            'password'              => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $this->assertDatabaseHas((new AuditLog)->getTable(), [
            'user_id'   => $user->id,
            'aktivitas' => 'UBAH_PASSWORD_MANDIRI',
        ]);
    }
}
