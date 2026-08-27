<?php

namespace App\Services\Sirkulasi;

use App\Exceptions\AturanBisnisException;
use App\Models\AuditLog;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use App\Services\PengaturanService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Sirkulasi buku: mencatat peminjaman di meja petugas dan memproses
 * pengembaliannya.
 *
 * Seluruh perubahan stok dikerjakan di sini, di dalam transaksi dengan baris
 * bukunya terkunci, supaya dua petugas yang bekerja bersamaan di dua komputer
 * tidak saling menimpa hitungan stok.
 */
class PeminjamanService
{
    public function __construct(private PengaturanService $pengaturan)
    {
    }

    /**
     * Peminjaman yang sedang berjalan, untuk halaman Sirkulasi.
     */
    public function daftarAktif(?string $cari = null, bool $hanyaTerlambat = false)
    {
        // buku.penulis ikut dimuat karena modal "Detail" menampilkannya; tanpa
        // ini setiap baris memicu query tambahan sendiri.
        $query = Peminjaman::with(['user', 'buku.penulis', 'petugas'])->where('status', 'dipinjam');

        if ($hanyaTerlambat) {
            $query->whereDate('tanggal_jatuh_tempo', '<', Carbon::today()->toDateString());
        }

        $this->terapkanPencarian($query, $cari);

        return $query->latest()->paginate(10)->withQueryString();
    }

    /**
     * Angka ringkas di atas halaman Sirkulasi.
     */
    public function ringkasanAktif(): array
    {
        $hariIni = Carbon::today()->toDateString();

        return [
            'totalActive'  => Peminjaman::where('status', 'dipinjam')->count(),
            'totalOverdue' => Peminjaman::where('status', 'dipinjam')->whereDate('tanggal_jatuh_tempo', '<', $hariIni)->count(),
        ];
    }

    /**
     * Buku yang masih boleh dipinjamkan, untuk pilihan di form peminjaman.
     */
    public function bukuYangBisaDipinjam()
    {
        return Buku::where('status', 'tersedia')
            ->where('available_quantity', '>', 0)
            ->orderBy('judul', 'asc')
            ->get();
    }

    /**
     * Seluruh transaksi yang pernah terjadi, untuk halaman Riwayat.
     */
    public function riwayat(array $filter)
    {
        $query = Peminjaman::with(['user', 'buku', 'petugas']);

        if (filled($filter['status'] ?? null)) {
            $query->where('status', $filter['status']);
        }
        if (filled($filter['tanggal'] ?? null)) {
            $query->whereDate('tanggal_pinjam', $filter['tanggal']);
        }
        if (filled($filter['cari'] ?? null)) {
            $cari = trim($filter['cari']);
            $query->where(function ($q) use ($cari) {
                $q->where('kode_peminjaman', 'like', "%{$cari}%")
                  ->orWhere('nama_peminjam', 'like', "%{$cari}%")
                  ->orWhere('jurusan', 'like', "%{$cari}%")
                  ->orWhere('nomor_induk', 'like', "%{$cari}%")
                  ->orWhereHas('user', function ($qu) use ($cari) {
                      $qu->where('name', 'like', "%{$cari}%");
                  })
                  ->orWhereHas('buku', function ($qb) use ($cari) {
                      $qb->where('judul', 'like', "%{$cari}%");
                  });
            });
        }

        return $query->latest('tanggal_pinjam')->paginate(15)->withQueryString();
    }

    /**
     * Catat peminjaman baru dari meja petugas.
     *
     * @throws AturanBisnisException bila stok bukunya tidak mencukupi
     */
    public function catat(array $data): Peminjaman
    {
        $buku = Buku::findOrFail($data['buku_id']);
        $jumlah = (int) $data['jumlah'];

        if ($buku->available_quantity < $jumlah) {
            throw new AturanBisnisException("Stok buku tidak mencukupi. Sisa stok tersedia saat ini: {$buku->available_quantity} buku.");
        }

        $durasiHari = (int) Pengaturan::ambil('durasi_pinjam_hari', 7);
        $kode = Peminjaman::buatKode('PJ');
        $hariIni = Carbon::today()->toDateString();
        $jatuhTempo = Carbon::today()->addDays($durasiHari)->toDateString();

        $peminjaman = DB::transaction(function () use ($buku, $data, $jumlah, $kode, $hariIni, $jatuhTempo) {
            // Stok dibaca ulang dengan barisnya terkunci: nilai yang diperiksa
            // di atas bisa saja sudah berubah oleh petugas lain sejak dibaca.
            $bukuTerkunci = Buku::where('id', $buku->id)->lockForUpdate()->first();
            if ($bukuTerkunci->available_quantity < $jumlah) {
                throw new AturanBisnisException('Stok buku tidak mencukupi untuk jumlah peminjaman yang diminta.');
            }

            $bukuTerkunci->available_quantity -= $jumlah;
            $bukuTerkunci->save();

            return Peminjaman::create([
                'kode_peminjaman'     => $kode,
                'sumber'              => 'petugas',
                'nama_peminjam'       => trim($data['nama_peminjam']),
                'jurusan'             => trim($data['jurusan']),
                'nomor_induk'         => filled($data['nomor_induk'] ?? null) ? trim($data['nomor_induk']) : null,
                'no_wa'               => trim($data['no_wa']),
                'user_id'             => auth()->id(),
                'buku_id'             => $buku->id,
                'jumlah'              => $jumlah,
                'tanggal_pinjam'      => $hariIni,
                'tanggal_jatuh_tempo' => $jatuhTempo,
                'status'              => 'dipinjam',
                'petugas_id'          => auth()->id(),
            ]);
        });

        AuditLog::catat('TRANSAKSI_PINJAM', "Mencatat peminjaman {$peminjaman->kode_peminjaman} untuk {$peminjaman->nama_peminjam} ({$peminjaman->jumlah} buku)");

        return $peminjaman;
    }

    /**
     * Proses pengembalian: stok bukunya dikembalikan, transaksinya ditutup.
     *
     * @throws AturanBisnisException bila transaksinya sudah pernah dikembalikan
     */
    public function kembalikan(int $id): Peminjaman
    {
        $peminjaman = DB::transaction(function () use ($id) {
            $terkunci = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($terkunci->status === 'dikembalikan') {
                throw new AturanBisnisException('Transaksi peminjaman ini sudah berstatus dikembalikan sebelumnya.');
            }

            $buku = Buku::where('id', $terkunci->buku_id)->lockForUpdate()->first();
            if ($buku) {
                // Dibatasi total_quantity supaya stok tidak pernah melebihi
                // jumlah fisik yang benar-benar dimiliki perpustakaan.
                $buku->available_quantity = min($buku->total_quantity, $buku->available_quantity + $terkunci->jumlah);
                $buku->save();
            }

            $terkunci->status = 'dikembalikan';
            $terkunci->waktu_kembali = Carbon::now();
            $terkunci->save();

            return $terkunci;
        });

        AuditLog::catat('TRANSAKSI_KEMBALI', "Buku transaksi {$peminjaman->kode_peminjaman} telah berhasil dikembalikan.");

        return $peminjaman;
    }

    /**
     * Siapkan data laporan sirkulasi sekaligus catat pencetakannya.
     *
     * @param string $format 'excel' atau 'pdf'
     */
    public function siapkanLaporan(array $filter, string $format): array
    {
        $query = Peminjaman::with(['user', 'buku.rak', 'buku.laci', 'petugas']);

        if (filled($filter['status'] ?? null) && $filter['status'] !== 'all') {
            $query->where('status', $filter['status']);
        }
        if (filled($filter['tanggal'] ?? null)) {
            $query->whereDate('tanggal_pinjam', $filter['tanggal']);
        }
        $this->terapkanPencarian($query, $filter['cari'] ?? null);

        $items = $query->latest('tanggal_pinjam')->get();

        $ringkasan = [
            // Identitas sekolah ikut dibawa karena kedua format laporan
            // mencetaknya di kop dan kolom tanda tangan.
            'identitas'       => $this->pengaturan->identitasLaporan(),
            'pengaturan'      => $this->pengaturan->semua(),
            'loanItems'       => $items,
            'totalTransaksi'  => $items->count(),
            'totalDipinjam'   => $items->where('status', 'dipinjam')->count(),
            'totalKembali'    => $items->where('status', 'dikembalikan')->count(),
            'totalBukuPinjam' => (int) $items->sum('jumlah'),
        ];

        if ($format === 'excel') {
            AuditLog::catat('EXPORT_PINJAM_EXCEL', "Mengekspor {$ringkasan['totalTransaksi']} data sirkulasi peminjaman ke format Excel");
        } else {
            AuditLog::catat('EXPORT_PINJAM_PDF', "Membuka dan mencetak laporan sirkulasi peminjaman PDF ({$ringkasan['totalTransaksi']} transaksi)");
        }

        return $ringkasan;
    }

    /**
     * Pencarian yang dipakai halaman Sirkulasi dan laporannya: kode transaksi,
     * identitas peminjam, sampai judul bukunya.
     */
    private function terapkanPencarian($query, ?string $cari): void
    {
        if (blank($cari)) {
            return;
        }

        $cari = trim($cari);
        $query->where(function ($q) use ($cari) {
            $q->where('kode_peminjaman', 'like', "%{$cari}%")
              ->orWhere('nama_peminjam', 'like', "%{$cari}%")
              ->orWhere('jurusan', 'like', "%{$cari}%")
              ->orWhere('nomor_induk', 'like', "%{$cari}%")
              ->orWhereHas('buku', function ($qb) use ($cari) {
                  $qb->where('judul', 'like', "%{$cari}%");
              });
        });
    }
}
