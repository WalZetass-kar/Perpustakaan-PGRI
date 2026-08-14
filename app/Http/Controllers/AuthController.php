<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class AuthController extends Controller
{
    // Halaman Login Admin / Pengelola Perpustakaan
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    // Proses Login Pengelola
    public function login(Request $request)
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
                'aktivitas'  => 'ADMIN_LOGIN',
                'deskripsi'  => "Admin/Pengelola berhasil login: {$user->email}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kredensial email atau kata sandi pengelola tidak sesuai.',
        ])->onlyInput('email');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id'    => Auth::id(),
                'user_name'  => Auth::user()->name,
                'aktivitas'  => 'ADMIN_LOGOUT',
                'deskripsi'  => 'Pengelola logout dari sistem perpustakaan',
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
