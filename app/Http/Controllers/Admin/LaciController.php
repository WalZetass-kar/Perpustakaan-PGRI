<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Rak\LaciService;
use Illuminate\Http\Request;

class LaciController extends Controller
{
    public function __construct(private LaciService $laci)
    {
    }

    public function store(Request $request, $rakId)
    {
        $rak = $this->laci->rak((int) $rakId);

        $data = $request->validate([
            'nama_laci'   => 'required|string|max:100',
            'nomor_laci'  => 'nullable|integer|min:1',
            'keterangan'  => 'nullable|string|max:255',
        ]);

        $laci = $this->laci->simpan((int) $rak->id, $data);

        return back()->with('success', "Laci '{$laci->nama_laci}' berhasil ditambahkan ke rak {$rak->nama_rak}.");
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama_laci'  => 'required|string|max:100',
            'nomor_laci' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $laci = $this->laci->perbarui((int) $id, $data);

        return back()->with('success', "Data laci '{$laci->nama_laci}' berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $terhapus = $this->laci->hapus((int) $id);

        return back()->with('success', "Laci '{$terhapus['laci']}' berhasil dihapus.");
    }

    public function byRak($rakId)
    {
        return response()->json($this->laci->ringkasanUntukRak((int) $rakId));
    }
}
