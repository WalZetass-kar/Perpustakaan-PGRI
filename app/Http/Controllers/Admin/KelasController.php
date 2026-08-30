<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AturanBisnisException;
use App\Http\Controllers\Controller;
use App\Rules\KelasBelumTerdaftar;
use App\Rules\TingkatSelarasDenganNama;
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
        $data = $request->validate($this->aturanKelas($request));

        try {
            $this->kelas->simpan($data);
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return back()->with('success', 'Kelas baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate($this->aturanKelas($request, (int) $id));

        try {
            $this->kelas->perbarui((int) $id, $data);
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

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
     *
     * Dua pemeriksaan menempel di `nama_kelas` — bukan di `tingkat` — karena
     * di situlah pesan kesalahannya paling masuk akal dibaca petugas, walau
     * yang dibandingkan sebenarnya pasangan keduanya: namanya tidak boleh
     * bertentangan dengan tingkat yang dipilih, dan tidak boleh kembar dengan
     * kelas lain pada tingkat yang sama.
     *
     * `$kecualiId` diisi hanya saat mengubah data, supaya sebuah kelas boleh
     * disimpan ulang dengan namanya sendiri.
     */
    private function aturanKelas(Request $request, ?int $kecualiId = null): array
    {
        return [
            'tingkat'    => 'nullable|string|max:10',
            'nama_kelas' => [
                'required',
                'string',
                'max:255',
                // Keselarasan diperiksa lebih dulu: kalau tingkat dan namanya
                // saja sudah bertentangan, pesan "sudah terdaftar" hanya
                // membingungkan sebelum pertentangan itu dibereskan.
                new TingkatSelarasDenganNama($request->input('tingkat')),
                new KelasBelumTerdaftar($request->input('tingkat'), $kecualiId),
            ],
            'deskripsi'  => 'nullable|string|max:255',
        ];
    }
}
