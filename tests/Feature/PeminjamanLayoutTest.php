<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Menjaga struktur toolbar halaman sirkulasi peminjaman. Sebelumnya ada satu
 * </div> yang hilang sehingga toolbar mobile ikut terkurung di dalam wadah
 * "hidden sm:flex" dan kartu tabel tersarang di dalam kartu toolbar.
 */
class PeminjamanLayoutTest extends TestCase
{
    use RefreshDatabase;

    private function login(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $this->actingAs(User::create([
            'name' => 'Petugas Uji', 'email' => 'uji@uji.test',
            'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]));
    }

    public function test_halaman_peminjaman_render(): void
    {
        $this->login();
        $this->get(route('admin.peminjaman'))->assertOk();
    }

    public function test_markup_div_seimbang_di_luar_script(): void
    {
        $this->login();
        $html = $this->get(route('admin.peminjaman'))->getContent();

        $tanpaScript = preg_replace('/<script\b.*?<\/script>/s', '', $html);

        $buka  = preg_match_all('/<div\b/', $tanpaScript);
        $tutup = preg_match_all('/<\/div>/', $tanpaScript);

        $this->assertSame(
            $buka,
            $tutup,
            "Jumlah <div> dan </div> tidak seimbang: {$buka} dibuka, {$tutup} ditutup."
        );
    }

    public function test_toolbar_mobile_tidak_tersarang_di_wadah_khusus_desktop(): void
    {
        $this->login();
        $html = $this->get(route('admin.peminjaman'))->getContent();

        $posisiDesktop = strpos($html, 'hidden sm:flex sm:flex-row items-center justify-between');
        $posisiMobile  = strpos($html, 'sm:hidden space-y-3');

        $this->assertNotFalse($posisiDesktop, 'Wadah toolbar desktop tidak ditemukan.');
        $this->assertNotFalse($posisiMobile, 'Wadah toolbar mobile tidak ditemukan.');

        // Hitung kedalaman div tepat sebelum toolbar mobile dibuka. Bila toolbar
        // desktop sudah tertutup dengan benar, keduanya berada pada kedalaman sama.
        $sebelumDesktop = substr($html, 0, $posisiDesktop);
        $sebelumMobile  = substr($html, 0, $posisiMobile);

        $kedalaman = function (string $potongan): int {
            $potongan = preg_replace('/<script\b.*?<\/script>/s', '', $potongan);
            return preg_match_all('/<div\b/', $potongan) - preg_match_all('/<\/div>/', $potongan);
        };

        $this->assertSame(
            $kedalaman($sebelumDesktop),
            $kedalaman($sebelumMobile),
            'Toolbar mobile tidak sejajar dengan toolbar desktop, berarti masih tersarang di dalamnya.'
        );
    }
}
