@extends('layouts.dashboard')

@section('title', 'Dashboard Administrator')
@section('page_heading', 'Dashboard Administrator Utama')

@section('content')
<div class="space-y-6">

    <!-- Primary Stats Grid (Prominent 2px Border Cards with Hover Shadows) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Total Judul Buku -->
        <div class="bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-sm hover:border-brand-300 hover:shadow-md transition duration-300 flex items-center justify-between group">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Total Judul Buku</span>
                <span class="text-2xl sm:text-3xl font-black text-gray-900 group-hover:text-brand-700 transition">{{ number_format($stats['total_buku']) }}</span>
                <span class="text-[10px] text-gray-400 font-medium block">Koleksi Terdaftar</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-brand-50 border border-brand-100 text-brand-700 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-110 transition duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>

        <!-- Card 2: Total Eksemplar -->
        <div class="bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-sm hover:border-blue-300 hover:shadow-md transition duration-300 flex items-center justify-between group">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Total Eksemplar</span>
                <span class="text-2xl sm:text-3xl font-black text-gray-900 group-hover:text-blue-700 transition">{{ number_format($stats['total_eksemplar']) }}</span>
                <span class="text-[10px] text-gray-400 font-medium block">Fisik Buku Unik</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 text-blue-700 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-110 transition duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
        </div>

        <!-- Card 3: Anggota Terdaftar -->
        <div class="bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-sm hover:border-emerald-300 hover:shadow-md transition duration-300 flex items-center justify-between group">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Anggota Terdaftar</span>
                <span class="text-2xl sm:text-3xl font-black text-gray-900 group-hover:text-emerald-700 transition">{{ number_format($stats['total_anggota']) }}</span>
                <span class="text-[10px] text-gray-400 font-medium block">Siswa &amp; Anggota Active</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-110 transition duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>

        <!-- Card 4: Total Kas Denda -->
        <div class="bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-sm hover:border-rose-300 hover:shadow-md transition duration-300 flex items-center justify-between group">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Total Kas Denda</span>
                <span class="text-xl sm:text-2xl font-black text-brand-700 block">Rp {{ number_format($stats['total_denda'], 0, ',', '.') }}</span>
                <span class="text-[10px] text-gray-400 font-medium block">Akumulasi Penerimaan</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 text-rose-700 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-110 transition duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Analytics & Most Borrowed Books Grid (2px Border Containers) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Clean Bar Graph Simulation (Statistik Tren 7 Hari) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border-2 border-gray-200 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b-2 border-gray-100 pb-4">
                <div>
                    <h3 class="text-sm font-black text-gray-900">Statistik Sirkulasi 7 Hari Terakhir</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">Grafik perbandingan transaksi peminjaman dan pengembalian</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-100 uppercase tracking-wider">
                    Realtime Metrics
                </span>
            </div>

            <div class="h-52 flex items-end justify-between gap-3 pt-6 px-2 border-b-2 border-gray-200">
                @foreach($chartDates as $index => $dateStr)
                    @php
                        $lCount = $chartLoans[$index] ?? 0;
                        $rCount = $chartReturns[$index] ?? 0;
                        $lHeight = min($lCount * 30 + 12, 150);
                        $rHeight = min($rCount * 30 + 12, 150);
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1 group">
                        <div class="w-full flex items-end justify-center gap-1.5">
                            <div style="height: {{ $lHeight }}px" class="w-1/2 bg-brand-700 rounded-t-md transition-all duration-300 group-hover:bg-brand-800 shadow-2xs" title="Pinjam: {{ $lCount }}"></div>
                            <div style="height: {{ $rHeight }}px" class="w-1/2 bg-emerald-600 rounded-t-md transition-all duration-300 group-hover:bg-emerald-700 shadow-2xs" title="Kembali: {{ $rCount }}"></div>
                        </div>
                        <span class="text-[10px] font-bold text-gray-600 mt-2">{{ $dateStr }}</span>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-center gap-6 text-xs font-bold text-gray-700 pt-1">
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-md bg-brand-700 inline-block shadow-2xs"></span>
                    <span>Peminjaman</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-md bg-emerald-600 inline-block shadow-2xs"></span>
                    <span>Pengembalian</span>
                </div>
            </div>
        </div>

        <!-- Most Borrowed Books Container -->
        <div class="bg-white rounded-2xl border-2 border-gray-200 p-6 shadow-sm space-y-4">
            <div class="border-b-2 border-gray-100 pb-4">
                <h3 class="text-sm font-black text-gray-900">Buku Paling Sering Dipinjam</h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Top 5 modul populer kejuruan</p>
            </div>
            <div class="space-y-3">
                @foreach($mostBorrowedBooks as $mBook)
                    <div class="p-3.5 bg-gray-50/90 rounded-xl border border-gray-200 hover:border-brand-300 transition duration-300 flex items-center justify-between text-xs group">
                        <div class="min-w-0 pr-2 space-y-0.5">
                            <h4 class="font-bold text-gray-900 group-hover:text-brand-700 transition truncate">{{ $mBook->judul }}</h4>
                            <p class="text-[10px] text-gray-500 font-mono">ISBN: {{ $mBook->isbn }}</p>
                        </div>
                        <span class="px-2.5 py-1 bg-white border border-gray-300 text-gray-900 font-extrabold text-[10px] rounded-lg shrink-0 shadow-2xs">
                            {{ $mBook->peminjaman_count }}x Pinjam
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Recent System Audit Log (Prominent 2px Border Card Table) -->
    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b-2 border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-gray-900">Aktivitas Audit Log Sistem Terbaru</h3>
                <p class="text-[11px] text-gray-500">Catatan transaksi dan perubahan data back-office</p>
            </div>
            <a href="{{ route('admin.audit-log') }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 transition">Lihat Audit Log &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider font-extrabold">
                        <th class="py-3.5 px-5 font-bold">Waktu</th>
                        <th class="py-3.5 px-5 font-bold">User Pelaku</th>
                        <th class="py-3.5 px-5 font-bold">Aktivitas</th>
                        <th class="py-3.5 px-5 font-bold">Deskripsi</th>
                        <th class="py-3.5 px-5 font-bold">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    @foreach($recentAuditLogs as $log)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3.5 px-5 text-gray-500">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="py-3.5 px-5 font-bold text-gray-900">{{ $log->user_name ?? 'System' }}</td>
                            <td class="py-3.5 px-5 font-mono text-[11px] font-bold text-brand-700">{{ $log->aktivitas }}</td>
                            <td class="py-3.5 px-5 text-gray-600">{{ $log->deskripsi }}</td>
                            <td class="py-3.5 px-5 font-mono text-gray-400">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
