@extends('layouts.app')

@section('title', 'Beranda - ' . ($pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI'))

@section('content')

<section class="relative bg-brand-900 text-white py-14 lg:py-20 overflow-hidden border-b border-brand-800">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

            <div class="lg:col-span-7 space-y-5 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-red-200 text-xs font-bold border border-white/10">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>{{ $pengaturan['nama_sekolah'] ?? 'SMK PGRI Pekanbaru' }}</span>
                </div>
                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight text-white">
                    {{ $pengaturan['judul_hero'] ?? 'Sistem Perpustakaan SMK PGRI Pekanbaru (Inlislite)' }}
                </h1>
                <p class="text-xs sm:text-sm text-red-100 max-w-xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    {{ $pengaturan['subjudul_hero'] ?? 'Sebuah Perpustakaan Digital Sekolah yang dikembangkan langsung untuk menghimpun koleksi kejuruan, referensi modul, dan pelayanan perpustakaan dalam bentuk digital.' }}
                </p>
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 pt-2">
                    <a href="{{ route('katalog') }}" class="px-6 py-3 bg-white text-brand-700 font-extrabold text-xs rounded-xl hover:bg-gray-100 transition duration-200 shadow-sm flex items-center gap-2 group">
                        <span>Buka Katalog OPAC</span>
                        <svg class="w-4 h-4 text-brand-700 transform group-hover:translate-x-1 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-brand-800 hover:bg-brand-950 text-white font-extrabold text-xs rounded-xl transition duration-200 border border-brand-700">
                            Dashboard Pengelola
                        </a>
                    @else
                        <a href="#pusat-data-section" class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-extrabold text-xs rounded-xl transition duration-200">
                            Pusat Informasi
                        </a>
                    @endauth
                </div>
            </div>

            <div class="lg:col-span-5 flex justify-center">
                <div class="w-full max-w-md bg-white rounded-3xl p-6 text-gray-800 shadow-xl border border-gray-200">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo SMK PGRI Official" class="w-11 h-11 object-contain drop-shadow-xs">
                        <div>
                            <span class="text-xs font-black text-gray-900 block leading-tight">INLISLITE SMK PGRI</span>
                            <span class="text-[10px] text-gray-500 font-bold uppercase">{{ $pengaturan['nama_sekolah'] ?? 'SMK PGRI Pekanbaru' }}</span>
                        </div>
                    </div>

                    <form action="{{ route('katalog') }}" method="GET" class="space-y-3" x-data="{
                        searchQuery: '',
                        suggestions: [],
                        loading: false,
                        showDropdown: false,
                        fetchSuggestions() {
                            if (this.searchQuery.trim().length < 2) {
                                this.suggestions = [];
                                this.showDropdown = false;
                                return;
                            }
                            this.loading = true;
                            fetch('/api/buku/search-suggestions?q=' + encodeURIComponent(this.searchQuery))
                                .then(res => res.json())
                                .then(data => {
                                    this.suggestions = data;
                                    this.showDropdown = data.length > 0;
                                    this.loading = false;
                                })
                                .catch(() => { this.loading = false; });
                        }
                    }" @click.outside="showDropdown = false">
                        <label class="block text-xs font-bold text-gray-700">Pencarian Koleksi Katalog (OPAC)</label>
                        <div class="relative group">
                            <input type="text" name="search" x-model="searchQuery" @input.debounce.200ms="fetchSuggestions()" @focus="if(suggestions.length > 0) showDropdown = true" placeholder="Ketik judul buku, penulis, ISBN..." autocomplete="off" class="w-full pl-9 pr-8 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none transition font-medium">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <div x-show="loading" class="absolute right-3 top-3" x-cloak>
                                <svg class="animate-spin w-4 h-4 text-brand-700" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>

                            <div x-show="showDropdown && suggestions.length > 0" x-cloak class="absolute left-0 right-0 top-full mt-2 bg-white rounded-2xl border border-gray-200 shadow-xl z-50 max-h-72 overflow-y-auto p-2 space-y-1.5 text-left">
                                <div class="px-2.5 py-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider flex items-center justify-between border-b border-gray-100">
                                    <span>Rekomendasi Buku</span>
                                    <span class="text-emerald-600 font-bold" x-text="suggestions.length + ' Ditemukan'"></span>
                                </div>
                                <template x-for="item in suggestions" :key="item.id">
                                    <a :href="item.detail_url" class="p-2 rounded-xl hover:bg-gray-50 transition flex items-center gap-2.5 group border border-transparent hover:border-gray-200">
                                        <div class="w-8 h-11 bg-gray-100 rounded-md overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center">
                                            <template x-if="item.cover_url">
                                                <img :src="item.cover_url" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!item.cover_url">
                                                <div class="w-full h-full bg-brand-700 text-white font-bold text-[10px] flex items-center justify-center" x-text="item.judul.substr(0, 1)"></div>
                                            </template>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-bold text-gray-900 text-xs truncate group-hover:text-brand-700" x-text="item.judul"></p>
                                            <p class="text-[10px] text-gray-500 truncate" x-text="item.penulis + ' • ' + item.kategori"></p>
                                            <div class="flex items-center gap-1.5 mt-1 text-[9.5px]">
                                                <span class="px-1.5 py-0.5 rounded bg-gray-100 font-semibold text-gray-700 border border-gray-200" x-text="'📍 ' + item.rak + ' (' + item.laci + ')'"></span>
                                                <span class="px-1.5 py-0.5 rounded font-bold" :class="item.available_quantity > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'" x-text="'Stok: ' + item.available_quantity + ' Eks'"></span>
                                            </div>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-brand-700 text-white font-bold text-xs rounded-xl hover:bg-brand-800 transition duration-200 shadow-sm flex items-center justify-center gap-2">
                            <span>Cari Katalog Sekarang</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<section id="pusat-data-section" class="bg-white text-gray-800 py-14 lg:py-20 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8">

        <div class="space-y-2">
            <span class="inline-block px-3.5 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200">
                Pusat Data dan Informasi
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">
                {{ $pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI Pekanbaru' }}
            </h2>
            <p class="text-xs sm:text-sm text-gray-600 max-w-2xl mx-auto leading-relaxed font-normal">
                Sistem perpustakaan digital terpadu untuk pencarian koleksi buku kejuruan, peminjaman, keanggotaan hingga pelayanan literasi sekolah.
            </p>
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-3xl p-6 lg:p-8 text-left max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 items-center shadow-2xs">

            <div class="lg:col-span-6 bg-white border border-gray-200 rounded-2xl p-5 shadow-2xs">
                <div class="grid grid-cols-2 gap-3 text-xs font-bold text-white mb-3">
                    <div class="bg-brand-700 p-4 rounded-xl">
                        <span class="text-[10px] text-brand-100 font-normal block">Total Judul Koleksi</span>
                        <span class="text-xl font-bold">{{ number_format($stats['total_koleksi']) }} Judul</span>
                    </div>
                    <div class="bg-emerald-600 p-4 rounded-xl">
                        <span class="text-[10px] text-emerald-100 font-normal block">Buku Siap Pinjam</span>
                        <span class="text-xl font-bold">{{ number_format($stats['buku_tersedia']) }} Eks</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 text-xs font-bold text-gray-800">
                    <div class="bg-amber-50 border border-amber-200 p-3 rounded-xl">
                        <span class="text-[10px] text-amber-700 font-medium block">Sedang Dipinjam</span>
                        <span class="text-base font-bold text-amber-900">{{ number_format($stats['sedang_dipinjam']) }} Buku</span>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 p-3 rounded-xl">
                        <span class="text-[10px] text-blue-700 font-medium block">Total Pengelola</span>
                        <span class="text-base font-bold text-blue-900">{{ number_format($stats['anggota_aktif']) }} Akun</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 space-y-3.5">
                <h3 class="text-base sm:text-lg font-bold text-gray-900">
                    Layanan Perpustakaan Terpadu
                </h3>
                <p class="text-xs text-gray-600 leading-relaxed font-normal">
                    Pengelolaan katalog buku otomatis terpusat di server lokal sekolah dengan penataan lokasi rak, laci fleksibel, barcode eksemplar, dan rekapitulasi data sirkulasi siswa.
                </p>
                <ul class="space-y-2 text-xs text-gray-700 font-medium">
                    <li class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                        <span>Pencarian buku cepat OPAC dengan penunjuk lokasi nomor rak & laci.</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                        <span>Sirkulasi peminjaman siswa cepat dan pencatatan instan.</span>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</section>

<section class="py-14 lg:py-20 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <div class="text-center space-y-1.5">
            <span class="inline-block px-3.5 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200">
                Katalog Pilihan
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Koleksi Terpopuler</h2>
            <p class="text-xs sm:text-sm text-gray-600 max-w-xl mx-auto">
                Buku-buku kejuruan dan referensi yang paling banyak diakses di {{ $pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI Pekanbaru' }}.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 text-left">
            @forelse($buku_populer as $index => $buku)
                @php
                    $eksemplarTersedia = $buku->available_quantity;
                    $totalEksemplar = $buku->total_quantity;
                @endphp
                <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-2xs hover:border-brand-700 transition duration-200 flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-200 uppercase tracking-wider">
                                {{ $buku->kategori->nama ?? 'Kejuruan' }}
                            </span>
                            @if($buku->rak)
                                <span class="text-[10px] font-mono font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded border border-gray-200">
                                    📍 {{ $buku->rak->kode_rak }} • {{ $buku->laci->nama_laci ?? 'Laci 1' }}
                                </span>
                            @endif
                        </div>

                        <div class="flex gap-3.5 items-start">
                            <div class="w-18 h-24 bg-gray-100 border border-gray-200 rounded-xl flex items-center justify-center shrink-0 overflow-hidden relative">
                                @if($buku->cover_url)
                                    <img src="{{ $buku->cover_url }}" alt="{{ $buku->judul }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-brand-700 text-white font-black text-lg flex items-center justify-center">
                                        {{ strtoupper(substr($buku->judul, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0 space-y-1">
                                <h3 class="text-xs font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-brand-700 transition">
                                    <a href="{{ route('buku.detail', $buku->id) }}">{{ $buku->judul }}</a>
                                </h3>
                                <p class="text-[11px] text-gray-600 font-medium">
                                    Penulis: <span class="text-gray-900 font-semibold">{{ $buku->penulis->nama ?? '-' }}</span>
                                </p>
                                <p class="text-[10px] text-gray-400 font-mono">
                                    Thn: {{ $buku->tahun_terbit ?? '-' }} | ISBN: {{ $buku->isbn ?? 'Tanpa ISBN' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 mt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                        <div>
                            @if($eksemplarTersedia > 0)
                                <span class="text-[11px] font-bold text-emerald-600">
                                    {{ $eksemplarTersedia }} / {{ $totalEksemplar }} Tersedia
                                </span>
                            @else
                                <span class="text-[11px] font-bold text-rose-500">
                                    Sedang Dipinjam
                                </span>
                            @endif
                        </div>

                        <a href="{{ route('buku.detail', $buku->id) }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-brand-700 hover:text-brand-800 transition">
                            <span>Detail</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-10 text-center bg-gray-50 rounded-2xl border border-gray-200 space-y-2">
                    <p class="text-xs font-bold text-gray-600">Belum ada koleksi buku di pangkalan data.</p>
                </div>
            @endforelse
        </div>

        <div class="text-center pt-2">
            <a href="{{ route('katalog') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand-700 text-white font-bold text-xs rounded-xl hover:bg-brand-800 transition duration-200 shadow-2xs">
                <span>Lihat Seluruh Katalog Buku</span>
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

    </div>
</section>

@endsection
