@extends('layouts.dashboard')

@section('title', 'Temukan Buku & Pelacak Lokasi Rak')
@section('page_heading', 'Temukan Buku & Pelacak Lokasi')

@section('content')
<div class="space-y-5">

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Judul Koleksi</span>
                <span class="text-base sm:text-lg font-bold text-gray-900">{{ number_format($metrics['total_koleksi']) }} Judul</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Buku Siap Pinjam</span>
                <span class="text-base sm:text-lg font-bold text-emerald-700">{{ number_format($metrics['buku_tersedia']) }} Eks</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Sedang Dipinjam</span>
                <span class="text-base sm:text-lg font-bold text-amber-900">{{ number_format($metrics['sedang_pinjam']) }} Eks</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gray-100 border border-gray-200 text-gray-700 flex items-center justify-center shrink-0">
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
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul buku, ISBN, penulis, kode rak..."
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
                    <option value="">Semua Rak</option>
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

    
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3.5">
        @forelse($bukuList as $buku)
            @php
                $isAvailable  = $buku->available_quantity > 0;
                $isFull       = $buku->available_quantity === $buku->total_quantity && $buku->total_quantity > 0;
                $borrowedCount = max(0, $buku->total_quantity - $buku->available_quantity);

                if ($isFull) {
                    $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    $dotClass   = 'bg-emerald-500';
                    $badgeText  = 'Tersedia';
                } elseif ($isAvailable) {
                    $badgeClass = 'bg-amber-50 text-amber-800 border-amber-200';
                    $dotClass   = 'bg-amber-500';
                    $badgeText  = 'Sebagian';
                } else {
                    $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                    $dotClass   = 'bg-rose-500';
                    $badgeText  = 'Habis';
                }

                $modalData = json_encode([
                    'id'                 => $buku->id,
                    'judul'              => $buku->judul,
                    'isbn'               => $buku->isbn ?? 'Tidak Tercatat',
                    'penulis'            => $buku->penulis->nama ?? '-',
                    'penerbit'           => $buku->penerbit->nama ?? '-',
                    'kategori'           => $buku->kategori->nama ?? '-',
                    'tahun_terbit'       => $buku->tahun_terbit ?? '-',
                    'total_quantity'     => $buku->total_quantity,
                    'available'          => $buku->available_quantity,
                    'dipinjam'           => $borrowedCount,
                    'lokasi'             => $buku->rak->lokasi ?? 'Lantai 1 Perpustakaan',
                    'nama_rak'           => $buku->rak->nama_rak ?? 'Rak Umum',
                    'kode_rak'           => $buku->rak->kode_rak ?? '-',
                    'nama_laci'          => optional($buku->laci)->nama_laci ?? ($buku->rak ? 'Laci 1' : 'Tingkat Standar'),
                    'ket_laci'           => optional($buku->laci)->keterangan ?? '',
                    'keterangan_posisi'  => $buku->keterangan_posisi ?? '',
                    'cover_url'          => $buku->cover_url ?? '',
                    'katalog_url'        => route('admin.buku', ['search' => $buku->isbn ?? $buku->judul]),
                    'pinjam_url'         => route('admin.peminjaman'),
                    'badge_class'        => $badgeClass,
                    'dot_class'          => $dotClass,
                    'badge_text'         => $badgeText,
                ]);
            @endphp

            
            <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs hover:shadow-md hover:border-gray-300 transition-all duration-200 flex flex-col overflow-hidden group">

                
                <div class="relative w-full h-48 bg-gray-100 overflow-hidden">
                    @if($buku->cover_url)
                        <img src="{{ $buku->cover_url }}"
                             alt="{{ $buku->judul }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-brand-900 via-brand-800 to-red-950 text-white p-3.5 border-l-[5px] border-amber-400/50 flex flex-col justify-between select-none relative overflow-hidden shadow-inner">
                            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
                            <div class="flex items-center justify-end">
                                <span class="text-[8px] font-black uppercase tracking-widest text-amber-300/90 bg-black/30 px-2 py-0.5 rounded-md border border-white/10 backdrop-blur-xs">{{ substr($buku->kategori->nama ?? 'Buku', 0, 15) }}</span>
                            </div>
                            <div class="flex flex-col items-center justify-center my-auto text-center px-1">
                                <i class="fa-solid fa-book-bookmark text-white/25 text-2xl mb-1.5 drop-shadow-xs"></i>
                                <p class="text-[11px] font-black text-white leading-tight line-clamp-3 drop-shadow-sm">{{ $buku->judul }}</p>
                                <p class="text-[9px] text-white/70 font-medium truncate max-w-full mt-1">{{ $buku->penulis->nama ?? 'SMK PGRI' }}</p>
                            </div>
                            <div class="flex items-center justify-between border-t border-white/10 pt-1 text-[7px] font-bold text-white/60 tracking-wider uppercase">
                                <span>PERPUSTAKAAN</span>
                                <span>SMK PGRI</span>
                            </div>
                        </div>
                    @endif

                    <div class="absolute top-2 left-2 z-10">
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-bold border {{ $badgeClass }} shadow-xs backdrop-blur-md">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }} shrink-0"></span>
                            {{ $badgeText }}
                        </span>
                    </div>
                </div>

                
                <div class="p-3 flex flex-col gap-2 flex-1">
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] font-black text-gray-900 line-clamp-2 leading-snug">{{ $buku->judul }}</p>
                        <p class="text-[10px] text-gray-500 mt-0.5 truncate">{{ $buku->penulis->nama ?? '-' }}</p>
                    </div>

                    <div class="flex items-center justify-between gap-1">
                        <span class="px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-brand-50 text-brand-700 border border-brand-200 truncate max-w-[70%]">
                            {{ $buku->kategori->nama ?? 'Umum' }}
                        </span>
                        <span class="text-[9px] font-mono font-semibold text-gray-400">
                            {{ $buku->available_quantity }}/{{ $buku->total_quantity }} Eks
                        </span>
                    </div>

                    
                    <button
                        type="button"
                        onclick="openBookDetail({{ $modalData }})"
                        class="w-full mt-1 py-1.5 bg-gray-50 hover:bg-brand-700 hover:text-white border border-gray-200 hover:border-brand-700 text-gray-700 text-[10px] font-bold rounded-xl transition-all duration-200 flex items-center justify-center gap-1"
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Lihat Detail</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-14 text-center bg-white rounded-2xl border border-gray-200 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto border border-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Tidak Ada Buku Ditemukan</h3>
                    <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Tidak ada data koleksi buku yang sesuai dengan pencarian atau filter Anda.</p>
                </div>
                <a href="{{ route('admin.temukan-buku') }}" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs rounded-xl transition shadow-2xs inline-block">
                    Reset Pencarian
                </a>
            </div>
        @endforelse
    </div>

    
    @if($bukuList->hasPages())
        <div class="p-3 bg-white rounded-2xl border border-gray-200 shadow-2xs">
            {{ $bukuList->links() }}
        </div>
    @endif

</div>

<div id="bookDetailModal" class="fixed inset-0 z-[100] !mt-0 hidden items-center justify-center p-4" role="dialog" aria-modal="true">
    
    <div id="modalBackdrop" onclick="closeBookDetail()" class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

    
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

        
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-900">Detail Buku</span>
            </div>
            <button onclick="closeBookDetail()" class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        
        <div class="overflow-y-auto flex-1 p-5 space-y-4">

            
            <div class="flex items-start gap-4">
                <div id="modal-cover-wrap" class="w-24 h-36 rounded-xl overflow-hidden border border-gray-200 shrink-0 bg-gray-100 flex items-center justify-center shadow-md">
                    <img id="modal-cover" src="" alt="" class="w-full h-full object-cover hidden">
                    <div id="modal-cover-placeholder" class="w-full h-full bg-gradient-to-br from-brand-900 via-brand-800 to-red-950 text-white p-2.5 border-l-[4px] border-amber-400/50 flex flex-col justify-between select-none relative overflow-hidden shadow-inner hidden">
                        <span id="modal-cover-kategori" class="text-[7.5px] font-black text-amber-300/90 uppercase tracking-wider text-right"></span>
                        <div class="text-center my-auto px-1">
                            <i class="fa-solid fa-book-bookmark text-white/25 text-xl mb-1"></i>
                            <p id="modal-cover-judul" class="text-[9.5px] font-black text-white line-clamp-3 leading-tight drop-shadow-sm"></p>
                        </div>
                        <span class="text-[6.5px] font-bold text-white/60 tracking-widest text-center uppercase border-t border-white/10 pt-0.5">SMK PGRI</span>
                    </div>
                </div>

                <div class="flex-1 min-w-0 space-y-1.5">
                    <div id="modal-status-badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border">
                        <span id="modal-dot" class="w-1.5 h-1.5 rounded-full shrink-0"></span>
                        <span id="modal-badge-text"></span>
                    </div>
                    <h3 id="modal-judul" class="text-sm font-black text-gray-900 leading-snug"></h3>
                    <p id="modal-penulis" class="text-xs text-gray-600"></p>
                    <p id="modal-penerbit" class="text-[11px] text-gray-500"></p>
                    <div class="flex flex-wrap gap-1.5 pt-0.5">
                        <span id="modal-kategori-badge" class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-200"></span>
                        <span id="modal-tahun" class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 text-gray-600 border border-gray-200"></span>
                    </div>
                </div>
            </div>

            
            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <div>
                    <span class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider block">ISBN</span>
                    <span id="modal-isbn" class="text-[11px] font-mono font-bold text-gray-800"></span>
                </div>
            </div>

            
            <div>
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-2">Informasi Stok</span>
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200">
                        <span class="text-[9px] font-semibold text-gray-400 uppercase block">Total Fisik</span>
                        <span id="modal-total" class="font-bold text-gray-900 text-xs mt-0.5 block"></span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200">
                        <span class="text-[9px] font-semibold text-emerald-600 uppercase block">Siap Pinjam</span>
                        <span id="modal-available" class="font-bold text-emerald-800 text-xs mt-0.5 block"></span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200">
                        <span class="text-[9px] font-semibold text-amber-600 uppercase block">Dipinjam</span>
                        <span id="modal-dipinjam" class="font-bold text-amber-900 text-xs mt-0.5 block"></span>
                    </div>
                </div>
            </div>

            
            <div>
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-2">Panduan Lokasi Rak</span>
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 space-y-2">
                    <div class="flex flex-wrap items-center gap-1.5 text-[11px] font-medium text-gray-800">
                        <span id="modal-lokasi" class="px-2 py-0.5 bg-white rounded-md border border-gray-200 font-bold text-gray-700 inline-flex items-center gap-1">
                            <svg class="w-3 h-3 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span id="modal-lokasi-text"></span>
                        </span>
                        <span class="text-gray-400">&rarr;</span>
                        <span class="px-2 py-0.5 bg-brand-50 text-brand-800 rounded-md border border-brand-200 font-bold inline-flex items-center gap-1">
                            <svg class="w-3 h-3 text-brand-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <span id="modal-rak-text"></span>
                        </span>
                        <span class="text-gray-400">&rarr;</span>
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 rounded-md border border-emerald-200 font-bold inline-flex items-center gap-1">
                            <svg class="w-3 h-3 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            <span id="modal-laci-text"></span>
                        </span>
                    </div>
                    <p id="modal-ket-laci" class="text-[10px] text-gray-500 italic hidden"></p>
                </div>
            </div>

            
            <div id="modal-posisi-wrap" class="hidden">
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-2">Catatan Posisi Buku</span>
                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 flex items-start gap-2">
                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p id="modal-keterangan-posisi" class="text-[11px] text-amber-900 font-medium leading-relaxed"></p>
                </div>
            </div>

        </div>

        
        <div class="px-5 py-3.5 border-t border-gray-100 shrink-0 flex items-center justify-end gap-1.5">
            <a id="modal-katalog-link" href="#" class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold rounded-xl border border-gray-200 transition text-[11px]">Katalog</a>
            <a id="modal-pinjam-link" href="#" class="px-3.5 py-1.5 bg-brand-700 hover:bg-brand-800 text-white font-bold rounded-xl transition shadow-2xs text-[11px] flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Catat Pinjam
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openBookDetail(data) {
        const modal = document.getElementById('bookDetailModal');

        const cover      = document.getElementById('modal-cover');
        const placeholder = document.getElementById('modal-cover-placeholder');
        if (data.cover_url) {
            cover.src = data.cover_url;
            cover.alt = data.judul;
            cover.classList.remove('hidden');
            placeholder.classList.add('hidden');
        } else {
            cover.classList.add('hidden');
            document.getElementById('modal-cover-kategori').textContent = data.kategori.substring(0, 10);
            document.getElementById('modal-cover-judul').textContent    = data.judul;
            placeholder.classList.remove('hidden');
        }

        const badge = document.getElementById('modal-status-badge');
        const dot   = document.getElementById('modal-dot');
        badge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border ' + data.badge_class;
        dot.className   = 'w-1.5 h-1.5 rounded-full shrink-0 ' + data.dot_class;
        document.getElementById('modal-badge-text').textContent = data.badge_text;

        document.getElementById('modal-judul').textContent            = data.judul;
        document.getElementById('modal-penulis').textContent          = 'Penulis: ' + data.penulis;
        document.getElementById('modal-penerbit').textContent         = 'Penerbit: ' + data.penerbit;
        document.getElementById('modal-kategori-badge').textContent   = data.kategori;
        document.getElementById('modal-tahun').textContent            = 'Tahun ' + data.tahun_terbit;
        document.getElementById('modal-isbn').textContent             = data.isbn;

        document.getElementById('modal-total').textContent     = data.total_quantity + ' Eks';
        document.getElementById('modal-available').textContent = data.available + ' Eks';
        document.getElementById('modal-dipinjam').textContent  = data.dipinjam + ' Eks';

        document.getElementById('modal-lokasi-text').textContent = data.lokasi;
        document.getElementById('modal-rak-text').textContent    = data.nama_rak + ' (' + data.kode_rak + ')';
        document.getElementById('modal-laci-text').textContent   = data.nama_laci;
        const ketLaci = document.getElementById('modal-ket-laci');
        if (data.ket_laci) {
            ketLaci.textContent = 'Catatan: ' + data.ket_laci;
            ketLaci.classList.remove('hidden');
        } else {
            ketLaci.classList.add('hidden');
        }

        const posisiWrap = document.getElementById('modal-posisi-wrap');
        const posisiText = document.getElementById('modal-keterangan-posisi');
        if (data.keterangan_posisi && data.keterangan_posisi.trim() !== '') {
            posisiText.textContent = data.keterangan_posisi;
            posisiWrap.classList.remove('hidden');
        } else {
            posisiWrap.classList.add('hidden');
        }

        document.getElementById('modal-katalog-link').href = data.katalog_url;
        document.getElementById('modal-pinjam-link').href  = data.pinjam_url;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeBookDetail() {
        const modal = document.getElementById('bookDetailModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeBookDetail();
    });
</script>
@endpush

@endsection
