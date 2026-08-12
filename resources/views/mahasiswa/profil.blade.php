@extends('layouts.dashboard')

@section('title', 'Profil Saya')
@section('page_heading', 'Profil Saya')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">
    <div class="border-b border-gray-100 pb-4">
        <h2 class="text-base font-bold text-gray-900">Informasi Pengguna</h2>
        <p class="text-xs text-gray-500">Perbarui informasi kontak personal akun Anda</p>
    </div>

    <form action="{{ route('mahasiswa.profil.update') }}" method="POST" class="space-y-4 text-xs">
        @csrf
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none">
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Alamat Email (Akademik)</label>
            <input type="email" value="{{ $user->email }}" disabled
                class="w-full px-3.5 py-2.5 bg-gray-100 border border-gray-200 text-gray-500 rounded-lg text-xs cursor-not-allowed">
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Nomor Telepon / WhatsApp</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0812xxxxxxxx"
                class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none">
        </div>

        <div class="pt-4 border-t border-gray-100 flex justify-end">
            <button type="submit" class="px-5 py-2.5 bg-brand-700 text-white font-medium text-xs rounded-lg hover:bg-brand-800 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
