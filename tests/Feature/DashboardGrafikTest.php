<?php
namespace Tests\Feature;

use App\Models\{Buku,Kategori,Peminjaman,Rak};
use App\Services\Statistik\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardGrafikTest extends TestCase
{
    use RefreshDatabase;

    private function buku(): Buku
    {
        $rak = Rak::create(['kode_rak'=>'R','nama_rak'=>'R','lokasi'=>'L','status'=>'aktif']);
        $kategori = Kategori::create(['nama'=>'Sastra','slug'=>'sastra','status'=>'aktif']);
        return Buku::create(['judul'=>'Bumi Manusia','isbn'=>'X','rak_id'=>$rak->id,'kategori_id'=>$kategori->id,
            'tahun_terbit'=>2024,'total_quantity'=>50,'available_quantity'=>50,'status'=>'tersedia']);
    }

    private function catat(Buku $buku, string $status, int $jumlah, ?string $kembali): void
    {
        Peminjaman::create([
            'kode_peminjaman' => Peminjaman::buatKode('T'),
            'nama_peminjam'   => 'Siswa',
            'buku_id'         => $buku->id,
            'jumlah'          => $jumlah,
            'tanggal_pinjam'  => date('Y-m-d'),
            'tanggal_jatuh_tempo' => date('Y-m-d', strtotime('+7 days')),
            'waktu_kembali'   => $kembali,
            'status'          => $status,
        ]);
    }

    /**
     * Pengajuan yang ditolak dan yang masih menunggu tidak pernah keluar dari
     * rak, jadi tidak boleh mengangkat garis peminjaman. Kalau ikut terhitung,
     * garis merah berdiri di atas garis hijau meski semua buku sudah kembali.
     */
    public function test_garis_peminjaman_sama_dengan_pengembalian_saat_semua_buku_sudah_kembali(): void
    {
        $buku = $this->buku();
        $this->catat($buku, 'dikembalikan', 6, now()->toDateTimeString());
        $this->catat($buku, 'dikembalikan', 4, now()->toDateTimeString());
        $this->catat($buku, 'ditolak', 5, null);
        $this->catat($buku, 'pending', 3, null);

        $data = (new DashboardService)->ringkasan();
        $bulan = (int) date('n') - 1;

        $this->assertSame(10, $data['chartMonthly']['loans'][$bulan]);
        $this->assertSame(10, $data['chartMonthly']['returns'][$bulan]);
        $this->assertSame(10, $data['chartYearly']['loans'][4]);
        $this->assertSame(10, $data['chartYearly']['returns'][4]);
        $this->assertSame(10, $data['kategoriBorrowTotal']);
        $this->assertSame(2, $data['mostBorrowedBooks']->first()->peminjaman_count);
    }

    /**
     * Peminjaman yang masih berjalan tetap boleh mengangkat garis merah --
     * bedanya dengan pengajuan ditolak, bukunya memang sedang di luar rak.
     */
    public function test_peminjaman_yang_masih_berjalan_tetap_dihitung(): void
    {
        $buku = $this->buku();
        $this->catat($buku, 'dipinjam', 7, null);

        $data = (new DashboardService)->ringkasan();
        $bulan = (int) date('n') - 1;

        $this->assertSame(7, $data['chartMonthly']['loans'][$bulan]);
        $this->assertSame(0, $data['chartMonthly']['returns'][$bulan]);
    }
}
