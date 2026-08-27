<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AturanBisnisException;
use App\Http\Controllers\Controller;
use App\Services\MasterData\KategoriService;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function __construct(private KategoriService $kategori)
    {
    }

    public function index(Request $request)
    {
        return view('admin.kategori.index', [
            'kategoriList' => $this->kategori->daftar($request->input('search')),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'      => 'required|unique:kategori,nama|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $this->kategori->simpan($data);

        return back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama'      => 'required|max:255|unique:kategori,nama,' . $id,
            'deskripsi' => 'nullable|string',
        ]);

        $this->kategori->perbarui((int) $id, $data);

        return back()->with('success', 'Data kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $this->kategori->hapus((int) $id);
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
