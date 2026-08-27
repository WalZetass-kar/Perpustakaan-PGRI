<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Pengguna\AnggotaService;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function __construct(private AnggotaService $anggota)
    {
    }

    public function index()
    {
        return view('admin.profil.index', ['user' => auth()->user()]);
    }

    public function ubahPassword(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Administrator yang dapat mengubah password. Hubungi Super Admin untuk mereset password Anda.');
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $this->anggota->ubahPasswordSendiri($data['password']);

        return back()->with('success', 'Password Anda berhasil diperbarui.');
    }
}
