<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AturanBisnisException;
use App\Http\Controllers\Controller;
use App\Services\Sirkulasi\PengajuanService;
use Illuminate\Http\Request;

class PengajuanPeminjamanController extends Controller
{
    public function __construct(private PengajuanService $pengajuan)
    {
    }

    public function index(Request $request)
    {
        $status = $this->pengajuan->statusValid($request->get('status', 'pending'));

        return view('admin.peminjaman.request', [
            'requestList' => $this->pengajuan->daftar($status, $request->input('search')),
            'counts'      => $this->pengajuan->jumlahPerStatus(),
            'status'      => $status,
        ]);
    }

    public function approve(Request $request, $id)
    {
        try {
            $peminjaman = $this->pengajuan->setujui((int) $id);
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat menyetujui pengajuan.');
        }

        return back()->with('success', "Pengajuan peminjaman untuk {$peminjaman->nama_peminjam} berhasil disetujui! Kode: {$peminjaman->kode_peminjaman}");
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'nullable|string|max:500',
        ]);

        try {
            $peminjaman = $this->pengajuan->tolak((int) $id, $request->input('alasan_penolakan'));
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat menolak pengajuan.');
        }

        return back()->with('success', "Pengajuan peminjaman untuk {$peminjaman->nama_peminjam} telah ditolak.");
    }
}
