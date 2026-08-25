<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Menjaga agar Pengaturan Sistem hanya dapat diakses Super Administrator,
 * baik lewat tautan di sidebar maupun lewat URL yang diketik langsung.
 */
class PengaturanAksesTest extends TestCase
{
    use RefreshDatabase;

    private function buat(string $peran): User
    {
        $role = Role::firstOrCreate(['name' => $peran], ['display_name' => $peran]);

        return User::create([
            'name'     => 'Uji ' . $peran,
            'email'    => $peran . '@uji.test',
            'password' => Hash::make('rahasia123'),
            'role_id'  => $role->id,
            'status'   => 'active',
        ]);
    }

    public function test_petugas_ditolak_membuka_pengaturan(): void
    {
        $this->actingAs($this->buat('admin'))
            ->get('/admin/pengaturan')
            ->assertForbidden();
    }

    public function test_petugas_ditolak_menyimpan_pengaturan(): void
    {
        $this->actingAs($this->buat('admin'))
            ->post('/admin/pengaturan', [
                'nama_perpustakaan'  => 'Diretas',
                'durasi_pinjam_hari' => 99,
            ])
            ->assertForbidden();
    }

    public function test_super_admin_tetap_bisa_membuka_pengaturan(): void
    {
        $this->actingAs($this->buat('super_admin'))
            ->get('/admin/pengaturan')
            ->assertOk();
    }

    public function test_menu_pengaturan_tidak_muncul_di_sidebar_petugas(): void
    {
        $this->actingAs($this->buat('admin'))
            ->get('/admin/buku')
            ->assertOk()
            ->assertDontSee('Pengaturan Sistem');
    }

    public function test_menu_pengaturan_muncul_di_sidebar_super_admin(): void
    {
        $this->actingAs($this->buat('super_admin'))
            ->get('/admin/buku')
            ->assertOk()
            ->assertSee('Pengaturan Sistem');
    }
}
