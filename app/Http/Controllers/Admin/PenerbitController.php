<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AturanBisnisException;
use App\Http\Controllers\Controller;
use App\Services\MasterData\PenerbitService;
use Illuminate\Http\Request;

class PenerbitController extends Controller
{
    public function __construct(private PenerbitService $penerbit)
    {
    }

    public function index(Request $request)
    {
        return view('admin.penerbit.index', [
            'penerbitList' => $this->penerbit->daftar($request->input('search')),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kota' => 'nullable|string|max:255',
        ]);

        $this->penerbit->simpan($data);

        return back()->with('success', 'Penerbit baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kota' => 'nullable|string|max:255',
        ]);

        $this->penerbit->perbarui((int) $id, $data);

        return back()->with('success', 'Data penerbit berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $this->penerbit->hapus((int) $id);
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Penerbit berhasil dihapus.');
    }
}
