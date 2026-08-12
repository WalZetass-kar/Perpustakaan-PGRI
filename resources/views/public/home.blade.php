@extends('layouts.app')

@section('title', 'Beranda - ' . $nama_perpustakaan)

@section('content')
<!-- Hero Section (Centered & Focused Hierarchy) -->
<section class="bg-white border-b border-gray-200 py-10 lg:py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <!-- Headline & Subheading Centered -->
        <div class="mb-6 space-y-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-brand-50 text-brand-700 border border-brand-100">
                Pusat Informasi & Literasi Sekolah
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                Cari & Temukan Koleksi Buku Perpustakaan SMK PGRI
            </h1>
            <p class="text-xs sm:text-sm text-gray-600 max-w-2xl mx-auto leading-relaxed">
                Akses cepat modul kejuruan, literatur umum, dan referensi akademik secara otomatis dan terstruktur.
            </p>
        </div>

        <!-- Primary Search Bar & Surrounding Filters Centered -->
        <form action="{{ route('katalog') }}" method="GET" class="space-y-3 bg-gray-50/90 p-4 rounded-2xl border border-gray-200 shadow-xs max-w-3xl mx-auto">
            <!-- Search Input Row -->
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul buku, penulis, ISBN, atau kategori..." 
                        class="w-full pl-10 pr-4 py-3 bg-white border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-700 transition shadow-2xs">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-6 py-3 bg-brand-700 text-white font-bold text-xs rounded-xl hover:bg-brand-800 transition shadow-xs shrink-0 flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cari Koleksi</span>
                </button>
            </div>

            <!-- Surrounding Simple Filter Bar -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs pt-2 border-t border-gray-200/70">
                <select name="kategori_id" class="px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-[11px] focus:ring-1 focus:ring-brand-700 text-gray-700">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori_list as $kat)
                        <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                    @endforeach
                </select>

                <select name="penulis_id" class="px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-[11px] focus:ring-1 focus:ring-brand-700 text-gray-700">
                    <option value="">Semua Penulis</option>
                    @foreach($penulis_list as $pen)
                        <option value="{{ $pen->id }}">{{ $pen->nama }}</option>
                    @endforeach
                </select>

                <select name="tahun" class="px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-[11px] focus:ring-1 focus:ring-brand-700 text-gray-700">
                    <option value="">Semua Tahun</option>
                    @foreach($tahun_list as $th)
                        <option value="{{ $th }}">{{ $th }}</option>
                    @endforeach
                </select>

                <select name="status" class="px-2.5 py-1.5 bg-white border border-gray-300 rounded-lg text-[11px] focus:ring-1 focus:ring-brand-700 text-gray-700">
                    <option value="">Semua Status</option>
                    <option value="tersedia">Tersedia Dipinjam</option>
                    <option value="dipinjam">Sedang Dipinjam</option>
                </select>
            </div>
        </form>
    </div>
</section>

<!-- Relevant Stats Bar (Thin border, light shadow, clean) -->
<section class="py-6 bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs text-center sm:text-left">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Total Koleksi</span>
                <span class="text-xl sm:text-2xl font-bold text-gray-900 mt-0.5 block">{{ number_format($stats['total_koleksi']) }} Judul</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs text-center sm:text-left">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Buku Tersedia</span>
                <span class="text-xl sm:text-2xl font-bold text-emerald-700 mt-0.5 block">{{ number_format($stats['buku_tersedia']) }} Eksemplar</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs text-center sm:text-left">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Sedang Dipinjam</span>
                <span class="text-xl sm:text-2xl font-bold text-blue-700 mt-0.5 block">{{ number_format($stats['sedang_dipinjam']) }} Eksemplar</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs text-center sm:text-left">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Anggota Aktif</span>
                <span class="text-xl sm:text-2xl font-bold text-gray-900 mt-0.5 block">{{ number_format($stats['anggota_aktif']) }} Siswa</span>
            </div>
        </div>
    </div>
</section>

<!-- Section Jelajahi Berdasarkan Kategori -->
<section id="kategori-section" class="py-8 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-gray-900">Jelajahi Berdasarkan Kategori</h2>
                <p class="text-xs text-gray-500">Pilih bidang keahlian atau jenis literatur kejuruan</p>
            </div>
        </div>

        <!-- Compact Category Cards / Pills Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            @foreach($kategori_list as $kat)
                <a href="{{ route('katalog', ['kategori_id' => $kat->id]) }}" 
                   class="bg-gray-50 hover:bg-brand-50 border border-gray-200 hover:border-brand-200 rounded-lg p-3 text-center transition group">
                    <span class="text-xs font-bold text-gray-800 group-hover:text-brand-700 block truncate">{{ $kat->nama }}</span>
                    <span class="text-[10px] text-gray-500 group-hover:text-brand-600 mt-0.5 block">{{ $kat->buku()->count() }} Koleksi</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Section Catalog Showcase (Tabs: Buku Populer | Buku Terbaru) -->
<section class="py-10" x-data="{ activeTab: 'populer' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header & Simple Tabs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 pb-3">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Katalog Rekomendasi Koleksi</h2>
                <p class="text-xs text-gray-500">Buku referensi terpilih untuk mendukung pembelajaran siswa</p>
            </div>

            <!-- Tab Switcher -->
            <div class="flex items-center gap-2 bg-gray-100 p-1 rounded-lg self-start sm:self-auto">
                <button @click="activeTab = 'populer'" 
                    :class="activeTab === 'populer' ? 'bg-white font-bold text-brand-700 shadow-2xs' : 'text-gray-600 font-medium hover:text-gray-900'"
                    class="px-3 py-1.5 text-xs rounded-md transition">
                    Buku Populer
                </button>
                <button @click="activeTab = 'terbaru'" 
                    :class="activeTab === 'terbaru' ? 'bg-white font-bold text-brand-700 shadow-2xs' : 'text-gray-600 font-medium hover:text-gray-900'"
                    class="px-3 py-1.5 text-xs rounded-md transition">
                    Buku Terbaru
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
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-2xs flex flex-col justify-between hover:border-gray-300 transition">
                    <div class="p-4 flex gap-4">
                        <div class="w-20 h-28 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center shrink-0 text-brand-700 font-extrabold text-xl shadow-2xs">
                            {{ substr($buku->judul, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0 space-y-1">
                            <div class="flex items-center justify-between gap-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700 truncate max-w-[120px]">
                                    {{ $buku->kategori->nama ?? 'Umum' }}
                                </span>
                                @if($available > 0)
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-1.5 py-0.5 rounded shrink-0">Tersedia ({{ $available }})</span>
                                @elseif($totalEx > 0)
                                    <span class="text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-100 px-1.5 py-0.5 rounded shrink-0">Sedang Dipinjam</span>
                                @else
                                    <span class="text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-100 px-1.5 py-0.5 rounded shrink-0">Tidak Tersedia</span>
                                @endif
                            </div>
                            <h3 class="text-xs font-bold text-gray-900 line-clamp-2 leading-snug">
                                <a href="{{ route('buku.detail', $buku->id) }}" class="hover:text-brand-700 transition">{{ $buku->judul }}</a>
                            </h3>
                            <p class="text-[11px] text-gray-500">Penulis: <span class="text-gray-800 font-medium">{{ $buku->penulis->nama ?? '-' }}</span></p>
                            <p class="text-[11px] text-gray-400">Rak: <span class="font-mono text-gray-700 font-bold">{{ $buku->rak->kode_rak ?? '-' }}</span></p>
                        </div>
                    </div>
                    <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs">
                        <span class="text-[11px] text-gray-400 font-mono">ISBN: {{ $buku->isbn }}</span>
                        <a href="{{ route('buku.detail', $buku->id) }}" class="px-3 py-1 bg-brand-700 text-white font-bold text-[11px] rounded hover:bg-brand-800 transition">Lihat Detail</a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center bg-white rounded-xl border border-gray-200 text-xs text-gray-400">
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
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-2xs flex flex-col justify-between hover:border-gray-300 transition">
                    <div class="p-4 flex gap-4">
                        <div class="w-20 h-28 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center shrink-0 text-brand-700 font-extrabold text-xl shadow-2xs">
                            {{ substr($buku->judul, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0 space-y-1">
                            <div class="flex items-center justify-between gap-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700 truncate max-w-[120px]">
                                    {{ $buku->kategori->nama ?? 'Umum' }}
                                </span>
                                @if($available > 0)
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-1.5 py-0.5 rounded shrink-0">Tersedia ({{ $available }})</span>
                                @elseif($totalEx > 0)
                                    <span class="text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-100 px-1.5 py-0.5 rounded shrink-0">Sedang Dipinjam</span>
                                @else
                                    <span class="text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-100 px-1.5 py-0.5 rounded shrink-0">Tidak Tersedia</span>
                                @endif
                            </div>
                            <h3 class="text-xs font-bold text-gray-900 line-clamp-2 leading-snug">
                                <a href="{{ route('buku.detail', $buku->id) }}" class="hover:text-brand-700 transition">{{ $buku->judul }}</a>
                            </h3>
                            <p class="text-[11px] text-gray-500">Penulis: <span class="text-gray-800 font-medium">{{ $buku->penulis->nama ?? '-' }}</span></p>
                            <p class="text-[11px] text-gray-400">Rak: <span class="font-mono text-gray-700 font-bold">{{ $buku->rak->kode_rak ?? '-' }}</span></p>
                        </div>
                    </div>
                    <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs">
                        <span class="text-[11px] text-gray-400 font-mono">ISBN: {{ $buku->isbn }}</span>
                        <a href="{{ route('buku.detail', $buku->id) }}" class="px-3 py-1 bg-brand-700 text-white font-bold text-[11px] rounded hover:bg-brand-800 transition">Lihat Detail</a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center bg-white rounded-xl border border-gray-200 text-xs text-gray-400">
                    Belum ada data koleksi buku terbaru.
                </div>
            @endforelse
        </div>

        <!-- Action Button: Lihat Semua Buku -->
        <div class="pt-4 text-center">
            <a href="{{ route('katalog') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white font-bold text-xs rounded-lg hover:bg-gray-800 transition shadow-2xs">
                <span>Lihat Semua Buku di Katalog</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

    </div>
</section>

<!-- Section Informasi Perpustakaan & Jam Operasional (Horizontal Layout) -->
<section id="tentang-section" class="py-10 bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 shadow-2xs">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Informasi & Layanan Operasional Sekolah</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-xs">
                <div>
                    <span class="font-bold text-gray-800 block mb-1">⏰ Jam Layanan</span>
                    <p class="text-gray-600 leading-relaxed">{{ $jam_operasional }}</p>
                </div>
                <div>
                    <span class="font-bold text-gray-800 block mb-1">📅 Hari Operasional</span>
                    <p class="text-gray-600 leading-relaxed">Senin s/d Sabtu (Hari Kerja Sekolah, Minggu & Hari Libur Nasional Tutup)</p>
                </div>
                <div>
                    <span class="font-bold text-gray-800 block mb-1">📍 Alamat Lokasi</span>
                    <p class="text-gray-600 leading-relaxed">Gedung Utama Lt. 1, Kompleks Perguruan PGRI, Jl. Pendidikan No. 45.</p>
                </div>
                <div>
                    <span class="font-bold text-gray-800 block mb-1">📞 Contact Person</span>
                    <p class="text-gray-600 leading-relaxed">Email: perpustakaan@smkpgri.sch.id<br>WhatsApp: 0812-9876-5432</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
