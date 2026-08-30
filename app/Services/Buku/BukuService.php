<?php

namespace App\Services\Buku;

use App\Exceptions\AturanBisnisException;
use App\Models\AuditLog;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Kelas;
use App\Models\Peminjaman;
use App\Models\Penerbit;
use App\Models\Penulis;
use App\Models\Rak;
use App\Services\CoverImageService;
use App\Services\PengaturanService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Katalog buku: menambah, mengubah, dan menghapus judul beserta sampulnya.
 */
class BukuService
{
    public function __construct(
        private CoverImageService $cover,
        private PengaturanService $pengaturan,
    ) {
    }

    /**
     * Ambil satu buku, atau hentikan permintaan dengan 404 bila tidak ada.
     */
    public function temukan(int $id): Buku
    {
        return Buku::findOrFail($id);
    }

    /**
     * Daftar pilihan untuk form tambah/ubah buku.
     */
    public function pilihanForm(): array
    {
        return [
            'penulisList'  => Penulis::orderBy('nama', 'asc')->get(),
            'penerbitList' => Penerbit::orderBy('nama', 'asc')->get(),
            'kategoriList' => Kategori::orderBy('nama', 'asc')->get(),
            'kelasList'    => Kelas::orderBy('tingkat_angka', 'asc')->orderBy('nama_kelas', 'asc')->get(),
            'rakList'      => Rak::with('laci')->orderBy('kode_rak', 'asc')->get(),
        ];
    }

    /**
     * Tambahkan judul baru. Stok tersedia dimulai sama dengan stok fisiknya,
     * karena belum ada satu eksemplar pun yang dipinjam.
     */
    public function simpan(array $data, ?UploadedFile $cover = null): Buku
    {
        $buku = Buku::create($this->kolomBuku($data) + [
            'available_quantity' => $data['total_quantity'],
            'cover'              => $cover ? $this->cover->store($cover) : null,
            'status'             => 'tersedia',
        ]);

        AuditLog::catat('TAMBAH_BUKU', "Menambahkan buku baru: '{$buku->judul}' (Stok: {$buku->total_quantity})");

        return $buku;
    }

    /**
     * Perbarui data buku, termasuk menyesuaikan stok bila jumlah fisiknya
     * berubah.
     */
    public function perbarui(Buku $buku, array $data, ?UploadedFile $cover = null): Buku
    {
        $berkasCover = $buku->cover;
        if ($cover) {
            // Hapus berikut variannya, jangan hanya file aslinya.
            $this->cover->delete($buku->cover);
            $berkasCover = $this->cover->store($cover);
        }

        // Stok dihitung ulang di dalam transaksi dengan baris bukunya terkunci.
        // $buku dibaca di awal request, dan di antara pembacaan itu dengan
        // penulisan ini ada kompresi cover yang bisa memakan hitungan detik —
        // cukup lama untuk sebuah peminjaman menyelip. Tanpa kunci, peminjaman
        // itu tertimpa nilai lama dan stoknya kembali seolah tidak pernah ada.
        DB::transaction(function () use ($buku, $data, $berkasCover) {
            $terkunci = Buku::where('id', $buku->id)->lockForUpdate()->firstOrFail();

            $selisih = (int) $data['total_quantity'] - (int) $terkunci->total_quantity;
            $tersediaBaru = max(0, (int) $terkunci->available_quantity + $selisih);

            $terkunci->update($this->kolomBuku($data) + [
                'available_quantity' => $tersediaBaru,
                'cover'              => $berkasCover,
            ]);
        });

        $buku->refresh();

        AuditLog::catat('UPDATE_BUKU', "Memperbarui data buku: '{$buku->judul}'");

        return $buku;
    }

    /**
     * Hapus judul dari katalog.
     *
     * @return string judul buku yang dihapus
     * @throws AturanBisnisException bila buku masih dipinjam atau punya riwayat
     */
    public function hapus(Buku $buku): string
    {
        $peminjamanAktif = Peminjaman::where('buku_id', $buku->id)->whereIn('status', ['dipinjam', 'pending'])->count();
        if ($peminjamanAktif > 0) {
            throw new AturanBisnisException("Buku tidak dapat dihapus karena masih memiliki {$peminjamanAktif} transaksi peminjaman aktif atau menunggu persetujuan.");
        }

        // Riwayat sirkulasi adalah catatan resmi perpustakaan, dan baris riwayat
        // tanpa data bukunya tidak berarti apa-apa. Penjaga di atas hanya
        // menahan peminjaman yang masih berjalan, sehingga buku yang seluruh
        // peminjamannya sudah dikembalikan bisa dihapus — dan seluruh arsip
        // peminjamannya ikut lenyap diam-diam lewat ON DELETE CASCADE.
        $riwayat = Peminjaman::where('buku_id', $buku->id)->count();
        if ($riwayat > 0) {
            throw new AturanBisnisException("Buku '{$buku->judul}' tidak dapat dihapus karena tercatat dalam {$riwayat} riwayat peminjaman. Menghapusnya akan menghilangkan catatan sirkulasi tersebut dari laporan perpustakaan.");
        }

        $this->cover->delete($buku->cover);

        $judul = $buku->judul;
        $buku->delete();

        AuditLog::catat('HAPUS_BUKU', "Menghapus buku: '{$judul}'");

        return $judul;
    }

    /**
     * Siapkan data laporan inventaris sekaligus catat pencetakannya.
     *
     * @param string $format 'excel' atau 'pdf'
     */
    public function siapkanLaporan(array $filter, string $format): array
    {
        $query = Buku::with(['penulis', 'penerbit', 'kategori', 'rak', 'laci', 'kelas']);

        foreach (['kategori_id', 'rak_id', 'kelas_id'] as $kolom) {
            if (filled($filter[$kolom] ?? null)) {
                $query->where($kolom, $filter[$kolom]);
            }
        }

        $items = $query->orderBy('judul', 'asc')->get();

        $totalEksemplar = (int) $items->sum('total_quantity');
        $totalTersedia = (int) $items->sum('available_quantity');

        $laporan = [
            // Identitas sekolah ikut dibawa karena kedua format laporan
            // mencetaknya di kop dan kolom tanda tangan.
            'identitas'      => $this->pengaturan->identitasLaporan(),
            'pengaturan'     => $this->pengaturan->semua(),
            'bukuItems'      => $items,
            'totalJudul'     => $items->count(),
            'totalEksemplar' => $totalEksemplar,
            'totalTersedia'  => $totalTersedia,
            'totalDipinjam'  => $totalEksemplar - $totalTersedia,
        ];

        if ($format === 'excel') {
            AuditLog::catat('EXPORT_BUKU_EXCEL', "Mengekspor {$laporan['totalJudul']} data buku ke format Excel");
        } else {
            AuditLog::catat('EXPORT_BUKU_PDF', "Membuka dan mencetak laporan inventaris PDF ({$laporan['totalJudul']} buku)");
        }

        return $laporan;
    }

    /**
     * Kolom yang sama-sama ditulis saat menambah maupun mengubah buku.
     */
    private function kolomBuku(array $data): array
    {
        return [
            'isbn'              => $data['isbn'] ?? null,
            'judul'             => $data['judul'],
            'tahun_terbit'      => $data['tahun_terbit'],
            'total_quantity'    => $data['total_quantity'],
            'penulis_id'        => $data['penulis_id'] ?? null,
            'penerbit_id'       => $data['penerbit_id'] ?? null,
            'kategori_id'       => $data['kategori_id'] ?? null,
            'kelas_id'          => $data['kelas_id'] ?? null,
            'rak_id'            => $data['rak_id'] ?? null,
            'rak_laci_id'       => $data['rak_laci_id'] ?? null,
            'sinopsis'          => $data['sinopsis'] ?? null,
            'keterangan_posisi' => $data['keterangan_posisi'] ?? null,
        ];
    }
}
