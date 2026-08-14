@extends('layouts.dashboard')

@section('title', 'Dashboard Perpustakaan')
@section('page_heading', 'Dashboard Overview Perpustakaan')

@section('content')
<div class="space-y-6">

    <!-- Quick Action Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-black text-gray-900">Aksi Cepat Perpustakaan</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Pintasan transaksi dan penambahan data master utama</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.peminjaman') }}" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs rounded-xl transition shadow-sm flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Peminjaman Baru</span>
            </a>
            <a href="{{ route('admin.buku') }}" class="px-3.5 py-2 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-sm flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>+ Tambah Buku</span>
            </a>
            <a href="{{ route('admin.kategori') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                Kelola Kategori
            </a>
            <a href="{{ route('admin.rak') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                Kelola Rak
            </a>
        </div>
    </div>

    <!-- 1. SIRKULASI HARI INI (Statistik Live Hari Ini) -->
    <div>
        <h3 class="text-xs font-black text-gray-500 uppercase tracking-wider mb-3">Sirkulasi Hari Ini ({{ date('d M Y') }})</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <div class="p-4 sm:p-5 rounded-2xl bg-white border-2 border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-gray-500 block">Peminjaman Hari Ini</span>
                    <span class="text-2xl font-black text-amber-600 mt-1 block">{{ $stats['peminjaman_hari_ini'] }} Transaksi</span>
                    <span class="text-[10px] text-gray-400 font-medium">Buku dipinjam hari ini</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-white border-2 border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-gray-500 block">Buku Sedang Dipinjam</span>
                    <span class="text-2xl font-black text-brand-700 mt-1 block">{{ $stats['buku_sedang_dipinjam'] }} Buku</span>
                    <span class="text-[10px] text-gray-400 font-medium">Belum dikembalikan</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-white border-2 border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-gray-500 block">Pengembalian Hari Ini</span>
                    <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $stats['pengembalian_hari_ini'] }} Transaksi</span>
                    <span class="text-[10px] text-gray-400 font-medium">Sudah kembali ke rak</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>

        </div>
    </div>

    <!-- 2. MASTER DATA OVERVIEW (Grid 6 Cards) -->
    <div>
        <h3 class="text-xs font-black text-gray-500 uppercase tracking-wider mb-3">Master Koleksi & Inventaris Fisik</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            
            <a href="{{ route('admin.buku') }}" class="p-4 rounded-2xl bg-white border-2 border-gray-200 shadow-sm hover:border-brand-300 transition block">
                <span class="text-[10.5px] font-bold text-gray-500 block">Total Judul</span>
                <span class="text-xl font-black text-gray-900 mt-1 block">{{ $stats['total_judul'] }}</span>
                <span class="text-[9.5px] text-brand-700 font-bold block mt-0.5">Judul Buku &rarr;</span>
            </a>

            <a href="{{ route('admin.buku') }}" class="p-4 rounded-2xl bg-white border-2 border-gray-200 shadow-sm hover:border-brand-300 transition block">
                <span class="text-[10.5px] font-bold text-gray-500 block">Total Fisik Buku</span>
                <span class="text-xl font-black text-gray-900 mt-1 block">{{ $stats['total_buku'] }}</span>
                <span class="text-[9.5px] text-emerald-600 font-bold block mt-0.5">{{ $stats['buku_tersedia'] }} Tersedia</span>
            </a>

            <a href="{{ route('admin.kategori') }}" class="p-4 rounded-2xl bg-white border-2 border-gray-200 shadow-sm hover:border-brand-300 transition block">
                <span class="text-[10.5px] font-bold text-gray-500 block">Kategori</span>
                <span class="text-xl font-black text-gray-900 mt-1 block">{{ $stats['total_kategori'] }}</span>
                <span class="text-[9.5px] text-gray-400 font-bold block mt-0.5">Klasifikasi &rarr;</span>
            </a>

            <a href="{{ route('admin.penulis') }}" class="p-4 rounded-2xl bg-white border-2 border-gray-200 shadow-sm hover:border-brand-300 transition block">
                <span class="text-[10.5px] font-bold text-gray-500 block">Penulis</span>
                <span class="text-xl font-black text-gray-900 mt-1 block">{{ $stats['total_penulis'] }}</span>
                <span class="text-[9.5px] text-gray-400 font-bold block mt-0.5">Pengarang &rarr;</span>
            </a>

            <a href="{{ route('admin.penerbit') }}" class="p-4 rounded-2xl bg-white border-2 border-gray-200 shadow-sm hover:border-brand-300 transition block">
                <span class="text-[10.5px] font-bold text-gray-500 block">Penerbit</span>
                <span class="text-xl font-black text-gray-900 mt-1 block">{{ $stats['total_penerbit'] }}</span>
                <span class="text-[9.5px] text-gray-400 font-bold block mt-0.5">Percetakan &rarr;</span>
            </a>

            <a href="{{ route('admin.rak') }}" class="p-4 rounded-2xl bg-white border-2 border-gray-200 shadow-sm hover:border-brand-300 transition block">
                <span class="text-[10.5px] font-bold text-gray-500 block">Rak Lokasi</span>
                <span class="text-xl font-black text-gray-900 mt-1 block">{{ $stats['total_rak'] }}</span>
                <span class="text-[9.5px] text-gray-400 font-bold block mt-0.5">Posisi Ruang &rarr;</span>
            </a>

        </div>
    </div>

    <!-- 3. PEMINJAMAN TERBARU & AUDIT AKTIVITAS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Peminjaman Terbaru (2 Kolom) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-black text-gray-900 uppercase">Peminjaman Terbaru</h3>
                    <p class="text-[10.5px] text-gray-500">Daftar transaksi sirkulasi buku terkini</p>
                </div>
                <a href="{{ route('admin.peminjaman') }}" class="text-[11px] font-extrabold text-brand-700 hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 font-bold">
                            <th class="py-2.5 px-4">Peminjam</th>
                            <th class="py-2.5 px-4">Judul Buku</th>
                            <th class="py-2.5 px-4 text-center">Jumlah</th>
                            <th class="py-2.5 px-4">Waktu</th>
                            <th class="py-2.5 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                        @forelse($recentLoans as $loan)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="py-2.5 px-4 font-bold text-gray-900">{{ $loan->user->name ?? '-' }}</td>
                                <td class="py-2.5 px-4 max-w-xs truncate">{{ $loan->buku->judul ?? '-' }}</td>
                                <td class="py-2.5 px-4 text-center font-bold">{{ $loan->jumlah }}</td>
                                <td class="py-2.5 px-4 text-gray-500 font-mono text-[10.5px]">{{ $loan->created_at->format('d M H:i') }}</td>
                                <td class="py-2.5 px-4 text-center">
                                    @if($loan->status === 'dikembalikan')
                                        <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">KEMBALI</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">DIPINJAM</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-400 font-medium">Belum ada transaksi peminjaman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Log Aktivitas Admin (1 Kolom) -->
        <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm p-4 flex flex-col justify-between">
            <div>
                <div class="pb-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-xs font-black text-gray-900 uppercase">Log Aktivitas Sistem</h3>
                    <a href="{{ route('admin.audit-log') }}" class="text-[10px] font-extrabold text-brand-700 hover:underline">Semua</a>
                </div>
                <div class="divide-y divide-gray-100 mt-2 space-y-2">
                    @forelse($recentAuditLogs as $log)
                        <div class="pt-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-900 text-[11px]">{{ $log->user_name ?? 'Sistem' }}</span>
                                <span class="text-[9.5px] text-gray-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[10.5px] text-gray-600 mt-0.5 line-clamp-1">{{ $log->deskripsi ?? $log->aktivitas }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-6">Belum ada aktivitas tercatat.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
