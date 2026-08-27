<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AturanBisnisException;
use App\Http\Controllers\Controller;
use App\Services\Pengguna\AnggotaService;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function __construct(private AnggotaService $anggota)
    {
    }

    public function index(Request $request)
    {
        return view('admin.anggota.index', [
            'anggotaList' => $this->anggota->daftar($request->input('search')),
            'roles'       => $this->anggota->peranTersedia(),
        ]);
    }

    public function store(Request $request)
    {
        $this->pastikanSuperAdmin('Hanya Super Administrator yang berwenang menambahkan akun pengelola baru.');

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email',
            'password'      => 'required|string|min:8',
            'phone'         => 'nullable|string|max:20',
            'role_id'       => 'required|exists:roles,id',
            'status'        => 'required|in:active,inactive',
        ]);

        $this->anggota->simpan($data);

        return back()->with('success', 'Akun pengelola/admin baru berhasil didaftarkan.');
    }

    public function update(Request $request, $id)
    {
        $user = $this->anggota->temukan((int) $id);

        $this->pastikanSuperAdmin('Hanya Super Administrator yang berwenang mengubah data akun admin.');

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone'    => 'nullable|string|max:20',
            'role_id'  => 'nullable|exists:roles,id',
            'status'   => 'nullable|in:active,inactive',
        ]);

        try {
            $this->anggota->perbarui($user, $data);
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Data akun pengelola berhasil diperbarui.');
    }

    public function resetPassword(Request $request, $id)
    {
        $this->pastikanSuperAdmin('Hanya Super Administrator yang berwenang mereset password akun admin.');

        $user = $this->anggota->temukan((int) $id);

        $data = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $this->anggota->resetPassword($user, $data['password']);

        return back()->with('success', "Password untuk akun {$user->name} berhasil diubah.");
    }

    public function toggleStatus(Request $request, $id)
    {
        $this->pastikanSuperAdmin('Hanya Super Administrator yang berwenang mengubah status aktif akun admin.');

        $user = $this->anggota->temukan((int) $id);

        try {
            $hasil = $this->anggota->ubahStatus($user);
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Akun {$user->name} berhasil {$hasil['teks']}.");
    }

    public function destroy(Request $request, $id)
    {
        $this->pastikanSuperAdmin('Hanya Super Administrator yang berwenang menghapus akun pengelola.');

        $user = $this->anggota->temukan((int) $id);

        try {
            $nama = $this->anggota->hapus($user);
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Akun admin {$nama} berhasil dihapus.");
    }

    /**
     * Lapis kedua di belakang middleware `role:super_admin` pada rutenya.
     * Sengaja dipertahankan: kalau suatu saat rutenya dipindah keluar dari
     * grup itu, aksi di halaman ini tetap tidak terbuka untuk petugas biasa.
     */
    private function pastikanSuperAdmin(string $pesan): void
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, $pesan);
        }
    }
}
