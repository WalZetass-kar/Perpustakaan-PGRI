@extends('layouts.app')

@section('title', 'Katalog Buku Digital OPAC')

@section('content')
<div class="space-y-6 pb-12" x-data="{ 
    showFilterBar: false,
    openDetailModal: false, 
    viewMode: 'grid',
    modalData: {} 
}">

    <!-- 1. COMPACT HERO SECTION & GLOBAL SEARCH (Centered) -->
    <div class="bg-gradient-to-r from-brand-900 via-brand-700 to-red-800 text-white rounded-3xl p-6 sm:p-8 shadow-xl border-2 border-brand-700 relative overflow-hidden text-center">
        <!-- Subtle Watermark Graphic -->
        <div class="absolute -right-8 -bottom-8 w-56 h-56 bg-white/5 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 space-y-4 max-w-3xl mx-auto flex flex-col items-center">
            <div class="space-y-1.5 flex flex-col items-center">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-400/20 border border-amber-300/30 rounded-full text-amber-300 text-[10px] font-extrabold uppercase tracking-wider backdrop-blur-xs">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>LAYANAN OPAC PERPUSTAKAAN DIGITAL</span>
                </div>
                <h1 class="text-xl sm:text-3xl font-black tracking-tight text-white leading-tight">
                    Katalog Koleksi Buku &amp; Modul Kejuruan
                </h1>
                <p class="text-xs text-red-100 font-medium max-w-xl mx-auto leading-relaxed">
                    Cari literatur modul pembelajaran kejuruan, referensi ujian, dan koleksi umum SMK PGRI Pekanbaru secara instan.
                </p>
            </div>

            <!-- 2. GLOBAL SEARCH BAR (Centered) -->
            <form action="{{ route('katalog') }}" method="GET" class="relative max-w-2xl w-full mx-auto">
                <!-- Keep other active filter query params -->
                @if(request('kategori_id')) <input type="hidden" name="kategori_id" value="{{ request('kategori_id') }}"> @endif
                @if(request('penulis_id')) <input type="hidden" name="penulis_id" value="{{ request('penulis_id') }}"> @endif
                @if(request('rak_id')) <input type="hidden" name="rak_id" value="{{ request('rak_id') }}"> @endif
                @if(request('tahun')) <input type="hidden" name="tahun" value="{{ request('tahun') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                <div class="flex items-center bg-white rounded-2xl p-1.5 shadow-lg border-2 border-amber-300">
                    <div class="pl-3 pr-2 text-gray-400 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul buku, penulis, kata kunci, atau ISBN..."
                        class="w-full px-2 py-2 text-xs sm:text-sm font-bold text-gray-900 placeholder-gray-400 bg-transparent focus:outline-none text-left">
                    
                    @if(request('search'))
                        <a href="{{ route('katalog', request()->except('search')) }}" class="px-2 text-gray-400 hover:text-gray-600 font-bold text-sm" title="Clear search">&times;</a>
                    @endif

                    <button type="submit" class="px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-md hover:shadow-lg shrink-0">
                        Cari Buku
                    </button>
                </div>
            </form>

            <!-- Real Database Statistics Bar (Centered) -->
            <div class="pt-1 flex flex-wrap items-center justify-center gap-3 text-xs font-bold text-red-100">
                <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-xl border border-white/10 text-[11px]">
                    <span class="text-amber-300 font-black">{{ $total_buku_count }}</span> Judul Ditemukan
                </div>
                <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-xl border border-white/10 text-[11px]">
                    <span class="text-amber-300 font-black">{{ $total_kategori_count }}</span> Kategori
                </div>
                <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-xl border border-white/10 text-[11px]">
                    <span class="text-amber-300 font-black">{{ $total_rak_count }}</span> Rak Lokasi
                </div>
            </div>
        </div>
    </div>

    <!-- UPPER TOOLBAR (FILTER TOGGLE, ACTIVE COUNT, RESULT COUNT, VIEW SWITCHER & SORTING BAR) -->
    <div class="bg-white p-4 rounded-2xl border-2 border-gray-200 shadow-2xs flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
        
        <!-- Left Side: Collapsible Filter Button & Result Count -->
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            @php
                $activeFilterCount = 0;
                if(request('kategori_id')) $activeFilterCount++;
                if(request('penulis_id')) $activeFilterCount++;
                if(request('rak_id')) $activeFilterCount++;
                if(request('tahun')) $activeFilterCount++;
                if(request('status')) $activeFilterCount++;
            @endphp

            <!-- Collapsible Dropdown Filter Toggle Button -->
            <button @click="showFilterBar = !showFilterBar" 
                    :class="showFilterBar || {{ $activeFilterCount }} > 0 ? 'bg-brand-700 text-white border-brand-700 shadow-sm' : 'bg-gray-50 text-gray-800 border-gray-200 hover:bg-gray-100'"
                    class="px-4 py-2 rounded-xl font-extrabold text-xs border-2 transition flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span>Filter Katalog</span>
                @if($activeFilterCount > 0)
                    <span class="px-2 py-0.5 rounded-full bg-amber-400 text-brand-950 font-black text-[10px]">{{ $activeFilterCount }}</span>
                @endif
                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showFilterBar ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <!-- Result Count Text -->
            <div>
                @if(request('search'))
                    <h3 class="font-extrabold text-gray-900 text-xs">Pencarian "<span class="text-brand-700">{{ request('search') }}</span>" — {{ $buku->total() }} buku</h3>
                @else
                    <h3 class="font-extrabold text-gray-900 text-xs">{{ $buku->total() }} Buku Ditemukan</h3>
                @endif
            </div>
        </div>

        <!-- Right Side: Grid/List View Switcher & Sorting Dropdown -->
        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <!-- Grid / List Switcher -->
            <div class="flex items-center bg-gray-100 p-1 rounded-xl border border-gray-200 shrink-0">
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white text-brand-700 shadow-2xs font-extrabold' : 'text-gray-500 font-medium'" class="px-2.5 py-1 rounded-lg transition text-[11px] flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Grid</span>
                </button>
                <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white text-brand-700 shadow-2xs font-extrabold' : 'text-gray-500 font-medium'" class="px-2.5 py-1 rounded-lg transition text-[11px] flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <span>List</span>
                </button>
            </div>

            <!-- Sorting Dropdown -->
            <form action="{{ route('katalog') }}" method="GET" class="inline flex items-center gap-1.5">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                @if(request('kategori_id')) <input type="hidden" name="kategori_id" value="{{ request('kategori_id') }}"> @endif
                @if(request('penulis_id')) <input type="hidden" name="penulis_id" value="{{ request('penulis_id') }}"> @endif
                @if(request('rak_id')) <input type="hidden" name="rak_id" value="{{ request('rak_id') }}"> @endif
                @if(request('tahun')) <input type="hidden" name="tahun" value="{{ request('tahun') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif

                <label class="text-[11px] font-bold text-gray-500 shrink-0">Urutkan:</label>
                <select name="sort" onchange="this.form.submit()" class="px-3 py-1.5 bg-gray-50 border-2 border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand-700">
                    <option value="terbaru" {{ request('sort', 'terbaru') == 'terbaru' ? 'selected' : '' }}>Buku Terbaru</option>
                    <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Buku Terlama</option>
                    <option value="judul_asc" {{ request('sort') == 'judul_asc' ? 'selected' : '' }}>Judul A–Z</option>
                    <option value="judul_desc" {{ request('sort') == 'judul_desc' ? 'selected' : '' }}>Judul Z–A</option>
                    <option value="populer" {{ request('sort') == 'populer' ? 'selected' : '' }}>Paling Populer</option>
                </select>
            </form>
        </div>
    </div>

    <!-- COLLAPSIBLE HORIZONTAL DROPDOWN FILTER PANEL -->
    <div x-show="showFilterBar" 
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-md text-xs space-y-4"
         x-cloak>
        <form action="{{ route('katalog') }}" method="GET">
            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block font-extrabold text-gray-700 mb-1">Kategori</label>
                    <select name="kategori_id" class="w-full px-3 py-2 bg-gray-50 border-2 border-gray-200 rounded-xl font-bold text-gray-800 focus:ring-2 focus:ring-brand-700">
                        <option value="">Semua Kategori</option>
                        @foreach($kategori_list as $kat)
                            <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-extrabold text-gray-700 mb-1">Penulis</label>
                    <select name="penulis_id" class="w-full px-3 py-2 bg-gray-50 border-2 border-gray-200 rounded-xl font-medium text-gray-800 focus:ring-2 focus:ring-brand-700">
                        <option value="">Semua Penulis</option>
                        @foreach($penulis_list as $pen)
                            <option value="{{ $pen->id }}" {{ request('penulis_id') == $pen->id ? 'selected' : '' }}>{{ $pen->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-extrabold text-gray-700 mb-1">Lokasi Rak</label>
                    <select name="rak_id" class="w-full px-3 py-2 bg-gray-50 border-2 border-gray-200 rounded-xl font-medium text-gray-800 focus:ring-2 focus:ring-brand-700">
                        <option value="">Semua Lokasi Rak</option>
                        @foreach($rak_list as $rk)
                            <option value="{{ $rk->id }}" {{ request('rak_id') == $rk->id ? 'selected' : '' }}>{{ $rk->kode_rak }} ({{ $rk->nama_rak }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-extrabold text-gray-700 mb-1">Tahun Terbit</label>
                    <select name="tahun" class="w-full px-3 py-2 bg-gray-50 border-2 border-gray-200 rounded-xl font-medium text-gray-800 focus:ring-2 focus:ring-brand-700">
                        <option value="">Semua Tahun</option>
                        @foreach($tahun_list as $th)
                            <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>Tahun {{ $th }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-extrabold text-gray-700 mb-1">Status Ketersediaan</label>
                    <select name="status" class="w-full px-3 py-2 bg-gray-50 border-2 border-gray-200 rounded-xl font-bold text-gray-800 focus:ring-2 focus:ring-brand-700">
                        <option value="">Semua Status</option>
                        <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia Dipinjam</option>
                        <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-2">
                <a href="{{ route('katalog', request()->only('search', 'sort')) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                    Reset Filter
                </a>
                <button type="submit" class="px-6 py-2 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-sm">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- ACTIVE FILTERS CHIPS -->
    @if(request()->anyFilled(['search', 'kategori_id', 'penulis_id', 'rak_id', 'tahun', 'status']))
        <div class="bg-gray-50 p-3 rounded-2xl border border-gray-200 flex flex-wrap items-center gap-2 text-xs">
            <span class="text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Filter Aktif:</span>

            @if(request('search'))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white border border-gray-300 text-gray-800 font-bold text-xs shadow-2xs">
                    <span>Pencarian: "{{ request('search') }}"</span>
                    <a href="{{ route('katalog', request()->except('search')) }}" class="text-gray-400 hover:text-rose-600 font-bold">&times;</a>
                </span>
            @endif

            @if(request('kategori_id'))
                @php $katActive = $kategori_list->firstWhere('id', request('kategori_id')); @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white border border-gray-300 text-gray-800 font-bold text-xs shadow-2xs">
                    <span>Kategori: {{ $katActive->nama ?? request('kategori_id') }}</span>
                    <a href="{{ route('katalog', request()->except('kategori_id')) }}" class="text-gray-400 hover:text-rose-600 font-bold">&times;</a>
                </span>
            @endif

            @if(request('penulis_id'))
                @php $penActive = $penulis_list->firstWhere('id', request('penulis_id')); @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white border border-gray-300 text-gray-800 font-bold text-xs shadow-2xs">
                    <span>Penulis: {{ $penActive->nama ?? request('penulis_id') }}</span>
                    <a href="{{ route('katalog', request()->except('penulis_id')) }}" class="text-gray-400 hover:text-rose-600 font-bold">&times;</a>
                </span>
            @endif

            @if(request('rak_id'))
                @php $rakActive = $rak_list->firstWhere('id', request('rak_id')); @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white border border-gray-300 text-gray-800 font-bold text-xs shadow-2xs">
                    <span>Rak: {{ $rakActive->kode_rak ?? request('rak_id') }}</span>
                    <a href="{{ route('katalog', request()->except('rak_id')) }}" class="text-gray-400 hover:text-rose-600 font-bold">&times;</a>
                </span>
            @endif

            @if(request('tahun'))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white border border-gray-300 text-gray-800 font-bold text-xs shadow-2xs">
                    <span>Tahun: {{ request('tahun') }}</span>
                    <a href="{{ route('katalog', request()->except('tahun')) }}" class="text-gray-400 hover:text-rose-600 font-bold">&times;</a>
                </span>
            @endif

            @if(request('status'))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white border border-gray-300 text-gray-800 font-bold text-xs shadow-2xs">
                    <span>Status: {{ request('status') === 'tersedia' ? 'Tersedia' : 'Sedang Dipinjam' }}</span>
                    <a href="{{ route('katalog', request()->except('status')) }}" class="text-gray-400 hover:text-rose-600 font-bold">&times;</a>
                </span>
            @endif

            <a href="{{ route('katalog') }}" class="px-3 py-1 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 font-extrabold text-xs hover:bg-rose-100 transition ml-auto">
                Hapus Semua
            </a>
        </div>
    @endif

    <!-- FULL WIDTH CATALOG SHOWCASE AREA -->
    <div class="space-y-5" x-data="{ isLoading: true }" x-init="setTimeout(() => isLoading = false, 200)">
        
        <!-- LOADING SKELETON STATE -->
        <div x-show="isLoading" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 animate-pulse" x-cloak>
            @for($i=0; $i<8; $i++)
                <div class="bg-white rounded-2xl border-2 border-gray-200 p-4 space-y-4">
                    <div class="w-full h-56 bg-gray-200 rounded-xl"></div>
                    <div class="space-y-2">
                        <div class="h-3 w-16 bg-gray-200 rounded-lg"></div>
                        <div class="h-4 w-full bg-gray-300 rounded-lg"></div>
                        <div class="h-3 w-28 bg-gray-100 rounded-lg"></div>
                    </div>
                </div>
            @endfor
        </div>

        <!-- BOOK SHOWCASE GRID & LIST VIEWS (Full 4-Column Layout) -->
        <div x-show="!isLoading" x-transition.opacity.duration.300ms>
            
            <!-- GRID VIEW MODE (4 Columns on Desktop for Maximum Space!) -->
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($buku as $item)
                    @php
                        $available = $item->jumlah_tersedia;
                        $totalEx = $item->jumlah_eksemplar;
                        $coverUrl = $item->cover ? asset('storage/' . $item->cover) : null;
                    @endphp

                    <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden shadow-2xs hover:shadow-lg hover:border-brand-700 transition duration-300 flex flex-col justify-between group">
                        
                        <div>
                            <!-- Aspect 2:3 Cover Box -->
                            <div class="relative w-full h-60 bg-gray-100 overflow-hidden flex items-center justify-center border-b-2 border-gray-100">
                                @if($coverUrl)
                                    <img src="{{ $coverUrl }}" alt="Cover {{ $item->judul }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="w-full h-full bg-gray-100 p-4 flex flex-col items-center justify-center text-center space-y-2">
                                        <div class="w-12 h-12 rounded-2xl bg-brand-700 text-white flex items-center justify-center font-black text-lg shadow-sm">
                                            {{ substr($item->judul, 0, 1) }}
                                        </div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Cover Tidak Tersedia</span>
                                    </div>
                                @endif

                                <!-- Status Availability Badge Overlay -->
                                <div class="absolute top-2.5 right-2.5 z-10">
                                    @if($available > 0)
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-emerald-600 text-white shadow-xs uppercase tracking-wide flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                                            <span>Tersedia ({{ $available }})</span>
                                        </span>
                                    @elseif($totalEx > 0)
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-blue-600 text-white shadow-xs uppercase tracking-wide flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-300"></span>
                                            <span>Dipinjam</span>
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-rose-600 text-white shadow-xs uppercase tracking-wide flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-300"></span>
                                            <span>Stok Habis</span>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Metadata Info -->
                            <div class="p-4 space-y-2.5">
                                <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold bg-gray-100 text-gray-700 border border-gray-200 uppercase inline-block">
                                    {{ $item->kategori->nama ?? 'Umum' }}
                                </span>

                                <h3 class="text-xs font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-brand-700 transition">
                                    <button @click="modalData = {
                                        id: {{ $item->id }},
                                        judul: '{{ addslashes($item->judul) }}',
                                        penulis: '{{ addslashes($item->penulis->nama ?? '-') }}',
                                        penerbit: '{{ addslashes($item->penerbit->nama ?? '-') }}',
                                        tahun: '{{ $item->tahun_terbit }}',
                                        isbn: '{{ $item->isbn }}',
                                        kategori: '{{ addslashes($item->kategori->nama ?? 'Umum') }}',
                                        rak: '{{ addslashes(($item->rak->kode_rak ?? '') . ' - ' . ($item->rak->nama_rak ?? '')) }}',
                                        sinopsis: '{{ addslashes($item->sinopsis ?? 'Modul pembelajaran resmi SMK PGRI Pekanbaru.') }}',
                                        tersedia: {{ $item->jumlah_tersedia }},
                                        total: {{ $item->jumlah_eksemplar }},
                                        cover: '{{ $coverUrl ?? '' }}'
                                    }; openDetailModal = true" class="text-left hover:underline">
                                        {{ $item->judul }}
                                    </button>
                                </h3>

                                <div class="space-y-1 text-[11px] text-gray-600 font-medium border-t border-gray-100 pt-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400">Penulis:</span>
                                        <span class="font-bold text-gray-900 truncate max-w-[120px]">{{ $item->penulis->nama ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400">Lokasi Rak:</span>
                                        <span class="font-mono font-bold text-brand-700 bg-brand-50 px-1.5 py-0.5 rounded border border-brand-200 text-[10px]">{{ $item->rak->kode_rak ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Bottom Bar -->
                        <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs">
                            <span class="text-[10px] text-gray-500 font-medium">{{ $available }} / {{ $totalEx }} eksemplar</span>
                            <button @click="modalData = {
                                id: {{ $item->id }},
                                judul: '{{ addslashes($item->judul) }}',
                                penulis: '{{ addslashes($item->penulis->nama ?? '-') }}',
                                penerbit: '{{ addslashes($item->penerbit->nama ?? '-') }}',
                                tahun: '{{ $item->tahun_terbit }}',
                                isbn: '{{ $item->isbn }}',
                                kategori: '{{ addslashes($item->kategori->nama ?? 'Umum') }}',
                                rak: '{{ addslashes(($item->rak->kode_rak ?? '') . ' - ' . ($item->rak->nama_rak ?? '')) }}',
                                sinopsis: '{{ addslashes($item->sinopsis ?? 'Modul pembelajaran resmi SMK PGRI Pekanbaru.') }}',
                                tersedia: {{ $item->jumlah_tersedia }},
                                total: {{ $item->jumlah_eksemplar }},
                                cover: '{{ $coverUrl ?? '' }}'
                            }; openDetailModal = true" class="px-3 py-1.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-[11px] rounded-lg transition shadow-2xs">
                                Lihat Detail
                            </button>
                        </div>

                    </div>
                @empty
                    <!-- EMPTY STATE -->
                    <div class="col-span-full py-16 px-6 text-center bg-white rounded-2xl border-2 border-gray-200 space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto border border-gray-200 font-bold text-lg">
                            ?
                        </div>
                        <div>
                            <h3 class="text-base font-black text-gray-900">Tidak Ada Buku Ditemukan</h3>
                            <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">Belum ada koleksi yang sesuai dengan pencarian atau filter yang dipilih.</p>
                        </div>
                        <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                            <a href="{{ route('katalog') }}" class="px-4 py-2 bg-brand-700 text-white font-extrabold text-xs rounded-xl hover:bg-brand-800 transition shadow-sm">
                                Hapus Semua Filter
                            </a>
                            @if(request('search'))
                                <a href="{{ route('katalog', request()->except('search')) }}" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold text-xs rounded-xl hover:bg-gray-200 transition">
                                    Ubah Kata Kunci
                                </a>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- LIST VIEW MODE -->
            <div x-show="viewMode === 'list'" class="space-y-4">
                @forelse($buku as $item)
                    @php
                        $available = $item->jumlah_tersedia;
                        $totalEx = $item->jumlah_eksemplar;
                        $coverUrl = $item->cover ? asset('storage/' . $item->cover) : null;
                    @endphp

                    <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden p-4 shadow-2xs hover:border-brand-700 transition duration-300 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <div class="w-20 h-28 bg-gray-100 border border-gray-200 rounded-xl overflow-hidden shrink-0">
                                @if($coverUrl)
                                    <img src="{{ $coverUrl }}" alt="Cover {{ $item->judul }}" loading="lazy" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center bg-brand-700 text-white font-black text-lg p-2 text-center">
                                        {{ substr($item->judul, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-1 text-xs">
                                <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold bg-gray-100 text-gray-700 border border-gray-200 uppercase inline-block">
                                    {{ $item->kategori->nama ?? 'Umum' }}
                                </span>
                                <h3 class="text-sm font-bold text-gray-900 leading-snug">
                                    <button @click="modalData = {
                                        id: {{ $item->id }},
                                        judul: '{{ addslashes($item->judul) }}',
                                        penulis: '{{ addslashes($item->penulis->nama ?? '-') }}',
                                        penerbit: '{{ addslashes($item->penerbit->nama ?? '-') }}',
                                        tahun: '{{ $item->tahun_terbit }}',
                                        isbn: '{{ $item->isbn }}',
                                        kategori: '{{ addslashes($item->kategori->nama ?? 'Umum') }}',
                                        rak: '{{ addslashes(($item->rak->kode_rak ?? '') . ' - ' . ($item->rak->nama_rak ?? '')) }}',
                                        sinopsis: '{{ addslashes($item->sinopsis ?? 'Modul pembelajaran resmi SMK PGRI Pekanbaru.') }}',
                                        tersedia: {{ $item->jumlah_tersedia }},
                                        total: {{ $item->jumlah_eksemplar }},
                                        cover: '{{ $coverUrl ?? '' }}'
                                    }; openDetailModal = true" class="text-left hover:text-brand-700 hover:underline">
                                        {{ $item->judul }}
                                    </button>
                                </h3>
                                <p class="text-[11px] text-gray-500">Penulis: <strong class="text-gray-900">{{ $item->penulis->nama ?? '-' }}</strong> | Rak: <strong class="font-mono text-brand-700">{{ $item->rak->kode_rak ?? '-' }}</strong></p>
                                <p class="text-[10px] text-gray-400 font-mono">ISBN: {{ $item->isbn }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 shrink-0 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-100">
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-gray-400 block">Stok Eksemplar:</span>
                                <span class="text-xs font-black text-emerald-600">{{ $available }} tersedia / {{ $totalEx }} total</span>
                            </div>
                            <button @click="modalData = {
                                id: {{ $item->id }},
                                judul: '{{ addslashes($item->judul) }}',
                                penulis: '{{ addslashes($item->penulis->nama ?? '-') }}',
                                penerbit: '{{ addslashes($item->penerbit->nama ?? '-') }}',
                                tahun: '{{ $item->tahun_terbit }}',
                                isbn: '{{ $item->isbn }}',
                                kategori: '{{ addslashes($item->kategori->nama ?? 'Umum') }}',
                                rak: '{{ addslashes(($item->rak->kode_rak ?? '') . ' - ' . ($item->rak->nama_rak ?? '')) }}',
                                sinopsis: '{{ addslashes($item->sinopsis ?? 'Modul pembelajaran resmi SMK PGRI Pekanbaru.') }}',
                                tersedia: {{ $item->jumlah_tersedia }},
                                total: {{ $item->jumlah_eksemplar }},
                                cover: '{{ $coverUrl ?? '' }}'
                            }; openDetailModal = true" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-2xs">
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center bg-white rounded-2xl border-2 border-gray-200">
                        <p class="text-xs text-gray-500 font-bold">Tidak ada buku ditemukan.</p>
                    </div>
                @endforelse
            </div>

        </div>

        <!-- SERVER-SIDE PAGINATION -->
        @if($buku->hasPages())
            <div class="mt-8 pt-4 border-t border-gray-100">
                {{ $buku->links() }}
            </div>
        @endif

    </div>

    <!-- QUICK BOOK DETAIL MODAL -->
    <div x-show="openDetailModal" @click.self="openDetailModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-xs p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border-2 border-gray-200 transform transition-all my-8 relative">
            <button @click="openDetailModal = false" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-900 flex items-center justify-center transition font-bold">&times;</button>

            <div class="flex flex-col sm:flex-row gap-6">
                <!-- Cover Image Frame -->
                <div class="w-full sm:w-48 h-64 bg-gray-900 rounded-2xl overflow-hidden shrink-0 border-2 border-gray-200 shadow-md relative">
                    <template x-if="modalData.cover">
                        <img :src="modalData.cover" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!modalData.cover">
                        <div class="w-full h-full bg-gradient-to-br from-brand-900 via-brand-700 to-red-950 p-4 flex flex-col justify-between text-white">
                            <span class="px-2 py-0.5 bg-amber-400 text-brand-950 text-[9px] font-black uppercase rounded self-start" x-text="modalData.kategori"></span>
                            <div>
                                <h4 class="text-xs font-black text-white" x-text="modalData.judul"></h4>
                                <p class="text-[9px] text-red-200 font-bold mt-1">SMK PGRI Pekanbaru</p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Info Details Column -->
                <div class="flex-1 space-y-4 text-xs">
                    <div class="space-y-1">
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-brand-50 text-brand-700 border border-brand-200 uppercase inline-block" x-text="modalData.kategori"></span>
                        <h2 class="text-lg font-black text-gray-900 leading-snug" x-text="modalData.judul"></h2>
                    </div>

                    <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3 rounded-2xl border border-gray-200">
                        <div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase block">Penulis</span>
                            <span class="font-extrabold text-gray-900" x-text="modalData.penulis"></span>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase block">Penerbit &amp; Tahun</span>
                            <span class="font-bold text-gray-900" x-text="modalData.penerbit + ' (' + modalData.tahun + ')'"></span>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase block">Kode Rak</span>
                            <span class="font-mono font-bold text-brand-700" x-text="modalData.rak"></span>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase block">ISBN</span>
                            <span class="font-mono text-gray-800" x-text="modalData.isbn"></span>
                        </div>
                    </div>

                    <div>
                        <span class="text-[9px] font-bold text-gray-400 uppercase block mb-1">Deskripsi / Ringkasan Modul</span>
                        <p class="text-gray-600 font-medium text-xs leading-relaxed" x-text="modalData.sinopsis"></p>
                    </div>

                    <div class="pt-4 border-t-2 border-gray-100 flex items-center justify-between gap-4">
                        <div>
                            <span class="text-[10px] font-extrabold text-gray-500 block">Status Ketersediaan:</span>
                            <span class="text-xs font-black text-emerald-600" x-text="modalData.tersedia + ' Tersedia / ' + modalData.total + ' Eksemplar Total'"></span>
                        </div>
                        @auth
                            <form :action="'{{ url('/mahasiswa/reservasi/buat') }}/' + modalData.id" method="POST" class="inline" onsubmit="return confirmAction(event, 'Booking Buku Ini?', 'Konfirmasi pengajuan booking online untuk buku ini.', 'Ya, Booking Sekarang!')">
                                @csrf
                                <button type="submit" class="px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-md flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Booking / Reservasi Buku Ini</span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-md flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                <span>Login Siswa untuk Booking Buku</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
