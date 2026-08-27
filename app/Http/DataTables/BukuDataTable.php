<?php

namespace App\Http\DataTables;

use App\Models\Buku;
use Illuminate\Http\Request;

/**
 * Sumber data tabel Koleksi Buku (DataTables sisi server).
 *
 * DataTables mengirim parameter pencarian, urutan, dan halaman lewat query
 * string, lalu menunggu balasan JSON berisi HTML tiap selnya. Perakitan HTML
 * itu dikerjakan Blade di resources/views/admin/buku/partials, supaya tampilan
 * baris bisa diubah tanpa menyentuh logika query di bawah ini.
 */
class BukuDataTable
{
    /**
     * Kolom yang boleh dipakai DataTables untuk mengurutkan, dipetakan dari
     * nomor kolom di tabel HTML-nya. Indeks di luar daftar ini jatuh ke 'id'
     * supaya nama kolom tidak pernah datang mentah dari pengguna.
     */
    private const KOLOM_URUT = [
        0 => 'judul',
        1 => 'penulis_id',
        2 => 'kategori_id',
        3 => 'available_quantity',
        4 => 'id',
    ];

    public function __construct(private Request $request)
    {
    }

    /**
     * Payload JSON yang diminta DataTables.
     */
    public function payload(): array
    {
        $totalData = Buku::count();

        $query = Buku::with(['penulis', 'penerbit', 'kategori', 'kelas', 'rak', 'laci']);
        $this->terapkanPencarian($query);
        $this->terapkanFilter($query);

        $totalFiltered = (clone $query)->count();

        $this->terapkanUrutan($query);
        $this->terapkanHalaman($query);

        return [
            'draw'            => (int) $this->request->input('draw', 1),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $query->get()->map(fn (Buku $buku) => $this->baris($buku))->all(),
        ];
    }

    /**
     * Kotak pencarian bawaan DataTables menelusuri judul dan ISBN sekaligus
     * seluruh data terkait -- penulis, penerbit, kategori, sampai kode rak dan
     * nama laci -- karena petugas sering ingat lokasinya, bukan judulnya.
     */
    private function terapkanPencarian($query): void
    {
        $searchValue = $this->request->input('search.value');
        if (empty($searchValue)) {
            return;
        }

        $search = trim($searchValue);
        $query->where(function ($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('isbn', 'like', "%{$search}%")
              ->orWhere('tahun_terbit', 'like', "%{$search}%")
              ->orWhereHas('penulis', function ($qp) use ($search) {
                  $qp->where('nama', 'like', "%{$search}%");
              })
              ->orWhereHas('penerbit', function ($qp) use ($search) {
                  $qp->where('nama', 'like', "%{$search}%");
              })
              ->orWhereHas('kategori', function ($qk) use ($search) {
                  $qk->where('nama', 'like', "%{$search}%");
              })
              ->orWhereHas('rak', function ($qr) use ($search) {
                  $qr->where('kode_rak', 'like', "%{$search}%")
                     ->orWhere('nama_rak', 'like', "%{$search}%");
              })
              ->orWhereHas('laci', function ($ql) use ($search) {
                  $ql->where('nama_laci', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Filter dropdown di atas tabel.
     */
    private function terapkanFilter($query): void
    {
        foreach (['kategori_id', 'rak_id', 'kelas_id'] as $kolom) {
            if ($this->request->filled($kolom)) {
                $query->where($kolom, $this->request->input($kolom));
            }
        }

        if ($this->request->filled('status_stok')) {
            if ($this->request->status_stok === 'tersedia') {
                $query->where('available_quantity', '>', 0);
            } elseif ($this->request->status_stok === 'habis') {
                $query->where('available_quantity', '<=', 0);
            }
        }
    }

    private function terapkanUrutan($query): void
    {
        $indeks = (int) $this->request->input('order.0.column', 0);
        $arah = strtolower($this->request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query->orderBy(self::KOLOM_URUT[$indeks] ?? 'id', $arah);
    }

    private function terapkanHalaman($query): void
    {
        $length = (int) $this->request->input('length', 10);
        if ($length > 0) {
            $query->skip((int) $this->request->input('start', 0))->take($length);
        }
    }

    /**
     * Satu baris tabel: sel-sel HTML untuk mode Tabel, ditambah field mentah
     * yang dipakai mode tampilan Grid di halaman yang sama.
     */
    private function baris(Buku $buku): array
    {
        return [
            'buku'     => view('admin.buku.partials.kolom-buku', compact('buku'))->render(),
            'penulis'  => view('admin.buku.partials.kolom-penulis', compact('buku'))->render(),
            'kategori' => view('admin.buku.partials.kolom-kategori', compact('buku'))->render(),
            'stok'     => view('admin.buku.partials.kolom-stok', compact('buku'))->render(),
            'aksi'     => view('admin.buku.partials.kolom-aksi', [
                'buku'     => $buku,
                'dataBuku' => $this->dataUntukModalEdit($buku),
            ])->render(),

            // field mentah untuk mode tampilan Grid
            'judul_raw'          => $buku->judul,
            'cover_url'          => $buku->cover_card_url,
            'penulis_nama'       => $buku->penulis->nama ?? '-',
            'kategori_nama'      => $buku->kategori->nama ?? 'Umum',
            'kelas_nama'         => $buku->kelas->nama_kelas ?? '',
            'rak_text'           => $buku->rak ? trim($buku->rak->kode_rak . ' - ' . $buku->rak->nama_rak) : 'Belum Ditentukan',
            'laci_nama'          => $buku->laci->nama_laci ?? ($buku->rak ? 'Laci 1' : 'Tanpa Laci'),
            'total_quantity'     => $buku->total_quantity,
            'available_quantity' => $buku->available_quantity,
        ];
    }

    /**
     * Isi form edit dititipkan pada atribut data-buku tombol "Edit Buku",
     * sehingga modalnya bisa terisi tanpa permintaan tambahan ke server.
     */
    private function dataUntukModalEdit(Buku $buku): string
    {
        return htmlspecialchars(json_encode([
            'id'                 => $buku->id,
            'isbn'               => $buku->isbn ?? '',
            'judul'              => $buku->judul,
            'tahun_terbit'       => $buku->tahun_terbit,
            'total_quantity'     => $buku->total_quantity,
            'penulis_id'         => $buku->penulis_id,
            'penerbit_id'        => $buku->penerbit_id,
            'kategori_id'        => $buku->kategori_id,
            'kelas_id'           => $buku->kelas_id,
            'rak_id'             => $buku->rak_id,
            'rak_laci_id'        => $buku->rak_laci_id,
            'sinopsis'           => $buku->sinopsis ?? '',
            'keterangan_posisi'  => $buku->keterangan_posisi ?? '',
            'cover_url'          => $buku->cover_url,
        ]), ENT_QUOTES, 'UTF-8');
    }
}
