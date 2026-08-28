<?php

namespace Tests\Feature;

use App\Exceptions\AturanBisnisException;
use App\Models\{Buku,Peminjaman,Rak};
use App\Services\Sirkulasi\PeminjamanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pengembalian menambahkan kembali stok yang dipotong saat buku keluar.
 * Pengajuan `pending` baru memotong stok ketika disetujui, dan yang `ditolak`
 * tidak pernah memotong sama sekali -- jadi memprosesnya sebagai pengembalian
 * menambahkan eksemplar yang tidak pernah meninggalkan rak.
 */
class PengembalianPenjagaStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{0: Buku, 1: Peminjaman} */
    private function siapkan(string $status): array
    {
        $rak = Rak::create(['kode_rak' => 'R', 'nama_rak' => 'R', 'lokasi' => 'L', 'status' => 'aktif']);

        // 10 eksemplar fisik, 4 di antaranya sedang dipegang siswa lain.
        $buku = Buku::create(['judul' => 'Uji', 'isbn' => 'X', 'rak_id' => $rak->id, 'tahun_terbit' => 2024,
            'total_quantity' => 10, 'available_quantity' => 6, 'status' => 'tersedia']);

        Peminjaman::create(['kode_peminjaman' => Peminjaman::buatKode('PJ'), 'nama_peminjam' => 'Siswa A',
            'buku_id' => $buku->id, 'jumlah' => 4, 'tanggal_pinjam' => date('Y-m-d'),
            'tanggal_jatuh_tempo' => date('Y-m-d'), 'status' => 'dipinjam']);

        $sasaran = Peminjaman::create(['kode_peminjaman' => Peminjaman::buatKode('RQ'), 'nama_peminjam' => 'Siswa B',
            'buku_id' => $buku->id, 'jumlah' => 4, 'tanggal_pinjam' => date('Y-m-d'),
            'tanggal_jatuh_tempo' => date('Y-m-d'), 'status' => $status]);

        return [$buku, $sasaran];
    }

    public static function statusYangBukanSirkulasi(): array
    {
        return [
            'pengajuan belum disetujui' => ['pending'],
            'pengajuan ditolak'         => ['ditolak'],
        ];
    }

    /**
     * @dataProvider statusYangBukanSirkulasi
     */
    public function test_stok_tidak_menggelembung(string $status): void
    {
        [$buku, $sasaran] = $this->siapkan($status);

        try {
            app(PeminjamanService::class)->kembalikan($sasaran->id);
            $this->fail("Status '{$status}' seharusnya ditolak sebagai pengembalian.");
        } catch (AturanBisnisException $e) {
            $this->assertStringContainsString('sedang berjalan', $e->getMessage());
        }

        $this->assertSame(6, $buku->fresh()->available_quantity, 'Stok naik padahal 4 eksemplar masih dipegang peminjam.');
        $this->assertSame($status, $sasaran->fresh()->status, 'Status pengajuan ikut berubah jadi dikembalikan.');
    }

    public function test_peminjaman_berjalan_tetap_bisa_dikembalikan(): void
    {
        [$buku, $sasaran] = $this->siapkan('dipinjam');

        app(PeminjamanService::class)->kembalikan($sasaran->id);

        $this->assertSame(10, $buku->fresh()->available_quantity);
        $this->assertSame('dikembalikan', $sasaran->fresh()->status);
        $this->assertNotNull($sasaran->fresh()->waktu_kembali);
    }

    public function test_pengembalian_ganda_tetap_ditolak(): void
    {
        [$buku, $sasaran] = $this->siapkan('dipinjam');
        app(PeminjamanService::class)->kembalikan($sasaran->id);

        $this->expectException(AturanBisnisException::class);
        $this->expectExceptionMessage('sudah berstatus dikembalikan');
        app(PeminjamanService::class)->kembalikan($sasaran->id);
    }
}
