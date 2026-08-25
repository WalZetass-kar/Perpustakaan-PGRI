@extends('layouts.dashboard')

@section('title', 'Profil Pengguna & Keamanan')
@section('page_heading', 'Profil Pengguna & Keamanan Akun')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-bold space-y-1">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                <span>Terdapat kesalahan pada input formulir:</span>
            </div>
            <ul class="list-disc list-inside font-normal pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="border-b border-gray-100 pb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-gray-900">{{ $user->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $user->email }} &bull; {{ $user->role->display_name ?? 'Staf' }}</p>
                </div>
            </div>
            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-bold uppercase tracking-wider">
                Status: {{ $user->status === 'active' ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-1">
                <span class="text-gray-500 font-medium">Hak Akses Sistem</span>
                <p class="font-bold text-gray-900">{{ $user->role->display_name ?? '-' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-1">
                <span class="text-gray-500 font-medium">Nomor Telepon / WA</span>
                <p class="font-bold text-gray-900">{{ $user->phone ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="border-b border-gray-100 pb-3 flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center font-bold">
                <i class="fa-solid fa-key"></i>
            </div>
            <div>
                <h3 class="text-sm font-black text-gray-900">Ubah Password Akun</h3>
                <p class="text-[11px] text-gray-500">Keamanan kata sandi akun sistem perpustakaan</p>
            </div>
        </div>

        @if(auth()->user()->isSuperAdmin())
            <form action="{{ route('admin.profil.ubah-password') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div class="space-y-1">
                    <label class="block font-bold text-gray-800">Password Saat Ini <span class="text-rose-500">*</span></label>
                    <input type="password" name="current_password" required
                           class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block font-bold text-gray-800">Password Baru (min. 8 karakter) <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div class="space-y-1">
                        <label class="block font-bold text-gray-800">Konfirmasi Password Baru <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" required minlength="8"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                </div>

                <div class="pt-3 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Password Baru</span>
                    </button>
                </div>
            </form>
        @else
            <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs">
                <i class="fa-solid fa-lock text-amber-500 mt-0.5 shrink-0 text-sm"></i>
                <div class="space-y-1">
                    <p class="font-bold text-amber-800">Perubahan password tidak diizinkan untuk akun ini.</p>
                    <p class="text-amber-700">Jika Anda perlu mengganti password, silakan hubungi <span class="font-bold">Super Administrator</span> perpustakaan untuk mereset password akun Anda.</p>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
