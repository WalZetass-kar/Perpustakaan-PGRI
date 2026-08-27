<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AturanBisnisException;
use App\Http\Controllers\Controller;
use App\Http\DataTables\BukuDataTable;
use App\Rules\LaciMilikRak;
use App\Services\Buku\BukuService;
use App\Http\Laporan\LaporanExcel;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function __construct(private BukuService $buku)
    {
    }

    /**
     * Halaman Koleksi Buku.
     *
     * Alamat yang sama melayani dua hal: permintaan biasa mengembalikan
     * halamannya, sedangkan permintaan AJAX dari DataTables mengembalikan
     * baris tabelnya sebagai JSON (lihat App\Http\DataTables\BukuDataTable).
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            return response()->json((new BukuDataTable($request))->payload());
        }

        return view('admin.buku.index', $this->buku->pilihanForm());
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturanBuku($request), $this->pesanValidasiBuku());

        $this->buku->simpan($data, $request->file('cover'));

        return back()->with('success', 'Buku baru berhasil ditambahkan ke katalog.');
    }

    public function update(Request $request, $id)
    {
        $buku = $this->buku->temukan((int) $id);

        $data = $request->validate($this->aturanBuku($request, $buku->id), $this->pesanValidasiBuku());

        $this->buku->perbarui($buku, $data, $request->file('cover'));

        return back()->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $buku = $this->buku->temukan((int) $id);

        try {
            $this->buku->hapus($buku);
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Buku berhasil dihapus dari katalog.');
    }

    public function exportExcel(Request $request)
    {
        $laporan = $this->buku->siapkanLaporan($this->filterLaporan($request), 'excel');

        return LaporanExcel::unduh('admin.buku.export-excel', $laporan, 'Laporan_Data_Buku', 'Data Koleksi Buku');
    }

    public function exportPdf(Request $request)
    {
        $laporan = $this->buku->siapkanLaporan($this->filterLaporan($request), 'pdf');

        return view('admin.buku.export-pdf', $laporan);
    }

    /**
     * Penyaring laporan yang dikirim lewat query string, dipakai versi Excel
     * maupun PDF supaya angka pada keduanya berasal dari kumpulan yang sama.
     */
    private function filterLaporan(Request $request): array
    {
        return $request->only(['kategori_id', 'rak_id', 'kelas_id']);
    }

    /**
     * Aturan validasi form buku, dipakai saat menambah maupun mengubah.
     * $abaikanId diisi ketika mengubah, supaya ISBN buku itu sendiri tidak
     * dianggap bentrok dengan dirinya sendiri.
     */
    private function aturanBuku(Request $request, $abaikanId = null): array
    {
        return [
            // `buku.isbn` ber-index unique di database. Tanpa aturan ini,
            // petugas yang mengetik ISBN yang sudah terdaftar — mudah terjadi
            // saat dua orang menginput dari dua komputer — bukan mendapat
            // pesan validasi, melainkan layar error 500.
            'isbn'              => 'nullable|string|max:50|unique:buku,isbn' . ($abaikanId ? ',' . $abaikanId : ''),
            'judul'             => 'required|string|max:255',
            'tahun_terbit'      => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'total_quantity'    => 'required|integer|min:1|max:10000',
            'penulis_id'        => 'nullable|exists:penulis,id',
            'penerbit_id'       => 'nullable|exists:penerbit,id',
            'kategori_id'       => 'nullable|exists:kategori,id',
            'kelas_id'          => 'nullable|exists:kelas,id',
            'rak_id'            => 'nullable|exists:rak,id',
            'rak_laci_id'       => ['nullable', 'exists:rak_laci,id', new LaciMilikRak($request->rak_id)],
            'sinopsis'          => 'nullable|string',
            'keterangan_posisi' => 'nullable|string|max:500',
            'cover'             => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ];
    }

    /**
     * Pesan validasi yang perlu ditulis ulang agar dimengerti petugas.
     */
    private function pesanValidasiBuku(): array
    {
        return [
            'isbn.unique' => 'ISBN ini sudah terdaftar pada buku lain. Periksa kembali, atau kosongkan kolom ISBN.',
        ];
    }
}
