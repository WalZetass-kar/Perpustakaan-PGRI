@extends('layouts.app')

@section('title', 'Masuk - Sistem Perpustakaan PGRI')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 rounded-xl border border-gray-200 shadow-sm space-y-6">
        
        <div class="text-center">
            <div class="w-12 h-12 rounded-xl bg-brand-700 text-white font-bold flex items-center justify-center text-xl mx-auto shadow-sm">
                P
            </div>
            <h2 class="mt-4 text-2xl font-bold text-gray-900">Masuk Akun Perpustakaan</h2>
            <p class="mt-1 text-xs text-gray-500">Gunakan akun civitas akademika SMK PGRI</p>
        </div>

        @if($errors->any())
            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-lg text-xs text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">Alamat Email Sekolah</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="contoh: siswa@smkpgri.sch.id"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">Kata Sandi</label>
                <input type="password" name="password" id="password" required placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-700 focus:ring-brand-700">
                    <span>Ingat Saya</span>
                </label>
                <span class="text-gray-400">Bantuan Lupa Kata Sandi? Hubungi Pustakawan</span>
            </div>

            <button type="submit" class="w-full py-2.5 bg-brand-700 text-white font-medium text-sm rounded-lg hover:bg-brand-800 transition shadow-sm">
                Masuk ke Sistem
            </button>
        </form>

        <div class="border-t border-gray-100 pt-4 text-center">
            <p class="text-xs text-gray-500 mb-2 font-medium">Akun Demo Pengujian (SMK PGRI):</p>
            <div class="text-[11px] text-gray-600 space-y-1 bg-gray-50 p-3 rounded-lg border border-gray-200">
                <p><strong>Admin:</strong> admin@smkpgri.sch.id | password</p>
                <p><strong>Petugas:</strong> pustakawan@smkpgri.sch.id | password</p>
                <p><strong>Siswa:</strong> siswa@smkpgri.sch.id | password</p>
            </div>
        </div>

    </div>
</div>
@endsection
