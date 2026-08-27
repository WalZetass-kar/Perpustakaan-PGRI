<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AturanBisnisException;
use App\Http\Controllers\Controller;
use App\Services\MasterData\PenulisService;
use Illuminate\Http\Request;

class PenulisController extends Controller
{
    public function __construct(private PenulisService $penulis)
    {
    }

    public function index()
    {
        return view('admin.penulis.index', ['penulisList' => $this->penulis->daftar()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'     => 'required|string|max:255',
            'biografi' => 'nullable|string',
        ]);

        $this->penulis->simpan($data);

        return back()->with('success', 'Penulis baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama'     => 'required|string|max:255',
            'biografi' => 'nullable|string',
        ]);

        $this->penulis->perbarui((int) $id, $data);

        return back()->with('success', 'Data penulis berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $this->penulis->hapus((int) $id);
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Penulis berhasil dihapus.');
    }
}
