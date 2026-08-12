@extends('layouts.dashboard')

@section('title', 'Pengaturan Operasional Perpustakaan')
@section('page_heading', 'Pengaturan Aturan Peminjaman, Deadline & Denda')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-xl flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
        
        <!-- Header Info -->
        <div class="border-b border-gray-100 pb-4">
            <h2 class="text-base font-bold text-gray-900">Konfigurasi Aturan Operasional Perpustakaan SMK PGRI</h2>
            <p class="text-xs text-gray-500 mt-1">Ubah durasi batas waktu pengembalian (deadline), sanksi denda, dan batasan peminjaman secara dinamis.</p>
        </div>

        <form action="{{ route('admin.pengaturan.update') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <!-- Kelompok 1: Aturan Peminjaman & Deadline Pengembalian -->
            <div class="space-y-4">
                <h3 class="text-xs font-extrabold text-brand-700 uppercase tracking-wider border-b border-gray-100 pb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>1. Batas Waktu & Deadline Pengembalian</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200 space-y-1">
                        <label class="block font-bold text-gray-900">Deadline Masa Pinjam Standar (Hari)</label>
                        <p class="text-[11px] text-gray-500 mb-2">Batas waktu maksimal siswa meminjam buku hingga tanggal jatuh tempo.</p>
                        <div class="flex items-center gap-2">
                            <input type="number" name="durasi_pinjam_hari" value="{{ old('durasi_pinjam_hari', \App\Models\Pengaturan::where('key', 'durasi_pinjam_hari')->value('value') ?? 7) }}" required min="1"
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700 font-mono font-bold text-sm">
                            <span class="font-bold text-gray-600">Hari</span>
                        </div>
                    </div>

                    <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200 space-y-1">
                        <label class="block font-bold text-gray-900">Maksimal Perpanjangan Online</label>
                        <p class="text-[11px] text-gray-500 mb-2">Berapa kali siswa diperbolehkan memperpanjang deadline peminjaman.</p>
                        <div class="flex items-center gap-2">
                            <input type="number" name="max_perpanjangan" value="{{ old('max_perpanjangan', \App\Models\Pengaturan::where('key', 'max_perpanjangan')->value('value') ?? 2) }}" required min="0"
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700 font-mono font-bold text-sm">
                            <span class="font-bold text-gray-600">Kali</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200 space-y-1">
                    <label class="block font-bold text-gray-900">Batas Maksimal Buku Dipinjam Per Siswa</label>
                    <p class="text-[11px] text-gray-500 mb-2">Jumlah maksimal judul/eksemplar yang dapat dipinjam bersamaan.</p>
                    <div class="flex items-center gap-2 max-w-xs">
                        <input type="number" name="max_buku_pinjam" value="{{ old('max_buku_pinjam', \App\Models\Pengaturan::where('key', 'max_buku_pinjam')->value('value') ?? 3) }}" required min="1"
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700 font-mono font-bold text-sm">
                        <span class="font-bold text-gray-600">Buku</span>
                    </div>
                </div>
            </div>

            <!-- Kelompok 2: Tarif Sanksi & Denda Keterlambatan -->
            <div class="space-y-4 pt-2">
                <h3 class="text-xs font-extrabold text-brand-700 uppercase tracking-wider border-b border-gray-100 pb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>2. Sanksi & Tarif Denda Keterlambatan</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200 space-y-1">
                        <label class="block font-bold text-gray-900">Denda Keterlambatan Per Hari</label>
                        <p class="text-[11px] text-gray-500 mb-2">Tarif denda telat mengembalikan per hari.</p>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-600">Rp</span>
                            <input type="number" name="denda_per_hari" value="{{ old('denda_per_hari', \App\Models\Pengaturan::where('key', 'denda_per_hari')->value('value') ?? 2000) }}" required min="0" step="500"
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700 font-mono font-bold text-sm text-brand-700">
                        </div>
                    </div>

                    <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200 space-y-1">
                        <label class="block font-bold text-gray-900">Denda Buku Rusak</label>
                        <p class="text-[11px] text-gray-500 mb-2">Sanksi penggantian fisik buku rusak.</p>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-600">Rp</span>
                            <input type="number" name="denda_buku_rusak" value="{{ old('denda_buku_rusak', \App\Models\Pengaturan::where('key', 'denda_buku_rusak')->value('value') ?? 30000) }}" required min="0" step="1000"
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700 font-mono font-bold text-sm text-rose-700">
                        </div>
                    </div>

                    <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200 space-y-1">
                        <label class="block font-bold text-gray-900">Denda Buku Hilang</label>
                        <p class="text-[11px] text-gray-500 mb-2">Sanksi penggantian fisik buku hilang.</p>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-600">Rp</span>
                            <input type="number" name="denda_buku_hilang" value="{{ old('denda_buku_hilang', \App\Models\Pengaturan::where('key', 'denda_buku_hilang')->value('value') ?? 100000) }}" required min="0" step="1000"
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700 font-mono font-bold text-sm text-rose-700">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kelompok 3: Profil Sekolah & Jam Layanan -->
            <div class="space-y-4 pt-2">
                <h3 class="text-xs font-extrabold text-brand-700 uppercase tracking-wider border-b border-gray-100 pb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>3. Profil Sekolah &amp; Jam Layanan</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block font-bold text-gray-800">Nama Resmi Perpustakaan</label>
                        <input type="text" name="nama_perpustakaan" value="{{ old('nama_perpustakaan', \App\Models\Pengaturan::where('key', 'nama_perpustakaan')->value('value') ?? 'Perpustakaan SMK PGRI') }}" required
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700 font-semibold">
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-800">Teks Jam Operasional Sekolah</label>
                        <input type="text" name="jam_operasional" value="{{ old('jam_operasional', \App\Models\Pengaturan::where('key', 'jam_operasional')->value('value') ?? 'Senin - Jumat: 07.00 - 15.30 WIB | Sabtu: 07.00 - 12.00 WIB') }}" required
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700 font-semibold">
                    </div>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="pt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-brand-700 text-white font-bold text-xs rounded-xl hover:bg-brand-800 transition shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan Konfigurasi Aturan</span>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
