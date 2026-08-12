@extends('layouts.app')

@section('title', 'Beranda - ' . $nama_perpustakaan)

@section('content')
<!-- Hero Section (Polished Background, Micro-Animations & Centered UX) -->
<section class="relative bg-gradient-to-b from-red-50/60 via-white to-gray-50/50 border-b border-gray-200/80 py-12 lg:py-16 overflow-hidden">
    <!-- Subtle Background Accent Shapes -->
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-brand-100/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 -right-24 w-80 h-80 bg-red-100/20 rounded-full blur-2xl pointer-events-none"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
        
        <!-- Animated Badge -->
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-white text-brand-700 border border-brand-200/80 shadow-xs hover:border-brand-300 transition duration-300 transform hover:-translate-y-0.5">
            <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse"></span>
            <span>Pusat Literasi & Layanan Otomasi SMK PGRI</span>
        </div>

        <!-- Main Headline -->
        <div class="space-y-3">
            <h1 class="text-3xl sm:text-5xl font-black text-gray-900 tracking-tight leading-tight">
                Cari & Temukan Koleksi Literatur <span class="text-brand-700 underline decoration-brand-200 decoration-wavy decoration-2">Perpustakaan</span>
            </h1>
            <p class="text-xs sm:text-sm text-gray-600 max-w-2xl mx-auto leading-relaxed">
                Akses cepat modul kejuruan TKJ, RPL, Akuntansi, serta literatur umum secara terintegrasi dan responsif.
            </p>
        </div>

        <!-- Primary Search Card with Soft Shadows & Interactive Micro-Animations -->
        <form action="{{ route('katalog') }}" method="GET" class="bg-white/95 backdrop-blur-xs p-4 sm:p-5 rounded-2xl border border-gray-200 shadow-md max-w-3xl mx-auto space-y-3.5 transition duration-300 hover:shadow-lg">
            <!-- Search Input Row -->
            <div class="flex flex-col sm:flex-row gap-2.5">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul buku, penulis, ISBN, atau kategori..." 
                        class="w-full pl-10 pr-4 py-3 bg-gray-50/70 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-700 focus:bg-white transition duration-200 shadow-2xs">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-6 py-3 bg-brand-700 text-white font-bold text-xs rounded-xl hover:bg-brand-800 transition duration-200 shadow-xs active:scale-95 shrink-0 flex items-center justify-center gap-2 group">
                    <svg class="w-4 h-4 transition duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cari Koleksi</span>
                </button>
            </div>

            <!-- Filter Controls Row -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs pt-3 border-t border-gray-100">
                <select name="kategori_id" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-[11px] font-medium text-gray-700 focus:ring-1 focus:ring-brand-700 focus:bg-white transition">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori_list as $kat)
                        <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                    @endforeach
                </select>

                <select name="penulis_id" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-[11px] font-medium text-gray-700 focus:ring-1 focus:ring-brand-700 focus:bg-white transition">
                    <option value="">Semua Penulis</option>
                    @foreach($penulis_list as $pen)
                        <option value="{{ $pen->id }}">{{ $pen->nama }}</option>
                    @endforeach
                </select>

                <select name="tahun" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-[11px] font-medium text-gray-700 focus:ring-1 focus:ring-brand-700 focus:bg-white transition">
                    <option value="">Semua Tahun</option>
                    @foreach($tahun_list as $th)
                        <option value="{{ $th }}">{{ $th }}</option>
                    @endforeach
                </select>

                <select name="status" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-[11px] font-medium text-gray-700 focus:ring-1 focus:ring-brand-700 focus:bg-white transition">
                    <option value="">Semua Status</option>
                    <option value="tersedia">Tersedia Dipinjam</option>
                    <option value="dipinjam">Sedang Dipinjam</option>
                </select>
            </div>
        </form>

    </div>
</section>

<!-- Relevant Stats Bar (Friendly Cards with Icons & Hover Effects) -->
<section class="py-8 bg-white border-b border-gray-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            
            <div class="bg-gray-50/70 hover:bg-white p-5 rounded-2xl border border-gray-200 hover:border-brand-200 shadow-2xs hover:shadow-md transition duration-300 flex items-center gap-4 group">
                <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-700 font-bold flex items-center justify-center shrink-0 group-hover:scale-110 transition duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Total Koleksi</span>
                    <span class="text-xl sm:text-2xl font-extrabold text-gray-900 block mt-0.5">{{ number_format($stats['total_koleksi']) }} Judul</span>
                </div>
            </div>

            <div class="bg-gray-50/70 hover:bg-white p-5 rounded-2xl border border-gray-200 hover:border-emerald-200 shadow-2xs hover:shadow-md transition duration-300 flex items-center gap-4 group">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center shrink-0 group-hover:scale-110 transition duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Buku Tersedia</span>
                    <span class="text-xl sm:text-2xl font-extrabold text-emerald-700 block mt-0.5">{{ number_format($stats['buku_tersedia']) }} Eksemplar</span>
                </div>
            </div>

            <div class="bg-gray-50/70 hover:bg-white p-5 rounded-2xl border border-gray-200 hover:border-blue-200 shadow-2xs hover:shadow-md transition duration-300 flex items-center gap-4 group">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-700 font-bold flex items-center justify-center shrink-0 group-hover:scale-110 transition duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Sedang Dipinjam</span>
                    <span class="text-xl sm:text-2xl font-extrabold text-blue-700 block mt-0.5">{{ number_format($stats['sedang_dipinjam']) }} Eksemplar</span>
                </div>
            </div>

            <div class="bg-gray-50/70 hover:bg-white p-5 rounded-2xl border border-gray-200 hover:border-purple-200 shadow-2xs hover:shadow-md transition duration-300 flex items-center gap-4 group">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-700 font-bold flex items-center justify-center shrink-0 group-hover:scale-110 transition duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Anggota Aktif</span>
                    <span class="text-xl sm:text-2xl font-extrabold text-gray-900 block mt-0.5">{{ number_format($stats['anggota_aktif']) }} Siswa</span>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Section Jelajahi Berdasarkan Kategori (Compact Pills with Hover Effects) -->
<section id="kategori-section" class="py-10 bg-white border-b border-gray-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Jelajahi Berdasarkan Kategori</h2>
                <p class="text-xs text-gray-500">Pilih bidang keahlian kejuruan atau literatur referensi umum</p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            @foreach($kategori_list as $kat)
                <a href="{{ route('katalog', ['kategori_id' => $kat->id]) }}" 
                   class="bg-gray-50 hover:bg-brand-700 border border-gray-200 hover:border-brand-700 rounded-xl p-3.5 text-center transition duration-200 group shadow-2xs hover:shadow-sm transform hover:-translate-y-1">
                    <span class="text-xs font-bold text-gray-800 group-hover:text-white block truncate transition">{{ $kat->nama }}</span>
                    <span class="text-[10px] font-medium text-gray-500 group-hover:text-brand-100 mt-1 block transition">{{ $kat->buku()->count() }} Koleksi</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Section Catalog Showcase (Card Layout Polish with Smooth Hover Effects) -->
<section class="py-12 bg-gray-50/50" x-data="{ activeTab: 'populer' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header & Tabs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 pb-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">Katalog Rekomendasi Koleksi</h2>
                <p class="text-xs text-gray-500 mt-0.5">Buku teks dan modul pembelajaran terpilih untuk siswa</p>
            </div>

            <!-- Friendly Tab Switcher -->
            <div class="flex items-center gap-1.5 bg-gray-200/80 p-1.5 rounded-xl self-start sm:self-auto">
                <button @click="activeTab = 'populer'" 
                    :class="activeTab === 'populer' ? 'bg-white font-bold text-brand-700 shadow-xs' : 'text-gray-600 font-medium hover:text-gray-900'"
                    class="px-4 py-2 text-xs rounded-lg transition duration-200">
                    🔥 Buku Populer
                </button>
                <button @click="activeTab = 'terbaru'" 
                    :class="activeTab === 'terbaru' ? 'bg-white font-bold text-brand-700 shadow-xs' : 'text-gray-600 font-medium hover:text-gray-900'"
                    class="px-4 py-2 text-xs rounded-lg transition duration-200">
                    ✨ Buku Terbaru
                </button>
            </div>
        </div>

        <!-- TAB 1: BUKU POPULER -->
        <div x-show="activeTab === 'populer'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($buku_populer as $buku)
                @php
                    $available = $buku->jumlah_tersedia;
                    $totalEx = $buku->jumlah_eksemplar;
                @endphp
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-2xs hover:shadow-md hover:border-gray-300 transition duration-300 transform hover:-translate-y-1 flex flex-col justify-between group">
                    <div class="p-5 flex gap-4">
                        <!-- Book Cover Placeholder with Lift Effect -->
                        <div class="w-22 h-32 bg-gradient-to-br from-brand-700 to-brand-900 border border-brand-800 rounded-xl flex flex-col items-center justify-center shrink-0 text-white font-black text-2xl shadow-sm group-hover:scale-105 transition duration-300">
                            <span>{{ substr($buku->judul, 0, 1) }}</span>
                            <span class="text-[9px] font-normal tracking-widest uppercase text-brand-200 mt-1">SMK BOOK</span>
                        </div>
                        <div class="flex-1 min-w-0 space-y-1.5">
                            <div class="flex items-center justify-between gap-1">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-gray-100 text-gray-700 truncate max-w-[110px]">
                                    {{ $buku->kategori->nama ?? 'Umum' }}
                                </span>
                                @if($available > 0)
                                    <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full shrink-0">Tersedia ({{ $available }})</span>
                                @elseif($totalEx > 0)
                                    <span class="text-[10px] font-extrabold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full shrink-0">Dipinjam</span>
                                @else
                                    <span class="text-[10px] font-extrabold text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-full shrink-0">Habis</span>
                                @endif
                            </div>
                            <h3 class="text-xs font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-brand-700 transition">
                                <a href="{{ route('buku.detail', $buku->id) }}">{{ $buku->judul }}</a>
                            </h3>
                            <p class="text-[11px] text-gray-500">Penulis: <span class="text-gray-800 font-semibold">{{ $buku->penulis->nama ?? '-' }}</span></p>
                            <p class="text-[11px] text-gray-400">Lokasi Rak: <span class="font-mono text-gray-800 font-bold bg-gray-100 px-1.5 py-0.5 rounded">{{ $buku->rak->kode_rak ?? '-' }}</span></p>
                        </div>
                    </div>
                    <div class="px-5 py-3 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between text-xs">
                        <span class="text-[11px] text-gray-400 font-mono">ISBN: {{ $buku->isbn }}</span>
                        <a href="{{ route('buku.detail', $buku->id) }}" class="px-3.5 py-1.5 bg-brand-700 text-white font-bold text-[11px] rounded-lg hover:bg-brand-800 transition shadow-2xs">Lihat Detail</a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-10 text-center bg-white rounded-2xl border border-gray-200 text-xs text-gray-400">
                    Belum ada data buku populer.
                </div>
            @endforelse
        </div>

        <!-- TAB 2: BUKU TERBARU -->
        <div x-show="activeTab === 'terbaru'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" x-cloak>
            @forelse($buku_terbaru as $buku)
                @php
                    $available = $buku->jumlah_tersedia;
                    $totalEx = $buku->jumlah_eksemplar;
                @endphp
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-2xs hover:shadow-md hover:border-gray-300 transition duration-300 transform hover:-translate-y-1 flex flex-col justify-between group">
                    <div class="p-5 flex gap-4">
                        <div class="w-22 h-32 bg-gradient-to-br from-brand-700 to-brand-900 border border-brand-800 rounded-xl flex flex-col items-center justify-center shrink-0 text-white font-black text-2xl shadow-sm group-hover:scale-105 transition duration-300">
                            <span>{{ substr($buku->judul, 0, 1) }}</span>
                            <span class="text-[9px] font-normal tracking-widest uppercase text-brand-200 mt-1">SMK BOOK</span>
                        </div>
                        <div class="flex-1 min-w-0 space-y-1.5">
                            <div class="flex items-center justify-between gap-1">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-gray-100 text-gray-700 truncate max-w-[110px]">
                                    {{ $buku->kategori->nama ?? 'Umum' }}
                                </span>
                                @if($available > 0)
                                    <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full shrink-0">Tersedia ({{ $available }})</span>
                                @elseif($totalEx > 0)
                                    <span class="text-[10px] font-extrabold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full shrink-0">Dipinjam</span>
                                @else
                                    <span class="text-[10px] font-extrabold text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-full shrink-0">Habis</span>
                                @endif
                            </div>
                            <h3 class="text-xs font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-brand-700 transition">
                                <a href="{{ route('buku.detail', $buku->id) }}">{{ $buku->judul }}</a>
                            </h3>
                            <p class="text-[11px] text-gray-500">Penulis: <span class="text-gray-800 font-semibold">{{ $buku->penulis->nama ?? '-' }}</span></p>
                            <p class="text-[11px] text-gray-400">Lokasi Rak: <span class="font-mono text-gray-800 font-bold bg-gray-100 px-1.5 py-0.5 rounded">{{ $buku->rak->kode_rak ?? '-' }}</span></p>
                        </div>
                    </div>
                    <div class="px-5 py-3 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between text-xs">
                        <span class="text-[11px] text-gray-400 font-mono">ISBN: {{ $buku->isbn }}</span>
                        <a href="{{ route('buku.detail', $buku->id) }}" class="px-3.5 py-1.5 bg-brand-700 text-white font-bold text-[11px] rounded-lg hover:bg-brand-800 transition shadow-2xs">Lihat Detail</a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-10 text-center bg-white rounded-2xl border border-gray-200 text-xs text-gray-400">
                    Belum ada data koleksi buku terbaru.
                </div>
            @endforelse
        </div>

        <!-- Action Button -->
        <div class="pt-4 text-center">
            <a href="{{ route('katalog') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 text-white font-bold text-xs rounded-xl hover:bg-gray-800 transition duration-200 shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                <span>Lihat Semua Buku di Katalog Sekolah</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

    </div>
</section>

<!-- Section Informasi Operasional Sekolah -->
<section id="tentang-section" class="py-12 bg-white border-t border-gray-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-gray-50 to-red-50/30 border border-gray-200 rounded-2xl p-6 sm:p-8 shadow-2xs">
            <h2 class="text-sm font-extrabold text-gray-900 uppercase tracking-wider mb-4 border-b border-gray-200 pb-3 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-brand-700"></span>
                <span>Informasi & Layanan Operasional Perpustakaan</span>
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-xs">
                <div class="space-y-1">
                    <span class="font-extrabold text-gray-900 block">⏰ Jam Layanan</span>
                    <p class="text-gray-600 leading-relaxed">{{ $jam_operasional }}</p>
                </div>
                <div class="space-y-1">
                    <span class="font-extrabold text-gray-900 block">📅 Hari Operasional</span>
                    <p class="text-gray-600 leading-relaxed">Senin s/d Sabtu (Hari Kerja Sekolah, Minggu & Hari Libur Nasional Tutup)</p>
                </div>
                <div class="space-y-1">
                    <span class="font-extrabold text-gray-900 block">📍 Alamat Lokasi</span>
                    <p class="text-gray-600 leading-relaxed">Gedung Utama Lt. 1, Kompleks Perguruan PGRI, Jl. Pendidikan No. 45.</p>
                </div>
                <div class="space-y-1">
                    <span class="font-extrabold text-gray-900 block">📞 Contact Person</span>
                    <p class="text-gray-600 leading-relaxed">Email: perpustakaan@smkpgri.sch.id<br>WhatsApp: 0812-9876-5432</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
