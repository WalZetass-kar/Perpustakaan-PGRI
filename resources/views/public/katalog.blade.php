@extends('layouts.app')

@section('title', 'Katalog OPAC - Perpustakaan SMK PGRI Pekanbaru')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" x-data="katalogPage()">

    <div class="relative bg-gradient-to-br from-brand-800 via-brand-700 to-red-900 text-white rounded-3xl p-6 sm:p-10 shadow-xl border border-brand-600/40 z-20">
        <div class="relative z-10 text-center space-y-4 max-w-3xl mx-auto">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-white text-[10px] font-black tracking-widest border border-white/20 uppercase">
                    <i class="fa-solid fa-book-bookmark text-white"></i>
                    <span>LAYANAN OPAC PERPUSTAKAAN DIGITAL</span>
                </div>
                <h1 class="text-xl sm:text-3xl font-black tracking-tight text-white leading-tight">
                    Katalog Koleksi Buku &amp; Modul Pembelajaran
                </h1>
                <p class="text-xs text-white/90 font-medium max-w-xl mx-auto leading-relaxed">
                    Cari literatur modul kejuruan, referensi umum, dan ajukan peminjaman buku secara mandiri
                </p>
            </div>

            <form action="{{ route('katalog') }}" method="GET" class="relative max-w-2xl w-full mx-auto" @click.outside="showSuggest = false">
                @if(request('kategori_id')) <input type="hidden" name="kategori_id" value="{{ request('kategori_id') }}"> @endif
                @if(request('penulis_id')) <input type="hidden" name="penulis_id" value="{{ request('penulis_id') }}"> @endif
                @if(request('rak_id')) <input type="hidden" name="rak_id" value="{{ request('rak_id') }}"> @endif
                @if(request('tahun')) <input type="hidden" name="tahun" value="{{ request('tahun') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                <div class="flex items-center bg-white rounded-2xl p-1.5 shadow-lg border border-brand-900/10 relative">
                    <div class="pl-3 pr-2 text-gray-400 shrink-0">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" name="search" x-model="searchQuery" @input.debounce.200ms="fetchSuggestions()" @focus="if(searchQuery.trim().length >= 2) showSuggest = true" autocomplete="off" placeholder="Cari judul buku, penulis, kata kunci, atau ISBN..."
                        class="w-full px-2 py-2 text-xs sm:text-sm font-bold text-gray-900 placeholder-gray-400 bg-transparent focus:outline-none text-left">

                    <div x-show="loadingSuggest" class="pr-2" x-cloak>
                        <i class="fa-solid fa-spinner fa-spin text-brand-700 text-sm"></i>
                    </div>

                    @if(request('search'))
                        <a href="{{ route('katalog', request()->except('search')) }}" class="px-2 text-gray-400 hover:text-gray-600 font-bold text-sm" title="Hapus Pencarian">&times;</a>
                    @endif

                    <button type="submit" class="px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-md hover:shadow-lg shrink-0 flex items-center gap-1.5">
                        <i class="fa-solid fa-search text-xs"></i>
                        <span>Cari Buku</span>
                    </button>
                </div>

                <div x-show="showSuggest && searchQuery.trim().length >= 2" x-cloak class="absolute left-0 right-0 top-full mt-2 bg-white rounded-2xl border-2 border-gray-200 shadow-2xl z-50 max-h-80 overflow-y-auto p-2 space-y-1.5 text-left text-gray-900">
                    <div class="px-2.5 py-1 text-[10px] font-black text-gray-400 uppercase tracking-wider flex items-center justify-between border-b border-gray-100">
                        <span>Rekomendasi Pencarian (Klik untuk Filter Grid)</span>
                        <span class="text-emerald-600 font-bold" x-text="suggestions.length + ' Ditemukan'"></span>
                    </div>
                    <template x-if="suggestions.length > 0">
                        <div class="space-y-1">
                            <template x-for="item in suggestions" :key="item.id">
                                <button type="button" @click="selectSuggestion(item)" class="w-full p-2.5 rounded-xl hover:bg-brand-50/80 transition flex items-center gap-3 group border border-transparent hover:border-brand-200 text-left">
                                    <div class="w-10 h-14 bg-gray-100 rounded-lg overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center shadow-xs">
                                        <template x-if="item.cover_url">
                                            <img :src="item.cover_url" width="40" height="56" loading="lazy" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!item.cover_url">
                                            <div class="w-full h-full bg-gradient-to-br from-brand-900 to-red-950 text-white font-black text-xs flex flex-col items-center justify-center p-1 border-l-2 border-amber-400/50">
                                                <i class="fa-solid fa-book text-[11px] opacity-40"></i>
                                                <span class="text-[7.5px] mt-0.5" x-text="item.judul.substr(0, 1)"></span>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="min-w-0 flex-1 text-xs">
                                        <p class="font-bold text-gray-900 truncate group-hover:text-brand-700" x-text="item.judul"></p>
                                        <p class="text-[10px] text-gray-500 truncate" x-text="item.penulis + ' • ' + item.kategori"></p>
                                        <div class="flex items-center gap-1.5 mt-1 text-[9.5px]">
                                            <span class="px-1.5 py-0.5 rounded bg-gray-100 font-bold text-gray-700 border border-gray-200" x-text="item.rak + ' (' + item.laci + ')'"></span>
                                            <span class="px-1.5 py-0.5 rounded font-black" :class="item.available_quantity > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'" x-text="'Stok: ' + item.available_quantity + ' Eks'"></span>
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-arrow-right text-gray-300 group-hover:text-brand-700 text-xs shrink-0 transform group-hover:translate-x-0.5 transition"></i>
                                </button>
                            </template>
                        </div>
                    </template>
                    <template x-if="suggestions.length === 0 && !loadingSuggest">
                        <div class="py-6 px-4 text-center">
                            <p class="text-xs font-bold text-gray-700">Tidak ada buku ditemukan</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Coba kata kunci judul, pengarang, atau nomor ISBN lain</p>
                        </div>
                    </template>
                </div>
            </form>

            <div class="pt-1 flex flex-wrap items-center justify-center gap-3 text-xs font-bold text-white">
                <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-xl border border-white/15 text-[11px]">
                    <span class="text-white font-black">{{ $total_buku_count }}</span> Judul Koleksi
                </div>
                <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-xl border border-white/15 text-[11px]">
                    <span class="text-white font-black">{{ $total_kategori_count }}</span> Kategori
                </div>
                <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-xl border border-white/15 text-[11px]">
                    <span class="text-white font-black">{{ $total_rak_count }}</span> Rak Penempatan
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border-2 border-gray-200 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
        <div class="flex items-center justify-between md:justify-start gap-3 w-full md:w-auto">
            @php
                $activeFilterCount = 0;
                if(request('kategori_id')) $activeFilterCount++;
                if(request('penulis_id')) $activeFilterCount++;
                if(request('rak_id')) $activeFilterCount++;
                if(request('tahun')) $activeFilterCount++;
                if(request('status')) $activeFilterCount++;
            @endphp

            <button @click="showFilterBar = !showFilterBar"
                    :class="showFilterBar || {{ $activeFilterCount }} > 0 ? 'bg-brand-700 text-white border-brand-700 shadow-sm' : 'bg-gray-50 text-gray-800 border-gray-200 hover:bg-gray-100'"
                    class="px-3.5 py-2 rounded-xl font-extrabold text-xs border-2 transition flex items-center gap-2 shrink-0">
                <i class="fa-solid fa-sliders text-xs"></i>
                <span>Filter Katalog</span>
                @if($activeFilterCount > 0)
                    <span class="px-2 py-0.5 rounded-full bg-amber-400 text-brand-950 font-black text-[10px]">{{ $activeFilterCount }}</span>
                @endif
                <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="showFilterBar ? 'rotate-180' : ''"></i>
            </button>

            <div class="text-right md:text-left">
                @if(request('search'))
                    <h3 class="font-extrabold text-gray-900 text-xs line-clamp-1">Pencarian "<span class="text-brand-700">{{ request('search') }}</span>" — {{ $buku->total() }} buku</h3>
                @else
                    <h3 class="font-extrabold text-gray-900 text-xs"><span class="text-brand-700 font-black">{{ $buku->total() }}</span> Buku Ditemukan</h3>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-between md:justify-end gap-2.5 w-full md:w-auto pt-2.5 md:pt-0 border-t border-gray-100 md:border-t-0">
            <div class="flex items-center bg-gray-100 p-1 rounded-xl border border-gray-200 shrink-0">
                <button type="button" @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white text-brand-700 shadow-2xs font-extrabold' : 'text-gray-400 hover:text-gray-700'" class="w-8 h-8 rounded-lg transition flex items-center justify-center" title="Tampilan Grid" aria-label="Tampilan Grid">
                    <i class="fa-solid fa-table-cells-large text-xs"></i>
                </button>
                <button type="button" @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white text-brand-700 shadow-2xs font-extrabold' : 'text-gray-400 hover:text-gray-700'" class="w-8 h-8 rounded-lg transition flex items-center justify-center" title="Tampilan List" aria-label="Tampilan List">
                    <i class="fa-solid fa-list text-xs"></i>
                </button>
            </div>

            <form action="{{ route('katalog') }}" method="GET" class="flex items-center gap-1.5 shrink-0">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                @if(request('kategori_id')) <input type="hidden" name="kategori_id" value="{{ request('kategori_id') }}"> @endif
                @if(request('penulis_id')) <input type="hidden" name="penulis_id" value="{{ request('penulis_id') }}"> @endif
                @if(request('rak_id')) <input type="hidden" name="rak_id" value="{{ request('rak_id') }}"> @endif
                @if(request('tahun')) <input type="hidden" name="tahun" value="{{ request('tahun') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif

                <span class="text-gray-400 font-medium text-[11px] hidden sm:inline">Urutkan:</span>
                <select name="sort" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 text-gray-800 text-xs font-bold rounded-xl px-3 py-1.5 focus:ring-1 focus:ring-brand-700 focus:outline-none">
                    <option value="terbaru" {{ request('sort') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="populer" {{ request('sort') === 'populer' ? 'selected' : '' }}>Paling Populer</option>
                    <option value="judul_asc" {{ request('sort') === 'judul_asc' ? 'selected' : '' }}>Judul A-Z</option>
                    <option value="judul_desc" {{ request('sort') === 'judul_desc' ? 'selected' : '' }}>Judul Z-A</option>
                    <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                </select>
            </form>
        </div>
    </div>

    <div x-show="showFilterBar" x-cloak class="bg-white rounded-2xl border-2 border-gray-200 p-5 shadow-sm space-y-4">
        <form action="{{ route('katalog') }}" method="GET" class="space-y-4 text-xs">
            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1 text-[11px]">Kategori</label>
                    <select name="kategori_id" class="w-full px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:outline-none text-xs">
                        <option value="">Semua Kategori</option>
                        @foreach($kategori_list as $kat)
                            <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1 text-[11px]">Penulis</label>
                    <select name="penulis_id" class="w-full px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:outline-none text-xs">
                        <option value="">Semua Penulis</option>
                        @foreach($penulis_list as $pen)
                            <option value="{{ $pen->id }}" {{ request('penulis_id') == $pen->id ? 'selected' : '' }}>{{ $pen->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1 text-[11px]">Lokasi Rak</label>
                    <select name="rak_id" class="w-full px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:outline-none text-xs">
                        <option value="">Semua Rak</option>
                        @foreach($rak_list as $rk)
                            <option value="{{ $rk->id }}" {{ request('rak_id') == $rk->id ? 'selected' : '' }}>{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1 text-[11px]">Tahun Terbit</label>
                    <select name="tahun" class="w-full px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:outline-none text-xs">
                        <option value="">Semua Tahun</option>
                        @foreach($tahun_list as $thn)
                            <option value="{{ $thn }}" {{ request('tahun') == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1 text-[11px]">Ketersediaan</label>
                    <select name="status" class="w-full px-2.5 py-1.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:outline-none text-xs">
                        <option value="">Semua Status</option>
                        <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="dipinjam" {{ request('status') === 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
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

    <div class="space-y-5">
        <div>
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($buku as $item)
                    @php
                        $available = $item->available_quantity;
                        $totalEx = $item->total_quantity;
                        $coverUrl = $item->cover_url;
                        $laciName = $item->laci->nama_laci ?? ($item->rak ? 'Laci 1' : 'Tanpa Laci');
                        $bookPayload = [
                            'id' => $item->id,
                            'judul' => $item->judul,
                            'penulis' => $item->penulis->nama ?? '-',
                            'penerbit' => $item->penerbit->nama ?? '-',
                            'tahun' => (string) $item->tahun_terbit,
                            'isbn' => (string) ($item->isbn ?? 'Tanpa ISBN'),
                            'kategori' => $item->kategori->nama ?? 'Umum',
                            'kelas' => $item->kelas->nama_kelas ?? '',
                            'rak' => ($item->rak->kode_rak ?? '') . ' - ' . ($item->rak->nama_rak ?? ''),
                            'laci' => $laciName,
                            'sinopsis' => $item->sinopsis ?? 'Buku perpustakaan resmi SMK PGRI Pekanbaru.',
                            'tersedia' => $item->available_quantity,
                            'total' => $item->total_quantity,
                            'cover' => $item->cover_card_url ?? ''
                        ];
                    @endphp

                    <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden shadow-2xs hover:shadow-lg hover:border-brand-700 transition duration-300 flex flex-col justify-between group">
                        <div>
                            <div class="relative w-full h-60 bg-gray-100 overflow-hidden flex items-center justify-center border-b-2 border-gray-100 cursor-pointer" @click="modalData = {{ json_encode($bookPayload) }}; openDetailModal = true">
                                @if($coverUrl)
                                    <img src="{{ $item->cover_card_url }}" alt="Cover {{ $item->judul }}" width="300" height="240" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-brand-900 via-brand-800 to-red-950 text-white p-4 border-l-[6px] border-amber-400/50 flex flex-col justify-between select-none relative overflow-hidden shadow-inner">
                                        <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
                                        <div class="flex items-center justify-end">
                                            <span class="text-[8.5px] font-black uppercase tracking-widest text-amber-300/90 bg-black/30 px-2.5 py-0.5 rounded-md border border-white/10 backdrop-blur-xs">{{ substr($item->kategori->nama ?? 'Buku', 0, 15) }}</span>
                                        </div>
                                        <div class="flex flex-col items-center justify-center my-auto text-center px-2">
                                            <i class="fa-solid fa-book-bookmark text-white/25 text-3xl mb-2 drop-shadow-xs"></i>
                                            <p class="text-xs sm:text-sm font-black text-white leading-snug line-clamp-3 drop-shadow-sm">{{ $item->judul }}</p>
                                            <p class="text-[10px] text-white/70 font-medium truncate max-w-full mt-1">{{ $item->penulis->nama ?? 'SMK PGRI' }}</p>
                                        </div>
                                        <div class="flex items-center justify-between border-t border-white/10 pt-1.5 text-[8px] font-bold text-white/60 tracking-wider uppercase">
                                            <span>PERPUSTAKAAN</span>
                                            <span>SMK PGRI</span>
                                        </div>
                                    </div>
                                @endif

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

                            <div class="p-4 space-y-2.5">
                                <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold bg-gray-100 text-gray-700 border border-gray-200 uppercase inline-block">
                                    {{ $item->kategori->nama ?? 'Umum' }}
                                </span>
                                @if($item->kelas)
                                    <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200 uppercase inline-block">
                                        Kelas {{ $item->kelas->nama_kelas }}
                                    </span>
                                @endif

                                <h3 class="text-xs font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-brand-700 transition">
                                    <button type="button" @click="modalData = {{ json_encode($bookPayload) }}; openDetailModal = true" class="text-left hover:underline">
                                        {{ $item->judul }}
                                    </button>
                                </h3>

                                <div class="space-y-1 text-[11px] text-gray-600 font-medium border-t border-gray-100 pt-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400">Penulis:</span>
                                        <span class="font-bold text-gray-900 truncate max-w-[120px]">{{ $item->penulis->nama ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400">Lokasi:</span>
                                        <span class="font-bold text-brand-700 bg-brand-50 px-1.5 py-0.5 rounded border border-brand-200 text-[10px]">
                                            {{ $item->rak->kode_rak ?? '-' }} • {{ $laciName }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-gray-50 border-t border-gray-100 flex items-center gap-2 text-xs">
                            <button type="button" @click="modalData = {{ json_encode($bookPayload) }}; openDetailModal = true" class="flex-1 py-2 bg-gray-200/80 hover:bg-gray-300 text-gray-800 font-bold text-[11px] rounded-xl transition text-center">
                                Detail
                            </button>
                            <button type="button" @click="startLoan({ id: {{ $item->id }}, judul: '{{ addslashes($item->judul) }}', cover: '{{ $item->cover_thumb_url ?? '' }}', penulis: '{{ addslashes($item->penulis->nama ?? '-') }}', rak: '{{ addslashes($item->rak->kode_rak ?? '-') }}', available: {{ $available }} })" class="flex-1 py-2 {{ $available > 0 ? 'bg-brand-700 hover:bg-brand-800 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }} font-extrabold text-[11px] rounded-xl transition shadow-xs text-center flex items-center justify-center gap-1">
                                <i class="fa-solid fa-hand-holding-hand text-xs"></i>
                                <span>Ajukan Pinjam</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 px-6 text-center bg-white rounded-2xl border-2 border-gray-200 space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto border border-gray-200 font-bold text-lg">
                            <i class="fa-solid fa-book-open text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-gray-900">Tidak Ada Buku Ditemukan</h3>
                            <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">Belum ada koleksi yang sesuai dengan pencarian atau filter yang dipilih.</p>
                        </div>
                        <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                            <a href="{{ route('katalog') }}" class="px-4 py-2 bg-brand-700 text-white font-extrabold text-xs rounded-xl hover:bg-brand-800 transition shadow-sm">
                                Hapus Semua Filter
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <div x-show="viewMode === 'list'" class="space-y-4">
                @forelse($buku as $item)
                    @php
                        $available = $item->available_quantity;
                        $totalEx = $item->total_quantity;
                        $coverUrl = $item->cover_url;
                        $laciName = $item->laci->nama_laci ?? ($item->rak ? 'Laci 1' : 'Tanpa Laci');
                        $bookPayload = [
                            'id' => $item->id,
                            'judul' => $item->judul,
                            'penulis' => $item->penulis->nama ?? '-',
                            'penerbit' => $item->penerbit->nama ?? '-',
                            'tahun' => (string) $item->tahun_terbit,
                            'isbn' => (string) ($item->isbn ?? 'Tanpa ISBN'),
                            'kategori' => $item->kategori->nama ?? 'Umum',
                            'kelas' => $item->kelas->nama_kelas ?? '',
                            'rak' => ($item->rak->kode_rak ?? '') . ' - ' . ($item->rak->nama_rak ?? ''),
                            'laci' => $laciName,
                            'sinopsis' => $item->sinopsis ?? 'Buku perpustakaan resmi SMK PGRI Pekanbaru.',
                            'tersedia' => $item->available_quantity,
                            'total' => $item->total_quantity,
                            'cover' => $item->cover_card_url ?? ''
                        ];
                    @endphp

                    <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden p-4 shadow-2xs hover:border-brand-700 transition duration-300 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <div class="w-20 h-28 bg-gray-100 border border-gray-200 rounded-xl overflow-hidden shrink-0 cursor-pointer shadow-xs" @click="modalData = {{ json_encode($bookPayload) }}; openDetailModal = true">
                                @if($coverUrl)
                                    <img src="{{ $item->cover_thumb_url }}" alt="Cover {{ $item->judul }}" width="80" height="112" loading="lazy" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-brand-900 via-brand-800 to-red-950 text-white p-2 border-l-[3px] border-amber-400/50 flex flex-col justify-between select-none relative overflow-hidden text-center">
                                        <span class="text-[6.5px] font-black text-amber-300/90 uppercase tracking-wider">{{ substr($item->kategori->nama ?? 'Buku', 0, 8) }}</span>
                                        <div class="my-auto">
                                            <i class="fa-solid fa-book text-white/30 text-xs mb-0.5"></i>
                                            <p class="text-[8px] font-bold text-white line-clamp-2 leading-tight">{{ $item->judul }}</p>
                                        </div>
                                        <span class="text-[6px] font-bold text-white/50 uppercase">SMK PGRI</span>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-1 text-xs">
                                <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold bg-gray-100 text-gray-700 border border-gray-200 uppercase inline-block">
                                    {{ $item->kategori->nama ?? 'Umum' }}
                                </span>
                                @if($item->kelas)
                                    <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200 uppercase inline-block">
                                        Kelas {{ $item->kelas->nama_kelas }}
                                    </span>
                                @endif
                                <h3 class="text-sm font-bold text-gray-900 leading-snug">
                                    <button type="button" @click="modalData = {{ json_encode($bookPayload) }}; openDetailModal = true" class="text-left hover:text-brand-700 hover:underline">
                                        {{ $item->judul }}
                                    </button>
                                </h3>
                                <p class="text-[11px] text-gray-500">Penulis: <strong class="text-gray-900">{{ $item->penulis->nama ?? '-' }}</strong> | Lokasi: <strong class="font-mono text-brand-700">{{ $item->rak->kode_rak ?? '-' }} ({{ $laciName }})</strong></p>
                                <p class="text-[10px] text-gray-400 font-mono">ISBN: {{ $item->isbn ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-100">
                            <div class="text-right hidden md:block">
                                <span class="text-[10px] font-bold text-gray-400 block">Stok:</span>
                                <span class="text-xs font-black {{ $available > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $available }} / {{ $totalEx }} Eks</span>
                            </div>
                            <button type="button" @click="modalData = {{ json_encode($bookPayload) }}; openDetailModal = true" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                                Detail
                            </button>
                            <button type="button" @click="startLoan({ id: {{ $item->id }}, judul: '{{ addslashes($item->judul) }}', cover: '{{ $item->cover_thumb_url ?? '' }}', penulis: '{{ addslashes($item->penulis->nama ?? '-') }}', rak: '{{ addslashes($item->rak->kode_rak ?? '-') }}', available: {{ $available }} })" class="px-4 py-2 {{ $available > 0 ? 'bg-brand-700 hover:bg-brand-800 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }} font-extrabold text-xs rounded-xl transition shadow-2xs flex items-center gap-1.5">
                                <i class="fa-solid fa-hand-holding-hand text-xs"></i>
                                <span>Ajukan Pinjam</span>
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

        @if($buku->hasPages())
            <div class="mt-8 pt-4 border-t border-gray-100">
                {{ $buku->links() }}
            </div>
        @endif
    </div>

    <div x-show="openDetailModal" @click.self="openDetailModal = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 backdrop-blur-xs p-4 overflow-y-auto"          x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border-2 border-gray-200 transform transition-all my-8 relative">
            <button @click="openDetailModal = false" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-900 flex items-center justify-center transition font-bold">&times;</button>

            <div class="flex flex-col sm:flex-row gap-6">
                <div class="w-full sm:w-48 h-64 bg-gray-900 rounded-2xl overflow-hidden shrink-0 border-2 border-gray-200 shadow-md relative">
                    <template x-if="modalData.cover">
                        <img :src="modalData.cover" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!modalData.cover">
                        <div class="w-full h-full bg-gradient-to-br from-brand-900 via-brand-800 to-red-950 p-4 border-l-[5px] border-amber-400/50 flex flex-col justify-between text-white relative overflow-hidden select-none">
                            <div class="flex items-center justify-end">
                                <span class="px-2 py-0.5 bg-black/30 text-amber-300/90 text-[9px] font-black uppercase rounded-md border border-white/10" x-text="modalData.kategori"></span>
                            </div>
                            <div class="text-center my-auto px-2">
                                <i class="fa-solid fa-book-bookmark text-white/25 text-3xl mb-2"></i>
                                <h4 class="text-xs sm:text-sm font-black text-white leading-snug drop-shadow-sm" x-text="modalData.judul"></h4>
                                <p class="text-[10px] text-white/70 font-medium mt-1 truncate" x-text="modalData.penulis"></p>
                            </div>
                            <div class="flex items-center justify-between border-t border-white/10 pt-1.5 text-[8px] font-bold text-white/60 tracking-wider uppercase">
                                <span>PERPUSTAKAAN</span>
                                <span>SMK PGRI</span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex-1 space-y-4 text-xs">
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-brand-50 text-brand-700 border border-brand-200 uppercase inline-block" x-text="modalData.kategori"></span>
                            <span x-show="modalData.kelas" x-cloak class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200 uppercase inline-block" x-text="'Kelas ' + modalData.kelas"></span>
                        </div>
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
                            <span class="text-[9px] font-bold text-gray-400 uppercase block">Lokasi Rak &amp; Laci</span>
                            <span class="font-mono font-bold text-brand-700" x-text="modalData.rak + ' • ' + modalData.laci"></span>
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

                    <div class="pt-4 border-t-2 border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div>
                            <span class="text-[10px] font-extrabold text-gray-500 block">Status Ketersediaan:</span>
                            <span class="text-xs font-black text-emerald-600" x-text="modalData.tersedia + ' Tersedia / ' + modalData.total + ' Eksemplar Total'"></span>
                        </div>
                        <button type="button" @click="startLoan({ id: modalData.id, judul: modalData.judul, cover: modalData.cover, penulis: modalData.penulis, rak: modalData.rak, available: modalData.tersedia })" class="w-full sm:w-auto px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold rounded-xl text-xs flex items-center justify-center gap-1.5 shadow-md">
                            <i class="fa-solid fa-hand-holding-hand"></i>
                            <span>Ajukan Peminjaman Ini</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="openLoanModal" @click.self="openLoanModal = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 backdrop-blur-xs p-4 overflow-y-auto"          x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 space-y-5 shadow-2xl border-2 border-gray-200 transform transition-all my-8 relative">
            <button @click="openLoanModal = false" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-900 flex items-center justify-center transition font-bold">&times;</button>

            <div class="space-y-1">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-brand-50 text-brand-800 text-[10px] font-black uppercase tracking-wider border border-brand-200">
                    <i class="fa-solid fa-file-signature text-brand-700"></i>
                    <span>Formulir Pengajuan Peminjaman</span>
                </div>
                <h3 class="text-base font-black text-gray-900">Form Pengajuan Peminjaman Buku</h3>
                <p class="text-[11px] text-gray-500">Lengkapi data diri Anda untuk konfirmasi peminjaman buku ke petugas perpustakaan.</p>
            </div>

            <div class="p-3 bg-gray-50 rounded-2xl border border-gray-200 flex items-center gap-3">
                <div class="w-12 h-16 bg-gray-200 rounded-lg overflow-hidden shrink-0 border border-gray-300 flex items-center justify-center shadow-2xs">
                    <template x-if="loanData.cover">
                        <img :src="loanData.cover" alt="Cover" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!loanData.cover">
                        <div class="w-full h-full bg-brand-800 text-white font-bold flex items-center justify-center text-xs">
                            <i class="fa-solid fa-book"></i>
                        </div>
                    </template>
                </div>
                <div class="min-w-0 flex-1 text-xs">
                    <p class="font-extrabold text-gray-900 line-clamp-1" x-text="loanData.judul"></p>
                    <p class="text-[11px] text-gray-500 mt-0.5">Penulis: <span class="font-bold text-gray-700" x-text="loanData.penulis"></span></p>
                    <p class="text-[10px] text-brand-700 font-mono font-bold mt-0.5">Lokasi Rak: <span x-text="loanData.rak"></span></p>
                </div>
            </div>

            <form @submit.prevent="submitLoanRequest()" class="space-y-3 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama Lengkap Siswa / Peminjam <span class="text-rose-600">*</span></label>
                    <input type="text" x-model="loanData.nama_peminjam" required placeholder="Contoh: Muhammad Ihwal" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Kelas &amp; Jurusan <span class="text-rose-600">*</span></label>
                        <input type="text" x-model="loanData.jurusan" required placeholder="Contoh: XII RPL 1 / XI TKJ 2" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">NISN / Nomor Induk (Opsional)</label>
                        <input type="text" x-model="loanData.nomor_induk" placeholder="Contoh: 0065123489" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">No. WhatsApp Aktif (Opsional)</label>
                    <input type="text" x-model="loanData.no_wa" placeholder="Contoh: 081234567890" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Catatan / Keterangan Tambahan</label>
                    <textarea x-model="loanData.catatan" rows="2" placeholder="Contoh: Untuk keperluan tugas akhir / ujian kejuruan..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button" @click="openLoanModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="submit" :disabled="submittingLoan" class="px-6 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold rounded-xl text-xs transition shadow-md flex items-center gap-1.5 disabled:opacity-50">
                        <template x-if="submittingLoan">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </template>
                        <template x-if="!submittingLoan">
                            <i class="fa-solid fa-paper-plane"></i>
                        </template>
                        <span x-text="submittingLoan ? 'Mengirim...' : 'Kirim Pengajuan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function katalogPage() {
    return {
        viewMode: 'grid',
        showFilterBar: false,
        openDetailModal: false,
        modalData: {},
        openLoanModal: false,
        loanData: {
            buku_id: '',
            judul: '',
            cover: '',
            penulis: '',
            rak: '',
            nama_peminjam: '',
            jurusan: '',
            nomor_induk: '',
            no_wa: '',
            catatan: ''
        },
        submittingLoan: false,
        searchQuery: @json(request('search', '')),
        suggestions: [],
        loadingSuggest: false,
        showSuggest: false,
        fetchSuggestions() {
            if (this.searchQuery.trim().length < 2) {
                this.suggestions = [];
                this.showSuggest = false;
                return;
            }
            this.loadingSuggest = true;
            fetch('/api/buku/search-suggestions?q=' + encodeURIComponent(this.searchQuery))
                .then(res => res.json())
                .then(data => {
                    this.suggestions = data;
                    this.showSuggest = data.length > 0;
                    this.loadingSuggest = false;
                })
                .catch(() => { this.loadingSuggest = false; });
        },
        selectSuggestion(item) {
            this.searchQuery = item.judul;
            this.showSuggest = false;
            window.location.href = '{{ route('katalog') }}?search=' + encodeURIComponent(item.judul);
        },
        startLoan(book) {
            if (book.available <= 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stok Buku Habis',
                        text: 'Seluruh eksemplar buku ini sedang dipinjam oleh murid lain.',
                        confirmButtonColor: '#991b1b'
                    });
                } else {
                    alert('Maaf, seluruh stok buku ini sedang habis dipinjam.');
                }
                return;
            }
            this.loanData.buku_id = book.id;
            this.loanData.judul = book.judul;
            this.loanData.cover = book.cover;
            this.loanData.penulis = book.penulis;
            this.loanData.rak = book.rak;
            this.openDetailModal = false;
            this.openLoanModal = true;
        },
        submitLoanRequest() {
            if (!this.loanData.nama_peminjam || !this.loanData.jurusan) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Belum Lengkap',
                        text: 'Mohon isi Nama Siswa dan Kelas/Jurusan.',
                        confirmButtonColor: '#991b1b'
                    });
                } else {
                    alert('Mohon isi Nama Siswa dan Kelas/Jurusan.');
                }
                return;
            }
            this.submittingLoan = true;
            const token = document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '{{ csrf_token() }}';
            const payload = Object.assign({}, this.loanData, { _token: token });
            fetch('{{ route('katalog.ajukan') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(res => {
                this.submittingLoan = false;
                if (res.status === 200 && res.body.success) {
                    this.openLoanModal = false;
                    this.loanData.nama_peminjam = '';
                    this.loanData.jurusan = '';
                    this.loanData.nomor_induk = '';
                    this.loanData.no_wa = '';
                    this.loanData.catatan = '';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Pengajuan Terkirim!',
                            html: '<p class="text-xs text-gray-600 mb-2">Pengajuan peminjaman buku <strong>' + res.body.judul_buku + '</strong> berhasil dikirim ke Admin Perpustakaan.</p><p class="text-sm font-mono font-bold text-brand-800 bg-gray-100 py-1.5 px-3 rounded-lg border border-gray-200">Kode Ref: ' + res.body.kode + '</p><p class="text-[11px] text-gray-500 mt-2">Silakan konfirmasi ke meja sirkulasi perpustakaan saat mengambil buku fisik.</p>',
                            confirmButtonColor: '#991b1b'
                        });
                    } else {
                        alert('Pengajuan peminjaman berhasil dikirim! Kode Referensi: ' + res.body.kode);
                    }
                } else {
                    const msg = res.body.message || 'Gagal mengajukan peminjaman buku.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Pengajuan Gagal',
                            text: msg,
                            confirmButtonColor: '#991b1b'
                        });
                    } else {
                        alert(msg);
                    }
                }
            })
            .catch(() => {
                this.submittingLoan = false;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Koneksi jaringan terputus atau server sedang sibuk.',
                        confirmButtonColor: '#991b1b'
                    });
                } else {
                    alert('Terjadi kesalahan jaringan.');
                }
            });
        }
    };
}
</script>
@endsection
