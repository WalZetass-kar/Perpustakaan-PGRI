@extends('layouts.app')

@section('title', $buku->judul . ' - ' . ($pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6" x-data="{ openPetaRak: false }">

    <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-xs font-semibold text-gray-500">
        <a href="{{ route('katalog') }}" class="hover:text-brand-700 transition flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Katalog OPAC</span>
        </a>
        <span class="text-gray-300">/</span>
        <a href="{{ route('katalog', ['kategori' => $buku->kategori_id]) }}" class="hover:text-brand-700 transition truncate max-w-[160px]">
            {{ $buku->kategori->nama ?? 'Koleksi Umum' }}
        </a>
        <span class="text-gray-300">/</span>
        <span class="text-gray-900 font-bold truncate max-w-[200px] sm:max-w-xs">{{ $buku->judul }}</span>
    </nav>

    <div class="bg-white border border-gray-200 rounded-3xl p-5 sm:p-8 shadow-2xs space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <div class="lg:col-span-4 flex flex-col items-center text-center space-y-4">
                <div class="w-full max-w-[260px] sm:max-w-[280px] aspect-[3/4] bg-gray-100 rounded-2xl border border-gray-200 overflow-hidden shadow-sm relative flex flex-col justify-between">
                    @if($buku->cover_url)
                        <img src="{{ $buku->cover_url }}" alt="Cover {{ $buku->judul }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full p-5 bg-stone-50 border-l-4 border-brand-700 flex flex-col justify-between text-left select-none">
                            <div class="space-y-1.5">
                                <span class="px-2 py-0.5 rounded bg-brand-50 text-brand-800 text-[10px] font-bold uppercase tracking-wider border border-brand-200 inline-block">
                                    {{ $buku->kategori->nama ?? 'Modul Sekolah' }}
                                </span>
                                <h2 class="text-xs sm:text-sm font-black text-gray-900 line-clamp-4 leading-snug mt-1">
                                    {{ $buku->judul }}
                                </h2>
                            </div>
                            <div class="pt-3 border-t border-gray-200/80 space-y-1">
                                <p class="text-[11px] text-gray-600 font-semibold truncate">{{ $buku->penulis->nama ?? 'Perpustakaan' }}</p>
                                <span class="text-[9px] font-mono text-gray-400 font-bold uppercase block">{{ $pengaturan['nama_sekolah'] ?? 'SMK PGRI PEKANBARU' }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="w-full max-w-[260px] sm:max-w-[280px] space-y-2 text-xs">
                    <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-200 flex items-center justify-between text-[11px]">
                        <span class="font-bold text-gray-500">ISBN:</span>
                        <span class="font-mono font-bold text-gray-800">{{ $buku->isbn ?? 'Tidak Tercatat' }}</span>
                    </div>
                    <div class="text-[11px] text-gray-400 font-medium">
                        Dilihat {{ number_format($buku->view_count) }} kali di katalog
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-6">
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200 uppercase tracking-wide">
                            {{ $buku->kategori->nama ?? 'Koleksi Umum' }}
                        </span>

                        @if($buku->available_quantity == $buku->total_quantity && $buku->total_quantity > 0)
                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Tersedia · {{ $buku->available_quantity }} dari {{ $buku->total_quantity }} buku siap dipinjam</span>
                            </span>
                        @elseif($buku->available_quantity > 0)
                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>Sebagian Tersedia · {{ $buku->available_quantity }} dari {{ $buku->total_quantity }} buku siap dipinjam ({{ $buku->total_quantity - $buku->available_quantity }} dipinjam)</span>
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span>Tidak Tersedia · Semua {{ $buku->total_quantity }} buku sedang dipinjam</span>
                            </span>
                        @endif
                    </div>

                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-gray-900 tracking-tight leading-tight">
                        {{ $buku->judul }}
                    </h1>

                    <p class="text-xs sm:text-sm text-gray-600 font-medium">
                        Penulis: <span class="text-gray-900 font-bold">{{ $buku->penulis->nama ?? 'Penulis Tidak Terdata' }}</span>
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-gray-50 border border-gray-200 rounded-2xl space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-gray-200/80 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-brand-50 text-brand-700 border border-brand-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </span>
                            <div>
                                <h3 class="text-xs sm:text-sm font-black text-gray-900 uppercase tracking-wide">Lokasi Fisik Buku (Wayfinding)</h3>
                                <p class="text-[11px] text-gray-500">Ikuti urutan lantai, lemari rak, dan laci untuk menemukan buku secara fisik.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 bg-white rounded-lg text-gray-800 font-mono font-bold text-[11px] border border-gray-200 shadow-2xs" title="Format: Lantai · Lokasi · Kode Rak · Kode Laci">
                                {{ Str::contains($buku->rak->lokasi ?? '', '2') ? 'L2' : 'L1' }} · {{ $buku->rak->kode_rak ?? 'RAK-01' }} · {{ $buku->laci ? 'L' . str_pad($buku->laci->nomor_laci ?? 1, 2, '0', STR_PAD_LEFT) : 'L01' }}
                            </span>

                            <button type="button" @click="openPetaRak = true" class="px-3 py-1 bg-brand-700 hover:bg-brand-800 text-white font-bold rounded-lg transition text-xs flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                <span>Lihat Peta Rak</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs">
                        <div class="p-3 bg-white rounded-xl border border-gray-200 shadow-2xs space-y-1">
                            <div class="flex items-center gap-1.5 text-gray-500 text-[10px] font-bold uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Lantai &amp; Ruangan</span>
                            </div>
                            <p class="font-black text-gray-900 text-xs sm:text-sm leading-snug">
                                {{ $buku->rak->lokasi ?? 'Lantai 1 Perpustakaan' }}
                            </p>
                            <span class="text-[10px] text-gray-500 block">{{ $pengaturan['nama_perpustakaan'] ?? 'Gedung Perpustakaan' }}</span>
                        </div>

                        <div class="p-3 bg-white rounded-xl border border-gray-200 shadow-2xs space-y-1">
                            <div class="flex items-center gap-1.5 text-brand-700 text-[10px] font-bold uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5 text-brand-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <span>Lemari Rak</span>
                            </div>
                            <p class="font-black text-gray-900 text-xs sm:text-sm leading-snug">
                                {{ $buku->rak->nama_rak ?? 'Rak Umum' }}
                            </p>
                            <span class="inline-block px-1.5 py-0.5 rounded bg-brand-50 text-brand-700 font-mono font-bold text-[10px] border border-brand-200">
                                {{ $buku->rak->kode_rak ?? 'RAK-01' }}
                            </span>
                        </div>

                        <div class="p-3 bg-emerald-50/70 rounded-xl border-2 border-emerald-300 shadow-2xs space-y-1 relative">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5 text-emerald-800 text-[10px] font-bold uppercase tracking-wider">
                                    <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                    <span>Laci Tujuan (Endpoint)</span>
                                </div>
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            </div>
                            <p class="font-black text-emerald-900 text-xs sm:text-sm leading-snug">
                                {{ $buku->laci->nama_laci ?? ($buku->rak ? 'Laci 1' : 'Tingkat Standar') }}
                            </p>
                            <p class="text-[10px] text-emerald-800 font-medium">
                                {{ $buku->laci->keterangan ?? 'Posisi rak penyimpanan fisik aktif' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 bg-gray-50/70 border border-gray-200 rounded-2xl text-xs">
                    <div class="p-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Penerbit</span>
                        <span class="text-xs font-bold text-gray-900 mt-0.5 block truncate">{{ $buku->penerbit->nama ?? '-' }}</span>
                    </div>
                    <div class="p-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Tahun Terbit</span>
                        <span class="text-xs font-bold text-gray-900 mt-0.5 block">{{ $buku->tahun_terbit ?? '-' }}</span>
                    </div>
                    <div class="p-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Total Koleksi</span>
                        <span class="text-xs font-bold text-gray-900 mt-0.5 block">{{ $buku->total_quantity }} Eksemplar</span>
                    </div>
                    <div class="p-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Stok Tersedia</span>
                        <span class="text-xs font-bold text-emerald-700 mt-0.5 block">{{ $buku->available_quantity }} Buku Fisik</span>
                    </div>
                    <div class="p-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Sedang Dipinjam</span>
                        <span class="text-xs font-bold text-amber-800 mt-0.5 block">{{ max(0, $buku->total_quantity - $buku->available_quantity) }} Siswa</span>
                    </div>
                    <div class="p-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Status Sirkulasi</span>
                        <span class="text-xs font-bold {{ $buku->available_quantity > 0 ? 'text-emerald-700' : 'text-rose-600' }} mt-0.5 block">
                            {{ $buku->available_quantity > 0 ? 'Bisa Dipinjam' : 'Menunggu Kembali' }}
                        </span>
                    </div>
                </div>

                <div class="space-y-2">
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Sinopsis &amp; Ringkasan Buku</h3>
                    <div class="text-xs text-gray-600 leading-relaxed bg-gray-50/50 p-4 rounded-2xl border border-gray-200">
                        {{ $buku->sinopsis ?? 'Buku pegangan dan bahan ajar referensi resmi untuk civitas akademika SMK PGRI Pekanbaru.' }}
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 space-y-2 text-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h4 class="font-bold text-gray-900">Informasi &amp; Prosedur Peminjaman Fisik</h4>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        {{ $pengaturan['pesan_sirkulasi'] ?? 'Untuk meminjam buku fisik, silakan langsung menuju meja layanan sirkulasi perpustakaan.' }}
                    </p>
                    <div class="pt-2 border-t border-gray-200/60 flex flex-wrap items-center gap-x-6 gap-y-1 text-[11px] text-gray-500 font-medium">
                        <span>Jam Layanan: <strong class="text-gray-800">{{ $pengaturan['jam_operasional'] ?? 'Senin - Jumat: 07.00 - 15.30 WIB' }}</strong></span>
                        <span>Maksimal Pinjam: <strong class="text-gray-800">{{ $pengaturan['max_buku_pinjam'] ?? 2 }} Buku</strong></span>
                    </div>
                </div>
            </div>

        </div>

        <div class="pt-6 border-t border-gray-100 space-y-4">
            <h3 class="text-sm font-bold text-gray-900">Spesifikasi Metadata Katalog</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border border-gray-200 rounded-xl overflow-hidden">
                    <tbody class="divide-y divide-gray-100">
                        <tr class="bg-gray-50/50">
                            <td class="px-4 py-2.5 font-bold text-gray-500 w-1/3">Judul Lengkap</td>
                            <td class="px-4 py-2.5 text-gray-900 font-semibold">{{ $buku->judul }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 font-bold text-gray-500">Nomor Standar (ISBN)</td>
                            <td class="px-4 py-2.5 text-gray-900 font-mono font-medium">{{ $buku->isbn ?? '-' }}</td>
                        </tr>
                        <tr class="bg-gray-50/50">
                            <td class="px-4 py-2.5 font-bold text-gray-500">Penulis / Pengarang</td>
                            <td class="px-4 py-2.5 text-gray-900 font-medium">{{ $buku->penulis->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 font-bold text-gray-500">Penerbit</td>
                            <td class="px-4 py-2.5 text-gray-900 font-medium">{{ $buku->penerbit->nama ?? '-' }}</td>
                        </tr>
                        <tr class="bg-gray-50/50">
                            <td class="px-4 py-2.5 font-bold text-gray-500">Tahun Terbit</td>
                            <td class="px-4 py-2.5 text-gray-900 font-medium">{{ $buku->tahun_terbit ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 font-bold text-gray-500">Kategori Subjek</td>
                            <td class="px-4 py-2.5 text-gray-900 font-medium">{{ $buku->kategori->nama ?? '-' }}</td>
                        </tr>
                        <tr class="bg-gray-50/50">
                            <td class="px-4 py-2.5 font-bold text-gray-500">Lokasi Lemari Rak</td>
                            <td class="px-4 py-2.5 text-gray-900 font-medium">{{ $buku->rak->nama_rak ?? '-' }} ({{ $buku->rak->kode_rak ?? '-' }})</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 font-bold text-gray-500">Nomor Laci / Tingkat</td>
                            <td class="px-4 py-2.5 text-gray-900 font-medium">{{ $buku->laci->nama_laci ?? ($buku->rak ? 'Laci 1' : '-') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if(isset($relatedBooks) && $relatedBooks->isNotEmpty())
            <div class="pt-6 border-t border-gray-100 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Buku Serupa</h3>
                        <p class="text-xs text-gray-500">Koleksi referensi terkait di kategori {{ $buku->kategori->nama ?? 'yang sama' }}</p>
                    </div>
                    <a href="{{ route('katalog', ['kategori' => $buku->kategori_id]) }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 transition flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($relatedBooks as $rBook)
                        <div class="p-3.5 bg-gray-50/60 hover:bg-white rounded-2xl border border-gray-200 hover:border-brand-700 transition duration-200 space-y-2.5 flex flex-col justify-between group">
                            <div class="space-y-2">
                                <div class="w-full aspect-[3/4] max-h-40 bg-gray-100 rounded-xl overflow-hidden border border-gray-200 relative flex items-center justify-center">
                                    @if($rBook->cover_url)
                                        <img src="{{ $rBook->cover_url }}" alt="{{ $rBook->judul }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full p-3 bg-stone-50 border-l-2 border-brand-700 flex flex-col justify-between text-left select-none">
                                            <span class="text-[9px] font-bold text-brand-800 uppercase">{{ $rBook->kategori->nama ?? 'Umum' }}</span>
                                            <p class="text-[10px] font-bold text-gray-900 line-clamp-3 leading-snug">{{ $rBook->judul }}</p>
                                            <span class="text-[8px] font-mono text-gray-400 uppercase">SMK PGRI</span>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <h4 class="text-xs font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-brand-700 transition">
                                        <a href="{{ route('buku.detail', $rBook->id) }}">{{ $rBook->judul }}</a>
                                    </h4>
                                    <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ $rBook->penulis->nama ?? '-' }}</p>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-gray-200/70 flex items-center justify-between text-[10px]">
                                <span class="font-mono text-gray-600 truncate max-w-[100px] inline-flex items-center gap-1" title="{{ $rBook->rak->kode_rak ?? '' }}">
                                    <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>{{ $rBook->rak->kode_rak ?? 'Umum' }}</span>
                                </span>
                                <span class="font-bold {{ $rBook->available_quantity > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                    {{ $rBook->available_quantity > 0 ? $rBook->available_quantity . ' Eks' : 'Dipinjam' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <div x-show="openPetaRak" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs" @keydown.escape.window="openPetaRak = false">
        <div class="bg-white rounded-3xl border border-gray-200 shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto p-5 sm:p-7 space-y-5" @click.outside="openPetaRak = false">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-brand-50 text-brand-700 border border-brand-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    </span>
                    <div>
                        <h3 class="text-sm sm:text-base font-black text-gray-900">Peta Tata Letak Rak Perpustakaan</h3>
                        <p class="text-xs text-gray-500">{{ $buku->rak->lokasi ?? 'Lantai 1 - Ruang Baca & Referensi' }}</p>
                    </div>
                </div>

                <button type="button" @click="openPetaRak = false" class="p-2 text-gray-400 hover:text-gray-900 rounded-xl hover:bg-gray-100 transition" aria-label="Tutup Modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-4 bg-brand-50/50 border border-brand-200 rounded-2xl flex items-start gap-3 text-xs">
                <div class="w-3 h-3 rounded-full bg-brand-700 shrink-0 mt-0.5"></div>
                <div class="space-y-1">
                    <p class="font-bold text-gray-900">
                        Buku ini berada di: <span class="text-brand-800 underline">{{ $buku->rak->nama_rak ?? 'Rak Terpilih' }} ({{ $buku->rak->kode_rak ?? '-' }})</span>
                    </p>
                    <p class="text-gray-600">
                        Posisi laci target: <strong class="text-emerald-800 font-black">{{ $buku->laci->nama_laci ?? 'Laci 1' }}</strong> ({{ $buku->laci->keterangan ?? 'Tingkat penyimpanan aktif' }})
                    </p>
                </div>
            </div>

            <div class="space-y-3">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Denah Posisi Lemari Rak di Ruangan</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($allRaks ?? [] as $rk)
                        @php
                            $isTargetRak = ($rk->id === $buku->rak_id);
                        @endphp
                        <div class="p-3.5 rounded-2xl border transition {{ $isTargetRak ? 'bg-brand-50/80 border-2 border-brand-700 shadow-sm ring-2 ring-brand-100' : 'bg-gray-50 border-gray-200 opacity-80 hover:opacity-100' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-0.5 rounded font-mono font-bold text-[11px] {{ $isTargetRak ? 'bg-brand-700 text-white' : 'bg-gray-200 text-gray-800' }}">
                                    {{ $rk->kode_rak }}
                                </span>
                                @if($isTargetRak)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-brand-700 text-white uppercase tracking-wider">
                                        Posisi Buku
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs font-bold text-gray-900 leading-snug">{{ $rk->nama_rak }}</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">{{ $rk->lokasi ?? 'Lantai 1' }}</p>

                            <div class="mt-2.5 pt-2 border-t {{ $isTargetRak ? 'border-brand-200' : 'border-gray-200' }} space-y-1">
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block">Tingkat Laci:</span>
                                <div class="flex flex-wrap gap-1">
                                    @forelse($rk->laci as $lc)
                                        @php
                                            $isTargetLaci = ($isTargetRak && $buku->rak_laci_id === $lc->id);
                                        @endphp
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $isTargetLaci ? 'bg-emerald-600 text-white ring-1 ring-emerald-700' : 'bg-white text-gray-700 border border-gray-200' }}">
                                            {{ $lc->nama_laci }}
                                        </span>
                                    @empty
                                        <span class="text-[10px] text-gray-400 italic">Standar</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-500">Perpustakaan SMK PGRI Pekanbaru</span>
                <button type="button" @click="openPetaRak = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl text-xs transition">
                    Tutup Peta
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
