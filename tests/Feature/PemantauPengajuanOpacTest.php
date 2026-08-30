<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Rak;
use App\Models\Role;
use App\Models\User;
use App\Services\Sirkulasi\PengajuanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Setelah mengajukan peminjaman dari katalog, siswa menunggu keputusan petugas
 * di halaman yang sama. Yang diuji di sini adalah alamat yang dipakai halaman
 * itu untuk menanyakan keputusannya.
 *
 * Kuncinya bukan kode pengajuan, melainkan sesi peramban pengaju sendiri —
 * jadi menebak-nebak id pengajuan orang lain tidak membuahkan apa pun.
 */
class PemantauPengajuanOpacTest extends TestCase
{
    use RefreshDatabase;

    private function buatBuku(int $total = 5): Buku
    {
        $rak = Rak::firstOrCreate(['kode_rak' => 'RAK-OPAC'],
            ['nama_rak' => 'Rak Uji', 'lokasi' => 'L1', 'status' => 'aktif']);

        return Buku::create([
            'judul' => 'Buku Katalog', 'isbn' => 'ISBN-' . uniqid(),
            'rak_id' => $rak->id, 'tahun_terbit' => 2024,
            'total_quantity' => $total, 'available_quantity' => $total, 'status' => 'tersedia',
        ]);
    }

    private function ajukan(Buku $buku, array $ganti = [])
    {
        return $this->postJson(route('katalog.ajukan'), array_merge([
            'buku_id' => $buku->id, 'nama_peminjam' => 'Rani Safitri',
            'jurusan' => 'XI DKV', 'nomor_induk' => '0065',
            'no_wa' => '081234567890', 'jumlah' => 1,
        ], $ganti));
    }

    /** Memproses pengajuan sebagaimana petugas melakukannya dari panel. */
    private function prosesOlehPetugas(int $id, string $keputusan, ?string $alasan = null): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $petugas = User::create([
            'name' => 'Petugas Uji', 'email' => 'petugas' . uniqid() . '@uji.test',
            'password' => Hash::make('rahasia123'), 'role_id' => $role->id, 'status' => 'active',
        ]);

        Auth::login($petugas);
        $layanan = app(PengajuanService::class);
        $keputusan === 'setujui' ? $layanan->setujui($id) : $layanan->tolak($id, $alasan);
        Auth::logout();
    }

    // ------------------------------------------------------------ jalur normal

    public function test_pengajuan_membawa_id_untuk_dipantau(): void
    {
        $respons = $this->ajukan($this->buatBuku());

        $respons->assertOk()->assertJson(['success' => true]);
        $this->assertIsInt($respons->json('id'), 'Halaman butuh id ini untuk memantau keputusan.');
    }

    public function test_status_awal_masih_menunggu(): void
    {
        $id = $this->ajukan($this->buatBuku())->json('id');

        $this->getJson(route('katalog.pengajuan.status', $id))
            ->assertOk()
            ->assertJson(['status' => 'pending', 'alasan_penolakan' => null]);
    }

    public function test_status_berubah_diterima_setelah_disetujui_petugas(): void
    {
        $id = $this->ajukan($this->buatBuku())->json('id');
        $this->prosesOlehPetugas($id, 'setujui');

        $this->getJson(route('katalog.pengajuan.status', $id))
            ->assertOk()
            ->assertJson(['status' => 'dipinjam', 'alasan_penolakan' => null])
            ->assertJsonPath('jatuh_tempo', fn ($v) => is_string($v) && $v !== '');
    }

    public function test_status_berubah_ditolak_beserta_alasannya(): void
    {
        $id = $this->ajukan($this->buatBuku())->json('id');
        $this->prosesOlehPetugas($id, 'tolak', 'Buku sedang dalam perbaikan sampul.');

        $this->getJson(route('katalog.pengajuan.status', $id))
            ->assertOk()
            ->assertJson([
                'status'           => 'ditolak',
                'alasan_penolakan' => 'Buku sedang dalam perbaikan sampul.',
            ]);
    }

    // -------------------------------------------------------------- keamanannya

    public function test_pengajuan_milik_peramban_lain_tidak_bisa_diintip(): void
    {
        $id = $this->ajukan($this->buatBuku())->json('id');

        // Peramban lain: sesi baru, tanpa catatan pengajuan apa pun.
        $this->flushSession();

        $this->getJson(route('katalog.pengajuan.status', $id))->assertNotFound();
    }

    public function test_id_yang_ditebak_tidak_membuahkan_apa_pun(): void
    {
        $buku = $this->buatBuku();
        $milikSendiri = $this->ajukan($buku)->json('id');

        // Pengajuan siswa lain yang tidak pernah lewat peramban ini.
        $milikOrangLain = Peminjaman::create([
            'kode_peminjaman' => Peminjaman::buatKode('REQ'), 'sumber' => 'opac',
            'nama_peminjam' => 'Siswa Lain', 'jurusan' => 'XII TKJ', 'no_wa' => '089999999999',
            'buku_id' => $buku->id, 'jumlah' => 1,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
            'status' => 'pending',
        ])->id;

        $this->getJson(route('katalog.pengajuan.status', $milikSendiri))->assertOk();
        $this->getJson(route('katalog.pengajuan.status', $milikOrangLain))->assertNotFound();
    }

    public function test_jawabannya_tidak_memuat_identitas_pengaju(): void
    {
        $id = $this->ajukan($this->buatBuku())->json('id');

        $isi = $this->getJson(route('katalog.pengajuan.status', $id))->getContent();

        foreach (['Rani Safitri', '081234567890', '0065', 'nama_peminjam', 'no_wa', 'nomor_induk'] as $bocor) {
            $this->assertStringNotContainsString($bocor, $isi, "Data \"{$bocor}\" tidak boleh ikut terkirim.");
        }
    }

    // ------------------------------------------------------- pemasangan halaman

    public function test_kedua_halaman_katalog_memuat_pemantaunya(): void
    {
        $buku = $this->buatBuku();

        foreach ([route('katalog'), route('buku.detail', $buku->id)] as $alamat) {
            $isi = $this->get($alamat)->assertOk()->getContent();
            $this->assertStringContainsString('pantauPengajuan', $isi, "Pemantau tidak terpasang di {$alamat}");
            $this->assertStringContainsString('Menunggu Verifikasi Petugas', $isi);

            // Angka stok & antrean ditanam saat halaman dirender, jadi keputusan
            // petugas membuatnya basi sampai halamannya dimuat ulang.
            $this->assertStringContainsString('muatUlangHalaman', $isi, "Pemuatan ulang setelah keputusan hilang di {$alamat}");
            $this->assertSame(
                2,
                substr_count($isi, '}).then(muatUlangHalaman);'),
                'Kedua keputusan — diterima dan ditolak — harus sama-sama menyegarkan halaman.'
            );
        }
    }

    public function test_kedua_halaman_menerjemahkan_sesi_kedaluwarsa(): void
    {
        $buku = $this->buatBuku();

        // Status 419 muncul ketika token CSRF halaman sudah tidak berlaku —
        // paling sering karena ada yang login petugas di peramban yang sama.
        // Siswa tidak boleh dibiarkan membaca "CSRF token mismatch."
        foreach ([route('katalog'), route('buku.detail', $buku->id)] as $alamat) {
            $isi = $this->get($alamat)->assertOk()->getContent();

            $this->assertStringContainsString('tampilkanKegagalanPengajuan', $isi, "Penanganan gagal tidak terpasang di {$alamat}");
            $this->assertStringContainsString('status === 419', $isi, "Cabang khusus 419 hilang di {$alamat}");
            $this->assertStringContainsString('Halaman Perlu Dimuat Ulang', $isi);
            $this->assertStringContainsString('Muat Ulang', $isi);
        }
    }
}
