<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Buku\PenelusuranBukuService;
use Illuminate\Http\Request;

class TemukanBukuController extends Controller
{
    public function __construct(private PenelusuranBukuService $penelusuran)
    {
    }

    public function index(Request $request)
    {
        $bukuList = $this->penelusuran->cari([
            'cari'        => $request->input('search'),
            'kategori_id' => $request->input('kategori_id'),
            'rak_id'      => $request->input('rak_id'),
            'status_stok' => $request->input('status_stok'),
        ]);

        return view('admin.temukan-buku.index', ['bukuList' => $bukuList] + $this->penelusuran->konteksPencarian());
    }

    /**
     * Saran judul yang muncul sementara petugas mengetik di kotak pencarian.
     * Isinya sengaja diringkas menjadi array biasa, bukan model utuh, supaya
     * kolom yang tidak dipakai di dropdown tidak ikut terkirim ke browser.
     */
    public function saran(Request $request)
    {
        $kata = substr(trim((string) $request->input('q', '')), 0, 100);

        if (mb_strlen($kata) < 2) {
            return response()->json([]);
        }

        $saran = $this->penelusuran->saran($kata)->map(fn ($buku) => [
            'id'                 => $buku->id,
            'judul'              => $buku->judul,
            'penulis'            => $buku->penulis->nama ?? 'Penulis Tidak Diketahui',
            'kategori'           => $buku->kategori->nama ?? 'Umum',
            'rak'                => $buku->rak->kode_rak ?? 'Belum Ditentukan',
            'laci'               => $buku->laci->nama_laci ?? '-',
            'available_quantity' => (int) $buku->available_quantity,
            // Dropdown-nya merender sampul kecil (w-10 h-14), cukup varian thumb.
            'cover_url'          => $buku->cover_thumb_url,
        ]);

        return response()->json($saran);
    }
}
