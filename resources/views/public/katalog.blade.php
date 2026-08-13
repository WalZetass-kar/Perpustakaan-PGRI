@extends('layouts.app')

@section('title', 'Katalog Buku Digital OPAC')

@section('content')
<div class="space-y-8 pb-16" x-data="{ openDetailModal: false, modalData: {} }">

    <!-- Top Hero Banner & Quick Search -->
    <div class="bg-gradient-to-r from-brand-900 via-brand-700 to-red-800 text-white rounded-3xl p-6 sm:p-10 shadow-2xl border-2 border-brand-700 relative overflow-hidden">
        <!-- Watermark Background Graphic -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 space-y-6 max-w-4xl">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-400/20 border border-amber-300/40 rounded-full text-amber-300 text-xs font-extrabold uppercase tracking-wider backdrop-blur-sm">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Layanan OPAC Perpustakaan Digital</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black tracking-tight text-white leading-tight">
                    Katalog Koleksi Buku &amp; Modul Kejuruan
                </h1>
                <p class="text-xs sm:text-sm text-red-100 font-medium max-w-2xl">
                    Cari literatur modul pembelajaran kejuruan RPL, TKJ, Otomotif, referensi ujian, dan koleksi umum SMK PGRI Pekanbaru secara instan.
                </p>
            </div>

            <!-- Quick Integrated Search Bar -->
            <form action="{{ route('katalog') }}" method="GET" class="relative max-w-2xl">
                <div class="flex items-center bg-white rounded-2xl p-2 shadow-xl border-2 border-amber-300">
                    <div class="pl-3 pr-2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul buku, penulis, kata kunci modul, atau ISBN..."
                        class="w-full px-2 py-2 text-xs sm:text-sm font-bold text-gray-900 placeholder-gray-400 bg-transparent focus:outline-none">
                    <button type="submit" class="px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-md hover:shadow-lg shrink-0">
                        Cari Buku
                    </button>
                </div>
            </form>

            <!-- Library Statistics Bar -->
            <div class="pt-2 flex flex-wrap items-center gap-4 text-xs font-bold text-red-100">
                <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-xl border border-white/10">
                    <span class="text-amber-300 font-black">{{ $buku->total() }}</span> Judul Ditemukan
                </div>
                <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-xl border border-white/10">
                    <span class="text-amber-300 font-black">{{ count($kategori_list) }}</span> Kategori Kejuruan
                </div>
                <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-xl border border-white/10">
                    <span class="text-amber-300 font-black">{{ count($rak_list) }}</span> Rak Lokasi
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area: Sidebar Filter & Grid Showcase -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Left Sidebar Filter Panel -->
        <div class="lg:col-span-1 space-y-6">
            <form action="{{ route('katalog') }}" method="GET" class="bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-sm space-y-4 text-xs">
                <div class="flex items-center justify-between border-b-2 border-gray-100 pb-3">
                    <h2 class="font-black text-gray-900 uppercase tracking-wider text-xs">Filter Pencarian</h2>
                    @if(request()->anyFilled(['search', 'kategori_id', 'penulis_id', 'rak_id', 'tahun', 'status', 'sort']))
                        <a href="{{ route('katalog') }}" class="text-[10px] font-bold text-rose-600 hover:underline">Reset All</a>
                    @endif
                </div>
                
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Kata Kunci</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul, Penulis, ISBN..."
                        class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl font-medium focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none transition">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Kategori Kejuruan</label>
                    <select name="kategori_id" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl font-bold text-gray-800 focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none transition">
                        <option value="">Semua Kategori</option>
                        @foreach($kategori_list as $kat)
                            <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Penulis Buku</label>
                    <select name="penulis_id" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl font-medium text-gray-800 focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none transition">
                        <option value="">Semua Penulis</option>
                        @foreach($penulis_list as $pen)
                            <option value="{{ $pen->id }}" {{ request('penulis_id') == $pen->id ? 'selected' : '' }}>{{ $pen->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Lokasi Rak Perpustakaan</label>
                    <select name="rak_id" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl font-medium text-gray-800 focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none transition">
                        <option value="">Semua Lokasi Rak</option>
                        @foreach($rak_list as $rk)
                            <option value="{{ $rk->id }}" {{ request('rak_id') == $rk->id ? 'selected' : '' }}>{{ $rk->kode_rak }} ({{ $rk->nama_rak }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Tahun Terbit</label>
                    <select name="tahun" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl font-medium text-gray-800 focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none transition">
                        <option value="">Semua Tahun</option>
                        @foreach($tahun_list as $th)
                            <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>Tahun {{ $th }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Status Ketersediaan</label>
                    <select name="status" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl font-bold text-gray-800 focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none transition">
                        <option value="">Semua Status</option>
                        <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia Dipinjam</option>
                        <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Urutan Hasil</label>
                    <select name="sort" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl font-bold text-gray-800 focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none transition">
                        <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Buku Terbaru</option>
                        <option value="populer" {{ request('sort') == 'populer' ? 'selected' : '' }}>Paling Populer</option>
                        <option value="judul_asc" {{ request('sort') == 'judul_asc' ? 'selected' : '' }}>Judul (A - Z)</option>
                        <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Buku Terlama</option>
                    </select>
                </div>

                <div class="pt-3 flex items-center gap-2">
                    <button type="submit" class="w-full py-3 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-md hover:shadow-lg transform active:scale-95">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('katalog') }}" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition shrink-0">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Book Grid Showcase -->
        <div class="lg:col-span-3 space-y-6" x-data="{ isLoading: true }" x-init="setTimeout(() => isLoading = false, 250)">
            
            <!-- Loading Skeleton Grid -->
            <div x-show="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 animate-pulse" x-cloak>
                @for($i=0; $i<6; $i++)
                    <div class="bg-white rounded-3xl border-2 border-gray-200 p-4 space-y-4">
                        <div class="w-full h-52 bg-gray-200 rounded-2xl"></div>
                        <div class="space-y-2">
                            <div class="h-4 w-20 bg-gray-200 rounded-lg"></div>
                            <div class="h-5 w-full bg-gray-300 rounded-lg"></div>
                            <div class="h-3 w-28 bg-gray-100 rounded-lg"></div>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Actual Book Cards Grid -->
            <div x-show="!isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" x-transition.opacity.duration.300ms>
                @forelse($buku as $item)
                    @php
                        $available = $item->jumlah_tersedia;
                        $totalEx = $item->jumlah_eksemplar;
                        $coverUrl = $item->cover ? asset('storage/' . $item->cover) : null;
                    @endphp

                    <div class="bg-white rounded-3xl border-2 border-gray-200 overflow-hidden shadow-sm hover:shadow-xl hover:border-brand-700 transition duration-300 flex flex-col justify-between group">
                        
                        <div>
                            <!-- Book Cover Image Aspect Box -->
                            <div class="relative w-full h-64 bg-gray-900 overflow-hidden flex items-center justify-center">
                                @if($coverUrl)
                                    <img src="{{ $coverUrl }}" alt="Sampul {{ $item->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-brand-900 via-brand-700 to-red-950 p-6 flex flex-col justify-between text-white relative">
                                        <div class="flex justify-between items-start">
                                            <span class="px-2.5 py-1 bg-amber-400 text-brand-950 text-[10px] font-black uppercase rounded-lg shadow-sm">
                                                {{ $item->kategori->nama ?? 'Kejuruan' }}
                                            </span>
                                            <svg class="w-6 h-6 text-red-300 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-black line-clamp-3 leading-snug text-white tracking-tight">{{ $item->judul }}</h4>
                                            <p class="text-[10px] text-red-200 font-bold mt-1">SMK PGRI Pekanbaru</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Floating Status Badge Overlay -->
                                <div class="absolute top-3 right-3 z-10">
                                    @if($available > 0)
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-emerald-500 text-white shadow-md uppercase tracking-wider">
                                            Tersedia ({{ $available }})
                                        </span>
                                    @elseif($totalEx > 0)
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-blue-600 text-white shadow-md uppercase tracking-wider">
                                            Dipinjam
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-rose-600 text-white shadow-md uppercase tracking-wider">
                                            Stok Habis
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Book Card Content Info -->
                            <div class="p-5 space-y-3">
                                <div class="space-y-1">
                                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold bg-brand-50 text-brand-700 border border-brand-200 uppercase inline-block">
                                        {{ $item->kategori->nama ?? 'Umum' }}
                                    </span>
                                    <h3 class="text-sm font-black text-gray-900 line-clamp-2 leading-snug group-hover:text-brand-700 transition">
                                        <a href="{{ route('buku.detail', $item->id) }}">{{ $item->judul }}</a>
                                    </h3>
                                </div>

                                <div class="space-y-1 text-xs text-gray-600 font-medium border-t border-gray-100 pt-2.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400">Penulis:</span>
                                        <span class="font-bold text-gray-900 truncate max-w-[130px]">{{ $item->penulis->nama ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400">Lokasi Rak:</span>
                                        <span class="font-mono font-bold text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-200">{{ $item->rak->kode_rak ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Bottom Bar & Trigger Modal Button -->
                        <div class="px-5 py-3.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-2">
                            <span class="text-[10px] text-gray-400 font-mono">ISBN: {{ $item->isbn }}</span>
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
                            }; openDetailModal = true" class="px-3.5 py-1.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-sm transform active:scale-95">
                                Lihat Detail
                            </button>
                        </div>

                    </div>
                @empty
                    <!-- Clean Empty State -->
                    <div class="col-span-full py-16 px-6 text-center bg-white rounded-3xl border-2 border-gray-200 space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-red-50 text-brand-700 flex items-center justify-center mx-auto border border-red-200 font-black text-xl">
                            !
                        </div>
                        <div>
                            <h3 class="text-base font-black text-gray-900">Buku Tidak Ditemukan</h3>
                            <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">Tidak ada koleksi modul atau buku yang sesuai dengan kriteria filter pencarian Anda saat ini.</p>
                        </div>
                        <a href="{{ route('katalog') }}" class="inline-block px-5 py-2.5 bg-brand-700 text-white font-extrabold text-xs rounded-xl hover:bg-brand-800 transition shadow-md">
                            Reset Filter Pencarian
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination Bar -->
            <div class="mt-8">
                {{ $buku->links() }}
            </div>
        </div>

    </div>

    <!-- Quick Detail Interactive Modal -->
    <div x-show="openDetailModal" @click.self="openDetailModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-4 overflow-y-auto"
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
                <!-- Cover Column -->
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

                <!-- Info Column -->
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
                            <span class="text-xs font-black text-emerald-600" x-text="modalData.tersedia + ' Eksemplar Tersedia'"></span>
                        </div>
                        @auth
                            <a href="{{ route('mahasiswa.peminjaman') }}" class="px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-md">
                                Ajukan Peminjaman Buku
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-5 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-md">
                                Login Siswa untuk Pinjam
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
