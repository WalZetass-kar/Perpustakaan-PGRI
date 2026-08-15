@extends('layouts.dashboard')

@section('title', 'Temukan Buku & Pelacak Lokasi Rak')
@section('page_heading', 'Temukan Buku & Pelacak Lokasi')

@section('content')
<div class="space-y-5">

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Judul Koleksi</span>
                <span class="text-base sm:text-lg font-bold text-gray-900">{{ number_format($metrics['total_koleksi']) }} Judul</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Buku Siap Pinjam</span>
                <span class="text-base sm:text-lg font-bold text-emerald-700">{{ number_format($metrics['buku_tersedia']) }} Eks</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Sedang Dipinjam</span>
                <span class="text-base sm:text-lg font-bold text-amber-900">{{ number_format($metrics['sedang_pinjam']) }} Eks</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 text-gray-700 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Struktur Rak</span>
                <span class="text-base sm:text-lg font-bold text-gray-900">{{ $metrics['total_rak'] }} Rak ({{ $metrics['total_laci'] }} Laci)</span>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200 shadow-2xs space-y-3.5">
        <div>
            <h2 class="text-sm font-bold text-gray-900">Pencarian & Pelacak Lokasi Buku</h2>
            <p class="text-xs text-gray-500 mt-0.5">Cari letak nomor rak, nomor laci, tingkat penyimpanan, dan status sirkulasi buku secara lengkap.</p>
        </div>

        <form action="{{ route('admin.temukan-buku') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-2 text-xs">
            <div class="sm:col-span-5 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul buku, ISBN, penulis, kode rak, atau nama laci..."
                       class="w-full pl-8 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div class="sm:col-span-3">
                <select name="kategori_id" onchange="this.form.submit()" class="w-full px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:ring-1 focus:ring-brand-700 focus:outline-none font-medium">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <select name="rak_id" onchange="this.form.submit()" class="w-full px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:ring-1 focus:ring-brand-700 focus:outline-none font-medium">
                    <option value="">Semua Lemari Rak</option>
                    @foreach($rakList as $rk)
                        <option value="{{ $rk->id }}" {{ request('rak_id') == $rk->id ? 'selected' : '' }}>{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-1.5">
                <select name="status_stok" onchange="this.form.submit()" class="w-full px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:ring-1 focus:ring-brand-700 focus:outline-none font-medium">
                    <option value="">Status Stok</option>
                    <option value="tersedia" {{ request('status_stok') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="penuh" {{ request('status_stok') === 'penuh' ? 'selected' : '' }}>Stok Penuh</option>
                    <option value="habis" {{ request('status_stok') === 'habis' ? 'selected' : '' }}>Stok Habis</option>
                </select>

                <button type="submit" class="p-2 bg-brand-700 hover:bg-brand-800 text-white rounded-xl font-bold transition flex items-center justify-center shrink-0" title="Cari">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>

                @if(request()->anyFilled(['search', 'kategori_id', 'rak_id', 'status_stok']))
                    <a href="{{ route('admin.temukan-buku') }}" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-bold transition flex items-center justify-center shrink-0" title="Reset Filter">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        @forelse($bukuList as $buku)
            @php
                $isAvailable = $buku->available_quantity > 0;
                $isFull = $buku->available_quantity === $buku->total_quantity;
                $borrowedCount = max(0, $buku->total_quantity - $buku->available_quantity);
            @endphp
            <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs hover:border-gray-300 transition duration-200 p-4 sm:p-5 flex flex-col justify-between space-y-4">
                <div class="space-y-3.5">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-200 uppercase tracking-wide">
                            {{ $buku->kategori->nama ?? 'Kejuruan' }}
                        </span>

                        @if($isFull && $buku->total_quantity > 0)
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Tersedia Penuh ({{ $buku->available_quantity }} Eks)</span>
                            </span>
                        @elseif($isAvailable)
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                <span>Sebagian Dipinjam ({{ $buku->available_quantity }} Sisa)</span>
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                <span>Stok Habis (Dipinjam Semua)</span>
                            </span>
                        @endif
                    </div>

                    <div class="flex items-start gap-3.5">
                        <div class="w-16 h-22 sm:w-20 sm:h-28 bg-gray-100 rounded-xl overflow-hidden border border-gray-200 shrink-0 flex items-center justify-center relative shadow-2xs">
                            @if($buku->cover_url)
                                <img src="{{ $buku->cover_url }}" alt="{{ $buku->judul }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full p-2 bg-stone-50 border-l-2 border-brand-700 flex flex-col justify-between text-left select-none">
                                    <span class="text-[8px] font-bold text-brand-800 uppercase">{{ substr($buku->kategori->nama ?? 'Buku', 0, 10) }}</span>
                                    <p class="text-[9px] font-black text-gray-900 line-clamp-3 leading-tight">{{ $buku->judul }}</p>
                                    <span class="text-[7px] font-mono text-gray-400">SMK PGRI</span>
                                </div>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1 space-y-1">
                            <h3 class="font-bold text-xs sm:text-sm text-gray-900 leading-snug">
                                <a href="{{ route('buku.detail', $buku->id) }}" target="_blank" class="hover:text-brand-700 transition" title="Buka Detail">
                                    {{ $buku->judul }}
                                </a>
                            </h3>
                            <p class="text-[11px] text-gray-600">
                                Penulis: <strong class="text-gray-900 font-semibold">{{ $buku->penulis->nama ?? '-' }}</strong>
                            </p>
                            <p class="text-[10px] text-gray-500 font-medium">
                                Penerbit: {{ $buku->penerbit->nama ?? '-' }} • Thn: {{ $buku->tahun_terbit ?? '-' }}
                            </p>
                            <p class="text-[10px] font-mono text-gray-400">
                                ISBN: {{ $buku->isbn ?? 'Tidak Tercatat' }}
                            </p>
                        </div>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 space-y-1.5 text-xs">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block">Panduan Lokasi Rak & Laci</span>
                        <div class="flex flex-wrap items-center gap-1.5 text-[11px] font-medium text-gray-800">
                            <span class="px-2 py-0.5 bg-white rounded-md border border-gray-200 font-bold text-gray-700 inline-flex items-center gap-1">
                                <svg class="w-3 h-3 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $buku->rak->lokasi ?? 'Lantai 1 Perpustakaan' }}</span>
                            </span>
                            <span class="text-gray-400">&rarr;</span>
                            <span class="px-2 py-0.5 bg-brand-50 text-brand-800 rounded-md border border-brand-200 font-bold inline-flex items-center gap-1">
                                <svg class="w-3 h-3 text-brand-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <span>{{ $buku->rak->nama_rak ?? 'Rak Umum' }} ({{ $buku->rak->kode_rak ?? '-' }})</span>
                            </span>
                            <span class="text-gray-400">&rarr;</span>
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 rounded-md border border-emerald-200 font-bold inline-flex items-center gap-1">
                                <svg class="w-3 h-3 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                <span>{{ $buku->laci->nama_laci ?? ($buku->rak ? 'Laci 1' : 'Tingkat Standar') }}</span>
                            </span>
                        </div>
                        @if($buku->laci && $buku->laci->keterangan)
                            <p class="text-[10px] text-gray-500 italic">Catatan laci: {{ $buku->laci->keterangan }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="p-2 rounded-xl bg-gray-50 border border-gray-200">
                            <span class="text-[10px] font-semibold text-gray-400 uppercase block">Total Fisik</span>
                            <span class="font-bold text-gray-900 text-xs mt-0.5 block">{{ $buku->total_quantity }} Eks</span>
                        </div>
                        <div class="p-2 rounded-xl bg-emerald-50 border border-emerald-200">
                            <span class="text-[10px] font-semibold text-emerald-600 uppercase block">Siap Pinjam</span>
                            <span class="font-bold text-emerald-800 text-xs mt-0.5 block">{{ $buku->available_quantity }} Eks</span>
                        </div>
                        <div class="p-2 rounded-xl bg-amber-50 border border-amber-200">
                            <span class="text-[10px] font-semibold text-amber-600 uppercase block">Dipinjam</span>
                            <span class="font-bold text-amber-900 text-xs mt-0.5 block">{{ $borrowedCount }} Eks</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2 text-xs">
                    <a href="{{ route('buku.detail', $buku->id) }}" target="_blank" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition flex items-center gap-1 text-[11px]">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Lihat di OPAC</span>
                    </a>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.buku', ['search' => $buku->isbn ?? $buku->judul]) }}" class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold rounded-xl border border-gray-200 transition text-[11px]">
                            Katalog
                        </a>
                        <a href="{{ route('admin.peminjaman') }}" class="px-3.5 py-1.5 bg-brand-700 hover:bg-brand-800 text-white font-bold rounded-xl transition shadow-2xs flex items-center gap-1 text-[11px]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Catat Pinjam</span>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-gray-200 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto border border-gray-200 font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Tidak Ada Buku Ditemukan</h3>
                    <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Tidak ada data koleksi buku atau letak rak yang sesuai dengan pencarian atau filter Anda.</p>
                </div>
                <div class="pt-1">
                    <a href="{{ route('admin.temukan-buku') }}" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs rounded-xl transition shadow-2xs inline-block">
                        Reset Pencarian
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($bukuList->hasPages())
        <div class="p-3 bg-white rounded-2xl border border-gray-200 shadow-2xs">
            {{ $bukuList->links() }}
        </div>
    @endif

</div>
@endsection
