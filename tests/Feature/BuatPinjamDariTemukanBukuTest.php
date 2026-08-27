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
 * Tidak semua siswa mengajukan sendiri lewat katalog OPAC — banyak yang datang
 * ke meja dan minta dibuatkan. Halaman Temukan Buku adalah tempat petugas
 * sudah berada saat mencarikan bukunya, jadi peminjamannya dibuat dari situ.
 */
class BuatPinjamDariTemukanBukuTest extends TestCase
{
    use RefreshDatabase;

    private function buatBuku(int $total = 5, string $judul = 'Bumi Manusia'): Buku
    {
        $rak = Rak::firstOrCreate(['kode_rak' => 'RAK-TB'],
            ['nama_rak' => 'Rak Sastra', 'lokasi' => 'Lantai 1', 'status' => 'aktif']);

        return Buku::create([
            'judul' => $judul, 'isbn' => 'ISBN-' . uniqid(),
            'rak_id' => $rak->id, 'tahun_terbit' => 2024,
            'total_quantity' => $total, 'available_quantity' => $total, 'status' => 'tersedia',
        ]);
    }

    private function loginPetugas(): User
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $user = User::create([
            'name' => 'Petugas Meja', 'email' => 'meja@uji.test', 'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]);
        $this->actingAs($user);

        return $user;
    }

    // ------------------------------------------------------------ tampilan

    public function test_halaman_menyediakan_tombol_buat_pinjam(): void
    {
        $this->buatBuku();
        $this->loginPetugas();

        $html = $this->get(route('admin.temukan-buku'))->assertOk()->getContent();

        $this->assertStringContainsString('Buat Pinjam', $html);
        $this->assertStringContainsString('openLoanForm(', $html);
        $this->assertStringContainsString(route('admin.peminjaman.store'), $html,
            'Formulirnya harus menembak endpoint sirkulasi yang sama.');
    }

    public function test_tombol_mati_untuk_buku_yang_stoknya_habis(): void
    {
        $ada = $this->buatBuku(3, 'Masih Ada');
        $habis = $this->buatBuku(2, 'Sudah Habis');
        $habis->update(['available_quantity' => 0]);

        $this->loginPetugas();
        $html = $this->get(route('admin.temukan-buku'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/Sudah Habis.*?disabled/s', $html,
            'Buku tanpa stok tidak boleh bisa dibuatkan peminjaman.'
        );
        // Payload kartu ditanam lewat {{ }}, jadi tanda kutipnya ter-escape.
        $this->assertMatchesRegularExpression(
            '/&quot;id&quot;:' . $ada->id . '.*?&quot;bisa_dipinjam&quot;:true/s', $html,
            'Buku yang stoknya masih ada harus ditandai bisa dipinjam.'
        );
    }

    // ------------------------------------------------------------- fungsi

    /** Yang paling penting: hasilnya langsung aktif, bukan mengantre. */
    public function test_peminjaman_langsung_masuk_ke_peminjaman_aktif(): void
    {
        $buku = $this->buatBuku(5);
        $petugas = $this->loginPetugas();

        $this->from(route('admin.temukan-buku'))
            ->post(route('admin.peminjaman.store'), [
                'nama_peminjam' => 'Ihwal Maulana',
                'jurusan'       => 'XII RPL 1',
                'nomor_induk'   => '0065123489',
                'no_wa'         => '081234567890',
                'buku_id'       => $buku->id,
                'jumlah'        => 2,
            ])
            ->assertRedirect(route('admin.temukan-buku'))
            ->assertSessionHas('success');

        $loan = Peminjaman::first();
        $this->assertSame('dipinjam', $loan->status, 'Harus langsung aktif, bukan pending.');
        $this->assertSame('petugas', $loan->sumber);
        $this->assertSame(2, (int) $loan->jumlah);
        $this->assertSame($petugas->id, $loan->petugas_id);
        $this->assertStringStartsWith('PJ-', $loan->kode_peminjaman);

        $this->assertSame(3, $buku->refresh()->available_quantity, 'Stok harus langsung berkurang.');

        // Benar-benar terbaca di halaman Peminjaman Aktif.
        $html = $this->get(route('admin.peminjaman'))->assertOk()->getContent();
        $this->assertStringContainsString('Ihwal Maulana', $html);
        $this->assertStringContainsString($loan->kode_peminjaman, $html);
    }

    public function test_tidak_ikut_ke_daftar_pengajuan_yang_perlu_disetujui(): void
    {
        $buku = $this->buatBuku();
        $this->loginPetugas();

        $this->post(route('admin.peminjaman.store'), [
            'nama_peminjam' => 'Rani', 'jurusan' => 'XI TKJ', 'no_wa' => '081234567890',
            'buku_id' => $buku->id, 'jumlah' => 1,
        ]);

        $this->assertSame(0, Peminjaman::where('status', 'pending')->count(),
            'Peminjaman yang dibuat petugas tidak perlu persetujuan siapa pun.');

        // Diperiksa lewat data yang dikirim ke view, bukan teks halamannya:
        // nama peminjam ikut muncul di pesan flash "berhasil dicatat", jadi
        // mencarinya di HTML akan selalu ketemu dan tidak membuktikan apa pun.
        $daftar = $this->get(route('admin.peminjaman.request'))->assertOk()->viewData('requestList');
        $this->assertCount(0, $daftar, 'Daftar pengajuan yang menunggu harus tetap kosong.');
    }

    public function test_jumlah_melebihi_stok_ditolak_dan_stok_tidak_berubah(): void
    {
        $buku = $this->buatBuku(2);
        $this->loginPetugas();

        $this->from(route('admin.temukan-buku'))
            ->post(route('admin.peminjaman.store'), [
                'nama_peminjam' => 'Budi', 'jurusan' => 'XII', 'no_wa' => '081234567890',
                'buku_id' => $buku->id, 'jumlah' => 5,
            ])
            ->assertRedirect(route('admin.temukan-buku'))
            ->assertSessionHas('error');

        $this->assertSame(0, Peminjaman::count());
        $this->assertSame(2, $buku->refresh()->available_quantity);
    }

    public function test_kesalahan_isian_terlihat_di_halaman(): void
    {
        $buku = $this->buatBuku();
        $this->loginPetugas();

        $this->from(route('admin.temukan-buku'))
            ->post(route('admin.peminjaman.store'), [
                'nama_peminjam' => '', 'jurusan' => 'XII', 'no_wa' => '081234567890',
                'buku_id' => $buku->id, 'jumlah' => 1,
            ])
            ->assertSessionHasErrors('nama_peminjam');

        // Halaman ini sebelumnya tidak menampilkan $errors sama sekali, jadi
        // kegagalan validasi akan hilang tanpa jejak di layar petugas.
        $html = $this->followingRedirects()
            ->from(route('admin.temukan-buku'))
            ->post(route('admin.peminjaman.store'), [
                'nama_peminjam' => '', 'jurusan' => 'XII', 'no_wa' => '081234567890',
                'buku_id' => $buku->id, 'jumlah' => 1,
            ])->getContent();

        $this->assertStringContainsString('Terdapat kesalahan pada input formulir', $html);
    }

    /**
     * Petugas sering perlu mengingatkan buku yang jatuh tempo, dan satu-satunya
     * cara menghubungi siswa adalah nomor yang dicatat saat meminjam.
     */
    public function test_nomor_telepon_ikut_tersimpan_dan_bisa_dihubungi(): void
    {
        $buku = $this->buatBuku();
        $this->loginPetugas();

        $html = $this->get(route('admin.temukan-buku'))->assertOk()->getContent();
        $this->assertStringContainsString('name="no_wa"', $html,
            'Formulir buat pinjam harus punya kolom nomor telepon.');

        $this->post(route('admin.peminjaman.store'), [
            'nama_peminjam' => 'Ihwal Maulana', 'jurusan' => 'XII RPL 1',
            'no_wa' => '0812-3456-7890', 'buku_id' => $buku->id, 'jumlah' => 1,
        ])->assertSessionHas('success');

        $loan = Peminjaman::first();
        $this->assertSame('0812-3456-7890', $loan->no_wa);
        // Yang dipakai tombol "Hubungi lewat WhatsApp" di halaman sirkulasi.
        $this->assertSame('6281234567890', $loan->nomor_wa_internasional);
        $this->assertSame('0812-3456-7890', $loan->data_detail['no_wa']);
    }

    /** Wajib, sama seperti pengajuan lewat katalog OPAC. */
    public function test_nomor_telepon_wajib_diisi(): void
    {
        $buku = $this->buatBuku();
        $this->loginPetugas();

        $this->from(route('admin.temukan-buku'))
            ->post(route('admin.peminjaman.store'), [
                'nama_peminjam' => 'Rani', 'jurusan' => 'XI TKJ', 'buku_id' => $buku->id, 'jumlah' => 1,
            ])
            ->assertSessionHasErrors('no_wa');

        $this->assertSame(0, Peminjaman::count());
        $this->assertSame(5, $buku->refresh()->available_quantity, 'Stok tidak boleh berkurang.');
    }

    public function test_petugas_biasa_juga_boleh_membuat_peminjaman(): void
    {
        $buku = $this->buatBuku();
        $role = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator']);
        $this->actingAs(User::create([
            'name' => 'Petugas', 'email' => 'biasa@uji.test', 'password' => Hash::make('x'),
            'role_id' => $role->id, 'status' => 'active',
        ]));

        $this->get(route('admin.temukan-buku'))->assertOk();
        $this->post(route('admin.peminjaman.store'), [
            'nama_peminjam' => 'Sari', 'jurusan' => 'X MM', 'no_wa' => '081234567890',
            'buku_id' => $buku->id, 'jumlah' => 1,
        ])->assertSessionHas('success');

        $this->assertSame('dipinjam', Peminjaman::first()->status);
    }

    public function test_tamu_tidak_bisa_membuat_peminjaman(): void
    {
        $buku = $this->buatBuku();

        $this->post(route('admin.peminjaman.store'), [
            'nama_peminjam' => 'Penyusup', 'jurusan' => 'X', 'buku_id' => $buku->id, 'jumlah' => 1,
        ])->assertRedirect();

        $this->assertSame(0, Peminjaman::count());
        $this->assertSame(5, $buku->refresh()->available_quantity);
    }
}
