@extends('layouts.app')

@section('title', 'Katalog Buku Perpustakaan')

@section('content')
<div class="bg-white border-b border-gray-200 py-6 mb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Katalog Koleksi Buku Sekolah</h1>
        <p class="text-xs text-gray-500 mt-1">Pusat pencarian buku teks kejuruan, referensi modul, dan literatur umum</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Filter Panel -->
        <div class="lg:col-span-1 space-y-6">
            <form action="{{ route('katalog') }}" method="GET" class="bg-white p-5 rounded-xl border border-gray-200 shadow-2xs space-y-4">
                <h2 class="text-xs font-bold text-gray-900 border-b border-gray-100 pb-2 uppercase tracking-wider">Filter Pencarian Buku</h2>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kata Kunci</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul, Penulis, ISBN..."
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
                    <select name="kategori_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-brand-700 focus:outline-none">
                        <option value="">Semua Kategori</option>
                        @foreach($kategori_list as $kat)
                            <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Penulis</label>
                    <select name="penulis_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-brand-700 focus:outline-none">
                        <option value="">Semua Penulis</option>
                        @foreach($penulis_list as $pen)
                            <option value="{{ $pen->id }}" {{ request('penulis_id') == $pen->id ? 'selected' : '' }}>{{ $pen->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Lokasi Rak</label>
                    <select name="rak_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-brand-700 focus:outline-none">
                        <option value="">Semua Rak</option>
                        @foreach($rak_list as $rk)
                            <option value="{{ $rk->id }}" {{ request('rak_id') == $rk->id ? 'selected' : '' }}>{{ $rk->kode_rak }} ({{ $rk->nama_rak }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tahun Terbit</label>
                    <select name="tahun" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-brand-700 focus:outline-none">
                        <option value="">Semua Tahun</option>
                        @foreach($tahun_list as $th)
                            <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>{{ $th }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status Ketersediaan</label>
                    <select name="status" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-brand-700 focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia Dipinjam</option>
                        <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Urutan Hasil</label>
                    <select name="sort" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-brand-700 focus:outline-none">
                        <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                        <option value="populer" {{ request('sort') == 'populer' ? 'selected' : '' }}>Paling Populer</option>
                        <option value="judul_asc" {{ request('sort') == 'judul_asc' ? 'selected' : '' }}>Judul (A - Z)</option>
                        <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                    </select>
                </div>

                <div class="pt-2 flex items-center gap-2">
                    <button type="submit" class="w-full py-2 bg-brand-700 text-white font-bold text-xs rounded-lg hover:bg-brand-800 transition">Terapkan Filter</button>
                    <a href="{{ route('katalog') }}" class="px-3 py-2 bg-gray-100 text-gray-700 font-medium text-xs rounded-lg hover:bg-gray-200 transition shrink-0">Reset</a>
                </div>
            </form>
        </div>

        <!-- Book Grid Showcase with Loading Skeleton -->
        <div class="lg:col-span-3" x-data="{ isLoading: true }" x-init="setTimeout(() => isLoading = false, 350)">
            
            <!-- Book Cards Skeleton -->
            <div x-show="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 animate-pulse" x-cloak>
                @for($i=0; $i<6; $i++)
                    <div class="bg-white rounded-2xl border-2 border-gray-200 p-4 space-y-4">
                        <div class="flex gap-4">
                            <div class="w-20 h-28 bg-gray-200 rounded-xl shrink-0"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-3 w-16 bg-gray-200 rounded-lg"></div>
                                <div class="h-4 w-full bg-gray-300 rounded-lg"></div>
                                <div class="h-3 w-24 bg-gray-100 rounded-lg"></div>
                            </div>
                        </div>
                        <div class="h-8 bg-gray-100 rounded-xl w-full"></div>
                    </div>
                @endfor
            </div>

            <!-- Actual Book Cards Showcase -->
            <div x-show="!isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" x-transition.opacity.duration.300ms>
                @forelse($buku as $item)
                    @php
                        $available = $item->jumlah_tersedia;
                        $totalEx = $item->jumlah_eksemplar;
                    @endphp
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-2xs flex flex-col justify-between hover:border-gray-300 transition">
                        <div class="p-4 flex gap-4">
                            <div class="w-20 h-28 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center shrink-0 text-brand-700 font-extrabold text-xl shadow-2xs">
                                {{ substr($item->judul, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0 space-y-1">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700 truncate max-w-[110px]">
                                        {{ $item->kategori->nama ?? 'Umum' }}
                                    </span>
                                    @if($available > 0)
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-1.5 py-0.5 rounded shrink-0">Tersedia ({{ $available }})</span>
                                    @elseif($totalEx > 0)
                                        <span class="text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-100 px-1.5 py-0.5 rounded shrink-0">Sedang Dipinjam</span>
                                    @else
                                        <span class="text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-100 px-1.5 py-0.5 rounded shrink-0">Tidak Tersedia</span>
                                    @endif
                                </div>
                                <h3 class="text-xs font-bold text-gray-900 line-clamp-2 leading-snug">
                                    <a href="{{ route('buku.detail', $item->id) }}" class="hover:text-brand-700 transition">{{ $item->judul }}</a>
                                </h3>
                                <p class="text-[11px] text-gray-500">Penulis: <span class="text-gray-800 font-medium">{{ $item->penulis->nama ?? '-' }}</span></p>
                                <p class="text-[11px] text-gray-400">Rak: <span class="font-mono text-gray-700 font-bold">{{ $item->rak->kode_rak ?? '-' }}</span></p>
                            </div>
                        </div>
                        <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs">
                            <span class="text-[11px] text-gray-400 font-mono">ISBN: {{ $item->isbn }}</span>
                            <a href="{{ route('buku.detail', $item->id) }}" class="px-3 py-1 bg-brand-700 text-white font-bold text-[11px] rounded hover:bg-brand-800 transition">Lihat Detail</a>
                        </div>
                    </div>
                @empty
                    <!-- Clean & Informative Empty State -->
                    <div class="col-span-full py-12 px-4 text-center bg-white rounded-xl border border-gray-200">
                        <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900">Buku tidak ditemukan</h3>
                        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Tidak ada koleksi buku yang sesuai dengan kriteria filter atau kata kunci pencarian Anda saat ini.</p>
                        <a href="{{ route('katalog') }}" class="inline-block mt-4 px-4 py-2 bg-gray-100 text-gray-700 font-semibold text-xs rounded-lg hover:bg-gray-200 transition">Reset Filter Pencarian</a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $buku->links() }}
            </div>
        </div>

    </div>
</div>
@endsection
