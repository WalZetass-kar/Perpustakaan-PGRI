<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Role;
use App\Models\Anggota;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // General / Main Login Form
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    // Siswa Registration Form
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.register-siswa');
    }

    // Siswa Registration Action
    public function registerSiswa(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'nim'           => ['required', 'string', 'max:50', 'unique:anggota,nim'],
            'program_studi' => ['required', 'string', 'max:150'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nim.unique'         => 'NISN / Nomor Induk ini sudah terdaftar sebagai anggota.',
            'email.unique'       => 'Email sekolah ini sudah terdaftar. Silakan login.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
        ]);

        $role = Role::where('name', 'admin')->first() ?? Role::first();
        $roleId = $role ? $role->id : 1;

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $roleId,
            'phone'    => $request->phone,
            'status'   => 'active',
        ]);

        $nomorAnggota = 'LIB-' . date('Y') . '-' . str_pad($user->id, 3, '0', STR_PAD_LEFT);

        Anggota::create([
            'user_id'       => $user->id,
            'nomor_anggota' => $nomorAnggota,
            'nim'           => $request->nim,
            'program_studi' => $request->program_studi,
            'status'        => 'aktif',
        ]);

        AuditLog::create([
            'user_id'    => $user->id,
            'user_name'  => $user->name,
            'aktivitas'  => 'USER_REGISTER',
            'deskripsi'  => "Pendaftaran akun perpustakaan baru ({$user->email})",
            'ip_address' => $request->ip(),
        ]);

        Auth::login($user);

        return redirect()->route('admin.dashboard')->with('success', 'Selamat datang! Pendaftaran akun berhasil.');
    }

    // Admin Dedicated Login Form
    public function showAdminLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login-admin');
    }

    // Login Action (Semua pengguna login dapat mengelola sistem)
    public function loginSiswa(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            $request->session()->regenerate();

            AuditLog::create([
                'user_id'    => $user->id,
                'user_name'  => $user->name,
                'aktivitas'  => 'USER_LOGIN',
                'deskripsi'  => "Login pengguna: {$user->email}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi tidak cocok.',
        ])->onlyInput('email');
    }

    public function loginAdmin(Request $request)
    {
        return $this->loginSiswa($request);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id'    => Auth::id(),
                'user_name'  => Auth::user()->name,
                'aktivitas'  => 'USER_LOGOUT',
                'deskripsi'  => 'User logout dari sistem perpustakaan',
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
