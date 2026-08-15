@extends('layouts.app')

@section('title', $buku->judul)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <a href="{{ route('katalog') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-brand-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Kembali ke Katalog OPAC</span>
    </a>

    <div class="bg-white border-2 border-gray-200 rounded-3xl overflow-hidden shadow-sm p-6 lg:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-1 bg-gray-50 border-2 border-gray-100 rounded-2xl p-6 flex flex-col items-center justify-center text-center space-y-4">
                <div class="w-44 h-64 bg-gray-200 border-2 border-gray-300 rounded-2xl overflow-hidden shadow-lg relative group">
                    @if($buku->cover_url)
                        <img src="{{ $buku->cover_url }}" alt="Cover {{ $buku->judul }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-brand-800 text-white font-black text-4xl p-4">
                            {{ substr($buku->judul, 0, 1) }}
                        </div>
                    @endif
                </div>

                <div class="space-y-1">
                    <span class="px-3 py-1 bg-white border border-gray-200 rounded-full text-xs font-mono font-bold text-gray-800 shadow-2xs block">
                        ISBN: {{ $buku->isbn ?? 'Tanpa ISBN' }}
                    </span>
                    <span class="text-[11px] font-medium text-gray-400 block">Dilihat {{ number_format($buku->view_count) }} kali</span>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div>
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="px-3 py-1 rounded-xl text-xs font-black bg-brand-50 text-brand-700 border border-brand-200 uppercase">
                            {{ $buku->kategori->nama ?? 'Umum' }}
                        </span>
                        @if($buku->available_quantity > 0)
                            <span class="px-3 py-1 rounded-xl text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">Tersedia {{ $buku->available_quantity }} dari {{ $buku->total_quantity }} Buku</span>
                        @else
                            <span class="px-3 py-1 rounded-xl text-xs font-black bg-rose-50 text-rose-700 border border-rose-200">Sedang Dipinjam Semua</span>
                        @endif
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight">{{ $buku->judul }}</h1>
                    <p class="text-xs text-gray-600 mt-1">Penulis: <strong class="text-gray-900 font-bold">{{ $buku->penulis->nama ?? '-' }}</strong></p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 p-5 bg-gray-50 border-2 border-gray-100 rounded-2xl text-xs">
                    <div>
                        <span class="text-gray-400 block font-bold text-[10px] uppercase">Penerbit</span>
                        <span class="text-gray-900 font-extrabold mt-0.5 block">{{ $buku->penerbit->nama ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-bold text-[10px] uppercase">Tahun Terbit</span>
                        <span class="text-gray-900 font-bold mt-0.5 block">{{ $buku->tahun_terbit }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-bold text-[10px] uppercase">Lokasi Rak</span>
                        <span class="text-brand-700 font-mono font-black mt-0.5 block">{{ $buku->rak->kode_rak ?? '-' }} ({{ $buku->rak->nama_rak ?? '' }})</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-bold text-[10px] uppercase">Total Stok Fisik</span>
                        <span class="text-gray-900 font-bold mt-0.5 block">{{ $buku->total_quantity }} Buku</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-bold text-[10px] uppercase">Status Ketersediaan</span>
                        <span class="text-emerald-700 font-black mt-0.5 block">{{ $buku->available_quantity }} Siap Dipinjam</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-bold text-[10px] uppercase">Posisi Ruangan</span>
                        <span class="text-gray-900 font-bold mt-0.5 block">{{ $buku->rak->lokasi ?? 'Ruang Perpustakaan Utama' }}</span>
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider mb-2">Sinopsis / Ringkasan Buku</h3>
                    <p class="text-xs text-gray-600 leading-relaxed bg-gray-50/50 p-4 rounded-xl border border-gray-200">{{ $buku->sinopsis ?? 'Buku perpustakaan resmi dan modul pembelajaran SMK PGRI Pekanbaru.' }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-brand-50 border-2 border-brand-100 text-brand-900 text-xs">
                    <p class="font-extrabold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-brand-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Informasi Peminjaman Fisik di Perpustakaan:</span>
                    </p>
                    <p class="mt-1 text-[11px] text-brand-800">
                        Untuk meminjam buku fisik ini, silakan datangi meja pengelola perpustakaan di Ruang Perpustakaan SMK PGRI Pekanbaru. Buku dipinjam dan dikembalikan pada hari yang sama.
                    </p>
                </div>

            </div>

        </div>
    </div>

</div>
@endsection
