<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AturanBisnisException;
use App\Http\Controllers\Controller;
use App\Services\MasterData\KelasService;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function __construct(private KelasService $kelas)
    {
    }

    public function index(Request $request)
    {
        return view('admin.kelas.index', ['kelasList' => $this->kelas->daftar($request->input('search'))]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturanKelas());

        $this->kelas->simpan($data);

        return back()->with('success', 'Kelas baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate($this->aturanKelas());

        $this->kelas->perbarui((int) $id, $data);

        return back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $this->kelas->hapus((int) $id);
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    /**
     * Tingkat diketik bebas oleh petugas (10, 11, 12, X, XI, dst.) dan boleh
     * dikosongkan supaya kelas non-jenjang tetap bisa didata. Batas 10 karakter
     * mengikuti lebar kolomnya di database.
     */
    private function aturanKelas(): array
    {
        return [
            'tingkat'    => 'nullable|string|max:10',
            'nama_kelas' => 'required|string|max:255',
            'deskripsi'  => 'nullable|string|max:255',
        ];
    }
}
