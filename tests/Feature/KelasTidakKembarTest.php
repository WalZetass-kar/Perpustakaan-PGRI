<?php

namespace Tests\Feature;

use App\Exceptions\AturanBisnisException;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\User;
use App\Services\MasterData\KelasService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Master Kelas tidak boleh memuat dua kelas yang sama.
 *
 * "Sama" di sini berarti tingkat DAN namanya kembar. Satu jurusan memang
 * dimiliki beberapa angkatan sekaligus, jadi "10 DKV" dan "11 DKV" wajib tetap
 * bisa hidup berdampingan — yang ditolak hanya pengulangan pada tingkat yang
 * sama, termasuk yang cuma berbeda spasi atau huruf besar/kecil seperti
 * "11 dkv" lawan "11dkv" yang sudah pernah masuk berdua ke data sungguhan.
 */
class KelasTidakKembarTest extends TestCase
{
    use RefreshDatabase;

    private function loginPetugas(): User
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $user = User::create([
            'name' => 'Petugas Uji', 'email' => 'petugas@uji.test', 'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]);
        $this->actingAs($user);

        return $user;
    }

    private function simpanKelas(?string $tingkat, string $nama)
    {
        return $this->post(route('admin.kelas.store'), [
            'tingkat' => $tingkat, 'nama_kelas' => $nama, 'deskripsi' => null,
        ]);
    }

    // -------------------------------------------------- tingkat sama, nama sama

    public function test_nama_kelas_yang_sama_pada_tingkat_sama_ditolak(): void
    {
        $this->loginPetugas();
        $this->simpanKelas('10', 'DKV');

        $respons = $this->simpanKelas('10', 'DKV');

        $respons->assertSessionHasErrors('nama_kelas');
        $this->assertSame(1, Kelas::count(), 'Kelas kembar seharusnya tidak ikut tersimpan.');
    }

    public function test_beda_huruf_besar_kecil_tetap_dianggap_kembar(): void
    {
        $this->loginPetugas();
        $this->simpanKelas('11', 'DKV');

        $this->simpanKelas('11', 'dkv')->assertSessionHasErrors('nama_kelas');
        $this->simpanKelas('11', 'Dkv')->assertSessionHasErrors('nama_kelas');

        $this->assertSame(1, Kelas::count());
    }

    public function test_beda_spasi_tetap_dianggap_kembar(): void
    {
        $this->loginPetugas();
        $this->simpanKelas('11', '11 dkv');

        // Persis kasus yang sudah telanjur ada di data sungguhan.
        $this->simpanKelas('11', '11dkv')->assertSessionHasErrors('nama_kelas');
        $this->simpanKelas('11', '11  dkv')->assertSessionHasErrors('nama_kelas');
        $this->simpanKelas('11', '  11 dkv  ')->assertSessionHasErrors('nama_kelas');

        $this->assertSame(1, Kelas::count());
    }

    public function test_tingkat_kosong_pun_tidak_boleh_kembar(): void
    {
        $this->loginPetugas();
        $this->simpanKelas(null, 'X RPL');

        // Tanpa penanganan khusus, dua NULL dianggap berbeda oleh indeks unik
        // dan kelas tanpa tingkat justru bisa masuk berkali-kali.
        $this->simpanKelas(null, 'x rpl')->assertSessionHasErrors('nama_kelas');

        $this->assertSame(1, Kelas::count());
    }

    // ---------------------------------------------------------- angka Romawi

    public function test_tingkat_romawi_dan_angka_dianggap_sama(): void
    {
        $this->loginPetugas();
        $this->simpanKelas('X', 'DKV');

        // Sekolah menulis tingkat dengan dua kebiasaan yang sama-sama benar.
        // Tanpa penyetaraan ini seluruh daftar kelas bisa tercatat dobel hanya
        // karena dua petugas punya kebiasaan berbeda.
        $this->simpanKelas('10', 'DKV')->assertSessionHasErrors('nama_kelas');
        $this->simpanKelas('x', 'dkv')->assertSessionHasErrors('nama_kelas');

        $this->assertSame(1, Kelas::count());
    }

    public function test_romawi_di_dalam_nama_kelas_ikut_disamakan(): void
    {
        $this->loginPetugas();
        $this->simpanKelas(null, 'X DKV');

        $this->simpanKelas(null, '10 DKV')->assertSessionHasErrors('nama_kelas');
        $this->simpanKelas(null, '10dkv')->assertSessionHasErrors('nama_kelas');

        $this->assertSame(1, Kelas::count());
    }

    public function test_romawi_berbeda_tetap_kelas_berbeda(): void
    {
        $this->loginPetugas();

        $this->simpanKelas('X', 'DKV')->assertSessionHasNoErrors();
        $this->simpanKelas('11', 'DKV')->assertSessionHasNoErrors();
        $this->simpanKelas('XII', 'DKV')->assertSessionHasNoErrors();

        $this->assertSame(3, Kelas::count(), 'X, 11, dan XII adalah tiga angkatan berbeda.');
    }

    public function test_seluruh_jenjang_sd_sampai_sma_ikut_disetarakan(): void
    {
        $pasangan = [['I', '1'], ['IV', '4'], ['VI', '6'], ['VII', '7'], ['VIII', '8'], ['IX', '9'], ['XII', '12']];

        foreach ($pasangan as [$romawi, $angka]) {
            $this->assertSame(
                Kelas::kunciUnik($angka, 'IPA'),
                Kelas::kunciUnik($romawi, 'IPA'),
                "Tingkat {$romawi} seharusnya setara dengan {$angka}."
            );
        }
    }

    public function test_nama_jurusan_tidak_ikut_tertukar_jadi_angka(): void
    {
        // Jebakan sebaliknya: kalau penerjemahan Romawi dilakukan tanpa batas
        // kata, nama jurusan yang kebetulan berawalan huruf Romawi ikut rusak.
        $ujian = [
            'Vokasi'      => 'vokasi',
            'Iklan'       => 'iklan',
            'MI Unggulan' => 'miunggulan',
            'IPA'         => 'ipa',
            'DKV'         => 'dkv',
            'Multimedia'  => 'multimedia',
        ];

        foreach ($ujian as $nama => $harusnya) {
            $this->assertSame(
                '|' . $harusnya,
                Kelas::kunciUnik(null, $nama),
                "Nama \"{$nama}\" tidak boleh ikut diterjemahkan sebagai angka Romawi."
            );
        }
    }

    // ------------------------------------------------- tingkat beda: harus boleh

    public function test_nama_sama_pada_tingkat_berbeda_tetap_diterima(): void
    {
        $this->loginPetugas();

        $this->simpanKelas('10', 'DKV')->assertSessionHasNoErrors();
        $this->simpanKelas('11', 'DKV')->assertSessionHasNoErrors();
        $this->simpanKelas('12', 'DKV')->assertSessionHasNoErrors();

        $this->assertSame(3, Kelas::count(), 'Satu jurusan harus bisa punya kelas di tiap angkatan.');
    }

    // ------------------------------------------------------------- saat mengubah

    public function test_kelas_boleh_disimpan_ulang_dengan_namanya_sendiri(): void
    {
        $this->loginPetugas();
        $this->simpanKelas('10', 'DKV');
        $kelas = Kelas::firstOrFail();

        $respons = $this->post(route('admin.kelas.update', $kelas->id), [
            'tingkat' => '10', 'nama_kelas' => 'DKV', 'deskripsi' => 'Desain Komunikasi Visual',
        ]);

        $respons->assertSessionHasNoErrors();
        $this->assertSame('Desain Komunikasi Visual', $kelas->fresh()->deskripsi);
    }

    public function test_mengubah_kelas_menjadi_nama_kelas_lain_ditolak(): void
    {
        $this->loginPetugas();
        $this->simpanKelas('10', 'DKV');
        $this->simpanKelas('10', 'TKJ');
        $tkj = Kelas::where('nama_kelas', 'TKJ')->firstOrFail();

        $respons = $this->post(route('admin.kelas.update', $tkj->id), [
            'tingkat' => '10', 'nama_kelas' => 'dkv',
        ]);

        $respons->assertSessionHasErrors('nama_kelas');
        $this->assertSame('TKJ', $tkj->fresh()->nama_kelas);
    }

    public function test_memindahkan_kelas_ke_tingkat_lain_yang_masih_kosong_diterima(): void
    {
        $this->loginPetugas();
        $this->simpanKelas('10', 'DKV');
        $kelas = Kelas::firstOrFail();

        $respons = $this->post(route('admin.kelas.update', $kelas->id), [
            'tingkat' => '12', 'nama_kelas' => 'DKV',
        ]);

        $respons->assertSessionHasNoErrors();
        $this->assertSame('12', $kelas->fresh()->tingkat);
    }

    // ------------------------- jenjang yang mengulang tingkat di dalam nama

    public function test_nama_dengan_dan_tanpa_angka_jenjang_adalah_kelas_yang_sama(): void
    {
        $this->loginPetugas();
        $this->simpanKelas('11', '11 RPL');

        // "tingkat 11 + 11 RPL" dan "tingkat 11 + RPL" memaksudkan kelas yang
        // sama persis; yang kedua hanya tidak ikut menuliskan angkanya.
        $this->simpanKelas('11', 'RPL')->assertSessionHasErrors('nama_kelas');
        $this->simpanKelas('11', 'rpl')->assertSessionHasErrors('nama_kelas');
        $this->simpanKelas('11', 'XI RPL')->assertSessionHasErrors('nama_kelas');

        $this->assertSame(1, Kelas::count());
    }

    public function test_berlaku_juga_bila_yang_polos_didaftarkan_lebih_dulu(): void
    {
        $this->loginPetugas();
        $this->simpanKelas('11', 'RPL');

        $this->simpanKelas('11', '11 RPL')->assertSessionHasErrors('nama_kelas');
        $this->simpanKelas('11', 'XI RPL')->assertSessionHasErrors('nama_kelas');

        $this->assertSame(1, Kelas::count());
    }

    public function test_jenjang_di_nama_menggantikan_tingkat_yang_dikosongkan(): void
    {
        $this->loginPetugas();
        $this->simpanKelas(null, '11 RPL');

        // Tingkat dikosongkan dan jenjangnya hanya ada di dalam nama — tetap
        // kelas yang sama dengan "tingkat 11 + RPL".
        $this->simpanKelas('11', 'RPL')->assertSessionHasErrors('nama_kelas');

        $this->assertSame(1, Kelas::count());
    }

    public function test_jurusan_sama_di_tingkat_berbeda_tetap_boleh(): void
    {
        $this->loginPetugas();

        $this->simpanKelas('11', '11 RPL')->assertSessionHasNoErrors();
        $this->simpanKelas('12', 'RPL')->assertSessionHasNoErrors();
        $this->simpanKelas('10', 'X RPL')->assertSessionHasNoErrors();

        $this->assertSame(3, Kelas::count(), 'RPL di tiga angkatan adalah tiga kelas berbeda.');
    }

    public function test_angka_yang_bertentangan_tidak_ikut_dibuang(): void
    {
        // Tingkat 10 dengan nama "12 Pariwisata" bukan pengulangan, melainkan
        // pertentangan — dan pertentangan itu harus tetap terlihat, bukan
        // ditebak mana yang benar.
        $this->assertSame('10|12pariwisata', Kelas::kunciUnik('10', '12 Pariwisata'));
        $this->assertSame('11|112', Kelas::kunciUnik('11', '112'));
    }

    // ----------------------------- tingkat vs jenjang yang tertulis di nama

    public function test_nama_kelas_tidak_boleh_menunjuk_tingkat_lain(): void
    {
        $this->loginPetugas();

        // Kolom tingkat bilang 11, tetapi namanya sendiri bilang XII (=12).
        // Tidak ada cara menebak mana yang benar, jadi keduanya harus disamakan
        // lebih dulu sebelum bisa disimpan.
        $this->simpanKelas('11', 'XII DKV')->assertSessionHasErrors('nama_kelas');
        $this->simpanKelas('11', '12 DKV')->assertSessionHasErrors('nama_kelas');
        $this->simpanKelas('XI', '10 DKV')->assertSessionHasErrors('nama_kelas');

        $this->assertSame(0, Kelas::count());
    }

    public function test_nama_kelas_yang_menunjuk_tingkat_sama_tetap_diterima(): void
    {
        $this->loginPetugas();

        $this->simpanKelas('11', '11 DKV')->assertSessionHasNoErrors();
        $this->simpanKelas('11', 'XI TKJ')->assertSessionHasNoErrors();
        $this->simpanKelas('XII', '12 Mesin')->assertSessionHasNoErrors();

        $this->assertSame(3, Kelas::count(), 'Penulisan Romawi maupun angka sama-sama sah selama jenjangnya cocok.');
    }

    public function test_nama_tanpa_penunjuk_jenjang_tidak_ikut_diperiksa(): void
    {
        $this->loginPetugas();

        $this->simpanKelas('11', 'DKV')->assertSessionHasNoErrors();
        $this->simpanKelas('12', 'Vokasi')->assertSessionHasNoErrors();

        // Tingkat sengaja dikosongkan: tidak ada yang bisa bertentangan.
        $this->simpanKelas(null, 'X RPL')->assertSessionHasNoErrors();

        $this->assertSame(3, Kelas::count());
    }

    public function test_angka_panjang_di_nama_tidak_dikira_tingkat(): void
    {
        // "2024 Angkatan" tidak boleh dibaca sebagai tingkat 20.
        $this->assertNull(Kelas::tingkatDiAwalNama('2024 Angkatan'));
        $this->assertNull(Kelas::tingkatDiAwalNama('Vokasi'));
        $this->assertSame(12, Kelas::tingkatDiAwalNama('xii Mesin'));
        $this->assertSame(10, Kelas::tingkatDiAwalNama('X RPL'));
        $this->assertSame(11, Kelas::tingkatDiAwalNama('11dkv'));
    }

    public function test_mengubah_tingkat_saja_sampai_bertentangan_ikut_ditolak(): void
    {
        $this->loginPetugas();
        $this->simpanKelas('11', '11 DKV');
        $kelas = Kelas::firstOrFail();

        // Namanya tidak diubah, hanya tingkatnya — dan itu cukup untuk membuat
        // barisnya bertentangan dengan dirinya sendiri.
        $respons = $this->post(route('admin.kelas.update', $kelas->id), [
            'tingkat' => '12', 'nama_kelas' => '11 DKV',
        ]);

        $respons->assertSessionHasErrors('nama_kelas');
        $this->assertSame('11', $kelas->fresh()->tingkat);
    }

    public function test_service_menolak_tingkat_yang_bertentangan(): void
    {
        $this->expectException(AturanBisnisException::class);

        app(KelasService::class)->simpan(['tingkat' => '11', 'nama_kelas' => 'XII DKV']);
    }

    // ------------------------------------------ jalur di luar formulir & database

    public function test_service_menolak_kelas_kembar_walau_tanpa_formulir(): void
    {
        Kelas::create(['tingkat' => '10', 'nama_kelas' => 'DKV']);

        $this->expectException(AturanBisnisException::class);

        app(KelasService::class)->simpan(['tingkat' => '10', 'nama_kelas' => 'dkv']);
    }

    public function test_database_menolak_kunci_kembar_sebagai_jaring_terakhir(): void
    {
        Kelas::create(['tingkat' => '10', 'nama_kelas' => 'DKV']);

        $this->expectException(QueryException::class);

        // Menembus seluruh lapisan aplikasi: langsung ke tabelnya.
        DB::table('kelas')->insert([
            'tingkat' => '10', 'nama_kelas' => 'D K V',
            'kunci_unik' => Kelas::kunciUnik('10', 'DKV'),
        ]);
    }

    public function test_kunci_unik_tidak_bisa_diisi_dari_luar(): void
    {
        $kelas = Kelas::create([
            'tingkat' => '10', 'nama_kelas' => 'DKV', 'kunci_unik' => 'dipaksa',
        ]);

        $this->assertSame(Kelas::kunciUnik('10', 'DKV'), $kelas->fresh()->kunci_unik);
    }
}
