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
 * Petugas tidak selalu membuka menu Sirkulasi. Angka di sidebar dan banner di
 * dashboard-lah yang memberi tahu ada yang perlu ditangani tanpa harus dicari.
 */
class NotifikasiSirkulasiTest extends TestCase
{
    use RefreshDatabase;

    private function buatBuku(int $total = 10): Buku
    {
        $rak = Rak::firstOrCreate(['kode_rak' => 'RAK-NOTIF'],
            ['nama_rak' => 'Rak Uji', 'lokasi' => 'L1', 'status' => 'aktif']);

        return Buku::create([
            'judul' => 'Buku Notifikasi', 'isbn' => 'ISBN-' . uniqid(),
            'rak_id' => $rak->id, 'tahun_terbit' => 2024,
            'total_quantity' => $total, 'available_quantity' => $total, 'status' => 'tersedia',
        ]);
    }

    private function loginPetugas(): User
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $user = User::create([
            'name' => 'Petugas Uji', 'email' => 'notif@uji.test', 'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]);
        $this->actingAs($user);

        return $user;
    }

    private function buatPinjaman(Buku $buku, string $status, int $jumlah = 1, ?string $jatuhTempo = null): Peminjaman
    {
        return Peminjaman::create([
            'kode_peminjaman'     => Peminjaman::buatKode('PJ'),
            'sumber'              => 'petugas',
            'nama_peminjam'       => 'Siswa ' . uniqid(),
            'jurusan'             => 'XII RPL',
            'buku_id'             => $buku->id,
            'jumlah'              => $jumlah,
            'tanggal_pinjam'      => now()->subDays(3)->toDateString(),
            'tanggal_jatuh_tempo' => $jatuhTempo ?? now()->addDays(4)->toDateString(),
            'status'              => $status,
        ]);
    }

    // --------------------------------------------------------- badge sidebar

    public function test_sidebar_menampilkan_jumlah_peminjaman_yang_sedang_berjalan(): void
    {
        $buku = $this->buatBuku();
        $this->buatPinjaman($buku, 'dipinjam');
        $this->buatPinjaman($buku, 'dipinjam');
        $this->buatPinjaman($buku, 'dipinjam');
        $this->buatPinjaman($buku, 'dikembalikan');

        $this->loginPetugas();
        $html = $this->get(route('admin.dashboard'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/Peminjaman Aktif.*?rounded-full[^>]*>\s*3\s*</s',
            $html,
            'Badge sidebar harus memuat 3 peminjaman berjalan, tanpa menghitung yang sudah kembali.'
        );
    }

    public function test_badge_tidak_muncul_saat_tidak_ada_peminjaman_berjalan(): void
    {
        $buku = $this->buatBuku();
        $this->buatPinjaman($buku, 'dikembalikan');

        $this->loginPetugas();
        $html = $this->get(route('admin.dashboard'))->assertOk()->getContent();

        $this->assertDoesNotMatchRegularExpression(
            '/Peminjaman Aktif.*?rounded-full[^>]*>\s*0\s*</s',
            $html,
            'Angka nol tidak perlu ditampilkan sebagai badge.'
        );
    }

    /**
     * Merah disimpan untuk yang benar-benar perlu ditindak. Peminjaman yang
     * masih dalam tenggat adalah keadaan normal.
     */
    public function test_badge_berubah_merah_hanya_saat_ada_yang_terlambat(): void
    {
        $buku = $this->buatBuku();
        $this->buatPinjaman($buku, 'dipinjam');

        $this->loginPetugas();
        $html = $this->get(route('admin.dashboard'))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/Peminjaman Aktif.*?bg-brand-700 text-white/s', $html);

        $this->buatPinjaman($buku, 'dipinjam', 1, now()->subDay()->toDateString());

        $html = $this->get(route('admin.dashboard'))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/Peminjaman Aktif.*?bg-rose-600 text-white/s', $html);
    }

    /** Badge ikut halaman lain, bukan hanya dashboard. */
    public function test_badge_ikut_muncul_di_halaman_pengelola_lain(): void
    {
        $buku = $this->buatBuku();
        $this->buatPinjaman($buku, 'dipinjam');
        $this->buatPinjaman($buku, 'dipinjam');

        $this->loginPetugas();

        foreach ([route('admin.buku'), route('admin.anggota'), route('admin.riwayat')] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertMatchesRegularExpression(
                '/Peminjaman Aktif.*?rounded-full[^>]*>\s*2\s*</s',
                $html,
                "Badge hilang di {$url}."
            );
        }
    }

    // ------------------------------------------------------ banner dashboard

    public function test_dashboard_memberi_tahu_ada_pengajuan_yang_menunggu(): void
    {
        $buku = $this->buatBuku();
        $this->post(route('katalog.ajukan'), [
            'buku_id' => $buku->id, 'nama_peminjam' => 'Rani', 'jurusan' => 'XII RPL',
            'nomor_induk' => '001', 'no_wa' => '081234567890', 'jumlah' => 1,
        ])->assertSessionHasNoErrors();

        $this->loginPetugas();
        $html = $this->get(route('admin.dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('1 pengajuan peminjaman menunggu konfirmasi', $html);
        $this->assertStringContainsString(route('admin.peminjaman.request'), $html);
    }

    public function test_banner_hilang_setelah_semua_pengajuan_diproses(): void
    {
        $buku = $this->buatBuku();
        $this->post(route('katalog.ajukan'), [
            'buku_id' => $buku->id, 'nama_peminjam' => 'Rani', 'jurusan' => 'XII RPL',
            'nomor_induk' => '001', 'no_wa' => '081234567890', 'jumlah' => 1,
        ]);
        $loan = Peminjaman::first();

        $this->loginPetugas();
        $this->post(route('admin.peminjaman.request.approve', $loan->id))->assertSessionHas('success');

        $html = $this->get(route('admin.dashboard'))->assertOk()->getContent();
        $this->assertStringNotContainsString('pengajuan peminjaman menunggu konfirmasi', $html);
    }

    // --------------------------------------------------------- kartu sirkulasi

    /**
     * Satu peminjaman bisa membawa beberapa eksemplar, jadi jumlah buku dan
     * jumlah transaksi memang berbeda — dan keduanya perlu terbaca.
     */
    public function test_kartu_membedakan_jumlah_buku_dari_jumlah_transaksi(): void
    {
        $buku = $this->buatBuku(20);
        $this->buatPinjaman($buku, 'dipinjam', 3);
        $this->buatPinjaman($buku, 'dipinjam', 2);

        $this->loginPetugas();
        $html = $this->get(route('admin.dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('5 Buku', $html, 'Jumlah eksemplar harus dijumlahkan.');
        $this->assertStringContainsString('dari 2 peminjaman aktif', $html, 'Jumlah transaksinya harus terbaca juga.');
    }
}
