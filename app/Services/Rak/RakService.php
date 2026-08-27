<?php

namespace App\Services\Rak;

use App\Exceptions\AturanBisnisException;
use App\Models\AuditLog;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Rak;
use App\Models\RakLaci;

/**
 * Denah penyimpanan fisik: rak beserta laci-lacinya.
 */
class RakService
{
    /** Jumlah laci yang dibuatkan otomatis bila petugas tidak menentukannya. */
    private const LACI_BAWAAN = 3;

    /**
     * Daftar rak untuk halaman Rak, lengkap dengan isi tiap lacinya.
     */
    public function daftar(array $filter)
    {
        $query = Rak::with([
            'kategori',
            'buku.kategori',
            'laci.buku.kategori',
            'laci.buku.penulis'
        ])->withCount(['buku', 'laci']);

        if (filled($filter['cari'] ?? null)) {
            $cari = trim($filter['cari']);
            $query->where(function ($q) use ($cari) {
                $q->where('nama_rak', 'like', "%{$cari}%")
                  ->orWhere('kode_rak', 'like', "%{$cari}%")
                  ->orWhere('lokasi', 'like', "%{$cari}%")
                  ->orWhereHas('kategori', function ($qk) use ($cari) {
                      $qk->where('nama', 'like', "%{$cari}%");
                  })
                  ->orWhereHas('buku', function ($qb) use ($cari) {
                      $qb->where('judul', 'like', "%{$cari}%")
                         ->orWhere('isbn', 'like', "%{$cari}%")
                         ->orWhereHas('kategori', function ($qbk) use ($cari) {
                             $qbk->where('nama', 'like', "%{$cari}%");
                         });
                  })
                  ->orWhereHas('laci', function ($ql) use ($cari) {
                      $ql->where('nama_laci', 'like', "%{$cari}%")
                         ->orWhere('keterangan', 'like', "%{$cari}%");
                  });
            });
        }

        if (filled($filter['status'] ?? null)) {
            if ($filter['status'] === 'berisi') {
                $query->has('buku');
            } elseif ($filter['status'] === 'kosong') {
                $query->doesntHave('buku');
            }
        }

        if (filled($filter['lokasi'] ?? null)) {
            $query->where('lokasi', $filter['lokasi']);
        }

        return $query->orderBy('kode_rak', 'asc')->paginate(12)->withQueryString();
    }

    /**
     * Pilihan penyaring dan angka ringkas di halaman Rak.
     */
    public function konteksHalaman(): array
    {
        return [
            'kategoriList' => Kategori::orderBy('nama', 'asc')->get(),
            'statsSummary' => [
                'total_rak'       => Rak::count(),
                'total_laci'      => RakLaci::count(),
                'total_judul'     => Buku::whereNotNull('rak_id')->count(),
                'total_eksemplar' => (int) Buku::whereNotNull('rak_id')->sum('total_quantity'),
            ],
            'lokasiList'   => Rak::whereNotNull('lokasi')->where('lokasi', '!=', '')->distinct()->pluck('lokasi'),
        ];
    }

    /**
     * Buat rak baru sekaligus laci-laci awalnya, supaya petugas bisa langsung
     * menempatkan buku tanpa menyiapkan laci satu per satu.
     *
     * @return array{rak: Rak, jumlah_laci: int}
     */
    public function simpan(array $data): array
    {
        $rak = Rak::create($this->kolomRak($data));

        // Kunci yang tidak dikirim sama sekali berarti petugas memakai jumlah
        // bawaan; kunci yang dikirim kosong berarti ia sengaja tidak ingin
        // dibuatkan laci apa pun.
        $jumlahLaci = (int) (array_key_exists('jumlah_laci', $data) ? $data['jumlah_laci'] : self::LACI_BAWAAN);

        for ($i = 1; $i <= $jumlahLaci; $i++) {
            RakLaci::create([
                'rak_id'     => $rak->id,
                'nomor_laci' => $i,
                'nama_laci'  => 'Laci ' . $i,
                'keterangan' => 'Tingkat ' . $i . ' pada ' . $rak->nama_rak,
            ]);
        }

        AuditLog::catat('TAMBAH_RAK', "Menambahkan rak '{$rak->nama_rak}' ({$rak->kode_rak}) dengan {$jumlahLaci} laci awal");

        return ['rak' => $rak, 'jumlah_laci' => $jumlahLaci];
    }

    public function perbarui(int $id, array $data): Rak
    {
        $rak = Rak::findOrFail($id);
        $rak->update($this->kolomRak($data));

        AuditLog::catat('UPDATE_RAK', "Memperbarui rak '{$rak->nama_rak}' ({$rak->kode_rak})");

        return $rak;
    }

    /**
     * Hapus rak beserta seluruh lacinya.
     *
     * @return array{nama: string, kode: string}
     * @throws AturanBisnisException bila raknya masih menampung buku
     */
    public function hapus(int $id): array
    {
        $rak = Rak::withCount('buku')->findOrFail($id);

        if ($rak->buku_count > 0) {
            throw new AturanBisnisException("Rak '{$rak->nama_rak}' tidak dapat dihapus karena masih menampung {$rak->buku_count} judul buku. Pindahkan atau ubah lokasi rak buku terlebih dahulu.");
        }

        RakLaci::where('rak_id', $rak->id)->delete();

        $identitas = ['nama' => $rak->nama_rak, 'kode' => $rak->kode_rak];
        $rak->delete();

        AuditLog::catat('HAPUS_RAK', "Menghapus rak '{$identitas['nama']}' ({$identitas['kode']})");

        return $identitas;
    }

    /**
     * Kode rak selalu disimpan huruf besar supaya "r-01" dan "R-01" tidak
     * menjadi dua rak yang berbeda di mata petugas.
     */
    private function kolomRak(array $data): array
    {
        return [
            'kode_rak'    => strtoupper(trim($data['kode_rak'])),
            'nama_rak'    => trim($data['nama_rak']),
            'lokasi'      => filled($data['lokasi'] ?? null) ? trim($data['lokasi']) : null,
            'kategori_id' => $data['kategori_id'] ?? null,
        ];
    }
}
