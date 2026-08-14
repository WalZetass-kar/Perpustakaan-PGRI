@extends('layouts.dashboard')

@section('title', 'Dashboard Siswa')
@section('page_heading', 'Dashboard & Layanan Mandiri Siswa')

@section('content')
<div class="space-y-6" x-data="{ openDetailModal: false, modalData: {} }">
    
    <!-- Primary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Buku Dipinjam -->
        <div class="bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-sm hover:border-blue-300 hover:shadow-md transition duration-300 flex items-center justify-between group">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Buku Dipinjam</span>
                <span class="text-2xl sm:text-3xl font-black text-gray-900 group-hover:text-blue-700 transition">{{ $activeLoans->count() }}</span>
                <span class="text-[10px] text-gray-400 font-medium block">Aktif Ditangan</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 text-blue-700 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-110 transition duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>

        <!-- Card 2: Mendekati Tempo -->
        <div class="bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-sm hover:border-amber-300 hover:shadow-md transition duration-300 flex items-center justify-between group">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Mendekati Tempo</span>
                <span class="text-2xl sm:text-3xl font-black text-amber-600 block">{{ $nearingDueLoans->count() }}</span>
                <span class="text-[10px] text-amber-500 font-medium block">Perlu Perpanjangan</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 text-amber-700 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-110 transition duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Card 3: Total Denda Aktif -->
        <div class="bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-sm hover:border-rose-300 hover:shadow-md transition duration-300 flex items-center justify-between group">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Total Denda Aktif</span>
                <span class="text-xl sm:text-2xl font-black text-brand-700 block">Rp {{ number_format($totalFines, 0, ',', '.') }}</span>
                <span class="text-[10px] text-gray-400 font-medium block">Tunggakan Pengembalian</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 text-rose-700 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-110 transition duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Card 4: Reservasi Aktif -->
        <div class="bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-sm hover:border-purple-300 hover:shadow-md transition duration-300 flex items-center justify-between group">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Reservasi Aktif</span>
                <span class="text-2xl sm:text-3xl font-black text-purple-700 block">{{ $activeReservations->count() }}</span>
                <span class="text-[10px] text-gray-400 font-medium block">Antrean Diproses</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 border border-purple-100 text-purple-700 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-110 transition duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>

    <!-- Active Loans Table Card -->
    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b-2 border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-black text-gray-900">Buku Sedang Dipinjam Saya</h2>
                <p class="text-[11px] text-gray-500 mt-0.5">Daftar buku fisik yang saat ini berada dalam masa pinjam Anda</p>
            </div>
            <a href="{{ route('mahasiswa.peminjaman') }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 transition">Lihat Semua Peminjaman &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider font-extrabold">
                        <th class="py-3.5 px-5 font-bold">Kode TRX</th>
                        <th class="py-3.5 px-5 font-bold">Judul Buku</th>
                        <th class="py-3.5 px-5 font-bold">Tanggal Pinjam</th>
                        <th class="py-3.5 px-5 font-bold">Jatuh Tempo</th>
                        <th class="py-3.5 px-5 font-bold">Status Tempo</th>
                        <th class="py-3.5 px-5 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    @forelse($activeLoans as $loan)
                        @php
                            $today = \Carbon\Carbon::today();
                            $due = \Carbon\Carbon::parse($loan->tanggal_jatuh_tempo);
                            $diffDays = (int) $today->diffInDays($due, false);
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3.5 px-5 font-mono font-bold text-gray-900">{{ $loan->kode_peminjaman }}</td>
                            <td class="py-3.5 px-5 font-bold text-gray-900">{{ $loan->buku->judul ?? '-' }}</td>
                            <td class="py-3.5 px-5 text-gray-600">{{ $loan->tanggal_pinjam }}</td>
                            <td class="py-3.5 px-5 text-gray-600 font-bold">{{ $loan->tanggal_jatuh_tempo }}</td>
                            <td class="py-3.5 px-5">
                                @if($diffDays < 0)
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">Terlambat {{ abs($diffDays) }} Hari</span>
                                @elseif($diffDays <= 3)
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">Sisa {{ $diffDays }} Hari</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">Sisa {{ $diffDays }} Hari</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                @if($diffDays >= 0 && $loan->jumlah_perpanjangan < 2)
                                    <form action="{{ route('mahasiswa.peminjaman.perpanjang', $loan->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3.5 py-1.5 bg-brand-700 text-white font-extrabold rounded-xl text-[11px] hover:bg-brand-800 transition shadow-2xs transform active:scale-95">Perpanjang Online</button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-[11px] font-medium">Batas Maksimum</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400 font-medium">Anda sedang tidak memiliki peminjaman buku aktif saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- DEDICATED STUDENT BOOK SHOWCASE & DIRECT BORROWING SECTION -->
    <div class="bg-white rounded-3xl border-2 border-gray-200 shadow-sm p-6 space-y-6">
        
        <!-- Header & Search Filter Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b-2 border-gray-100">
            <div>
                <h2 class="text-base font-black text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Koleksi Buku &amp; Modul Kejuruan Sekolah</span>
                </h2>
                <p class="text-xs text-gray-500">Cari judul buku dan ajukan booking / peminjaman langsung dari dashboard Anda.</p>
            </div>

            <!-- Search Form -->
            <form action="{{ route('mahasiswa.dashboard') }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, ISBN, penulis..."
                    class="px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-brand-700 w-48 sm:w-64">
                
                <select name="kategori_id" onchange="this.form.submit()" class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-brand-700">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-2xs">Cari</button>
            </form>
        </div>

        <!-- 4-Column Book Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($koleksiBuku as $buku)
                @php
                    $available = $buku->jumlah_tersedia;
                    $coverUrl = $buku->cover ? asset('storage/' . $buku->cover) : null;
                @endphp
                <div class="bg-gray-50/60 rounded-2xl border-2 border-gray-200 overflow-hidden hover:border-brand-600 hover:shadow-md transition duration-300 flex flex-col justify-between p-4 group">
                    <div class="space-y-3">
                        <!-- Book Artwork Preview -->
                        <div class="w-full h-44 bg-gray-200 border border-gray-300 rounded-xl overflow-hidden shadow-2xs relative">
                            @if($coverUrl)
                                <img src="{{ $coverUrl }}" alt="Cover {{ $buku->judul }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-brand-800 text-white font-black text-3xl p-3 text-center">
                                    {{ substr($buku->judul, 0, 1) }}
                                </div>
                            @endif

                            <span class="absolute top-2 right-2 px-2.5 py-0.5 rounded-lg text-[9.5px] font-black uppercase bg-white/90 backdrop-blur-xs text-gray-800 shadow-xs border border-gray-200">
                                {{ $buku->kategori->nama ?? 'Umum' }}
                            </span>
                        </div>

                        <!-- Info -->
                        <div class="space-y-1">
                            <h3 class="text-xs font-bold text-gray-900 leading-snug line-clamp-2 group-hover:text-brand-700 transition">
                                {{ $buku->judul }}
                            </h3>
                            <p class="text-[11px] text-gray-500">Penulis: <strong class="text-gray-800">{{ $buku->penulis->nama ?? '-' }}</strong></p>
                            <div class="flex items-center justify-between text-[10px] pt-1">
                                <span class="font-mono text-brand-700 font-bold">Rak: {{ $buku->rak->kode_rak ?? '-' }}</span>
                                <span class="font-black text-emerald-600">{{ $available }} Ready</span>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Action Buttons -->
                    <div class="pt-3 mt-3 border-t border-gray-200 flex items-center justify-between gap-2">
                        <a href="{{ route('buku.detail', $buku->id) }}" class="text-[11px] font-bold text-gray-600 hover:text-gray-900 underline">
                            Detail
                        </a>
                        <form action="{{ route('mahasiswa.reservasi.buat', $buku->id) }}" method="POST" onsubmit="return confirmAction(event, 'Booking Buku Ini?', 'Konfirmasi booking online untuk buku: {{ addslashes($buku->judul) }}', 'Ya, Booking Sekarang!')">
                            @csrf
                            <button type="submit" class="px-3.5 py-1.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-[11px] rounded-xl transition shadow-2xs flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Booking</span>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-gray-500 font-medium">
                    Tidak ditemukan buku yang cocok dengan pencarian Anda.
                </div>
            @endforelse
        </div>

        <!-- Pagination Bar -->
        <div class="pt-4 border-t border-gray-100">
            {{ $koleksiBuku->links() }}
        </div>
    </div>

</div>
@endsection
