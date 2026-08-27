<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AturanBisnisException;
use App\Http\Controllers\Controller;
use App\Http\Laporan\LaporanExcel;
use App\Services\Sirkulasi\PeminjamanService;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function __construct(private PeminjamanService $peminjaman)
    {
    }

    public function index(Request $request)
    {
        $activeLoans = $this->peminjaman->daftarAktif(
            $request->input('search'),
            $request->get('filter') === 'terlambat'
        );
        $bukuList = $this->peminjaman->bukuYangBisaDipinjam();

        // `peminjamanList` dan `booksList` adalah nama lama untuk data yang
        // sama; keduanya masih dipakai di beberapa bagian view.
        return view('admin.peminjaman.index', [
            'activeLoans'    => $activeLoans,
            'peminjamanList' => $activeLoans,
            'bukuList'       => $bukuList,
            'booksList'      => $bukuList,
        ] + $this->peminjaman->ringkasanAktif());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_peminjam' => 'required|string|max:255',
            'jurusan'       => 'required|string|max:150',
            'nomor_induk'   => 'nullable|string|max:50',
            'no_wa'         => 'required|string|max:30',
            'buku_id'       => 'required|exists:buku,id',
            'jumlah'        => 'required|integer|min:1|max:10',
        ], [
            'no_wa.required' => 'Nomor WhatsApp / telepon peminjam wajib diisi agar buku yang jatuh tempo bisa ditagih.',
            'no_wa.max'      => 'Nomor WhatsApp terlalu panjang (maksimal 30 karakter).',
        ]);

        try {
            $peminjaman = $this->peminjaman->catat($data);
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat mencatat peminjaman.');
        }

        return back()->with('success', "Peminjaman berhasil dicatat untuk {$peminjaman->nama_peminjam}! Kode: {$peminjaman->kode_peminjaman}");
    }

    public function kembali(Request $request, $id)
    {
        try {
            $this->peminjaman->kembalikan((int) $id);
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat memproses pengembalian buku.');
        }

        return back()->with('success', "Pengembalian buku berhasil diproses.");
    }

    public function exportExcel(Request $request)
    {
        $laporan = $this->peminjaman->siapkanLaporan($this->filterLaporan($request), 'excel');

        return LaporanExcel::unduh('admin.peminjaman.export-excel', $laporan, 'Laporan_Sirkulasi_Peminjaman', 'Sirkulasi Peminjaman');
    }

    public function exportPdf(Request $request)
    {
        $laporan = $this->peminjaman->siapkanLaporan($this->filterLaporan($request), 'pdf');

        return view('admin.peminjaman.export-pdf', $laporan);
    }

    public function riwayat(Request $request)
    {
        return view('admin.peminjaman.riwayat', [
            'riwayatList' => $this->peminjaman->riwayat([
                'status'  => $request->input('status'),
                'tanggal' => $request->input('tanggal'),
                'cari'    => $request->input('search'),
            ]),
        ]);
    }

    /**
     * Penyaring laporan yang dikirim lewat query string, dipakai versi Excel
     * maupun PDF supaya keduanya selalu memuat kumpulan data yang sama.
     */
    private function filterLaporan(Request $request): array
    {
        return [
            'status'  => $request->input('status'),
            'tanggal' => $request->input('tanggal'),
            'cari'    => $request->input('search'),
        ];
    }
}
