@extends('layouts.dashboard')

@section('title', 'Pengaturan Sistem Perpustakaan')
@section('page_heading', 'Pengaturan Sistem & Operasional')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">

        <div class="border-b border-gray-100 pb-4">
            <h2 class="text-base font-bold text-gray-900">Konfigurasi Sistem Perpustakaan SMK PGRI Pekanbaru</h2>
            <p class="text-xs text-gray-500 mt-1">Kelola identitas resmi perpustakaan dan aturan sirkulasi</p>
        </div>

        <form action="{{ route('admin.pengaturan.update') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <div class="space-y-4">
                <h3 class="text-xs font-extrabold text-brand-700 uppercase tracking-wider border-b border-gray-100 pb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>1. Identitas Perpustakaan & Jam Layanan</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block font-bold text-gray-800">Nama Resmi Perpustakaan <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_perpustakaan" value="{{ old('nama_perpustakaan', $pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI Pekanbaru') }}" required
                            class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-semibold">
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-800">Teks Jam Operasional Sekolah <span class="text-rose-500">*</span></label>
                        <input type="text" name="jam_operasional" value="{{ old('jam_operasional', $pengaturan['jam_operasional'] ?? 'Senin - Jumat: 07.00 - 15.30 WIB') }}" required
                            class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-semibold">
                    </div>
                </div>
            </div>

            <div class="space-y-4 pt-2">
                <h3 class="text-xs font-extrabold text-brand-700 uppercase tracking-wider border-b border-gray-100 pb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>2. Aturan Batasan Peminjaman</span>
                </h3>

                <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200 space-y-1">
                    <label class="block font-bold text-gray-900">Batas Maksimal Buku Dipinjam Bersamaan Per Anggota</label>
                    <p class="text-[11px] text-gray-500 mb-2">Jumlah maksimal buku yang dapat dipinjam siswa dalam satu transaksi peminjaman harian.</p>
                    <div class="flex items-center gap-2 max-w-xs">
                        <input type="number" name="max_buku_pinjam" value="{{ old('max_buku_pinjam', $pengaturan['max_buku_pinjam'] ?? 3) }}" required min="1" max="20"
                            class="w-full px-3.5 py-2 bg-white border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 font-mono font-bold text-sm">
                        <span class="font-bold text-gray-600">Buku</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-brand-700 text-white font-extrabold text-xs rounded-xl hover:bg-brand-800 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
