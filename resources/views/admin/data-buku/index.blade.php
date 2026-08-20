@extends('layouts.dashboard')

@section('title', 'Data Buku Lengkap')
@section('page_heading', 'Data Koleksi Buku')

@section('content')
<div class="space-y-5" x-data="{
    viewMode: localStorage.getItem('dataRakView') || 'grid',
    setView(mode) {
        this.viewMode = mode;
        localStorage.setItem('dataRakView', mode);
    }
}">

    {{-- Stat Ringkas --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 text-gray-700 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Total Rak</span>
                <span class="text-base sm:text-lg font-bold text-gray-900">{{ number_format($stats['total_rak']) }} Rak</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 text-gray-700 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-book-bookmark"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Judul Terdata</span>
                <span class="text-base sm:text-lg font-bold text-gray-900">{{ number_format($stats['total_judul']) }} Judul</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 text-gray-700 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-box"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Total Eksemplar</span>
                <span class="text-base sm:text-lg font-bold text-gray-900">{{ number_format($stats['total_stok']) }} Buku</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="min-w-0">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Tersedia Dipinjam</span>
                <span class="text-base sm:text-lg font-bold text-gray-900">{{ number_format($stats['buku_tersedia']) }} Buku</span>
            </div>
        </div>
    </div>

    {{-- Header: Toggle Grid/List & Aksi --}}
    <div class="bg-white p-4 rounded-2xl border-2 border-gray-200 shadow-sm space-y-3">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            {{-- Kiri: Toggle Grid / List --}}
            <div class="flex items-center bg-gray-100 rounded-xl p-0.5 border border-gray-200 self-start md:self-auto">
                <button type="button" @click="setView('grid')"
                    :class="viewMode === 'grid' ? 'bg-white text-brand-700 shadow-xs border border-gray-200' : 'text-gray-500 hover:text-gray-700'"
                    class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5" title="Tampilan Grid">
                    <i class="fa-solid fa-grip text-[11px]"></i>
                    <span>Grid</span>
                </button>
                <button type="button" @click="setView('list')"
                    :class="viewMode === 'list' ? 'bg-white text-brand-700 shadow-xs border border-gray-200' : 'text-gray-500 hover:text-gray-700'"
                    class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5" title="Tampilan List">
                    <i class="fa-solid fa-list text-[11px]"></i>
                    <span>List</span>
                </button>
            </div>

            {{-- Kanan: Tombol Aksi --}}
            <div class="flex items-center gap-2 self-start md:self-auto shrink-0">
                <a href="{{ route('admin.buku') }}" class="px-3.5 py-2 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-pen-to-square text-emerald-300"></i>
                    <span class="hidden sm:inline">Kelola di Master Buku</span>
                    <span class="sm:hidden">Master Buku</span>
                </a>
                <a href="{{ route('admin.rak') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-extrabold rounded-xl transition flex items-center gap-1.5 border border-gray-200">
                    <i class="fa-solid fa-layer-group text-gray-500"></i>
                    <span class="hidden sm:inline">Kelola Rak &amp; Laci</span>
                    <span class="sm:hidden">Rak &amp; Laci</span>
                </a>
            </div>
        </div>

        <form action="{{ route('admin.data-buku') }}" method="GET" class="pt-2 border-t border-gray-100 flex items-center gap-2">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode rak, nama rak, atau lokasi..."
                       class="w-full pl-8 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none">
                <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs absolute left-3 top-1/2 -translate-y-1/2"></i>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white font-bold text-xs rounded-xl transition shrink-0">
                Cari
            </button>
            @if(request()->filled('search'))
                <a href="{{ route('admin.data-buku') }}" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-bold transition flex items-center justify-center shrink-0" title="Reset Pencarian">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- ======================== GRID RAK ======================== --}}
    <div x-show="viewMode === 'grid'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($rakList as $rak)
            @php
                $totalJudulRak = $rak->buku_count ?? 0;
                $totalEksemplarRak = (int) ($rak->buku_sum_total_quantity ?? 0);
                $totalTersediaRak = (int) ($rak->buku_sum_available_quantity ?? 0);
                $isKosong = $totalJudulRak === 0;
            @endphp
            <div class="bg-white rounded-2xl border-2 border-gray-200 hover:border-brand-300 hover:shadow-md transition duration-200 overflow-hidden flex flex-col">
                <div class="p-4 flex items-start justify-between gap-2 bg-gradient-to-br from-gray-50 to-white border-b border-gray-100">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-xl bg-brand-800 text-white flex items-center justify-center shrink-0 shadow-sm">
                            <i class="fa-solid fa-layer-group text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-extrabold text-sm text-gray-900 truncate" title="{{ $rak->nama_rak }}">{{ $rak->nama_rak }}</h3>
                            <span class="font-mono text-[10.5px] font-bold text-gray-500">{{ $rak->kode_rak }}</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black shrink-0 {{ $isKosong ? 'bg-gray-100 text-gray-500 border border-gray-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                        {{ $isKosong ? 'Kosong' : 'Berisi' }}
                    </span>
                </div>

                <div class="p-4 flex-1 flex flex-col justify-between space-y-3 text-xs">
                    <div class="space-y-2">
                        <p class="text-[11px] text-gray-500 font-medium flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-gray-400 w-3 text-center"></i>
                            <span class="truncate">{{ $rak->lokasi ?? 'Lokasi umum perpustakaan' }}</span>
                        </p>
                        @if($rak->kategori)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-brand-50 text-brand-700 border border-brand-200 inline-block">
                                {{ $rak->kategori->nama }}
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-3 gap-2 pt-3 border-t border-gray-100 text-center">
                        <div>
                            <span class="block font-extrabold text-sm text-gray-900">{{ $totalJudulRak }}</span>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Judul</span>
                        </div>
                        <div>
                            <span class="block font-extrabold text-sm text-gray-900">{{ $totalEksemplarRak }}</span>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Eksemplar</span>
                        </div>
                        <div>
                            <span class="block font-extrabold text-sm text-emerald-700">{{ $totalTersediaRak }}</span>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Tersedia</span>
                        </div>
                    </div>

                    @if($isKosong)
                        <span class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gray-100 text-gray-400 text-xs font-extrabold rounded-xl cursor-not-allowed">
                            <i class="fa-solid fa-box-open text-[11px]"></i>
                            <span>Belum Ada Buku</span>
                        </span>
                    @else
                        <a href="{{ route('admin.data-buku.rak', $rak->id) }}" target="_blank"
                           class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition shadow-sm">
                            <i class="fa-solid fa-eye text-[11px]"></i>
                            <span>Lihat Buku di Rak Ini</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] opacity-70"></i>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 bg-white rounded-2xl border-2 border-gray-200 text-center text-gray-400">
                <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center mx-auto mb-2 text-gray-400">
                    <i class="fa-solid fa-layer-group text-xl"></i>
                </div>
                <p class="text-xs font-bold text-gray-700">Tidak ada rak yang sesuai dengan pencarian</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Coba ubah kata kunci, atau tambah rak baru di menu Kelola Rak &amp; Laci</p>
            </div>
        @endforelse
    </div>

    {{-- ======================== LIST RAK ======================== --}}
    <div x-show="viewMode === 'list'" x-cloak>
        <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
            @forelse($rakList as $rak)
                @php
                    $totalJudulRak = $rak->buku_count ?? 0;
                    $totalEksemplarRak = (int) ($rak->buku_sum_total_quantity ?? 0);
                    $totalTersediaRak = (int) ($rak->buku_sum_available_quantity ?? 0);
                    $isKosong = $totalJudulRak === 0;
                @endphp
                <div class="flex items-center gap-4 px-4 py-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition duration-150">

                    {{-- Ikon rak --}}
                    <div class="w-10 h-10 rounded-xl bg-brand-800 text-white flex items-center justify-center shrink-0 shadow-sm">
                        <i class="fa-solid fa-layer-group text-sm"></i>
                    </div>

                    {{-- Info utama --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xs font-extrabold text-gray-900 truncate" title="{{ $rak->nama_rak }}">
                            {{ $rak->nama_rak }}
                            <span class="font-mono text-[10.5px] font-bold text-gray-400 ml-1">{{ $rak->kode_rak }}</span>
                        </h3>
                        <p class="text-[11px] text-gray-500 font-medium mt-0.5 truncate flex items-center gap-1">
                            <i class="fa-solid fa-location-dot text-gray-400 text-[10px]"></i>
                            {{ $rak->lokasi ?? 'Lokasi umum perpustakaan' }}
                        </p>
                    </div>

                    {{-- Kategori --}}
                    <div class="hidden sm:block shrink-0">
                        @if($rak->kategori)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-brand-50 text-brand-700 border border-brand-200">
                                {{ $rak->kategori->nama }}
                            </span>
                        @else
                            <span class="text-[11px] text-gray-300 font-medium">-</span>
                        @endif
                    </div>

                    {{-- Statistik --}}
                    <div class="hidden md:flex items-center gap-3 shrink-0 text-[11px] font-bold text-gray-700 text-center">
                        <div class="w-14">
                            <span class="block text-sm text-gray-900">{{ $totalJudulRak }}</span>
                            <span class="text-[9.5px] text-gray-400 font-semibold uppercase tracking-wide">Judul</span>
                        </div>
                        <div class="w-14">
                            <span class="block text-sm text-gray-900">{{ $totalEksemplarRak }}</span>
                            <span class="text-[9.5px] text-gray-400 font-semibold uppercase tracking-wide">Eks</span>
                        </div>
                        <div class="w-14">
                            <span class="block text-sm text-emerald-700">{{ $totalTersediaRak }}</span>
                            <span class="text-[9.5px] text-gray-400 font-semibold uppercase tracking-wide">Ada</span>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="shrink-0">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black {{ $isKosong ? 'bg-gray-100 text-gray-500 border border-gray-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                            {{ $isKosong ? 'Kosong' : 'Berisi' }}
                        </span>
                    </div>

                    {{-- Aksi --}}
                    <div class="shrink-0">
                        @if($isKosong)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-400 text-[11px] font-extrabold rounded-lg cursor-not-allowed">
                                <i class="fa-solid fa-box-open text-[10px]"></i>
                                <span class="hidden lg:inline">Belum Ada Buku</span>
                            </span>
                        @else
                            <a href="{{ route('admin.data-buku.rak', $rak->id) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-700 hover:bg-brand-800 text-white text-[11px] font-extrabold rounded-lg transition shadow-sm">
                                <i class="fa-solid fa-eye text-[10px]"></i>
                                <span class="hidden lg:inline">Lihat Buku</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[9px] opacity-70"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-16 text-center text-gray-400">
                    <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center mx-auto mb-2 text-gray-400">
                        <i class="fa-solid fa-layer-group text-xl"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-700">Tidak ada rak yang sesuai dengan pencarian</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Coba ubah kata kunci, atau tambah rak baru di menu Kelola Rak &amp; Laci</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if($rakList->hasPages())
        <div class="bg-white p-4 rounded-2xl border-2 border-gray-200 shadow-sm">
            {{ $rakList->links() }}
        </div>
    @endif

</div>
@endsection
