<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PengaturanService;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function __construct(private PengaturanService $pengaturan)
    {
    }

    public function index()
    {
        $this->pastikanSuperAdmin('Hanya Super Administrator yang berwenang membuka pengaturan sistem.');

        return view('admin.pengaturan.index', [
            'pengaturan' => $this->pengaturan->semua(),
            'systemInfo' => $this->pengaturan->infoSistem(),
        ]);
    }

    public function update(Request $request)
    {
        $this->pastikanSuperAdmin('Hanya Super Administrator yang berwenang mengubah pengaturan sistem.');

        $validated = $request->validate([
            'nama_perpustakaan'       => 'required|string|max:255',
            'nama_sekolah'            => 'nullable|string|max:255',
            'npsn'                    => 'nullable|string|max:50',
            'kepala_perpustakaan'     => 'nullable|string|max:255',
            'nip_kepala_perpustakaan' => 'nullable|string|max:50',
            'alamat'                  => 'nullable|string|max:500',
            'email_perpustakaan'      => 'nullable|string|max:255',
            'telepon'                 => 'nullable|string|max:50',
            'website_sekolah'         => 'nullable|string|max:255',
            'jam_operasional'         => 'required|string|max:255',
            'jam_operasional_jumat'   => 'nullable|string|max:255',
            'pesan_sirkulasi'         => 'nullable|string|max:500',
            'durasi_pinjam_hari'      => 'required|integer|min:1|max:365',
            'syarat_peminjaman'       => 'nullable|string|max:500',
            'judul_hero'              => 'nullable|string|max:255',
            'subjudul_hero'           => 'nullable|string|max:500',
            'buku_per_halaman'        => 'nullable|integer|min:4|max:100',
        ]);

        $this->pengaturan->simpan($validated);

        return back()->with('success', 'Pengaturan sistem perpustakaan berhasil diperbarui.');
    }

    /**
     * Lapis kedua di belakang middleware `role:super_admin` pada rutenya.
     */
    private function pastikanSuperAdmin(string $pesan): void
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, $pesan);
        }
    }
}
