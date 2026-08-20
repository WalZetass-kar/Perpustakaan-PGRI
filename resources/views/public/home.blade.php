@extends('layouts.app')

@section('title', 'Beranda - ' . ($pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI'))

@section('content')

<section class="relative bg-gradient-to-r from-brand-900/95 via-brand-800/90 to-red-950/95 text-white min-h-[calc(100vh-5rem)] flex items-center py-16 lg:py-24 overflow-hidden shadow-lg">

    <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-30 pointer-events-none transform scale-105 transition duration-1000 animate-pulse" style="background-image: url('https://smkpgripekanbaru.sch.id/images/pgri.webp');"></div>

    <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none animate-bounce" style="animation-duration: 8s;"></div>
    <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-red-500/10 rounded-full blur-3xl pointer-events-none animate-pulse" style="animation-duration: 6s;"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">

            <div class="lg:col-span-7 space-y-6 text-center lg:text-left" data-aos="fade-right" data-aos-duration="1000">

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight text-white drop-shadow-md">
                    {{ $pengaturan['judul_hero'] ?? 'Sistem Perpustakaan SMK PGRI Pekanbaru (Inlislite)' }}
                </h1>
                <p class="text-sm sm:text-base text-red-100 max-w-xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    {{ $pengaturan['subjudul_hero'] ?? 'Sebuah Perpustakaan Digital Sekolah yang dikembangkan langsung untuk menghimpun koleksi kejuruan, referensi modul, dan pelayanan perpustakaan dalam bentuk digital.' }}
                </p>
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="{{ route('katalog') }}" class="px-7 py-3.5 bg-white text-brand-700 font-extrabold text-xs rounded-xl hover:bg-gray-100 transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center gap-2 group">
                        <span>Buka Katalog OPAC</span>
                        <svg class="w-4 h-4 text-emerald-600 transform group-hover:translate-x-1 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-7 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            Dashboard Pengelola
                        </a>
                    @else
                        <a href="#pusat-data-section" class="px-7 py-3.5 bg-white/10 hover:bg-white/20 border border-white/30 text-white font-extrabold text-xs rounded-xl transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            Pusat Informasi
                        </a>
                    @endauth
                </div>
            </div>

            <div class="lg:col-span-5 flex justify-center" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                <div class="w-full max-w-md bg-white/95 backdrop-blur-md rounded-3xl p-6 text-gray-800 shadow-2xl border border-white/40 relative z-30 transition duration-300">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo SMK PGRI Official" class="w-11 h-11 object-contain drop-shadow-xs">
                        <div>
                            <span class="text-xs font-black text-gray-900 block leading-tight">INLISLITE SMK PGRI</span>
                            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Official School Library Portal</span>
                        </div>
                    </div>

                    <form action="{{ route('katalog') }}" method="GET" class="space-y-3.5" x-data="{
                        searchQuery: '',
                        suggestions: [],
                        loading: false,
                        showDropdown: false,
                        hasSearched: false,
                        fetchSuggestions() {
                            if (this.searchQuery.trim().length < 2) {
                                this.suggestions = [];
                                this.showDropdown = false;
                                this.hasSearched = false;
                                return;
                            }
                            this.loading = true;
                            this.hasSearched = true;
                            fetch('/api/buku/search-suggestions?q=' + encodeURIComponent(this.searchQuery))
                                .then(res => res.json())
                                .then(data => {
                                    this.suggestions = data;
                                    this.showDropdown = true;
                                    this.loading = false;
                                })
                                .catch(() => {
                                    this.loading = false;
                                });
                        }
                    }" @click.outside="showDropdown = false">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Pencarian Koleksi Katalog (OPAC)</label>
                            <div class="relative">
                                <input type="text" name="search" x-model="searchQuery" @input.debounce.200ms="fetchSuggestions()" @focus="if(searchQuery.trim().length >= 2) showDropdown = true" placeholder="Ketik judul buku, penulis, ISBN..." autocomplete="off" class="w-full pl-9 pr-9 py-2.5 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none transition duration-200 font-bold text-gray-900">
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <div x-show="loading" class="absolute right-3 top-3 pointer-events-none" x-cloak>
                                    <svg class="animate-spin w-4 h-4 text-brand-700" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>

                                <div x-show="showDropdown && hasSearched" x-cloak class="absolute left-0 right-0 top-full mt-2 bg-white rounded-2xl border-2 border-gray-200 shadow-2xl z-50 max-h-80 overflow-y-auto p-2 space-y-1.5 text-left text-gray-900">
                                    <div class="px-3 py-1.5 text-[10px] font-black text-gray-400 uppercase tracking-wider flex items-center justify-between border-b border-gray-100">
                                        <span>Rekomendasi Koleksi Buku</span>
                                        <span class="text-emerald-600 font-bold" x-text="suggestions.length + ' Ditemukan'"></span>
                                    </div>
                                    <template x-if="suggestions.length > 0">
                                        <div class="space-y-1">
                                            <template x-for="item in suggestions" :key="item.id">
                                                <a :href="item.detail_url" class="p-2.5 rounded-xl hover:bg-brand-50/70 transition flex items-center gap-3 group border border-transparent hover:border-brand-200">
                                                    <div class="w-10 h-14 bg-gray-100 rounded-lg overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center shadow-xs">
                                                        <template x-if="item.cover_url">
                                                            <img :src="item.cover_url" width="40" height="56" loading="lazy" class="w-full h-full object-cover">
                                                        </template>
                                                        <template x-if="!item.cover_url">
                                                            <div class="w-full h-full bg-brand-700 text-white font-black text-xs flex items-center justify-center" x-text="item.judul.substr(0, 1)"></div>
                                                        </template>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="font-bold text-gray-900 text-xs truncate group-hover:text-brand-700 transition" x-text="item.judul"></p>
                                                        <p class="text-[10px] text-gray-500 truncate" x-text="item.penulis + ' • ' + item.kategori"></p>
                                                        <div class="flex items-center gap-1.5 mt-1 text-[9.5px]">
                                                            <span class="px-1.5 py-0.5 rounded bg-gray-100 font-semibold text-gray-700 border border-gray-200" x-text="item.rak + ' (' + item.laci + ')'"></span>
                                                            <span class="px-1.5 py-0.5 rounded font-black" :class="item.available_quantity > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'" x-text="'Stok: ' + item.available_quantity + ' Eks'"></span>
                                                        </div>
                                                    </div>
                                                    <svg class="w-4 h-4 text-gray-300 group-hover:text-brand-700 shrink-0 transform group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </a>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="suggestions.length === 0 && !loading">
                                        <div class="py-6 px-4 text-center">
                                            <p class="text-xs font-bold text-gray-700">Tidak ada buku ditemukan</p>
                                            <p class="text-[11px] text-gray-400 mt-0.5">Coba kata kunci judul, pengarang, atau nomor ISBN lain</p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 bg-brand-700 text-white font-extrabold text-xs rounded-xl hover:bg-brand-800 transition duration-300 shadow-md hover:shadow-lg transform active:scale-95 flex items-center justify-center gap-2">
                            <span>Cari Katalog Sekarang</span>
                            <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<section id="pusat-data-section" class="bg-white text-gray-800 py-16 lg:py-24 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-10">

        <div class="space-y-3" data-aos="fade-up">
            <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold bg-brand-50 text-brand-700 shadow-xs border border-brand-200">
                Pusat Data dan Informasi
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-gray-900">
                {{ $pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI Pekanbaru' }}
            </h2>
            <p class="text-xs sm:text-sm text-gray-600 max-w-2xl mx-auto leading-relaxed font-normal">
                Sistem perpustakaan digital terpadu untuk pencarian koleksi buku kejuruan, peminjaman, keanggotaan hingga pelayanan literasi sekolah.
            </p>
        </div>

        <div class="bg-gray-50 border-2 border-gray-200/80 rounded-3xl p-6 lg:p-10 text-left max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-center shadow-sm" data-aos="fade-up" data-aos-delay="200">

            <div class="lg:col-span-6 bg-white border-2 border-gray-200/80 rounded-2xl p-6 shadow-sm">
                <div class="grid grid-cols-2 gap-4 text-xs font-bold text-white mb-4">
                    <div class="bg-brand-700 p-4 rounded-xl shadow-xs">
                        <span class="text-[10px] text-brand-100 font-normal block">Total Judul Koleksi</span>
                        <span class="text-2xl font-black">{{ number_format($stats['total_koleksi']) }} Judul</span>
                    </div>
                    <div class="bg-emerald-600 p-4 rounded-xl shadow-xs">
                        <span class="text-[10px] text-emerald-100 font-normal block">Buku Siap Pinjam</span>
                        <span class="text-2xl font-black">{{ number_format($stats['buku_tersedia']) }} Eks</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 text-xs font-bold text-gray-800">
                    <div class="bg-amber-50 border border-amber-200 p-3.5 rounded-xl">
                        <span class="text-[10px] text-amber-700 font-medium block">Sedang Dipinjam</span>
                        <span class="text-lg font-black text-amber-900">{{ number_format($stats['sedang_dipinjam']) }} Buku</span>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 p-3.5 rounded-xl">
                        <span class="text-[10px] text-blue-700 font-medium block">Total Pengelola</span>
                        <span class="text-lg font-black text-blue-900">{{ number_format($stats['anggota_aktif']) }} Akun</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 space-y-4">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900">
                    Layanan Perpustakaan Terpadu
                </h3>
                <p class="text-xs text-gray-600 leading-relaxed font-normal">
                    Pengelolaan katalog buku otomatis terpusat di server lokal sekolah dengan penataan lokasi rak, laci fleksibel, barcode eksemplar, dan rekapitulasi data sirkulasi siswa.
                </p>
                <ul class="space-y-2.5 text-xs text-gray-700 font-medium">
                    <li class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs shrink-0">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span>Pencarian buku cepat OPAC dengan penunjuk lokasi nomor rak & laci.</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs shrink-0">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span>Sirkulasi peminjaman siswa cepat dan pencatatan instan.</span>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</section>

<section class="py-16 lg:py-24 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

        <div class="text-center space-y-2" data-aos="fade-up">
            <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold bg-brand-50 text-brand-700 shadow-xs border border-brand-200">
                Katalog Pilihan
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Koleksi Terpopuler</h2>
            <p class="text-xs sm:text-sm text-gray-600 max-w-xl mx-auto">
                Buku-buku kejuruan dan referensi yang paling banyak diakses di {{ $pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI Pekanbaru' }}.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
            @forelse($buku_populer as $index => $buku)
                @php
                    $eksemplarTersedia = $buku->available_quantity;
                    $totalEksemplar = $buku->total_quantity;
                @endphp
                <div class="bg-white rounded-3xl border-2 border-gray-200/80 p-5 shadow-xs hover:shadow-xl hover:border-brand-700 transition duration-500 transform hover:-translate-y-1 flex flex-col justify-between group" data-aos="fade-up" data-aos-delay="{{ 100 * (($index % 3) + 1) }}">

                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-brand-50 text-brand-700 border border-brand-200 uppercase tracking-wider">
                                {{ $buku->kategori->nama ?? 'Kejuruan' }}
                            </span>
                            @if($buku->rak)
                                <span class="text-[10px] font-mono font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded-md border border-gray-200 inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>{{ $buku->rak->kode_rak }} • {{ $buku->laci->nama_laci ?? 'Laci 1' }}</span>
                                </span>
                            @endif
                        </div>

                        <div class="flex gap-4 items-start">
                            <div class="w-20 h-28 bg-gradient-to-br from-brand-900 via-brand-800 to-red-950 text-white font-black rounded-2xl flex items-center justify-center shrink-0 shadow-md group-hover:scale-105 transition duration-300 border border-brand-700 overflow-hidden relative">
                                @if($buku->cover_url)
                                    <img src="{{ $buku->cover_thumb_url }}" alt="{{ $buku->judul }}" width="80" height="112" loading="lazy" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full p-2 border-l-[3px] border-amber-400/50 flex flex-col justify-between select-none relative overflow-hidden text-center">
                                        <span class="text-[6.5px] font-black text-amber-300/90 uppercase tracking-wider">{{ substr($buku->kategori->nama ?? 'Buku', 0, 8) }}</span>
                                        <div class="my-auto">
                                            <i class="fa-solid fa-book text-white/30 text-xs mb-0.5"></i>
                                            <p class="text-[8px] font-bold text-white line-clamp-2 leading-tight">{{ $buku->judul }}</p>
                                        </div>
                                        <span class="text-[6px] font-bold text-white/50 uppercase">SMK PGRI</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0 space-y-1.5">
                                <h3 class="text-xs sm:text-sm font-black text-gray-900 line-clamp-2 leading-snug group-hover:text-brand-700 transition">
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

                    <div class="pt-4 mt-4 border-t border-gray-100 flex items-center justify-between">
                        <div>
                            @if($eksemplarTersedia > 0)
                                <span class="text-[10px] font-bold text-emerald-600 flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>{{ $eksemplarTersedia }} / {{ $totalEksemplar }} Tersedia</span>
                                </span>
                            @else
                                <span class="text-[10px] font-bold text-rose-500 flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                    <span>Sedang Dipinjam</span>
                                </span>
                            @endif
                        </div>

                        <a href="{{ route('buku.detail', $buku->id) }}" class="inline-flex items-center gap-1 text-[11px] font-extrabold text-brand-700 hover:text-brand-800 transition">
                            <span>Detail</span>
                            <span class="transform group-hover:translate-x-1 transition duration-300">&rarr;</span>
                        </a>
                    </div>

                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-gray-50 rounded-3xl border border-gray-200 space-y-3">
                    <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <p class="text-xs font-bold text-gray-600">Belum ada koleksi buku di pangkalan data.</p>
                </div>
            @endforelse
        </div>

        <div class="text-center pt-4">
            <a href="{{ route('katalog') }}" class="inline-flex items-center gap-2 px-7 py-3 bg-brand-700 text-white font-extrabold text-xs rounded-xl hover:bg-brand-800 transition duration-300 shadow-md hover:shadow-lg transform active:scale-95">
                <span>Lihat Seluruh Katalog Buku</span>
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

    </div>
</section>

<section class="bg-gray-100 py-16 lg:py-20 border-b border-gray-200 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6" data-aos="zoom-in" data-aos-duration="800">
        <h2 class="text-2xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
            Kemudahan Layanan Perpustakaan Sekolah
        </h2>
        <p class="text-xs sm:text-sm text-gray-600 max-w-xl mx-auto">
            Akses sistem informasi terpadu <strong class="text-gray-900">{{ $pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI Pekanbaru' }}</strong> kapan saja dan di mana saja.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4 pt-2 text-xs">
            <a href="{{ route('katalog') }}" class="px-6 py-3 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 transition duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Buka Katalog Koleksi Lengkap</span>
            </a>
            <a href="#pusat-data-section" class="px-6 py-3 bg-white border border-gray-300 rounded-xl text-gray-800 font-bold hover:bg-gray-50 transition duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1 flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Informasi Layanan Perpustakaan</span>
            </a>
        </div>
    </div>
</section>

@endsection
