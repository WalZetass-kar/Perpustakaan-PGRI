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
            
            <!-- Book Cover Showcase -->
            <div class="lg:col-span-1 bg-gray-50 border-2 border-gray-100 rounded-2xl p-6 flex flex-col items-center justify-center text-center space-y-4">
                <div class="w-44 h-64 bg-gray-200 border-2 border-gray-300 rounded-2xl overflow-hidden shadow-lg relative group">
                    @if($buku->cover)
                        <img src="{{ asset('storage/' . $buku->cover) }}" alt="Cover {{ $buku->judul }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-brand-800 text-white font-black text-4xl p-4">
                            {{ substr($buku->judul, 0, 1) }}
                        </div>
                    @endif
                </div>

                <div class="space-y-1">
                    <span class="px-3 py-1 bg-white border border-gray-200 rounded-full text-xs font-mono font-bold text-gray-800 shadow-2xs block">
                        ISBN: {{ $buku->isbn }}
                    </span>
                    <span class="text-[11px] font-medium text-gray-400 block">Dilihat {{ number_format($buku->view_count) }} kali</span>
                </div>
            </div>

            <!-- Detailed Information & Action Buttons -->
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="px-3 py-1 rounded-xl text-xs font-black bg-brand-50 text-brand-700 border border-brand-200 uppercase">
                            {{ $buku->kategori->nama ?? 'Umum' }}
                        </span>
                        @if($buku->jumlah_tersedia > 0)
                            <span class="px-3 py-1 rounded-xl text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">Tersedia {{ $buku->jumlah_tersedia }} Eksemplar</span>
                        @elseif($buku->jumlah_eksemplar > 0)
                            <span class="px-3 py-1 rounded-xl text-xs font-black bg-amber-50 text-amber-700 border border-amber-200">Sedang Dipinjam Semua</span>
                        @else
                            <span class="px-3 py-1 rounded-xl text-xs font-black bg-rose-50 text-rose-700 border border-rose-200">Stok Kosong</span>
                        @endif
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight">{{ $buku->judul }}</h1>
                    <p class="text-xs text-gray-600 mt-1">Penulis: <strong class="text-gray-900 font-bold">{{ $buku->penulis->nama ?? '-' }}</strong></p>
                </div>

                <!-- Attributes Grid -->
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
                        <span class="text-gray-400 block font-bold text-[10px] uppercase">Total Eksemplar</span>
                        <span class="text-gray-900 font-bold mt-0.5 block">{{ $buku->jumlah_eksemplar }} Fisik</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-bold text-[10px] uppercase">Status Ketersediaan</span>
                        <span class="text-emerald-700 font-black mt-0.5 block">{{ $buku->jumlah_tersedia }} Ready</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-bold text-[10px] uppercase">Gedung / Lantai</span>
                        <span class="text-gray-900 font-bold mt-0.5 block">{{ $buku->rak->lokasi ?? 'Lantai 1' }}</span>
                    </div>
                </div>

                <!-- Synopsis -->
                <div>
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider mb-2">Sinopsis / Ringkasan Buku</h3>
                    <p class="text-xs text-gray-600 leading-relaxed bg-gray-50/50 p-4 rounded-xl border border-gray-200">{{ $buku->sinopsis ?? 'Modul pembelajaran dan pustaka kejuruan SMK PGRI Pekanbaru.' }}</p>
                </div>

                @if($buku->file_pdf)
                    <div x-data="{ openPdf: false }">
                        <button @click="openPdf = true" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl transition shadow-md flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Baca Digital Sample / E-Book Preview (PDF)</span>
                        </button>

                        <div x-show="openPdf" @click.self="openPdf = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/80 backdrop-blur-xs p-4" x-cloak>
                            <div class="bg-white rounded-3xl max-w-4xl w-full h-[85vh] p-4 flex flex-col shadow-2xl relative" @click.stop>
                                <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                                    <h3 class="text-sm font-black text-gray-900">E-Book Reader: {{ $buku->judul }}</h3>
                                    <button @click="openPdf = false" class="w-8 h-8 rounded-full bg-gray-100 text-gray-700 font-bold">&times;</button>
                                </div>
                                <iframe src="{{ asset('storage/' . $buku->file_pdf) }}" class="w-full flex-1 rounded-2xl border border-gray-300 mt-3"></iframe>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Button Workflow based on User Auth -->
                <div class="pt-4 border-t-2 border-gray-100 flex flex-wrap items-center justify-between gap-4">
                    @auth
                        @if((auth()->user()->role->name ?? '') === 'mahasiswa')
                            @if($userLoan)
                                <div class="px-4 py-2 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-extrabold flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Sedang Anda Pinjam (Jatuh Tempo: {{ $userLoan->tanggal_jatuh_tempo }})</span>
                                </div>
                            @elseif($userReservation)
                                <div class="px-4 py-2 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl text-xs font-extrabold flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Dalam Antrean Booking Anda (Posisi #{{ $userReservation->posisi_antrean }})</span>
                                </div>
                            @else
                                <form action="{{ route('mahasiswa.reservasi.buat', $buku->id) }}" method="POST" onsubmit="return confirmAction(event, 'Booking Buku Ini?', 'Konfirmasi booking online untuk buku ini.', 'Ya, Booking Sekarang!')">
                                    @csrf
                                    <button type="submit" class="px-6 py-3 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition shadow-md flex items-center gap-2">
                                        <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>Booking / Reservasi Buku Ini</span>
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('pustakawan.peminjaman') }}" class="px-6 py-3 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition shadow-md">
                                Layani Peminjaman Petugas
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-3 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition shadow-md flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>Login Siswa untuk Booking Buku</span>
                        </a>
                    @endauth
                </div>

            </div>

        </div>
    </div>

    <!-- Physical Exemplars Table -->
    <div class="bg-white border-2 border-gray-200 rounded-3xl overflow-hidden shadow-sm p-6 space-y-4">
        <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">Daftar Fisik Eksemplar Buku Perpustakaan</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-4 font-bold">Kode Eksemplar</th>
                        <th class="py-3 px-4 font-bold">Barcode ID</th>
                        <th class="py-3 px-4 font-bold">Lokasi Rak</th>
                        <th class="py-3 px-4 font-bold">Kondisi Fisik</th>
                        <th class="py-3 px-4 font-bold">Status Eksemplar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($buku->eksemplar as $ex)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-3 px-4 font-mono font-bold text-gray-900">{{ $ex->kode_eksemplar }}</td>
                            <td class="py-3 px-4 font-mono text-gray-600">{{ $ex->barcode }}</td>
                            <td class="py-3 px-4 font-bold text-brand-700">{{ $ex->rak->kode_rak ?? '-' }}</td>
                            <td class="py-3 px-4 font-semibold capitalize">{{ $ex->kondisi }}</td>
                            <td class="py-3 px-4">
                                @if($ex->status === 'tersedia')
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">Tersedia</span>
                                @elseif($ex->status === 'dipinjam')
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200 uppercase">Dipinjam</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200 uppercase">{{ $ex->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500 font-medium">Belum ada eksemplar fisik registered untuk buku ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
