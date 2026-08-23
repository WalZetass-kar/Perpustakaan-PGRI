<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return $this->tolak($request, 'Silakan login terlebih dahulu untuk mengakses halaman pengelola.');
        }

        $user = Auth::user();

        // Status akun dievaluasi ulang di setiap request, bukan hanya saat
        // login. Tanpa ini, pengelola yang dinonaktifkan di tengah sesi masih
        // bisa memakai seluruh fitur admin sampai sesinya kedaluwarsa sendiri.
        if ($user->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $this->tolak($request, 'Akun pengelola ini sedang dinonaktifkan. Silakan hubungi Super Administrator.');
        }

        $userRole = $user->role->name ?? null;

        if (!in_array($userRole, $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }

            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }

    private function tolak(Request $request, string $pesan): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $pesan], 401);
        }

        return redirect()->route('login')->withErrors(['email' => $pesan]);
    }
}
