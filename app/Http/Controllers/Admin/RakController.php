<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AturanBisnisException;
use App\Http\Controllers\Controller;
use App\Services\Rak\RakService;
use Illuminate\Http\Request;

class RakController extends Controller
{
    public function __construct(private RakService $rak)
    {
    }

    public function index(Request $request)
    {
        $rakList = $this->rak->daftar([
            'cari'   => $request->input('search'),
            'status' => $request->input('status'),
            'lokasi' => $request->input('lokasi'),
        ]);

        return view('admin.rak.index', ['rakList' => $rakList] + $this->rak->konteksHalaman());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_rak'    => 'required|unique:rak,kode_rak|max:50',
            'nama_rak'    => 'required|string|max:255',
            'lokasi'      => 'nullable|string|max:255',
            'kategori_id' => 'nullable|exists:kategori,id',
            'jumlah_laci' => 'nullable|integer|min:1|max:20',
        ]);

        ['rak' => $rak, 'jumlah_laci' => $jumlahLaci] = $this->rak->simpan($data);

        return back()->with('success', "Rak '{$rak->nama_rak}' ({$rak->kode_rak}) beserta {$jumlahLaci} laci berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'kode_rak'    => 'required|max:50|unique:rak,kode_rak,' . $id,
            'nama_rak'    => 'required|string|max:255',
            'lokasi'      => 'nullable|string|max:255',
            'kategori_id' => 'nullable|exists:kategori,id',
        ]);

        $rak = $this->rak->perbarui((int) $id, $data);

        return back()->with('success', "Data rak '{$rak->nama_rak}' berhasil diperbarui.");
    }

    public function destroy($id)
    {
        try {
            $rak = $this->rak->hapus((int) $id);
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Rak '{$rak['nama']}' ({$rak['kode']}) dan seluruh lacinya berhasil dihapus.");
    }
}
