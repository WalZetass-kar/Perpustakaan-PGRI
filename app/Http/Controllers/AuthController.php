<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $email = trim(strtolower($request->email));
        $password = $request->password;
        $remember = $request->boolean('remember');

        $existingUser = User::where('email', $email)->first();

        if ($existingUser && $existingUser->status === 'inactive') {
            AuditLog::create([
                'user_id'    => $existingUser->id,
                'user_name'  => $existingUser->name,
                'aktivitas'  => 'LOGIN_DITOLAK_NONAKTIF',
                'deskripsi'  => "Upaya login ditolak karena akun berstatus nonaktif: {$email}",
                'ip_address' => $request->ip(),
            ]);

            return back()->withErrors([
                'email' => 'Akun pengelola ini sedang dinonaktifkan. Silakan hubungi Super Administrator.',
            ])->onlyInput('email');
        }

        if (Auth::attempt(['email' => $email, 'password' => $password, 'status' => 'active'], $remember)) {
            $user = Auth::user();
            $request->session()->regenerate();

            AuditLog::create([
                'user_id'    => $user->id,
                'user_name'  => $user->name,
                'aktivitas'  => 'ADMIN_LOGIN',
                'deskripsi'  => "Pengelola berhasil login: {$user->email}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }

        AuditLog::create([
            'user_id'    => $existingUser ? $existingUser->id : null,
            'user_name'  => $existingUser ? $existingUser->name : 'Tamu Tak Dikenal',
            'aktivitas'  => 'LOGIN_GAGAL',
            'deskripsi'  => "Percobaan login gagal untuk email: {$email}",
            'ip_address' => $request->ip(),
        ]);

        return back()->withErrors([
            'email' => 'Kredensial email atau kata sandi pengelola tidak sesuai.',
        ])->onlyInput('email');
    }

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
