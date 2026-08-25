<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Menjaga agar seluruh area /admin tidak pernah bisa dibuka tanpa sesi login
 * yang sah, termasuk endpoint aksi (POST) dan endpoint export.
 */
class AdminAksesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Halaman admin yang diakses lewat GET. Dipakai untuk menguji bahwa tamu
     * selalu ditolak -- request tamu berhenti di middleware dan tidak pernah
     * sampai ke controller, jadi seluruh rute aman dimasukkan ke sini.
     */
    public static function ruteAdminGet(): array
    {
        return [
            'akar admin'   => ['/admin'],
            'dashboard'    => ['/admin/dashboard'],
            'koleksi buku' => ['/admin/buku'],
            'master kelas' => ['/admin/kelas'],
            'anggota'      => ['/admin/anggota'],
            'audit log'    => ['/admin/audit-log'],
            'export excel' => ['/admin/buku/export/excel'],
            'export pdf'   => ['/admin/buku/export/pdf'],
            'shortcut'     => ['/dashboard'],
        ];
    }

    /**
     * Subset yang benar-benar dirender saat login. `/admin/dashboard` sengaja
     * tidak diikutkan: query grafiknya memakai MONTH()/YEAR() milik MySQL
     * sehingga tidak jalan di sqlite. Itu keterbatasan test, bukan celah akses
     * -- penolakan tamu untuk dashboard tetap diuji lewat ruteAdminGet().
     */
    public static function ruteAdminRender(): array
    {
        $rute = self::ruteAdminGet();
        unset($rute['dashboard']);

        return $rute;
    }

    private function buatAdmin(string $status = 'active'): User
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);

        return User::create([
            'name'     => 'Petugas Uji',
            'email'    => 'petugas.uji@example.test',
            'password' => Hash::make('rahasia123'),
            'role_id'  => $role->id,
            'status'   => $status,
        ]);
    }

    #[DataProvider('ruteAdminGet')]
    public function test_tamu_dilempar_ke_login(string $uri): void
    {
        $this->get($uri)->assertRedirect(route('login'));
    }

    public function test_endpoint_aksi_admin_ditolak_untuk_tamu(): void
    {
        // Tanpa sesi login, POST tidak boleh sampai ke controller.
        foreach (['/admin/buku', '/admin/kelas', '/admin/kategori'] as $uri) {
            $this->post($uri, [])->assertRedirect(route('login'));
        }
    }

    #[DataProvider('ruteAdminRender')]
    public function test_admin_aktif_tetap_bisa_masuk(string $uri): void
    {
        $response = $this->actingAs($this->buatAdmin())->get($uri);

        $this->assertContains(
            $response->getStatusCode(),
            [200, 302],
            "Rute {$uri} seharusnya tetap terbuka untuk admin aktif."
        );
        $response->assertDontSee(route('login'));
    }

    public function test_akun_yang_dinonaktifkan_di_tengah_sesi_langsung_terputus(): void
    {
        $admin = $this->buatAdmin();

        $this->actingAs($admin)->get('/admin/buku')->assertOk();

        // Super Admin menonaktifkan akun ini sementara sesinya masih hidup.
        $admin->update(['status' => 'inactive']);

        $this->get('/admin/buku')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_setelah_logout_area_admin_tertutup_kembali(): void
    {
        $this->actingAs($this->buatAdmin())->get('/admin/buku')->assertOk();

        $this->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();

        $this->get('/admin/buku')->assertRedirect(route('login'));
    }

    public function test_sesi_kedaluwarsa_diperlakukan_sebagai_tamu(): void
    {
        $this->buatAdmin();

        // Login sungguhan, bukan actingAs, supaya sesinya benar-benar terisi.
        $this->post(route('login.post'), [
            'email'    => 'petugas.uji@example.test',
            'password' => 'rahasia123',
        ]);
        $this->get('/admin/buku')->assertOk();

        // Meniru sesi yang habis masa berlakunya: isi sesi hilang, dan guard
        // dipaksa membaca ulang dari sesi yang sudah kosong itu.
        $this->flushSession();
        $this->app['auth']->forgetGuards();

        $this->get('/admin/buku')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_login_gagal_tidak_membuka_area_admin(): void
    {
        $this->buatAdmin();

        $this->post(route('login.post'), [
            'email'    => 'petugas.uji@example.test',
            'password' => 'password-salah',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->get('/admin/buku')->assertRedirect(route('login'));
    }

    public function test_login_benar_mengarah_ke_dashboard(): void
    {
        $this->buatAdmin();

        $this->post(route('login.post'), [
            'email'    => 'petugas.uji@example.test',
            'password' => 'rahasia123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }
}
