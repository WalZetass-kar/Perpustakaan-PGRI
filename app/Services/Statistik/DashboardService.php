<?php

namespace App\Services\Statistik;

use App\Models\AuditLog;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\Penerbit;
use App\Models\Penulis;
use App\Models\Rak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Angka-angka yang ditampilkan di dashboard: stok koleksi, grafik sirkulasi
 * per bulan dan per tahun, serta kategori buku yang paling sering dipinjam.
 */
class DashboardService
{
    private const NAMA_BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    /** Kategori terlaris ditampilkan sebanyak ini, sisanya dijadikan satu. */
    private const KATEGORI_DITAMPILKAN = 6;

    /**
     * Seluruh data yang ditampilkan di dashboard.
     */
    public function ringkasan(): array
    {
        [$kategoriChart, $kategoriTerpopuler, $kategoriBorrowTotal] = $this->ringkasanKategori();

        return [
            'stats'               => $this->angkaRingkas(),
            'chartMonthly'        => $this->grafikBulanan(),
            'chartYearly'         => $this->grafikTahunan(),
            'kategoriChart'       => $kategoriChart,
            'kategoriTerpopuler'  => $kategoriTerpopuler,
            'kategoriBorrowTotal' => $kategoriBorrowTotal,
            'recentLoans'         => Peminjaman::with(['user', 'buku'])->latest()->take(6)->get(),
            'mostBorrowedBooks'   => Buku::withCount('peminjaman')->orderBy('peminjaman_count', 'desc')->take(5)->get(),
            'recentAuditLogs'     => AuditLog::latest()->take(6)->get(),
        ];
    }

    /**
     * Kartu-kartu angka di bagian atas dashboard.
     */
    private function angkaRingkas(): array
    {
        $today = Carbon::today()->toDateString();

        return [
            'total_judul'            => Buku::count(),
            'total_buku'             => (int) Buku::sum('total_quantity'),
            'buku_tersedia'          => (int) Buku::sum('available_quantity'),
            'buku_sedang_dipinjam'   => (int) Peminjaman::where('status', 'dipinjam')->sum('jumlah'),
            // Jumlah transaksinya, bukan jumlah bukunya: satu peminjaman bisa
            // membawa beberapa eksemplar sekaligus.
            'peminjaman_aktif'       => Peminjaman::where('status', 'dipinjam')->count(),
            'pengajuan_menunggu'     => Peminjaman::where('status', 'pending')->count(),
            'total_terlambat'        => Peminjaman::where('status', 'dipinjam')->whereDate('tanggal_jatuh_tempo', '<', $today)->count(),
            'total_kategori'         => Kategori::count(),
            'total_penulis'          => Penulis::count(),
            'total_penerbit'         => Penerbit::count(),
            'total_rak'              => Rak::count(),
            'total_anggota'          => User::count(),
            'peminjaman_hari_ini'    => Peminjaman::whereIn('status', ['dipinjam', 'dikembalikan'])->whereDate('tanggal_pinjam', $today)->count(),
            'pengembalian_hari_ini'  => Peminjaman::where('status', 'dikembalikan')->whereDate('waktu_kembali', $today)->count(),
        ];
    }

    /**
     * Grafik peminjaman vs pengembalian per bulan pada tahun berjalan.
     */
    private function grafikBulanan(): array
    {
        $tahun = (int) date('Y');

        $pinjam  = $this->jumlahEksemplarPer('n', Peminjaman::whereYear('tanggal_pinjam', $tahun), 'tanggal_pinjam');
        $kembali = $this->jumlahEksemplarPer('n', Peminjaman::where('status', 'dikembalikan')->whereYear('waktu_kembali', $tahun), 'waktu_kembali');

        $grafik = [
            'labels'  => self::NAMA_BULAN,
            'loans'   => [],
            'returns' => [],
            'year'    => $tahun,
        ];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $grafik['loans'][] = (int) ($pinjam[$bulan] ?? 0);
            $grafik['returns'][] = (int) ($kembali[$bulan] ?? 0);
        }

        // Bulan dengan jumlah peminjaman tertinggi tahun berjalan, untuk badge "bulan tersibuk".
        $tertinggi = max($grafik['loans']) > 0 ? array_search(max($grafik['loans']), $grafik['loans']) : false;
        $grafik['busiest_month'] = $tertinggi !== false ? self::NAMA_BULAN[$tertinggi] : null;

        return $grafik;
    }

    /**
     * Grafik yang sama untuk lima tahun terakhir.
     */
    private function grafikTahunan(): array
    {
        $tahunIni = (int) date('Y');
        $rentang = range($tahunIni - 4, $tahunIni);

        $pinjam  = $this->jumlahEksemplarPer('Y', Peminjaman::whereYear('tanggal_pinjam', '>=', $tahunIni - 4), 'tanggal_pinjam');
        $kembali = $this->jumlahEksemplarPer('Y', Peminjaman::where('status', 'dikembalikan')->whereYear('waktu_kembali', '>=', $tahunIni - 4), 'waktu_kembali');

        $grafik = [
            'labels'  => array_map('strval', $rentang),
            'loans'   => [],
            'returns' => [],
        ];

        foreach ($rentang as $tahun) {
            $grafik['loans'][] = (int) ($pinjam[$tahun] ?? 0);
            $grafik['returns'][] = (int) ($kembali[$tahun] ?? 0);
        }

        return $grafik;
    }

    /**
     * Jumlahkan eksemplar per satuan waktu, mis. per bulan ('n') atau per
     * tahun ('Y'). Yang dihitung eksemplarnya, bukan transaksinya, karena satu
     * peminjaman bisa membawa beberapa buku sekaligus.
     */
    private function jumlahEksemplarPer(string $format, $query, string $kolomTanggal): array
    {
        $total = [];

        foreach ($query->get([$kolomTanggal, 'jumlah']) as $baris) {
            $periode = (int) Carbon::parse($baris->{$kolomTanggal})->format($format);
            $total[$periode] = ($total[$periode] ?? 0) + (int) $baris->jumlah;
        }

        return $total;
    }

    /**
     * Ranking kategori buku berdasarkan total eksemplar yang pernah dipinjam
     * (bukan hanya jumlah transaksi) -- ini jawaban langsung untuk pertanyaan
     * "jenis buku apa yang sering dipinjam".
     *
     * @return array{0: \Illuminate\Support\Collection, 1: object|null, 2: int}
     */
    private function ringkasanKategori(): array
    {
        $peringkat = DB::table('peminjaman')
            ->join('buku', 'peminjaman.buku_id', '=', 'buku.id')
            ->join('kategori', 'buku.kategori_id', '=', 'kategori.id')
            ->selectRaw('kategori.nama as nama, SUM(peminjaman.jumlah) as total')
            ->groupBy('kategori.id', 'kategori.nama')
            ->orderByDesc('total')
            ->get();

        $totalSemua = (int) $peringkat->sum('total');
        $persen = fn ($nilai) => $totalSemua > 0 ? round(($nilai / $totalSemua) * 100, 1) : 0;

        $grafik = $peringkat->take(self::KATEGORI_DITAMPILKAN)->map(fn ($baris) => [
            'nama'   => $baris->nama,
            'total'  => (int) $baris->total,
            'persen' => $persen($baris->total),
        ])->values();

        // Ekor daftarnya digabung jadi satu irisan supaya diagramnya tetap terbaca.
        $sisa = (int) $peringkat->slice(self::KATEGORI_DITAMPILKAN)->sum('total');
        if ($sisa > 0) {
            $grafik->push([
                'nama'   => 'Kategori Lainnya',
                'total'  => $sisa,
                'persen' => $persen($sisa),
            ]);
        }

        return [$grafik, $peringkat->first(), $totalSemua];
    }
}
