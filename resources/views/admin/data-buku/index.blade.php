@extends('layouts.dashboard')

@section('title', 'Data Buku Lengkap (Read-Only)')
@section('page_heading', 'Data Koleksi Buku')

@section('content')
<div class="space-y-5" x-data="{ 
    openDetailModal: false, 
    detailData: {} 
}">

    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-black text-gray-900">Showcase Data Koleksi Buku</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Eksplorasi visual seluruh master buku, pengarang, penerbit, dan lokasi penempatan fisik</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.buku') }}" class="px-3.5 py-2 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-pen-to-square text-emerald-300"></i>
                <span>Kelola di Master Buku</span>
            </a>
            <a href="{{ route('admin.rak') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-extrabold rounded-xl transition flex items-center gap-1.5">
                <i class="fa-solid fa-layer-group text-gray-500"></i>
                <span>Kelola Rak &amp; Laci</span>
            </a>
        </div>
    </div>

    <div class="p-3.5 sm:p-4 bg-amber-50/80 border-2 border-amber-200/80 rounded-2xl flex items-center gap-3 text-xs text-amber-900 font-medium">
        <div class="w-8 h-8 rounded-xl bg-amber-200/80 text-amber-800 flex items-center justify-center shrink-0 font-bold">
            <i class="fa-solid fa-circle-info text-sm"></i>
        </div>
        <div class="min-w-0 flex-1">
            <p class="font-extrabold text-[11.5px] text-amber-950">Mode Visualisasi Data (Read-Only)</p>
            <p class="text-[10.5px] text-amber-800 leading-relaxed">
                Halaman ini ditujukan untuk melihat seluruh data spesifikasi buku secara cepat tanpa mengubah data. Untuk mengubah rincian judul atau stok, silakan menuju menu <a href="{{ route('admin.buku') }}" class="font-bold underline hover:text-brand-800">Koleksi Buku</a>; untuk mengubah rak dan laci, buka menu <a href="{{ route('admin.rak') }}" class="font-bold underline hover:text-brand-800">Master Rak &amp; Laci</a>.
            </p>
        </div>
    </div>



    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($bukuList as $buku)
            <div @click="detailData = {{ json_encode([
                'id' => $buku->id,
                'judul' => $buku->judul,
                'isbn' => $buku->isbn ?? 'Tanpa ISBN',
                'tahun_terbit' => $buku->tahun_terbit,
                'total_quantity' => $buku->total_quantity,
                'available_quantity' => $buku->available_quantity,
                'penulis' => $buku->penulis->nama ?? 'Penulis Tidak Diketahui',
                'penerbit' => $buku->penerbit->nama ?? 'Penerbit Tidak Diketahui',
                'kategori' => $buku->kategori->nama ?? 'Umum',
                'kode_rak' => $buku->rak->kode_rak ?? '-',
                'nama_rak' => $buku->rak->nama_rak ?? 'Belum Ditentukan',
                'nama_laci' => $buku->laci->nama_laci ?? ($buku->rak ? 'Laci 1' : '-'),
                'lokasi_lengkap' => $buku->lokasi_lengkap,
                'sinopsis' => $buku->sinopsis ?? 'Tidak ada ringkasan sinopsis untuk buku ini.',
                'cover_url' => $buku->cover_url
            ]) }}; openDetailModal = true" class="bg-white rounded-2xl border-2 border-gray-200 hover:border-brand-300 hover:shadow-md transition duration-200 overflow-hidden flex flex-col cursor-pointer group">
                {{-- Cover — fixed height + object-cover, kompatibel landscape & portrait --}}
                <div class="relative w-full h-48 bg-gray-100 overflow-hidden">
                    @if($buku->cover_url)
                        <img src="{{ $buku->cover_url }}"
                             alt="{{ $buku->judul }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-brand-800 to-red-900 flex flex-col items-center justify-center p-4 text-center text-white">
                            <i class="fa-solid fa-book text-3xl opacity-30 mb-2"></i>
                            <span class="text-xs font-bold line-clamp-3 leading-tight">{{ $buku->judul }}</span>
                        </div>
                    @endif

                    {{-- Badge kategori (kiri atas) --}}
                    <div class="absolute top-2.5 left-2.5">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-white/95 text-gray-800 shadow-xs border border-gray-200">
                            {{ $buku->kategori->nama ?? 'Umum' }}
                        </span>
                    </div>

                    {{-- Badge stok (kanan bawah) --}}
                    <div class="absolute bottom-2.5 right-2.5">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black {{ $buku->available_quantity > 0 ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }} shadow-xs">
                            {{ $buku->available_quantity }} / {{ $buku->total_quantity }} Eks
                        </span>
                    </div>
                </div>

                <div class="p-3.5 flex-1 flex flex-col justify-between space-y-2.5 text-xs">
                    <div>
                        <h3 class="font-extrabold text-gray-900 line-clamp-2 leading-snug group-hover:text-brand-700 transition" title="{{ $buku->judul }}">
                            {{ $buku->judul }}
                        </h3>
                        <p class="text-[11px] text-gray-500 font-medium line-clamp-1 mt-0.5">
                            {{ $buku->penulis->nama ?? 'Penulis Tidak Diketahui' }}
                        </p>
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-[10.5px]">
                        <div class="flex items-center gap-1 font-bold text-gray-700 font-mono">
                            <span class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200">{{ $buku->rak->kode_rak ?? '-' }}</span>
                            <span class="text-amber-700 font-sans font-bold">{{ $buku->laci->nama_laci ?? ($buku->rak ? 'Laci 1' : '-') }}</span>
                        </div>
                        <span class="text-brand-700 font-extrabold flex items-center gap-1 text-[11px] group-hover:translate-x-0.5 transition">
                            <span>Detail</span>
                            <i class="fa-solid fa-chevron-right text-[9px]"></i>
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 bg-white rounded-2xl border-2 border-gray-200 text-center text-gray-400">
                <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center mx-auto mb-2 text-gray-400">
                    <i class="fa-solid fa-book-open text-xl"></i>
                </div>
                <p class="text-xs font-bold text-gray-700">Tidak ada buku yang sesuai dengan pencarian</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Coba ubah kata kunci pencarian atau filter kategori</p>
            </div>
        @endforelse
    </div>

    @if($bukuList->hasPages())
        <div class="bg-white p-4 rounded-2xl border-2 border-gray-200 shadow-sm">
            {{ $bukuList->links() }}
        </div>
    @endif

    <div x-show="openDetailModal" @click.self="openDetailModal = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto"          x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-book-bookmark text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Rincian Data Buku Lengkap</h3>
                        <p class="text-[10px] text-gray-500 font-medium">Informasi katalog spesifikasi buku perpustakaan (Read-Only)</p>
                    </div>
                </div>
                <button @click="openDetailModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm">&times;</button>
            </div>

            <div class="flex-1 overflow-y-auto p-5 space-y-4 text-xs">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="w-32 h-44 bg-gray-100 rounded-xl overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center mx-auto sm:mx-0 shadow-xs">
                        <template x-if="detailData.cover_url">
                            <img :src="detailData.cover_url" alt="Cover" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!detailData.cover_url">
                            <div class="w-full h-full bg-brand-700 text-white font-bold flex flex-col items-center justify-center p-2 text-center">
                                <i class="fa-solid fa-book text-2xl opacity-40 mb-1"></i>
                                <span class="text-[10px] line-clamp-2" x-text="detailData.judul"></span>
                            </div>
                        </template>
                    </div>

                    <div class="flex-1 space-y-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-brand-50 text-brand-700 border border-brand-200 inline-block" x-text="detailData.kategori"></span>
                        <h2 class="text-sm font-black text-gray-900 leading-snug" x-text="detailData.judul"></h2>
                        <div class="grid grid-cols-2 gap-2 pt-1 text-[11px]">
                            <div>
                                <span class="text-gray-400 block text-[10px]">Penulis</span>
                                <span class="font-bold text-gray-800" x-text="detailData.penulis"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[10px]">Penerbit</span>
                                <span class="font-bold text-gray-800" x-text="detailData.penerbit"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[10px]">ISBN</span>
                                <span class="font-bold font-mono text-gray-800" x-text="detailData.isbn"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[10px]">Tahun Terbit</span>
                                <span class="font-bold text-gray-800" x-text="detailData.tahun_terbit"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t border-gray-100">
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-200">
                        <span class="text-[10px] font-extrabold text-gray-500 uppercase tracking-wider block mb-1">Lokasi Penyimpanan Fisik</span>
                        <div class="space-y-0.5">
                            <p class="text-xs font-black text-gray-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-layer-group text-brand-700"></i>
                                <span x-text="detailData.nama_rak"></span>
                                <span class="font-mono text-[10px] px-1 rounded bg-gray-200 text-gray-800 font-bold" x-text="detailData.kode_rak"></span>
                            </p>
                            <p class="text-[11px] font-bold text-amber-700 flex items-center gap-1.5">
                                <i class="fa-solid fa-inbox"></i>
                                <span x-text="detailData.nama_laci"></span>
                            </p>
                        </div>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-200">
                        <span class="text-[10px] font-extrabold text-gray-500 uppercase tracking-wider block mb-1">Status Ketersediaan Stok</span>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-black" :class="detailData.available_quantity > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'">
                                <span x-text="detailData.available_quantity + ' / ' + detailData.total_quantity + ' Eks Tersedia'"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1 pt-2 border-t border-gray-100">
                    <span class="text-[10px] font-extrabold text-gray-500 uppercase tracking-wider block">Ringkasan &amp; Sinopsis</span>
                    <p class="text-[11.5px] text-gray-700 leading-relaxed bg-gray-50 p-3 rounded-xl border border-gray-200 font-normal whitespace-pre-line" x-text="detailData.sinopsis"></p>
                </div>
            </div>

            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between shrink-0">
                <span class="text-[10.5px] text-gray-400 font-medium">Read-Only View</span>
                <button type="button" @click="openDetailModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-extrabold rounded-xl text-xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
