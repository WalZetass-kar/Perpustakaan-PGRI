<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kelas;
use App\Models\Rak;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tabel kelas menyimpan dua kolom yang bukan untuk dibaca siapa pun:
 *
 *   - `kunci_unik`    bentuk baku tingkat+nama, dipakai menjaga kelas kembar
 *   - `tingkat_angka` jenjang sebagai angka, dipakai mengurutkan daftar
 *
 * Keduanya turunan — selalu dihitung ulang model saat baris disimpan — dan
 * tidak berarti apa-apa bagi pembacanya. Model menyembunyikannya lewat
 * $hidden supaya tidak ikut terkirim.
 *
 * Yang dijaga di sini adalah penyembunyian itu. Hari ini tidak ada satu pun
 * halaman atau endpoint yang membocorkannya, tetapi $hidden mudah terhapus
 * oleh yang mengira kolomnya tidak terpakai, dan endpoint JSON baru yang
 * mengembalikan model kelas utuh akan langsung membocorkannya tanpa ada yang
 * sadar — nilai seperti "11|dkv" tiba-tiba muncul di respons.
 */
class KolomInternalKelasTest extends TestCase
{
    use RefreshDatabase;

    /** Kolom turunan beserta contoh nilainya yang sama-sama tidak boleh bocor. */
    private const TIDAK_BOLEH_BOCOR = ['kunci_unik', 'tingkat_angka', '11|dkv'];

    private function tanpaKolomInternal(string $isi, string $dimana): void
    {
        foreach (self::TIDAK_BOLEH_BOCOR as $bocor) {
            $this->assertStringNotContainsString(
                $bocor,
                $isi,
                "Kolom internal \"{$bocor}\" ikut terkirim di {$dimana}."
            );
        }
    }

    private function buatKelas(): Kelas
    {
        return Kelas::create(['tingkat' => '11', 'nama_kelas' => 'DKV', 'deskripsi' => 'Desain']);
    }

    public function test_tidak_ikut_saat_model_diserialisasi(): void
    {
        $kelas = $this->buatKelas();

        // Nilainya memang tersimpan dan terpakai di dalam sistem …
        $this->assertSame('11|dkv', $kelas->fresh()->kunci_unik);
        $this->assertSame(11, (int) $kelas->fresh()->tingkat_angka);

        // … tetapi tidak pernah ikut keluar.
        $this->tanpaKolomInternal($kelas->fresh()->toJson(), 'Kelas::toJson()');
        $this->tanpaKolomInternal(json_encode($kelas->fresh()->toArray()), 'Kelas::toArray()');
        $this->tanpaKolomInternal(Kelas::all()->toJson(), 'koleksi Kelas');

        // Yang tetap harus terbawa: isian yang memang milik petugas.
        $this->assertStringContainsString('DKV', $kelas->fresh()->toJson());
    }

    public function test_tidak_muncul_di_halaman_yang_menampilkan_kelas(): void
    {
        $kelas = $this->buatKelas();

        $rak = Rak::create(['kode_rak' => 'RAK-INT', 'nama_rak' => 'Rak Uji', 'lokasi' => 'L1', 'status' => 'aktif']);
        Buku::create([
            'judul' => 'Buku Uji Kolom', 'isbn' => 'ISBN-' . uniqid(), 'rak_id' => $rak->id,
            'kelas_id' => $kelas->id, 'tahun_terbit' => 2024,
            'total_quantity' => 2, 'available_quantity' => 2, 'status' => 'tersedia',
        ]);

        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $this->actingAs(User::create([
            'name' => 'Petugas Uji', 'email' => 'internal@uji.test', 'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]));

        $halaman = [
            'master Kelas'          => route('admin.kelas'),
            'Koleksi Buku'          => route('admin.buku'),
            'ekspor Excel koleksi'  => route('admin.buku.export.excel'),
            'katalog publik'        => route('katalog'),
        ];

        foreach ($halaman as $nama => $alamat) {
            $this->tanpaKolomInternal($this->get($alamat)->assertOk()->getContent(), $nama);
        }

        // Baris tabel Koleksi Buku dikirim terpisah sebagai JSON.
        $this->tanpaKolomInternal(
            $this->getJson(route('admin.buku') . '?draw=1&start=0&length=10')->assertOk()->getContent(),
            'JSON DataTables Koleksi Buku'
        );
    }
}
