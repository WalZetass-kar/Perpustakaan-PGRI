@extends('layouts.dashboard')

@section('title', 'Temukan Buku & Pelacak Lokasi Rak')
@section('page_heading', 'Temukan Buku & Pelacak Lokasi')

@section('content')
<div class="space-y-5">

    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-bold space-y-1">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                <span>Terdapat kesalahan pada input formulir:</span>
            </div>
            <ul class="list-disc list-inside font-normal pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200 shadow-2xs">
        <form action="{{ route('admin.temukan-buku') }}" method="GET"
              x-data="{ filterOpen: {{ request()->anyFilled(['kategori_id', 'rak_id', 'status_stok']) ? 'true' : 'false' }} }"
              class="flex flex-wrap lg:flex-nowrap items-stretch lg:items-center gap-2 text-xs">

            {{-- Tombol toggle filter: mobile/tablet saja --}}
            <button type="button" @click="filterOpen = !filterOpen"
                    class="lg:hidden order-1 relative w-10 shrink-0 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 flex items-center justify-center transition"
                    title="Tampilkan/Sembunyikan Filter" aria-label="Toggle Filter">
                <svg class="w-4 h-4 transition-transform duration-200" :class="filterOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                @if(request()->anyFilled(['kategori_id', 'rak_id', 'status_stok']))
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-brand-700 rounded-full border-2 border-white"></span>
                @endif
            </button>

            {{-- Search + tombol cari + reset: selalu tampil --}}
            <div class="order-2 lg:order-3 flex items-center gap-1.5 flex-1 lg:flex-none">
                <div class="relative flex-1 lg:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, ISBN, penulis, kode rak..."
                           class="w-full pl-8 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <button type="submit" class="p-2 bg-brand-700 hover:bg-brand-800 text-white rounded-xl font-bold transition flex items-center justify-center shrink-0" title="Cari">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>

                @if(request()->anyFilled(['search', 'kategori_id', 'rak_id', 'status_stok']))
                    <a href="{{ route('admin.temukan-buku') }}" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-bold transition flex items-center justify-center shrink-0" title="Reset Filter">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>

            {{-- 3 filter: collapsible di mobile/tablet, selalu tampil di desktop --}}
            <div :class="filterOpen ? 'flex' : 'hidden lg:flex'"
                 class="order-3 lg:order-1 w-full lg:w-auto flex-col sm:flex-row items-stretch sm:items-center gap-2 flex-1">
                <select name="kategori_id" onchange="this.form.submit()" class="px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:ring-1 focus:ring-brand-700 focus:outline-none font-medium">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>

                <select name="rak_id" onchange="this.form.submit()" class="px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:ring-1 focus:ring-brand-700 focus:outline-none font-medium">
                    <option value="">Semua Rak</option>
                    @foreach($rakList as $rk)
                        <option value="{{ $rk->id }}" {{ request('rak_id') == $rk->id ? 'selected' : '' }}>{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                    @endforeach
                </select>

                <select name="status_stok" onchange="this.form.submit()" class="px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:ring-1 focus:ring-brand-700 focus:outline-none font-medium">
                    <option value="">Status Stok</option>
                    <option value="tersedia" {{ request('status_stok') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="penuh" {{ request('status_stok') === 'penuh' ? 'selected' : '' }}>Stok Penuh</option>
                    <option value="habis" {{ request('status_stok') === 'habis' ? 'selected' : '' }}>Stok Habis</option>
                </select>
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
                    'kelas'              => $buku->kelas->nama_kelas ?? '',
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
                    'cover_url'          => $buku->cover_card_url ?? '',
                    'katalog_url'        => route('admin.buku', ['search' => $buku->judul]),
                    'pinjam_url'         => route('admin.peminjaman'),
                    'bisa_dipinjam'      => $isAvailable,
                    'badge_class'        => $badgeClass,
                    'dot_class'          => $dotClass,
                    'badge_text'         => $badgeText,
                ]);
            @endphp

            
            <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs hover:shadow-md hover:border-gray-300 transition-all duration-200 flex flex-col overflow-hidden group">

                
                <div class="relative w-full h-48 bg-gray-100 overflow-hidden">
                    @if($buku->cover_url)
                        <img src="{{ $buku->cover_card_url }}"
                             alt="{{ $buku->judul }}"
                             width="300" height="192"
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
                                <p class="text-[9px] text-white/70 font-medium truncate max-w-full mt-1">{{ $buku->penulis->nama ?? 'Penulis' }}</p>
                            </div>
                            <div class="flex items-center justify-between border-t border-white/10 pt-1 text-[7px] font-bold text-white/60 tracking-wider uppercase">
                                <span>PERPUSTAKAAN</span>
                                <span>SEKOLAH</span>
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
                        <div class="flex items-center gap-1 min-w-0">
                            <span class="px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-brand-50 text-brand-700 border border-brand-200 truncate max-w-[70px]">
                                {{ $buku->kategori->nama ?? 'Umum' }}
                            </span>
                            @if($buku->kelas)
                                <span class="px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200 truncate max-w-[60px]" title="Kelas {{ $buku->kelas->nama_kelas }}">
                                    {{ $buku->kelas->nama_kelas }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[9px] font-mono font-semibold text-gray-400 shrink-0">
                            {{ $buku->available_quantity }}/{{ $buku->total_quantity }} Eks
                        </span>
                    </div>

                    
                    <div class="grid grid-cols-2 gap-1 mt-1">
                        <button
                            type="button"
                            onclick="openBookDetail({{ $modalData }})"
                            class="w-full py-1.5 bg-gray-50 hover:bg-brand-700 hover:text-white border border-gray-200 hover:border-brand-700 text-gray-700 text-[10px] font-bold rounded-xl transition-all duration-200 flex items-center justify-center gap-1"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Detail</span>
                        </button>

                        {{-- Buku yang stoknya habis tetap ditampilkan tombolnya, tapi mati:
                             lebih jelas bagi petugas daripada tombol yang hilang begitu saja. --}}
                        <button
                            type="button"
                            @if($isAvailable)
                                onclick="openLoanForm({{ $modalData }})"
                            @else
                                disabled title="Seluruh eksemplar sedang dipinjam"
                            @endif
                            class="w-full py-1.5 text-[10px] font-bold rounded-xl transition-all duration-200 flex items-center justify-center gap-1 border {{ $isAvailable ? 'bg-brand-700 hover:bg-brand-800 text-white border-brand-700' : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed' }}"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Buat Pinjam</span>
                        </button>
                    </div>
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
                        <span class="text-[6.5px] font-bold text-white/60 tracking-widest text-center uppercase border-t border-white/10 pt-0.5">Perpustakaan</span>
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
                        <span id="modal-kelas-badge" class="hidden px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200"></span>
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
            <button type="button" id="modal-pinjam-link" onclick="lanjutkanKeFormulirPinjam()" class="px-3.5 py-1.5 bg-brand-700 hover:bg-brand-800 text-white font-bold rounded-xl transition shadow-2xs text-[11px] flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Catat Pinjam
            </button>
        </div>
    </div>
</div>

{{-- ------------------------------------------------------------------
     Buatkan peminjaman langsung dari hasil pencarian.

     Tidak semua siswa mengajukan sendiri lewat katalog OPAC — banyak yang
     datang ke meja dan minta dibuatkan. Formulir ini menembak endpoint
     sirkulasi yang sama (admin.peminjaman.store), jadi hasilnya langsung
     berstatus `dipinjam` dan muncul di Peminjaman Aktif, bukan mengantre
     sebagai pengajuan yang masih perlu disetujui.
------------------------------------------------------------------- --}}
<div id="loanFormModal" class="fixed inset-0 z-[100] !mt-0 hidden items-center justify-center p-4" role="dialog" aria-modal="true">

    <div onclick="closeLoanForm()" class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-900">Buatkan Peminjaman</span>
            </div>
            <button type="button" onclick="closeLoanForm()" class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.peminjaman.store') }}" method="POST" class="flex-1 overflow-y-auto p-5 space-y-3.5 text-xs">
            @csrf
            <input type="hidden" name="buku_id" id="loan-buku-id" value="">

            {{-- Buku sudah dipilih lewat kartu yang diklik, jadi ditampilkan
                 sebagai keterangan saja — bukan dropdown yang harus dicari ulang. --}}
            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200">
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block">Buku Yang Dipinjam</span>
                <span id="loan-judul" class="text-xs font-black text-gray-900 mt-0.5 block"></span>
                <span id="loan-lokasi" class="text-[10px] text-gray-500 font-medium mt-0.5 block"></span>
                <span id="loan-stok" class="text-[10px] font-bold text-emerald-700 mt-1 block"></span>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Nama Lengkap Siswa / Peminjam <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_peminjam" id="loan-nama" required maxlength="255" placeholder="Nama lengkap siswa"
                       class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Jurusan / Kelas <span class="text-rose-500">*</span></label>
                    <input type="text" name="jurusan" required maxlength="150" placeholder="Contoh: XII RPL 1"
                           class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">NIS / NIP <span class="text-gray-400 font-normal">(Opsional)</span></label>
                    <input type="text" name="nomor_induk" maxlength="50" placeholder="Nomor induk"
                           class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
            </div>

            <div>
                {{-- Wajib, sama seperti pengajuan lewat katalog OPAC: nomor ini
                     satu-satunya cara petugas menagih buku yang jatuh tempo,
                     dan tombol "Hubungi lewat WhatsApp" di halaman sirkulasi
                     ikut mati kalau kosong. --}}
                <label class="block font-bold text-gray-700 mb-1">No. WhatsApp / Telepon <span class="text-rose-500">*</span></label>
                <input type="tel" name="no_wa" id="loan-no-wa" required maxlength="30" placeholder="Contoh: 081234567890"
                       class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Jumlah Buku Dipinjam <span class="text-rose-500">*</span></label>
                {{-- Batas atasnya mengikuti stok buku ini, tapi tidak melewati 10
                     -- batas yang sama dengan yang divalidasi di server. --}}
                <input type="number" name="jumlah" id="loan-jumlah" value="1" required min="1" max="10"
                       class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-brand-700 focus:bg-white focus:outline-none font-black text-brand-700">
                <p id="loan-jumlah-ket" class="text-[10px] text-gray-500 font-medium mt-1"></p>
            </div>

            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[11px] leading-relaxed">
                <p class="font-bold flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Langsung tercatat sebagai peminjaman aktif</span>
                </p>
                <p class="mt-0.5 text-amber-800">Stok buku berkurang saat disimpan dan transaksinya langsung muncul di menu Peminjaman Aktif — tidak perlu disetujui lagi.</p>
            </div>

            <div class="pt-3 border-t border-gray-100 flex justify-end gap-2 shrink-0">
                <button type="button" onclick="closeLoanForm()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 text-xs">Simpan Peminjaman</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let bukuTerbuka = null;

    function openLoanForm(data) {
        document.getElementById('loan-buku-id').value = data.id;
        document.getElementById('loan-judul').textContent  = data.judul;
        document.getElementById('loan-lokasi').textContent =
            data.nama_rak + ' (' + data.kode_rak + ') \u2192 ' + data.nama_laci;
        document.getElementById('loan-stok').textContent =
            'Tersedia ' + data.available + ' dari ' + data.total_quantity + ' eksemplar';

        // Server membatasi maksimal 10 per transaksi. Kalau stoknya kurang dari
        // itu, stoklah yang jadi batas -- supaya petugas tidak mengetik angka
        // yang sudah pasti ditolak.
        const batas = Math.max(1, Math.min(10, data.available));
        const jumlah = document.getElementById('loan-jumlah');
        jumlah.max = batas;
        jumlah.value = 1;
        document.getElementById('loan-jumlah-ket').textContent =
            'Maksimal ' + batas + ' eksemplar untuk satu transaksi.';

        document.getElementById('bookDetailModal').classList.add('hidden');
        document.getElementById('bookDetailModal').classList.remove('flex');

        const modal = document.getElementById('loanFormModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        document.getElementById('loan-nama').focus();
    }

    function closeLoanForm() {
        const modal = document.getElementById('loanFormModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    /** Dari modal detail, lanjut ke formulir untuk buku yang sedang dibuka. */
    function lanjutkanKeFormulirPinjam() {
        if (bukuTerbuka && bukuTerbuka.bisa_dipinjam) {
            openLoanForm(bukuTerbuka);
        }
    }

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
        const kelasBadge = document.getElementById('modal-kelas-badge');
        if (data.kelas) {
            kelasBadge.textContent = 'Kelas ' + data.kelas;
            kelasBadge.classList.remove('hidden');
        } else {
            kelasBadge.classList.add('hidden');
        }
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

        // Diingat supaya tombol "Catat Pinjam" di bawah tahu buku mana yang
        // sedang dibuka, dan bisa dimatikan kalau stoknya memang habis.
        bukuTerbuka = data;
        const tombolPinjam = document.getElementById('modal-pinjam-link');
        tombolPinjam.disabled = !data.bisa_dipinjam;
        tombolPinjam.title = data.bisa_dipinjam ? '' : 'Seluruh eksemplar sedang dipinjam';
        tombolPinjam.className = data.bisa_dipinjam
            ? 'px-3.5 py-1.5 bg-brand-700 hover:bg-brand-800 text-white font-bold rounded-xl transition shadow-2xs text-[11px] flex items-center gap-1'
            : 'px-3.5 py-1.5 bg-gray-100 text-gray-400 font-bold rounded-xl text-[11px] flex items-center gap-1 cursor-not-allowed';

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
        if (e.key !== 'Escape') {
            return;
        }
        // Formulir ditutup lebih dulu: kalau dibuka dari modal detail, yang
        // terlihat di depan mata petugas memang formulirnya.
        if (!document.getElementById('loanFormModal').classList.contains('hidden')) {
            closeLoanForm();
            return;
        }
        closeBookDetail();
    });
</script>
@endpush

@endsection
