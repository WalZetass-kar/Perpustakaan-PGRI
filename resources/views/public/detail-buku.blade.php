@extends('layouts.app')

@section('title', $buku->judul)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('katalog') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-brand-700 mb-6 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Kembali ke Katalog Buku</span>
    </a>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-2xs p-6 lg:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Book Cover Showcase -->
            <div class="lg:col-span-1 bg-gray-50 border border-gray-200 rounded-xl p-8 flex flex-col items-center justify-center text-center">
                <div class="w-36 h-48 bg-gray-100 border-2 border-gray-300 text-brand-700 font-extrabold text-4xl flex items-center justify-center rounded-xl shadow-md mb-4">
                    {{ substr($buku->judul, 0, 1) }}
                </div>
                <span class="px-3 py-1 bg-white border border-gray-200 rounded-full text-xs font-bold text-gray-700 shadow-2xs">
                    ISBN: {{ $buku->isbn }}
                </span>
                <span class="text-xs text-gray-400 mt-2">Dilihat {{ number_format($buku->view_count) }} kali</span>
            </div>

            <!-- Detailed Information & Action Buttons -->
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-brand-50 text-brand-700 border border-brand-100">
                            {{ $buku->kategori->nama ?? 'Umum' }}
                        </span>
                        @if($buku->jumlah_tersedia > 0)
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Tersedia {{ $buku->jumlah_tersedia }} Eksemplar</span>
                        @elseif($buku->jumlah_eksemplar > 0)
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">Sedang Dipinjam Semua</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">Tidak Tersedia</span>
                        @endif
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-tight">{{ $buku->judul }}</h1>
                    <p class="text-xs text-gray-600 mt-1">Penulis: <strong class="text-gray-900">{{ $buku->penulis->nama ?? '-' }}</strong></p>
                </div>

                <!-- Attributes Table -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 p-4 bg-gray-50 border border-gray-200 rounded-lg text-xs">
                    <div>
                        <span class="text-gray-400 block font-medium text-[11px]">Penerbit</span>
                        <span class="text-gray-800 font-bold mt-0.5 block">{{ $buku->penerbit->nama ?? '-' }} ({{ $buku->penerbit->kota ?? '' }})</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-medium text-[11px]">Tahun Terbit</span>
                        <span class="text-gray-800 font-bold mt-0.5 block">{{ $buku->tahun_terbit }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-medium text-[11px]">Lokasi Rak</span>
                        <span class="text-gray-800 font-bold mt-0.5 block">{{ $buku->rak->kode_rak ?? '-' }} - {{ $buku->rak->nama_rak ?? '' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-medium text-[11px]">Total Eksemplar</span>
                        <span class="text-gray-800 font-bold mt-0.5 block">{{ $buku->jumlah_eksemplar }} Fisik</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-medium text-[11px]">Eksemplar Tersedia</span>
                        <span class="text-emerald-700 font-bold mt-0.5 block">{{ $buku->jumlah_tersedia }} Ready</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-medium text-[11px]">Lokasi Gedung</span>
                        <span class="text-gray-800 font-bold mt-0.5 block">{{ $buku->rak->lokasi ?? 'Lantai 1' }}</span>
                    </div>
                </div>

                <!-- Synopsis -->
                <div>
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-2">Sinopsis / Ringkasan Buku</h3>
                    <p class="text-xs text-gray-600 leading-relaxed bg-gray-50/50 p-4 rounded-lg border border-gray-100">{{ $buku->sinopsis ?? 'Belum ada ringkasan sinopsis untuk buku ini.' }}</p>
                </div>

                <!-- Action Button Workflow based on User Auth -->
                <div class="pt-4 border-t border-gray-100 flex flex-wrap items-center gap-4">
                    @auth
                        @if(auth()->user()->role->name === 'mahasiswa')
                            @if($userLoan)
                                <div class="px-4 py-2 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-lg text-xs font-semibold">
                                    Status: Buku ini sedang Anda pinjam (Jatuh Tempo: {{ $userLoan->tanggal_jatuh_tempo }})
                                </div>
                            @elseif($userReservation)
                                <div class="px-4 py-2 bg-amber-50 text-amber-800 border border-amber-200 rounded-lg text-xs font-semibold">
                                    Status: Dalam antrean reservasi Anda (Posisi #{{ $userReservation->posisi_antrean }})
                                </div>
                            @elseif($buku->jumlah_tersedia > 0)
                                <div class="px-4 py-2.5 bg-brand-700 text-white rounded-lg text-xs font-bold shadow-2xs">
                                    Status: Tersedia Dipinjam Fisik di Meja Pustakawan Sekolah (Sebutkan NISN/QR Siswa)
                                </div>
                            @else
                                <form action="{{ route('mahasiswa.reservasi.buat', $buku->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-6 py-2.5 bg-brand-700 text-white text-xs font-bold rounded-lg hover:bg-brand-800 transition shadow-2xs">
                                        Reservasi Buku
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('pustakawan.peminjaman') }}" class="px-5 py-2.5 bg-brand-700 text-white text-xs font-bold rounded-lg hover:bg-brand-800 transition shadow-2xs">
                                Layani Peminjaman Petugas
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-2.5 bg-brand-700 text-white text-xs font-bold rounded-lg hover:bg-brand-800 transition shadow-2xs">
                            Masuk untuk Meminjam
                        </a>
                    @endauth
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
