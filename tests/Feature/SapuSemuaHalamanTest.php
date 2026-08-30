<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Kelas;
use App\Models\Peminjaman;
use App\Models\Penerbit;
use App\Models\Penulis;
use App\Models\Rak;
use App\Models\RakLaci;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Setiap halaman yang bisa dibuka petugas maupun pengunjung harus benar-benar
 * terbuka — bukan melempar galat server.
 *
 * Pemeriksaan ini menyusuri seluruh rute GET yang terdaftar, bukan daftar yang
 * ditulis tangan, sehingga halaman baru ikut terperiksa tanpa perlu diingat.
 * Yang dianggap gagal hanya status 5xx: 404 dari isian contoh yang tidak cocok
 * itu wajar dan bukan kerusakan.
 */
class SapuSemuaHalamanTest extends TestCase
{
    use RefreshDatabase;

    public function test_setiap_halaman_get_terbuka_tanpa_galat(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $this->actingAs(User::create([
            'name' => 'Sapu', 'email' => 'sapu@uji.test', 'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]));

        $rak = Rak::create(['kode_rak' => 'RAK-S', 'nama_rak' => 'Rak Sapu', 'lokasi' => 'L1', 'status' => 'aktif']);
        $laci = RakLaci::create(['rak_id' => $rak->id, 'nama_laci' => 'Laci 1', 'urutan' => 1]);
        $kelas = Kelas::create(['tingkat' => '11', 'nama_kelas' => 'RPL']);
        $kategori = Kategori::create(['nama' => 'Umum', 'slug' => 'umum']);
        $penulis = Penulis::create(['nama' => 'Penulis Uji']);
        $penerbit = Penerbit::create(['nama' => 'Penerbit Uji']);
        $buku = Buku::create([
            'judul' => 'Buku Sapu', 'isbn' => 'ISBN-SAPU', 'rak_id' => $rak->id, 'rak_laci_id' => $laci->id,
            'kelas_id' => $kelas->id, 'kategori_id' => $kategori->id, 'penulis_id' => $penulis->id,
            'penerbit_id' => $penerbit->id, 'tahun_terbit' => 2024,
            'total_quantity' => 5, 'available_quantity' => 5, 'status' => 'tersedia',
        ]);
        Peminjaman::create([
            'kode_peminjaman' => Peminjaman::buatKode('PJ'), 'sumber' => 'petugas',
            'nama_peminjam' => 'Siswa', 'jurusan' => 'XI RPL', 'no_wa' => '08123',
            'buku_id' => $buku->id, 'jumlah' => 1,
            'tanggal_pinjam' => now()->toDateString(), 'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
            'status' => 'dipinjam',
        ]);

        $isian = ['id' => $buku->id, 'rakId' => $rak->id, 'laciId' => $laci->id];
        $gagal = [];
        $diperiksa = 0;

        foreach (Route::getRoutes() as $rute) {
            if (!in_array('GET', $rute->methods(), true)) {
                continue;
            }

            $uri = $rute->uri();
            if (str_starts_with($uri, '_') || $uri === 'up' || str_contains($uri, '{path}')) {
                continue;
            }

            $alamat = '/' . ltrim(preg_replace_callback('/\{(\w+)\??\}/', function ($c) use ($isian) {
                return (string) ($isian[$c[1]] ?? 1);
            }, $uri), '/');

            $status = $this->get($alamat)->getStatusCode();
            $diperiksa++;

            if ($status >= 500) {
                $gagal[] = "{$alamat} => {$status}";
            }
        }

        $this->assertSame([], $gagal, "Halaman berikut melempar galat server:\n" . implode("\n", $gagal));

        // Penjaga bagi penjaganya: kalau penyusuran rutenya rusak, jumlah yang
        // diperiksa akan anjlok dan test ini tetap lulus tanpa menguji apa pun.
        $this->assertGreaterThan(30, $diperiksa, 'Penyusuran rute tampaknya gagal menemukan halaman.');
    }
}
