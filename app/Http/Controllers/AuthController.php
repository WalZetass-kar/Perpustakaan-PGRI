<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class AuthController extends Controller
{
    // General / Siswa Dedicated Login Form
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login-siswa');
    }

    // Admin & Staff Dedicated Login Form
    public function showAdminLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login-admin');
    }

    // Siswa Login Submission
    public function loginSiswa(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            $roleName = $user->role->name ?? '';

            // Strict Role Validation: Siswa only allowed
            if (in_array($roleName, ['admin', 'pustakawan'])) {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withErrors([
                    'email' => 'Akun Pengelola (Admin/Pustakawan) tidak diizinkan masuk melalui Portal Siswa. Silakan gunakan Portal Login Admin.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            AuditLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'aktivitas' => 'USER_LOGIN',
                'deskripsi' => "Siswa logged in via Student Portal ({$user->email})",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->intended(route('mahasiswa.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi siswa tidak cocok.',
        ])->onlyInput('email');
    }

    // Admin & Staff Login Submission
    public function loginAdmin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            $roleName = $user->role->name ?? '';

            // Strict Role Validation: Admin & Pustakawan only allowed
            if (!in_array($roleName, ['admin', 'pustakawan'])) {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withErrors([
                    'email' => 'Akun Siswa tidak memiliki hak akses ke Portal Pengelola Administrator.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            AuditLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'aktivitas' => 'USER_LOGIN',
                'deskripsi' => "Staff logged in via Admin Portal ({$user->role->name})",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi pengelola tidak cocok.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'aktivitas' => 'USER_LOGOUT',
                'deskripsi' => "User logged out",
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
