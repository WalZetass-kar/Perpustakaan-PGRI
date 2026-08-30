<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Role;
use App\Models\User;
use App\Services\Buku\BukuService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Daftar kelas harus urut menurut jenjangnya yang sebenarnya, bukan menurut
 * cara menulisnya.
 *
 * Sekolah menulis tingkat dengan dua kebiasaan yang sama-sama benar — "11"
 * maupun "XI" — dan sejak sistem menyetarakan keduanya pada penjagaan nama
 * kembar, keduanya pasti bercampur di satu daftar. SQL sendiri tidak mengenal
 * angka Romawi: CAST('XI' AS UNSIGNED) bernilai 0, yang dulu membuat kelas XI
 * selalu nangkring di atas kelas 9.
 */
class UrutanTingkatKelasTest extends TestCase
{
    use RefreshDatabase;

    private function loginPetugas(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $this->actingAs(User::create([
            'name' => 'Petugas Uji', 'email' => 'urut@uji.test', 'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]));
    }

    private function buat(array $tingkatDanNama): void
    {
        foreach ($tingkatDanNama as [$tingkat, $nama]) {
            Kelas::create(['tingkat' => $tingkat, 'nama_kelas' => $nama]);
        }
    }

    public function test_romawi_dan_angka_berbaur_dalam_urutan_yang_benar(): void
    {
        $this->loginPetugas();
        $this->buat([['XII', 'Mesin'], ['9', 'IPA'], ['X', 'DKV'], ['11', 'TKJ']]);

        $urut = $this->get(route('admin.kelas'))->viewData('kelasList')->pluck('tingkat')->all();

        $this->assertSame(['9', 'X', '11', 'XII'], $urut, 'XI tidak boleh mendahului 9.');
    }

    public function test_sepuluh_tidak_mendahului_sembilan(): void
    {
        $this->loginPetugas();
        $this->buat([['10', 'X A'], ['9', 'IX A'], ['12', 'XII A']]);

        $urut = $this->get(route('admin.kelas'))->viewData('kelasList')->pluck('tingkat')->all();

        $this->assertSame(['9', '10', '12'], $urut);
    }

    public function test_dropdown_koleksi_buku_ikut_urut(): void
    {
        $this->buat([['XII', 'Mesin'], ['9', 'IPA'], ['X', 'DKV']]);

        // Dipakai filter dan form di halaman Koleksi Buku — daftarnya harus
        // urut dengan aturan yang sama seperti halaman master Kelas.
        $urut = app(BukuService::class)->pilihanForm()['kelasList']->pluck('tingkat')->all();

        $this->assertSame(['9', 'X', 'XII'], $urut);
    }

    public function test_kelas_tanpa_tingkat_tetap_ikut_terdaftar(): void
    {
        $this->loginPetugas();
        $this->buat([['11', 'TKJ'], [null, 'Kelas Khusus'], ['9', 'IPA']]);

        $daftar = $this->get(route('admin.kelas'))->viewData('kelasList');

        $this->assertCount(3, $daftar, 'Kelas tanpa tingkat tidak boleh hilang dari daftar.');
        $this->assertNull($daftar->first()->tingkat, 'Kelas tanpa tingkat muncul lebih dulu, seperti sebelumnya.');
    }

    public function test_angka_tingkat_ikut_diperbarui_saat_tingkatnya_diubah(): void
    {
        $kelas = Kelas::create(['tingkat' => 'X', 'nama_kelas' => 'DKV']);
        $this->assertSame(10, (int) $kelas->fresh()->tingkat_angka);

        $kelas->update(['tingkat' => '12']);
        $this->assertSame(12, (int) $kelas->fresh()->tingkat_angka);

        // Tingkat yang bukan penunjuk jenjang tidak menghasilkan angka.
        $kelas->update(['tingkat' => 'Khusus']);
        $this->assertNull($kelas->fresh()->tingkat_angka);
    }
}
