@extends('layouts.dashboard')

@section('title', 'Dashboard Mahasiswa')
@section('page_heading', 'Dashboard Mahasiswa')

@section('content')
<div class="space-y-6">
    
    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Buku Dipinjam</span>
                <span class="text-2xl font-bold text-gray-900 mt-1 block">{{ $activeLoans->count() }}</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Mendekati Tempo</span>
                <span class="text-2xl font-bold text-amber-600 mt-1 block">{{ $nearingDueLoans->count() }}</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Total Denda Aktif</span>
                <span class="text-2xl font-bold text-brand-700 mt-1 block">Rp {{ number_format($totalFines, 0, ',', '.') }}</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Reservasi Aktif</span>
                <span class="text-2xl font-bold text-gray-900 mt-1 block">{{ $activeReservations->count() }}</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>

    <!-- Active Loans Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-900">Buku Sedang Dipinjam</h2>
            <a href="{{ route('mahasiswa.peminjaman') }}" class="text-xs font-medium text-brand-700 hover:text-brand-800">Lihat Semua Peminjaman &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-5 font-semibold">Kode TRX</th>
                        <th class="py-3 px-5 font-semibold">Judul Buku</th>
                        <th class="py-3 px-5 font-semibold">Tanggal Pinjam</th>
                        <th class="py-3 px-5 font-semibold">Jatuh Tempo</th>
                        <th class="py-3 px-5 font-semibold">Status Tempo</th>
                        <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($activeLoans as $loan)
                        @php
                            $today = \Carbon\Carbon::today();
                            $due = \Carbon\Carbon::parse($loan->tanggal_jatuh_tempo);
                            $diffDays = (int) $today->diffInDays($due, false);
                        @endphp
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-3 px-5 font-mono font-medium text-gray-900">{{ $loan->kode_peminjaman }}</td>
                            <td class="py-3 px-5 font-semibold text-gray-900">{{ $loan->buku->judul ?? '-' }}</td>
                            <td class="py-3 px-5">{{ $loan->tanggal_pinjam }}</td>
                            <td class="py-3 px-5">{{ $loan->tanggal_jatuh_tempo }}</td>
                            <td class="py-3 px-5">
                                @if($diffDays < 0)
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-100">Terlambat {{ abs($diffDays) }} Hari</span>
                                @elseif($diffDays <= 3)
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-100">Sisa {{ $diffDays }} Hari</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Sisa {{ $diffDays }} Hari</span>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-right">
                                @if($diffDays >= 0 && $loan->jumlah_perpanjangan < 2)
                                    <form action="{{ route('mahasiswa.peminjaman.perpanjang', $loan->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-brand-700 text-white font-medium rounded text-[11px] hover:bg-brand-800 transition">Perpanjang</button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-[11px]">Tidak dapat diperpanjang</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">Anda sedang tidak memiliki peminjaman buku aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recommendations Grid -->
    <div>
        <h2 class="text-base font-bold text-gray-900 mb-4">Rekomendasi Koleksi Buku</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($recommendations as $rec)
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-semibold bg-gray-100 text-gray-600 px-2 py-0.5 rounded block w-max mb-2">{{ $rec->kategori->nama ?? 'Umum' }}</span>
                        <h3 class="text-xs font-bold text-gray-900 line-clamp-2 mb-1">{{ $rec->judul }}</h3>
                        <p class="text-[11px] text-gray-500 mb-2">Penulis: {{ $rec->penulis->nama ?? '-' }}</p>
                    </div>
                    <a href="{{ route('buku.detail', $rec->id) }}" class="text-xs font-semibold text-brand-700 hover:text-brand-800 mt-2 block">Detail Buku &rarr;</a>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
