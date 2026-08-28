<?php

namespace App\Services\Buku;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\Rak;
use App\Models\RakLaci;

/**
 * Penelusuran koleksi dari dua arah.
 *
 * Halaman "Data Buku" menyusuri koleksi mengikuti wujud fisiknya — rak lalu
 * laci — sedangkan "Temukan Buku" mencari langsung ke seluruh koleksi ketika
 * petugas hanya ingat sepotong judul atau nama penulisnya.
 */
class PenelusuranBukuService
{
    /**
     * Daftar rak beserta hitungan isinya, untuk halaman Data Buku.
     */
    public function daftarRak(?string $cari = null)
    {
        $query = Rak::with('kategori')
            ->withCount('buku')
            ->withCount('laci')
            ->withSum('buku', 'total_quantity')
            ->withSum('buku', 'available_quantity');

        if (filled($cari)) {
            $cari = trim($cari);
            $query->where(function ($q) use ($cari) {
                $q->where('nama_rak', 'like', "%{$cari}%")
                  ->orWhere('kode_rak', 'like', "%{$cari}%")
                  ->orWhere('lokasi', 'like', "%{$cari}%");
            });
        }

        return $query->orderBy('kode_rak', 'asc')->paginate(12)->withQueryString();
    }

    public function statistikKoleksi(): array
    {
        return [
            'total_rak'     => Rak::count(),
            'total_judul'   => Buku::count(),
            'total_stok'    => (int) Buku::sum('total_quantity'),
            'buku_tersedia' => (int) Buku::sum('available_quantity'),
        ];
    }

    public function rak(int $rakId): Rak
    {
        return Rak::with('kategori')->findOrFail($rakId);
    }

    public function laciDiRak(Rak $rak)
    {
        return RakLaci::where('rak_id', $rak->id)
            ->withCount('buku')
            ->withSum('buku', 'total_quantity')
            ->withSum('buku', 'available_quantity')
            ->orderBy('nomor_laci', 'asc')
            ->get();
    }

    public function laci(Rak $rak, int $laciId): RakLaci
    {
        return RakLaci::where('rak_id', $rak->id)->findOrFail($laciId);
    }

    /**
     * rak_laci_id bersifat nullable dengan onDelete('set null'), sehingga buku
     * bisa kehilangan lacinya saat laci dihapus. Tanpa jalur khusus, buku
     * seperti itu tidak akan terjangkau dari halaman mana pun.
     */
    public function jumlahTanpaLaci(Rak $rak): int
    {
        return Buku::where('rak_id', $rak->id)->whereNull('rak_laci_id')->count();
    }

    public function bukuDiLaci(Rak $rak, RakLaci $laci, ?string $cari = null)
    {
        return $this->queryBukuDiRak($rak->id, $cari)
            ->where('rak_laci_id', $laci->id)
            ->paginate(12)
            ->withQueryString();
    }

    public function bukuTanpaLaci(Rak $rak, ?string $cari = null)
    {
        return $this->queryBukuDiRak($rak->id, $cari)
            ->whereNull('rak_laci_id')
            ->paginate(12)
            ->withQueryString();
    }

    /**
     * Pencarian bebas untuk halaman Temukan Buku, lengkap dengan penyaring
     * kategori, rak, dan status stok.
     */
    public function cari(array $filter)
    {
        $query = Buku::with([
            'penulis',
            'penerbit',
            'kategori',
            'kelas',
            'rak.laci',
            'laci.rak'
        ]);

        if (filled($filter['cari'] ?? null)) {
            $this->terapkanKataKunci($query, trim($filter['cari']));
        }

        if (filled($filter['kategori_id'] ?? null)) {
            $query->where('kategori_id', $filter['kategori_id']);
        }
        if (filled($filter['rak_id'] ?? null)) {
            $query->where('rak_id', $filter['rak_id']);
        }

        if (filled($filter['status_stok'] ?? null)) {
            if ($filter['status_stok'] === 'tersedia') {
                $query->where('available_quantity', '>', 0);
            } elseif ($filter['status_stok'] === 'penuh') {
                $query->whereColumn('available_quantity', '=', 'total_quantity')->where('total_quantity', '>', 0);
            } elseif ($filter['status_stok'] === 'habis') {
                $query->where('available_quantity', '<=', 0);
            }
        }

        return $query->orderBy('judul', 'asc')->paginate(12)->withQueryString();
    }

    /**
     * Saran yang muncul di bawah kotak pencarian Temukan Buku sementara
     * petugas mengetik. Sengaja lewat `terapkanKataKunci()` yang sama dengan
     * `cari()`: kalau keduanya memakai daftar kolom yang berbeda, petugas
     * bisa mengetik kode rak, tidak melihat saran apa pun, lalu mengira
     * bukunya tidak ada -- padahal menekan Enter menemukannya.
     */
    public function saran(string $cari, int $batas = 8)
    {
        $query = Buku::with(['penulis', 'kategori', 'rak', 'laci']);

        $this->terapkanKataKunci($query, trim($cari));

        return $query->orderBy('judul', 'asc')->take($batas)->get();
    }

    /**
     * Satu kata kunci ditelusuri ke judul, ISBN, dan seluruh data terkait
     * termasuk kode rak serta nama laci -- karena petugas kerap lebih ingat
     * letaknya daripada judulnya.
     */
    private function terapkanKataKunci($query, string $cari): void
    {
        $query->where(function ($q) use ($cari) {
            $q->where('judul', 'like', "%{$cari}%")
              ->orWhere('isbn', 'like', "%{$cari}%")
              ->orWhereHas('penulis', function ($qp) use ($cari) {
                  $qp->where('nama', 'like', "%{$cari}%");
              })
              ->orWhereHas('penerbit', function ($qp) use ($cari) {
                  $qp->where('nama', 'like', "%{$cari}%");
              })
              ->orWhereHas('kategori', function ($qk) use ($cari) {
                  $qk->where('nama', 'like', "%{$cari}%");
              })
              ->orWhereHas('rak', function ($qr) use ($cari) {
                  $qr->where('kode_rak', 'like', "%{$cari}%")
                     ->orWhere('nama_rak', 'like', "%{$cari}%")
                     ->orWhere('lokasi', 'like', "%{$cari}%");
              })
              ->orWhereHas('laci', function ($ql) use ($cari) {
                  $ql->where('nama_laci', 'like', "%{$cari}%")
                     ->orWhere('keterangan', 'like', "%{$cari}%");
              });
        });
    }

    /**
     * Pilihan penyaring dan angka ringkas di halaman Temukan Buku.
     */
    public function konteksPencarian(): array
    {
        return [
            'kategoriList' => Kategori::orderBy('nama', 'asc')->get(),
            'rakList'      => Rak::with('laci')->orderBy('kode_rak', 'asc')->get(),
            'metrics'      => [
                'total_koleksi'  => Buku::count(),
                'total_buku'     => (int) Buku::sum('total_quantity'),
                'buku_tersedia'  => (int) Buku::sum('available_quantity'),
                'sedang_pinjam'  => (int) Peminjaman::where('status', 'dipinjam')->sum('jumlah'),
                'total_rak'      => Rak::count(),
                'total_laci'     => RakLaci::count(),
            ],
        ];
    }

    private function queryBukuDiRak(int $rakId, ?string $cari)
    {
        $query = Buku::with([
            'penulis',
            'penerbit',
            'kategori',
            'kelas',
            'rak.laci',
            'laci.rak'
        ])->where('rak_id', $rakId);

        if (filled($cari)) {
            $cari = trim($cari);
            $query->where(function ($q) use ($cari) {
                $q->where('judul', 'like', "%{$cari}%")
                  ->orWhere('isbn', 'like', "%{$cari}%");
            });
        }

        return $query->orderBy('judul', 'asc');
    }
}
