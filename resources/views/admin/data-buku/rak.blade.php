@extends('layouts.rak-viewer')

@section('title', 'Laci di Rak ' . $rak->kode_rak)

@section('content')
<div class="space-y-5">

    {{-- Header Bar --}}
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border-2 border-gray-200 shadow-sm space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 rounded-xl bg-brand-800 text-white flex items-center justify-center shrink-0 shadow-sm">
                    <i class="fa-solid fa-layer-group text-sm"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="font-extrabold text-sm text-gray-900 truncate">{{ $rak->nama_rak }}</h2>
                    <p class="text-[11px] text-gray-500 font-medium truncate">
                        <span class="font-mono font-bold text-gray-600">{{ $rak->kode_rak }}</span>
                        <span class="mx-1 text-gray-300">&bull;</span>
                        {{ $rak->lokasi ?? 'Lokasi umum perpustakaan' }}
                    </p>
                </div>
            </div>
            {{-- Halaman ini dibuka di tab baru dari menu Data Buku, jadi tombol
                 ini satu-satunya jalan menutupnya dari dalam aplikasi. Dulu
                 ber-"hidden sm:flex" sehingga lenyap di HP dan petugas terjebak
                 di tab tanpa jalan keluar. Di HP dibuat selebar penuh karena
                 header-nya menumpuk ke bawah; di layar >=640px lebarnya kembali
                 mengikuti isi, persis seperti semula. --}}
            <button type="button" onclick="window.close()" class="flex w-full sm:w-auto justify-center px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-extrabold rounded-xl transition items-center gap-1.5 border border-gray-200 shrink-0 self-center">
                <i class="fa-solid fa-xmark text-gray-500"></i>
                <span>Tutup Tab</span>
            </button>
        </div>

        <div class="pt-3 border-t border-gray-100 flex flex-wrap items-center gap-2 text-[11px] font-semibold text-gray-500">
            <i class="fa-solid fa-circle-info text-gray-400"></i>
            <span>Pilih laci untuk melihat buku yang tersimpan di dalamnya.</span>
            @if($rak->kategori)
                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-brand-50 text-brand-700 border border-brand-200">
                    {{ $rak->kategori->nama }}
                </span>
            @endif
        </div>
    </div>

    {{-- Kartu Laci --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($laciList as $laci)
            @php
                $jumlahJudul  = $laci->buku_count;
                $jumlahStok   = (int) ($laci->buku_sum_total_quantity ?? 0);
                $jumlahSiap   = (int) ($laci->buku_sum_available_quantity ?? 0);
                $laciKosong   = $jumlahJudul === 0;
            @endphp
            <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex items-start justify-between gap-2">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-xl {{ $laciKosong ? 'bg-gray-200 text-gray-500' : 'bg-amber-500 text-white' }} flex items-center justify-center shrink-0 shadow-sm">
                            <i class="fa-solid fa-box-archive text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-extrabold text-sm text-gray-900 truncate" title="{{ $laci->nama_laci }}">{{ $laci->nama_laci }}</h3>
                            <span class="font-mono text-[10.5px] font-bold text-gray-500">Laci ke-{{ $laci->nomor_laci }}</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black shrink-0 {{ $laciKosong ? 'bg-gray-100 text-gray-500 border border-gray-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                        {{ $laciKosong ? 'Kosong' : 'Berisi' }}
                    </span>
                </div>

                <div class="p-4 flex-1 flex flex-col justify-between space-y-3 text-xs">
                    <p class="text-[11px] text-gray-500 font-medium flex items-start gap-1.5">
                        <i class="fa-solid fa-note-sticky text-gray-400 w-3 text-center mt-0.5"></i>
                        <span class="line-clamp-2">{{ $laci->keterangan ?: 'Tidak ada keterangan tambahan' }}</span>
                    </p>

                    <div class="grid grid-cols-3 gap-2 pt-3 border-t border-gray-100 text-center">
                        <div>
                            <span class="block font-extrabold text-sm text-gray-900">{{ $jumlahJudul }}</span>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Judul</span>
                        </div>
                        <div>
                            <span class="block font-extrabold text-sm text-gray-900">{{ $jumlahStok }}</span>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Eksemplar</span>
                        </div>
                        <div>
                            <span class="block font-extrabold text-sm text-emerald-700">{{ $jumlahSiap }}</span>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Tersedia</span>
                        </div>
                    </div>

                    @if($laciKosong)
                        <span class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gray-100 text-gray-400 text-xs font-extrabold rounded-xl cursor-not-allowed">
                            <i class="fa-solid fa-box-open text-[11px]"></i>
                            <span>Belum Ada Buku</span>
                        </span>
                    @else
                        <a href="{{ route('admin.data-buku.laci', [$rak->id, $laci->id]) }}"
                           class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition shadow-sm">
                            <i class="fa-solid fa-eye text-[11px]"></i>
                            <span>Lihat Buku di Laci Ini</span>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border-2 border-dashed border-gray-300 p-10 text-center">
                <i class="fa-solid fa-box-open text-3xl text-gray-300"></i>
                <p class="mt-3 font-extrabold text-sm text-gray-700">Rak ini belum memiliki laci</p>
                <p class="mt-1 text-xs text-gray-500">Tambahkan laci terlebih dahulu melalui menu Rak &amp; Laci Buku.</p>
            </div>
        @endforelse

        {{-- Buku yang kehilangan laci: hanya tampil bila memang ada --}}
        @if($jumlahTanpaLaci > 0)
            <div class="bg-white rounded-2xl border-2 border-amber-300 shadow-sm flex flex-col overflow-hidden">
                <div class="p-4 border-b border-amber-100 flex items-start justify-between gap-2">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-extrabold text-sm text-gray-900 truncate">Belum Ditempatkan di Laci</h3>
                            <span class="text-[10.5px] font-bold text-amber-700">Perlu ditata ulang</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 flex-1 flex flex-col justify-between space-y-3 text-xs">
                    <p class="text-[11px] text-gray-500 font-medium flex items-start gap-1.5">
                        <i class="fa-solid fa-circle-info text-amber-400 w-3 text-center mt-0.5"></i>
                        <span>Buku ini terdaftar di rak, namun laci tempatnya tidak lagi tersedia.</span>
                    </p>

                    <div class="pt-3 border-t border-gray-100 text-center">
                        <span class="block font-extrabold text-sm text-gray-900">{{ $jumlahTanpaLaci }}</span>
                        <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Judul Tanpa Laci</span>
                    </div>

                    <a href="{{ route('admin.data-buku.tanpa-laci', $rak->id) }}"
                       class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-extrabold rounded-xl transition shadow-sm">
                        <i class="fa-solid fa-eye text-[11px]"></i>
                        <span>Lihat Buku Tanpa Laci</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
