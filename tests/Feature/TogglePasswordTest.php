<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Menjaga agar SETIAP kolom kata sandi di seluruh sistem punya tombol mata
 * untuk melihat/menyembunyikan isinya (kebutuhan aksesibilitas petugas).
 *
 * Kalau nanti ada form password baru yang ditulis pakai <input type="password">
 * mentah dan bukan <x-input-password>, test ini yang akan menangkapnya.
 */
class TogglePasswordTest extends TestCase
{
    use RefreshDatabase;

    private function loginSuperAdmin(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $this->actingAs(User::create([
            'name' => 'Petugas Uji', 'email' => 'uji@uji.test',
            'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]));
    }

    /** Ambil semua tag <input ...> dari sebuah HTML. */
    private function semuaInput(string $html): array
    {
        preg_match_all('/<input\b[^>]*>/i', $html, $m);

        return $m[0];
    }

    /**
     * Setiap input bertipe password wajib punya binding Alpine :type, karena
     * itulah yang membuat tombol mata bisa membuka samaran teksnya.
     */
    private function pastikanSemuaPasswordPunyaToggle(string $html, string $halaman): void
    {
        $kolomPassword = array_values(array_filter(
            $this->semuaInput($html),
            fn ($tag) => str_contains($tag, 'type="password"')
        ));

        $this->assertNotEmpty($kolomPassword, "Halaman {$halaman} tidak punya kolom password sama sekali.");

        foreach ($kolomPassword as $tag) {
            $this->assertStringContainsString(
                ":type=\"showPass ? 'text' : 'password'\"",
                $tag,
                "Kolom password di halaman {$halaman} belum memakai tombol mata: {$tag}"
            );
        }

        // Satu tombol mata untuk satu kolom password.
        $jumlahTombol = preg_match_all('/:aria-pressed="showPass/', $html);
        $this->assertSame(
            count($kolomPassword),
            $jumlahTombol,
            "Jumlah tombol mata di halaman {$halaman} tidak sama dengan jumlah kolom password."
        );
    }

    public function test_halaman_login_punya_tombol_mata(): void
    {
        $html = $this->get(route('login'))->assertOk()->getContent();

        $this->pastikanSemuaPasswordPunyaToggle($html, 'login');
    }

    public function test_halaman_profil_punya_tombol_mata_di_dua_kolom(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.profil'))->assertOk()->getContent();

        $this->pastikanSemuaPasswordPunyaToggle($html, 'profil');
        $this->assertSame(
            2,
            substr_count($html, 'type="password"'),
            'Halaman profil harusnya tinggal 2 kolom: password baru dan konfirmasinya.'
        );
        $this->assertStringNotContainsString(
            'current_password',
            $html,
            'Kolom "Password Saat Ini" sudah dihapus, seharusnya tidak muncul lagi.'
        );
    }

    public function test_halaman_pengelola_punya_tombol_mata_di_tiga_kolom(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.anggota'))->assertOk()->getContent();

        $this->pastikanSemuaPasswordPunyaToggle($html, 'pengelola');
        $this->assertSame(3, substr_count($html, 'type="password"'), 'Halaman pengelola harusnya punya 3 kolom password.');
    }

    /**
     * Tombolnya harus bisa dijangkau dan dimengerti tanpa melihat layar:
     * punya label, punya status ditekan, dan ikonnya diabaikan pembaca layar.
     */
    public function test_tombol_mata_punya_atribut_aksesibilitas(): void
    {
        $html = $this->get(route('login'))->assertOk()->getContent();

        $this->assertStringContainsString('aria-label="Tampilkan kata sandi"', $html);
        $this->assertStringContainsString(":aria-label=\"showPass ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'\"", $html);
        $this->assertStringContainsString(':aria-pressed="showPass', $html);
        $this->assertStringContainsString('aria-hidden="true"', $html);
    }

    /**
     * Ruang untuk tombolnya harus disediakan (pr-11), kalau tidak ikon mata
     * akan menimpa teks sandi yang sedang diketik.
     */
    public function test_kolom_password_diberi_ruang_untuk_tombol(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.profil'))->assertOk()->getContent();

        foreach ($this->semuaInput($html) as $tag) {
            if (str_contains($tag, 'type="password"')) {
                $this->assertStringContainsString('pr-11', $tag, "Kolom password belum diberi ruang kanan: {$tag}");
            }
        }
    }
}
