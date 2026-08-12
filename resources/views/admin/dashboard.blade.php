@extends('layouts.dashboard')

@section('title', 'Dashboard Administrator')
@section('page_heading', 'Dashboard Administrator Utama')

@section('content')
<div class="space-y-6">

    <!-- Primary Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Total Judul Buku</span>
                <span class="text-2xl font-extrabold text-gray-900 mt-1 block">{{ number_format($stats['total_buku']) }}</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Total Eksemplar</span>
                <span class="text-2xl font-extrabold text-gray-900 mt-1 block">{{ number_format($stats['total_eksemplar']) }}</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Anggota Terdaftar</span>
                <span class="text-2xl font-extrabold text-gray-900 mt-1 block">{{ number_format($stats['total_anggota']) }}</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Total Kas Denda</span>
                <span class="text-2xl font-extrabold text-brand-700 mt-1 block">Rp {{ number_format($stats['total_denda'], 0, ',', '.') }}</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Chart & Analytics Visual Representation -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Clean Bar Graph Simulation (Statistik Tren 7 Hari) -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-900">Statistik Peminjaman & Pengembalian 7 Hari Terakhir</h3>
                <span class="text-xs text-gray-400 font-medium">Realtime Metrics</span>
            </div>

            <div class="h-48 flex items-end justify-between gap-3 pt-6 px-2 border-b border-gray-200">
                @foreach($chartDates as $index => $dateStr)
                    @php
                        $lCount = $chartLoans[$index] ?? 0;
                        $rCount = $chartReturns[$index] ?? 0;
                        $lHeight = min($lCount * 30 + 10, 140);
                        $rHeight = min($rCount * 30 + 10, 140);
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1 group">
                        <div class="w-full flex items-end justify-center gap-1">
                            <div style="height: {{ $lHeight }}px" class="w-1/2 bg-brand-700 rounded-t transition-all group-hover:bg-brand-800" title="Pinjam: {{ $lCount }}"></div>
                            <div style="height: {{ $rHeight }}px" class="w-1/2 bg-emerald-600 rounded-t transition-all group-hover:bg-emerald-700" title="Kembali: {{ $rCount }}"></div>
                        </div>
                        <span class="text-[10px] font-medium text-gray-500 mt-2">{{ $dateStr }}</span>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-center gap-6 text-xs text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-brand-700 inline-block"></span>
                    <span>Peminjaman</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-emerald-600 inline-block"></span>
                    <span>Pengembalian</span>
                </div>
            </div>
        </div>

        <!-- Most Borrowed Books -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm space-y-4">
            <div class="border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-900">Buku Paling Sering Dipinjam</h3>
            </div>
            <div class="space-y-3">
                @foreach($mostBorrowedBooks as $mBook)
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-between text-xs">
                        <div class="min-w-0 pr-2">
                            <h4 class="font-bold text-gray-900 truncate">{{ $mBook->judul }}</h4>
                            <p class="text-[11px] text-gray-500">ISBN: {{ $mBook->isbn }}</p>
                        </div>
                        <span class="px-2 py-1 bg-white border border-gray-200 text-gray-800 font-bold text-[10px] rounded shrink-0">
                            {{ $mBook->peminjaman_count }}x Pinjam
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Recent System Audit Log -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Aktivitas Audit Log Sistem Terbaru</h3>
            <a href="{{ route('admin.audit-log') }}" class="text-xs font-medium text-brand-700 hover:text-brand-800">Lihat Audit Log &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-5 font-semibold">Waktu</th>
                        <th class="py-3 px-5 font-semibold">User Pelaku</th>
                        <th class="py-3 px-5 font-semibold">Aktivitas</th>
                        <th class="py-3 px-5 font-semibold">Deskripsi</th>
                        <th class="py-3 px-5 font-semibold">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @foreach($recentAuditLogs as $log)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-3 px-5 text-gray-500">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="py-3 px-5 font-bold text-gray-900">{{ $log->user_name ?? 'System' }}</td>
                            <td class="py-3 px-5 font-mono text-[11px] font-bold text-brand-700">{{ $log->aktivitas }}</td>
                            <td class="py-3 px-5 text-gray-600">{{ $log->deskripsi }}</td>
                            <td class="py-3 px-5 font-mono text-gray-400">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
