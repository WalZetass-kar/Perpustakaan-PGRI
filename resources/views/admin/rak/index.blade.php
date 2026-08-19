@extends('layouts.dashboard')

@section('title', 'Manajemen Lokasi Rak & Laci Buku')
@section('page_heading', 'Manajemen Lokasi Rak & Laci Buku')

@section('content')
<div class="space-y-5"
     x-data="{
        expandedRaks: {{ json_encode($rakList->pluck('id')->all()) }},
        allExpanded: true,
        openAddModal: false,
        openEditModal: false,
        openAddLaciModal: false,
        openEditLaciModal: false,
        activeMenuId: null,
        editData: {},
        laciData: {},
        toggleRak(id) {
            if (this.expandedRaks.includes(id)) {
                this.expandedRaks = this.expandedRaks.filter(item => item !== id);
            } else {
                this.expandedRaks.push(id);
            }
            this.allExpanded = this.expandedRaks.length === {{ $rakList->count() }};
        },
        toggleAll() {
            if (this.allExpanded) {
                this.expandedRaks = [];
                this.allExpanded = false;
            } else {
                this.expandedRaks = {{ json_encode($rakList->pluck('id')->all()) }};
                this.allExpanded = true;
            }
        }
     }"
     @click.outside="activeMenuId = null"
     @keydown.escape.window="openAddModal = false; openEditModal = false; openAddLaciModal = false; openEditLaciModal = false; activeMenuId = null"
     x-init="openAddModal = false; openEditModal = false; openAddLaciModal = false; openEditLaciModal = false; editData = {}; laciData = {}">

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 text-gray-700 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Total Rak</span>
                <span class="text-base sm:text-lg font-bold text-gray-900">{{ number_format($statsSummary['total_rak']) }} Rak</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 text-gray-700 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Total Laci</span>
                <span class="text-base sm:text-lg font-bold text-gray-900">{{ number_format($statsSummary['total_laci']) }} Tingkat</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 text-gray-700 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Judul Terdata</span>
                <span class="text-base sm:text-lg font-bold text-gray-900">{{ number_format($statsSummary['total_judul']) }} Judul</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 text-gray-700 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Fisik Eksemplar</span>
                <span class="text-base sm:text-lg font-bold text-gray-900">{{ number_format($statsSummary['total_eksemplar']) }} Buku</span>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs space-y-3">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-gray-900">Manajemen Lokasi Rak & Laci Buku</h2>
                <p class="text-xs text-gray-500 mt-0.5">Kelola lokasi rak, laci, tingkat, dan pembagian kategori buku.</p>
            </div>
            <div class="flex items-center gap-2 self-start md:self-auto">
                <button type="button" @click="toggleAll()" class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-semibold rounded-xl border border-gray-200 transition flex items-center gap-1.5" :title="allExpanded ? 'Tutup Seluruh Laci' : 'Buka Seluruh Laci'">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                    <span x-text="allExpanded ? 'Collapse Semua' : 'Expand Semua'"></span>
                </button>
                <button @click="openAddModal = true" class="px-3.5 py-1.5 bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold rounded-xl transition shadow-2xs flex items-center gap-1.5 shrink-0">
                    <i class="fa-solid fa-plus text-emerald-400"></i>
                    <span>Tambah Rak</span>
                </button>
            </div>
        </div>

        <form action="{{ route('admin.rak') }}" method="GET" class="pt-2 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-12 gap-2 text-xs">
            <div class="sm:col-span-6 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode rak, nama rak, lokasi, atau kategori buku..."
                       class="w-full pl-8 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none">
                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div class="sm:col-span-3">
                <select name="lokasi" onchange="this.form.submit()" class="w-full px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:ring-1 focus:ring-brand-700 focus:outline-none font-medium">
                    <option value="">Semua Lokasi</option>
                    @foreach($lokasiList as $lok)
                        <option value="{{ $lok }}" {{ request('lokasi') === $lok ? 'selected' : '' }}>{{ $lok }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <select name="status" onchange="this.form.submit()" class="w-full px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:ring-1 focus:ring-brand-700 focus:outline-none font-medium">
                    <option value="">Semua Status</option>
                    <option value="berisi" {{ request('status') === 'berisi' ? 'selected' : '' }}>Rak Berisi Buku</option>
                    <option value="kosong" {{ request('status') === 'kosong' ? 'selected' : '' }}>Rak Kosong</option>
                </select>
            </div>

            <div class="sm:col-span-1 flex items-center gap-1">
                <button type="submit" class="w-full py-2 bg-gray-800 hover:bg-gray-900 text-white font-bold text-xs rounded-xl transition flex items-center justify-center">
                    Cari
                </button>
                @if(request()->anyFilled(['search', 'lokasi', 'status']))
                    <a href="{{ route('admin.rak') }}" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-bold transition flex items-center justify-center" title="Reset Filter">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="space-y-4">
        @forelse($rakList as $rak)
            @php
                $totalJudulRak = $rak->buku_count ?? $rak->buku->count();
                $totalEksemplarRak = (int) $rak->buku->sum('total_quantity');
                $totalLaciRak = $rak->laci_count ?? $rak->laci->count();
                $distinctCategoriesRak = $rak->buku->pluck('kategori.nama')->filter()->unique()->values();
            @endphp
            <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs transition duration-200 relative"
                 :class="{ 'z-30': activeMenuId === {{ $rak->id }} }">
                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/50 rounded-2xl"
                     :class="expandedRaks.includes({{ $rak->id }}) ? 'rounded-b-none' : ''">
                    <div class="flex items-start sm:items-center gap-3 min-w-0">
                        <span class="px-2.5 py-1 rounded-lg bg-gray-100 border border-gray-200 text-gray-700 font-mono font-bold text-xs shrink-0 tracking-wide">
                            {{ $rak->kode_rak }}
                        </span>
                        <div class="min-w-0 space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-bold text-sm text-gray-900 truncate">{{ $rak->nama_rak }}</h3>
                                <span class="text-xs text-gray-400 font-medium">•</span>
                                <span class="text-xs text-gray-600 font-medium">
                                    {{ $totalEksemplarRak }} Buku · {{ $totalJudulRak }} Judul · {{ $totalLaciRak }} Laci
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $rak->lokasi ?? 'Lokasi umum perpustakaan' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                        <button type="button" @click="laciData = { rak_id: {{ $rak->id }}, rak_nama: '{{ $rak->nama_rak }}' }; openAddLaciModal = true" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-1.5">
                            <i class="fa-solid fa-plus text-white text-xs"></i>
                            <span>Tambah Laci</span>
                        </button>

                        <button type="button" @click="editData = {{ json_encode([
                            'id' => $rak->id,
                            'kode_rak' => $rak->kode_rak,
                            'nama_rak' => $rak->nama_rak,
                            'lokasi' => $rak->lokasi ?? '',
                            'kategori_id' => $rak->kategori_id
                        ]) }}; openEditModal = true" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl border border-gray-200 transition">
                            Edit
                        </button>

                        <div class="relative" x-data="{ menuOpen: false }">
                            <button type="button" @click="menuOpen = !menuOpen; activeMenuId = menuOpen ? {{ $rak->id }} : null" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-sm border border-gray-200 transition cursor-pointer" aria-label="Menu Opsi Rak">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                            </button>

                            <div x-show="menuOpen" @click.outside="menuOpen = false; if(activeMenuId === {{ $rak->id }}) activeMenuId = null" x-cloak class="absolute right-0 top-full mt-1.5 w-44 bg-white rounded-xl border border-gray-200 shadow-xl py-1 z-50 text-xs font-semibold">
                                <button type="button" @click="menuOpen = false; activeMenuId = null; editData = {{ json_encode([
                                    'id' => $rak->id,
                                    'kode_rak' => $rak->kode_rak,
                                    'nama_rak' => $rak->nama_rak,
                                    'lokasi' => $rak->lokasi ?? '',
                                    'kategori_id' => $rak->kategori_id
                                ]) }}; openEditModal = true" class="w-full px-3 py-2 text-left text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Edit Info Rak</span>
                                </button>
                                <button type="button" @click="menuOpen = false; activeMenuId = null; laciData = { rak_id: {{ $rak->id }}, rak_nama: '{{ $rak->nama_rak }}' }; openAddLaciModal = true" class="w-full px-3 py-2 text-left text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span>Tambah Laci Baru</span>
                                </button>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form action="{{ route('admin.rak.delete', $rak->id) }}" method="POST" onsubmit="return confirmDelete(event, 'Hapus Rak {{ $rak->nama_rak }}?', '{{ $totalJudulRak > 0 ? 'Peringatan: Rak ini masih menampung ' . $totalJudulRak . ' judul buku! Pindahkan buku terlebih dahulu.' : 'Rak beserta susunan lacinya akan dihapus permanen.' }}')">
                                    @csrf
                                    <button type="submit" class="w-full px-3 py-2 text-left text-rose-600 hover:bg-rose-50 flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus Rak</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <button type="button" @click="toggleRak({{ $rak->id }})" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-sm border border-gray-200 transition" :title="expandedRaks.includes({{ $rak->id }}) ? 'Tutup Rincian' : 'Buka Rincian'" aria-label="Toggle Rincian Rak">
                            <svg class="w-4 h-4 transform transition-transform duration-200" :class="expandedRaks.includes({{ $rak->id }}) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                </div>

                <div x-show="expandedRaks.includes({{ $rak->id }})" x-collapse class="p-4 sm:p-5 border-t border-gray-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
                            <span>Laci Rak</span>
                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-mono text-[11px] font-bold">{{ $rak->laci->count() }}</span>
                        </span>
                        @if($distinctCategoriesRak->isNotEmpty())
                            <div class="hidden sm:flex items-center gap-1.5 text-[11px] text-gray-500">
                                <span>Kategori di Rak ini:</span>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($distinctCategoriesRak as $catRak)
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-md font-semibold text-[10px]">{{ $catRak }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @forelse($rak->laci as $laci)
                            @php
                                $categoriesInLaci = $laci->buku->pluck('kategori.nama')->filter()->unique()->values();
                                $totalJudulLaci = $laci->buku->count();
                                $totalEksemplarLaci = (int) $laci->buku->sum('total_quantity');
                            @endphp
                            <div class="p-3.5 rounded-xl border border-gray-200 bg-gray-50/40 hover:bg-white hover:border-gray-300 hover:shadow-2xs transition duration-200 space-y-2.5 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="w-6 h-6 rounded-md bg-gray-200/80 text-gray-800 font-mono font-bold text-xs flex items-center justify-center shrink-0">
                                                {{ str_pad($laci->nomor_laci, 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <h4 class="font-bold text-xs text-gray-900 truncate">{{ $laci->nama_laci }}</h4>
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button type="button" @click="laciData = {{ json_encode([
                                                'id' => $laci->id,
                                                'nomor_laci' => $laci->nomor_laci,
                                                'nama_laci' => $laci->nama_laci,
                                                'keterangan' => $laci->keterangan ?? ''
                                            ]) }}; openEditLaciModal = true" class="w-8 h-8 rounded-lg text-gray-500 hover:text-amber-600 hover:bg-amber-50 flex items-center justify-center transition" title="Edit Laci" aria-label="Edit Laci">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>
                                            <form action="{{ route('admin.rak.laci.delete', $laci->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Hapus Laci {{ $laci->nama_laci }}?', 'Data laci ini akan dihapus dari rak.')">
                                                @csrf
                                                <button type="submit" class="w-8 h-8 rounded-lg text-gray-500 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition" title="Hapus Laci" aria-label="Hapus Laci">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="text-xs space-y-1">
                                        <span class="text-[11px] font-medium text-gray-400 block">Kategori Buku:</span>
                                        <div class="flex flex-wrap gap-1">
                                            @if($categoriesInLaci->isNotEmpty())
                                                @foreach($categoriesInLaci as $cName)
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                                        {{ $cName }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-[11px] text-gray-400 italic">Belum ada buku di laci ini</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-gray-200/70 flex items-center justify-between text-xs">
                                    <span class="text-gray-500 text-[11px] truncate max-w-[140px]" title="{{ $laci->keterangan ?? 'Tingkat ' . $laci->nomor_laci }}">{{ $laci->keterangan ?? 'Tingkat ' . $laci->nomor_laci }}</span>
                                    <span class="font-bold text-gray-900">{{ $totalJudulLaci }} Judul <span class="text-gray-400 font-normal">({{ $totalEksemplarLaci }} Eks)</span></span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-6 px-4 text-center bg-gray-50 rounded-xl border border-dashed border-gray-300 space-y-2">
                                <p class="text-xs font-bold text-gray-700">Belum Ada Laci</p>
                                <p class="text-xs text-gray-500 max-w-sm mx-auto">Rak ini belum memiliki laci. Tambahkan laci untuk mulai mengatur kategori buku.</p>
                                <button type="button" @click="laciData = { rak_id: {{ $rak->id }}, rak_nama: '{{ $rak->nama_rak }}' }; openAddLaciModal = true" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-2xs inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-plus text-white text-xs"></i>
                                    <span>Tambah Laci</span>
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-200 p-10 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto border border-gray-200 font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                @if(request()->anyFilled(['search', 'lokasi', 'status']))
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Tidak Ada Rak Ditemukan</h3>
                        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Rak yang sesuai dengan pencarian atau filter tidak ditemukan.</p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('admin.rak') }}" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs rounded-xl transition shadow-2xs inline-block">
                            Reset Filter
                        </a>
                    </div>
                @else
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Belum Ada Rak</h3>
                        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Belum ada struktur rak yang dibuat. Tambahkan rak pertama untuk mulai mengatur lokasi buku.</p>
                    </div>
                    <div class="pt-2">
                        <button type="button" @click="openAddModal = true" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs rounded-xl transition shadow-2xs inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-plus text-emerald-400"></i>
                            <span>Tambah Rak</span>
                        </button>
                    </div>
                @endif
            </div>
        @endforelse

        @if($rakList->hasPages())
            <div class="p-3">
                {{ $rakList->links() }}
            </div>
        @endif
    </div>

    <div x-show="openAddModal" @click.self="openAddModal = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 p-4 overflow-y-auto"          x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col shadow-xl border border-gray-200 overflow-hidden my-auto">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/80">
                <h3 class="text-sm font-bold text-gray-900">Tambah Rak Buku Baru</h3>
                <button @click="openAddModal = false" class="w-8 h-8 rounded-xl bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm" aria-label="Tutup Modal">&times;</button>
            </div>

            <form action="{{ route('admin.rak.store') }}" method="POST" class="flex-1 overflow-y-auto p-5 space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Kode Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_rak" required placeholder="Contoh: RAK-01" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_rak" required placeholder="Contoh: Rak Kejuruan Rekayasa Perangkat Lunak" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Lokasi Ruang Perpustakaan</label>
                    <input type="text" name="lokasi" placeholder="Contoh: Lantai 1 · Gedung Utama" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Jumlah Laci Awal</label>
                    <input type="number" name="jumlah_laci" value="3" min="1" max="20" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    <p class="text-[11px] text-gray-400 mt-1">Sistem otomatis membuatkan Laci 1, Laci 2, dst. sesuai jumlah ini.</p>
                </div>
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white font-bold rounded-xl transition shadow-2xs">Simpan Rak</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openEditModal" @click.self="openEditModal = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 p-4 overflow-y-auto"          x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col shadow-xl border border-gray-200 overflow-hidden my-auto">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/80">
                <h3 class="text-sm font-bold text-gray-900">Edit Rak Lokasi</h3>
                <button @click="openEditModal = false" class="w-8 h-8 rounded-xl bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm" aria-label="Tutup Modal">&times;</button>
            </div>

            <form :action="'/admin/rak/update/' + editData.id" method="POST" class="flex-1 overflow-y-auto p-5 space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Kode Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_rak" x-model="editData.kode_rak" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_rak" x-model="editData.nama_rak" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Lokasi Ruang Perpustakaan</label>
                    <input type="text" name="lokasi" x-model="editData.lokasi" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white font-bold rounded-xl transition shadow-2xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openAddLaciModal" @click.self="openAddLaciModal = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 p-4 overflow-y-auto"          x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col shadow-xl border border-gray-200 overflow-hidden my-auto">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/80">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Tambah Laci / Tingkat Baru</h3>
                    <p class="text-xs text-gray-500">Menambahkan laci pada <span class="font-semibold text-gray-800" x-text="laciData.rak_nama"></span></p>
                </div>
                <button @click="openAddLaciModal = false" class="w-8 h-8 rounded-xl bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm" aria-label="Tutup Modal">&times;</button>
            </div>

            <form :action="'/admin/rak/' + laciData.rak_id + '/laci'" method="POST" class="flex-1 overflow-y-auto p-5 space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama / Label Laci <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_laci" required placeholder="Contoh: Laci 4 - Tingkat Bawah" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nomor Urut Tingkat (Opsional)</label>
                    <input type="number" name="nomor_laci" min="1" placeholder="Auto nomor berikutnya" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Keterangan / Catatan Penempatan</label>
                    <textarea name="keterangan" rows="2" placeholder="Contoh: Khusus modul kejuruan kelas 10 & 11" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium"></textarea>
                </div>
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                    <button type="button" @click="openAddLaciModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-2xs">Tambah Laci</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openEditLaciModal" @click.self="openEditLaciModal = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 p-4 overflow-y-auto"          x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col shadow-xl border border-gray-200 overflow-hidden my-auto">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/80">
                <h3 class="text-sm font-bold text-gray-900">Edit Data Laci Rak</h3>
                <button @click="openEditLaciModal = false" class="w-8 h-8 rounded-xl bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm" aria-label="Tutup Modal">&times;</button>
            </div>

            <form :action="'/admin/rak/laci/update/' + laciData.id" method="POST" class="flex-1 overflow-y-auto p-5 space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama / Label Laci <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_laci" x-model="laciData.nama_laci" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nomor Urut Tingkat <span class="text-rose-500">*</span></label>
                    <input type="number" name="nomor_laci" x-model="laciData.nomor_laci" required min="1" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Keterangan Penempatan</label>
                    <textarea name="keterangan" x-model="laciData.keterangan" rows="2" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium"></textarea>
                </div>
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                    <button type="button" @click="openEditLaciModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white font-bold rounded-xl transition shadow-2xs">Simpan Laci</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
