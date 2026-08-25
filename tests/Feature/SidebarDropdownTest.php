<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Dropdown sidebar tidak menyimpan keadaannya sendiri antar halaman: setiap
 * kali halaman dimuat, grup yang memuat halaman aktif dibuka ulang berdasarkan
 * daftar pola route di x-data layout dashboard.
 *
 * Konsekuensinya, satu tautan yang lupa didaftarkan membuat dropdown-nya
 * menutup sendiri begitu tautan itu diklik — persis yang terjadi pada
 * "Profil & Keamanan". Test di sini menjaga agar tidak terulang.
 */
class SidebarDropdownTest extends TestCase
{
    use RefreshDatabase;

    private const LAYOUT = 'resources/views/layouts/dashboard.blade.php';

    /** Tautan sidebar dan grup dropdown yang seharusnya ikut terbuka. */
    public static function tautanSidebar(): array
    {
        return [
            'Data Buku'          => ['admin.data-buku', 'openManageBuku'],
            'Koleksi Buku'       => ['admin.buku', 'openManageBuku'],
            'Kategori'           => ['admin.kategori', 'openManageBuku'],
            'Penulis'            => ['admin.penulis', 'openManageBuku'],
            'Penerbit'           => ['admin.penerbit', 'openManageBuku'],
            'Kelas'              => ['admin.kelas', 'openManageBuku'],
            'Rak & Laci'         => ['admin.rak', 'openManageBuku'],
            'Request Peminjaman' => ['admin.peminjaman.request', 'openSirkulasi'],
            'Peminjaman Aktif'   => ['admin.peminjaman', 'openSirkulasi'],
            'Riwayat Transaksi'  => ['admin.riwayat', 'openSirkulasi'],
            'Akun Pengelola'     => ['admin.anggota', 'openAdmin'],
            'Profil & Keamanan'  => ['admin.profil', 'openAdmin'],
            'Pengaturan Sistem'  => ['admin.pengaturan', 'openAdmin'],
            'Audit Log'          => ['admin.audit-log', 'openAdmin'],
        ];
    }

    private function loginSuperAdmin(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $this->actingAs(User::create([
            'name' => 'Petugas Uji', 'email' => 'uji@uji.test',
            'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('tautanSidebar')]
    public function test_dropdown_tetap_terbuka_setelah_tautannya_diklik(string $route, string $grup): void
    {
        $this->loginSuperAdmin();

        $html = $this->get(route($route))->assertOk()->getContent();

        $this->assertStringContainsString(
            "{$grup}: true",
            $html,
            "Membuka route [{$route}] seharusnya menyisakan dropdown [{$grup}] tetap terbuka, "
            ."tapi keadaannya justru tertutup. Tambahkan polanya ke x-data di ".self::LAYOUT
        );
    }

    /**
     * Penjaga agar tidak melenceng lagi di kemudian hari: dibaca langsung dari
     * berkas layout, jadi menu baru yang ditambahkan tanpa mendaftarkan polanya
     * akan langsung membuat test ini merah.
     */
    public function test_setiap_tautan_di_dalam_grup_punya_pola_route_nya(): void
    {
        $isi = file_get_contents(base_path(self::LAYOUT));

        $grup = ['openManageBuku', 'openSirkulasi', 'openAdmin'];

        // Pola yang dipakai untuk membuka ulang tiap grup.
        $pola = [];
        foreach ($grup as $nama) {
            preg_match("/{$nama}: \{\{ request\(\)->routeIs\((.*?)\)/", $isi, $m);
            $this->assertNotEmpty($m, "Tidak menemukan daftar pola untuk {$nama}.");
            preg_match_all("/'([^']+)'/", $m[1], $p);
            $pola[$nama] = $p[1];
        }

        // Potong berkas per grup: tautan sebuah grup berada di antara tombol
        // pembukanya sendiri dan tombol pembuka grup berikutnya.
        $batas = [];
        foreach ($grup as $nama) {
            $batas[$nama] = strpos($isi, "@click=\"toggleGrup('{$nama}')\"");
            $this->assertNotFalse($batas[$nama], "Tidak menemukan tombol dropdown {$nama}.");
        }

        foreach ($grup as $i => $nama) {
            $mulai = $batas[$nama];
            $akhir = isset($grup[$i + 1]) ? $batas[$grup[$i + 1]] : strlen($isi);
            $blok  = substr($isi, $mulai, $akhir - $mulai);

            preg_match_all("/route\('(admin\.[a-z0-9.\-]+)'\)/", $blok, $m);
            $tautan = array_unique($m[1]);
            $this->assertNotEmpty($tautan, "Grup {$nama} tidak punya tautan sama sekali?");

            foreach ($tautan as $route) {
                $cocok = false;
                foreach ($pola[$nama] as $p) {
                    if (Str::is($p, $route)) {
                        $cocok = true;
                        break;
                    }
                }

                $this->assertTrue(
                    $cocok,
                    "Tautan [{$route}] ada di dalam dropdown [{$nama}] tapi tidak cocok dengan pola mana pun, "
                    ."sehingga dropdown-nya akan menutup sendiri saat tautan itu diklik. "
                    ."Tambahkan polanya ke x-data di ".self::LAYOUT
                );
            }
        }
    }

    /**
     * Membuka menu di satu bagian tidak boleh menutup dropdown bagian lain
     * yang sengaja dibiarkan terbuka. Karena keadaan itu hidup di browser,
     * yang bisa dijamin dari sisi server adalah: setiap grup benar-benar
     * membaca pilihan tersimpannya, dan tombolnya benar-benar menyimpan.
     */
    public function test_setiap_grup_membaca_pilihan_yang_tersimpan(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.dashboard'))->assertOk()->getContent();

        foreach (['openManageBuku', 'openSirkulasi', 'openAdmin'] as $grup) {
            $this->assertStringContainsString(
                "localStorage.getItem('sidebar_{$grup}') === 'true'",
                $html,
                "Grup [{$grup}] tidak membaca pilihan tersimpannya, sehingga akan "
                ."menutup sendiri saat petugas membuka menu di bagian lain."
            );
        }
    }

    public function test_setiap_tombol_dropdown_menyimpan_pilihannya(): void
    {
        $isi = file_get_contents(base_path(self::LAYOUT));

        foreach (['openManageBuku', 'openSirkulasi', 'openAdmin'] as $grup) {
            $this->assertStringContainsString(
                "@click=\"toggleGrup('{$grup}')\"",
                $isi,
                "Tombol grup [{$grup}] masih membalik nilainya langsung, jadi "
                ."pilihannya tidak ikut tersimpan."
            );
        }

        $this->assertStringContainsString(
            "localStorage.setItem('sidebar_' + nama, this[nama]);",
            $isi,
            'toggleGrup() harus menyimpan pilihan petugas.'
        );
    }

    /**
     * Grup yang memuat halaman aktif tetap harus terbuka walaupun pilihan
     * tersimpannya tertutup — petugas harus selalu bisa melihat posisinya.
     */
    public function test_grup_halaman_aktif_terbuka_tanpa_bergantung_pada_pilihan_tersimpan(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.peminjaman'))->assertOk()->getContent();

        // Bagian sebelum OR sudah bernilai true, jadi apa pun isi localStorage
        // hasil akhirnya tetap terbuka.
        $this->assertStringContainsString('openSirkulasi: true ||', $html);
        $this->assertStringContainsString('openManageBuku: false ||', $html);
    }
}
