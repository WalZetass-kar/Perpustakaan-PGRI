<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Rak;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Dua hal yang saling berkaitan:
 *
 * 1. No. WhatsApp wajib diisi saat siswa mengajukan pinjam lewat katalog OPAC.
 * 2. Petugas bisa melihat data lengkap peminjam itu dari halaman sirkulasi —
 *    termasuk isian yang tidak muat di tabel (WhatsApp, catatan siswa).
 */
class PeminjamanOpacTest extends TestCase
{
    use RefreshDatabase;

    private function buatBuku(): Buku
    {
        $rak = Rak::create([
            'kode_rak' => 'RAK-UJI-01', 'nama_rak' => 'Rak Pengujian',
            'lokasi' => 'Lantai 1', 'status' => 'aktif',
        ]);

        return Buku::create([
            'judul' => 'Bumi Manusia', 'isbn' => 'ISBN-'.uniqid(),
            'rak_id' => $rak->id, 'tahun_terbit' => 2024,
            'total_quantity' => 3, 'available_quantity' => 3, 'status' => 'tersedia',
        ]);
    }

    private function loginPetugas(): User
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $user = User::create([
            'name' => 'Petugas Uji', 'email' => 'uji@uji.test',
            'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]);
        $this->actingAs($user);

        return $user;
    }

    private function dataPengajuan(Buku $buku, array $ganti = []): array
    {
        return array_merge([
            'buku_id'       => $buku->id,
            'nama_peminjam' => 'Ihwal Maulana',
            'jurusan'       => 'XII RPL 1',
            'nomor_induk'   => '0065123489',
            'no_wa'         => '081234567890',
            'catatan'       => 'Untuk tugas akhir.',
            'jumlah'        => 1,
        ], $ganti);
    }

    // ---------------------------------------------------------------- WA wajib

    public function test_pengajuan_tanpa_nomor_whatsapp_ditolak(): void
    {
        $buku = $this->buatBuku();

        $this->post(route('katalog.ajukan'), $this->dataPengajuan($buku, ['no_wa' => null]))
            ->assertSessionHasErrors('no_wa');

        $this->assertSame(0, Peminjaman::count(), 'Pengajuan tanpa WhatsApp seharusnya tidak tersimpan.');
    }

    public function test_pengajuan_dengan_nomor_whatsapp_diterima(): void
    {
        $buku = $this->buatBuku();

        $this->post(route('katalog.ajukan'), $this->dataPengajuan($buku))
            ->assertSessionHasNoErrors();

        $this->assertSame('081234567890', Peminjaman::first()->no_wa);
    }

    public function test_pesan_kesalahan_wa_berbahasa_indonesia(): void
    {
        $buku = $this->buatBuku();

        $response = $this->postJson(route('katalog.ajukan'), $this->dataPengajuan($buku, ['no_wa' => null]));

        $response->assertStatus(422);
        $this->assertStringContainsString(
            'Nomor WhatsApp wajib diisi',
            $response->json('message'),
            'Pesan gagal yang muncul di popup katalog harus berbahasa Indonesia.'
        );
    }

    // ------------------------------------------------------------ asal pinjam

    public function test_pengajuan_lewat_opac_ditandai_sumbernya(): void
    {
        $buku = $this->buatBuku();
        $this->post(route('katalog.ajukan'), $this->dataPengajuan($buku));

        $loan = Peminjaman::first();
        $this->assertSame('opac', $loan->sumber);
        $this->assertTrue($loan->isDariOpac());
    }

    public function test_pencatatan_oleh_petugas_tidak_ditandai_opac(): void
    {
        $buku = $this->buatBuku();
        $this->loginPetugas();

        $this->post(route('admin.peminjaman.store'), [
            'nama_peminjam' => 'Siti Aminah',
            'jurusan'       => 'XI TKJ 2',
            'no_wa'         => '081234567890',
            'buku_id'       => $buku->id,
            'jumlah'        => 1,
        ])->assertSessionHasNoErrors();

        $loan = Peminjaman::first();
        $this->assertSame('petugas', $loan->sumber);
        $this->assertFalse($loan->isDariOpac());
    }

    /**
     * Penanda ini harus bertahan setelah pengajuan disetujui — di titik itulah
     * kode REQ- ditimpa menjadi PJ-, sehingga asal-usulnya tidak lagi terbaca
     * dari kode peminjaman.
     */
    public function test_penanda_opac_bertahan_setelah_pengajuan_disetujui(): void
    {
        $buku = $this->buatBuku();
        $this->post(route('katalog.ajukan'), $this->dataPengajuan($buku));
        $loan = Peminjaman::first();

        $this->loginPetugas();
        $this->post(route('admin.peminjaman.request.approve', $loan->id))->assertSessionHasNoErrors();

        $loan->refresh();
        $this->assertSame('dipinjam', $loan->status);
        $this->assertStringStartsWith('PJ-', $loan->kode_peminjaman);
        $this->assertSame('opac', $loan->sumber, 'Asal OPAC tidak boleh hilang saat disetujui.');
    }

    // -------------------------------------------------------- modal detail

    public function test_halaman_sirkulasi_menyediakan_tombol_detail(): void
    {
        $buku = $this->buatBuku();
        $this->post(route('katalog.ajukan'), $this->dataPengajuan($buku));
        $loan = Peminjaman::first();

        $this->loginPetugas();
        $this->post(route('admin.peminjaman.request.approve', $loan->id));

        $html = $this->get(route('admin.peminjaman'))->assertOk()->getContent();

        $this->assertStringContainsString('openDetailModal = true', $html);
        $this->assertStringContainsString('Data lengkap peminjam', $html);
    }

    /**
     * Yang dicari petugas justru isian yang tidak muat di tabel. Kalau nomor
     * WhatsApp dan catatan siswa tidak ikut terkirim ke halaman, tombol
     * detailnya tidak ada gunanya.
     */
    public function test_data_yang_tidak_muat_di_tabel_ikut_terkirim(): void
    {
        $buku = $this->buatBuku();
        $this->post(route('katalog.ajukan'), $this->dataPengajuan($buku));
        $loan = Peminjaman::first();

        $this->loginPetugas();
        $this->post(route('admin.peminjaman.request.approve', $loan->id));

        $html = $this->get(route('admin.peminjaman'))->assertOk()->getContent();

        $this->assertStringContainsString('081234567890', $html, 'No. WhatsApp harus ikut ke modal detail.');
        $this->assertStringContainsString('Untuk tugas akhir.', $html, 'Catatan siswa harus ikut ke modal detail.');
        // @js() menyandikan payload sebagai JSON.parse('...'), sehingga garis
        // miring URL-nya ikut ter-escape. Cukup periksa dua bagian pentingnya.
        $this->assertStringContainsString('wa.me', $html, 'Tautan WhatsApp harus ikut dikirim.');
        $this->assertStringContainsString('6281234567890', $html, 'Nomor lokal harus sudah diubah ke format internasional.');
    }

    public function test_nomor_lokal_diubah_ke_format_internasional(): void
    {
        $loan = new Peminjaman(['no_wa' => '0812-3456-7890']);
        $this->assertSame('6281234567890', $loan->nomor_wa_internasional);

        $loan = new Peminjaman(['no_wa' => '+62 812 3456 7890']);
        $this->assertSame('6281234567890', $loan->nomor_wa_internasional);

        $loan = new Peminjaman(['no_wa' => '-']);
        $this->assertNull($loan->nomor_wa_internasional, 'Isian yang jelas bukan nomor tidak boleh jadi tautan.');
    }
}
