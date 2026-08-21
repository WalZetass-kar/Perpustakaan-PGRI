@extends('layouts.app')

@section('title', $buku->judul . ' - ' . ($pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6" x-data="detailBukuPage()">

    <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-xs font-semibold text-gray-500">
        <a href="{{ route('katalog') }}" class="hover:text-brand-700 transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Katalog OPAC</span>
        </a>
        <span class="text-gray-300">/</span>
        <a href="{{ route('katalog', ['kategori' => $buku->kategori_id]) }}" class="hover:text-brand-700 transition truncate max-w-[160px]">
            {{ $buku->kategori->nama ?? 'Koleksi Umum' }}
        </a>
        <span class="text-gray-300">/</span>
        <span class="text-gray-900 font-bold truncate max-w-[200px] sm:max-w-xs">{{ $buku->judul }}</span>
    </nav>

    <div class="bg-white border border-gray-200 rounded-3xl p-5 sm:p-8 shadow-2xs space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <div class="lg:col-span-4 flex flex-col items-center text-center space-y-4">
                <div class="w-full max-w-[260px] sm:max-w-[280px] aspect-3/4 bg-gray-100 rounded-2xl border border-gray-200 overflow-hidden shadow-sm relative flex flex-col justify-between">
                    @if($buku->cover_url)
                        <img src="{{ $buku->cover_url }}" alt="Cover {{ $buku->judul }}" width="280" height="373" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full p-5 bg-gradient-to-br from-brand-900 via-brand-800 to-red-950 text-white border-l-[6px] border-amber-400/50 flex flex-col justify-between text-left select-none relative overflow-hidden shadow-inner">
                            <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
                            <div class="flex items-center justify-end">
                                <span class="px-2.5 py-0.5 rounded-md bg-black/30 text-amber-300/90 text-[10px] font-black uppercase tracking-wider border border-white/10">
                                    {{ $buku->kategori->nama ?? 'Modul Sekolah' }}
                                </span>
                            </div>
                            <div class="my-auto text-center px-1">
                                <i class="fa-solid fa-book-bookmark text-white/25 text-4xl mb-2.5"></i>
                                <h2 class="text-sm sm:text-base font-black text-white line-clamp-4 leading-snug drop-shadow-sm">
                                    {{ $buku->judul }}
                                </h2>
                                <p class="text-xs text-white/70 font-medium mt-1.5 truncate">{{ $buku->penulis->nama ?? 'Penulis' }}</p>
                            </div>
                            <div class="pt-2 border-t border-white/10 flex items-center justify-between text-[8px] font-bold text-white/60 tracking-wider uppercase">
                                <span>PERPUSTAKAAN</span>
                                <span>SMK PGRI</span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="w-full max-w-[260px] sm:max-w-[280px] space-y-2 text-xs">
                    <button type="button" @click="startLoan()" class="w-full py-3 {{ $buku->available_quantity > 0 ? 'bg-brand-700 hover:bg-brand-800 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }} font-extrabold rounded-2xl text-xs flex items-center justify-center gap-2 shadow-md transition">
                        <i class="fa-solid fa-hand-holding-hand text-sm"></i>
                        <span>Ajukan Peminjaman</span>
                    </button>

                    <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-200 flex items-center justify-between text-[11px]">
                        <span class="font-bold text-gray-500">ISBN:</span>
                        <span class="font-mono font-bold text-gray-800">{{ $buku->isbn ?? 'Tidak Tercatat' }}</span>
                    </div>
                    <div class="text-[11px] text-gray-400 font-medium">
                        Dilihat {{ number_format($buku->view_count) }} kali di katalog
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-6">
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200 uppercase tracking-wide">
                            {{ $buku->kategori->nama ?? 'Koleksi Umum' }}
                        </span>

                        @if($buku->kelas)
                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wide flex items-center gap-1.5">
                                <i class="fa-solid fa-graduation-cap text-[11px]"></i>
                                <span>Kelas {{ $buku->kelas->nama_kelas }}</span>
                            </span>
                        @endif

                        @if($buku->available_quantity == $buku->total_quantity && $buku->total_quantity > 0)
                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Tersedia · {{ $buku->available_quantity }} dari {{ $buku->total_quantity }} buku siap dipinjam</span>
                            </span>
                        @elseif($buku->available_quantity > 0)
                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>Sebagian Tersedia · {{ $buku->available_quantity }} dari {{ $buku->total_quantity }} buku siap dipinjam ({{ $buku->total_quantity - $buku->available_quantity }} dipinjam)</span>
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span>Tidak Tersedia · Semua {{ $buku->total_quantity }} buku sedang dipinjam</span>
                            </span>
                        @endif
                    </div>

                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-gray-900 tracking-tight leading-tight">
                        {{ $buku->judul }}
                    </h1>

                    <p class="text-xs sm:text-sm text-gray-600 font-medium">
                        Penulis: <span class="text-gray-900 font-bold">{{ $buku->penulis->nama ?? 'Penulis Tidak Terdata' }}</span>
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-gray-50 border border-gray-200 rounded-2xl space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-gray-200/80 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-brand-50 text-brand-700 border border-brand-200">
                                <i class="fa-solid fa-location-dot text-sm"></i>
                            </span>
                            <div>
                                <h3 class="text-xs sm:text-sm font-black text-gray-900 uppercase tracking-wide">Lokasi Fisik Buku (Wayfinding)</h3>
                                <p class="text-[11px] text-gray-500">Ikuti urutan lantai, lemari rak, dan laci untuk menemukan buku secara fisik.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 bg-white rounded-lg text-gray-800 font-mono font-bold text-[11px] border border-gray-200 shadow-2xs" title="Format: Lantai · Lokasi · Kode Rak · Kode Laci">
                                {{ Str::contains($buku->rak->lokasi ?? '', '2') ? 'L2' : 'L1' }} · {{ $buku->rak->kode_rak ?? 'RAK-01' }} · {{ $buku->laci ? 'L' . str_pad($buku->laci->nomor_laci ?? 1, 2, '0', STR_PAD_LEFT) : 'L01' }}
                            </span>

                            <button type="button" @click="openPetaRak = true" class="px-3 py-1 bg-brand-700 hover:bg-brand-800 text-white font-bold rounded-lg transition text-xs flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                <i class="fa-solid fa-map-location-dot text-xs"></i>
                                <span>Lihat Peta Rak</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs">
                        <div class="p-3 bg-white rounded-xl border border-gray-200 shadow-2xs space-y-1">
                            <div class="flex items-center gap-1.5 text-gray-500 text-[10px] font-bold uppercase tracking-wider">
                                <i class="fa-solid fa-building text-gray-400"></i>
                                <span>Lantai &amp; Ruangan</span>
                            </div>
                            <p class="font-black text-gray-900 text-xs sm:text-sm leading-snug">
                                {{ $buku->rak->lokasi ?? 'Lantai 1 Perpustakaan' }}
                            </p>
                            <span class="text-[10px] text-gray-500 block">{{ $pengaturan['nama_perpustakaan'] ?? 'Gedung Perpustakaan' }}</span>
                        </div>

                        <div class="p-3 bg-white rounded-xl border border-gray-200 shadow-2xs space-y-1">
                            <div class="flex items-center gap-1.5 text-brand-700 text-[10px] font-bold uppercase tracking-wider">
                                <i class="fa-solid fa-layer-group text-brand-700"></i>
                                <span>Lemari Rak</span>
                            </div>
                            <p class="font-black text-gray-900 text-xs sm:text-sm leading-snug">
                                {{ $buku->rak->nama_rak ?? 'Rak Umum' }}
                            </p>
                            <span class="inline-block px-1.5 py-0.5 rounded bg-brand-50 text-brand-700 font-mono font-bold text-[10px] border border-brand-200">
                                {{ $buku->rak->kode_rak ?? 'RAK-01' }}
                            </span>
                        </div>

                        <div class="p-3 bg-emerald-50/70 rounded-xl border-2 border-emerald-300 shadow-2xs space-y-1 relative">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5 text-emerald-800 text-[10px] font-bold uppercase tracking-wider">
                                    <i class="fa-solid fa-inbox text-emerald-700"></i>
                                    <span>Laci Tujuan (Endpoint)</span>
                                </div>
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            </div>
                            <p class="font-black text-emerald-900 text-xs sm:text-sm leading-snug">
                                {{ $buku->laci->nama_laci ?? ($buku->rak ? 'Laci 1' : 'Tingkat Standar') }}
                            </p>
                            <p class="text-[10px] text-emerald-800 font-medium">
                                {{ $buku->laci->keterangan ?? 'Posisi rak penyimpanan fisik aktif' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 bg-gray-50/70 border border-gray-200 rounded-2xl text-xs">
                    <div class="p-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Penerbit</span>
                        <span class="text-xs font-bold text-gray-900 mt-0.5 block truncate">{{ $buku->penerbit->nama ?? '-' }}</span>
                    </div>
                    <div class="p-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Tahun Terbit</span>
                        <span class="text-xs font-bold text-gray-900 mt-0.5 block">{{ $buku->tahun_terbit ?? '-' }}</span>
                    </div>
                    <div class="p-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Total Koleksi</span>
                        <span class="text-xs font-bold text-gray-900 mt-0.5 block">{{ $buku->total_quantity }} Eksemplar</span>
                    </div>
                    <div class="p-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Stok Tersedia</span>
                        <span class="text-xs font-bold text-emerald-700 mt-0.5 block">{{ $buku->available_quantity }} Buku Fisik</span>
                    </div>
                    <div class="p-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Sedang Dipinjam</span>
                        <span class="text-xs font-bold text-amber-800 mt-0.5 block">{{ max(0, $buku->total_quantity - $buku->available_quantity) }} Siswa</span>
                    </div>
                    <div class="p-2">
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Status Sirkulasi</span>
                        <span class="text-xs font-bold {{ $buku->available_quantity > 0 ? 'text-emerald-700' : 'text-rose-600' }} mt-0.5 block">
                            {{ $buku->available_quantity > 0 ? 'Bisa Dipinjam' : 'Menunggu Kembali' }}
                        </span>
                    </div>
                </div>

                <div class="space-y-2">
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Sinopsis &amp; Ringkasan Buku</h3>
                    <div class="text-xs text-gray-600 leading-relaxed bg-gray-50/50 p-4 rounded-2xl border border-gray-200">
                        {{ $buku->sinopsis ?? 'Buku pegangan dan bahan ajar referensi resmi untuk civitas akademika SMK PGRI Pekanbaru.' }}
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 space-y-2 text-xs">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-brand-700"></i>
                        <h4 class="font-bold text-gray-900">Informasi &amp; Prosedur Peminjaman Fisik</h4>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        {{ $pengaturan['pesan_sirkulasi'] ?? 'Untuk meminjam buku fisik, silakan gunakan tombol Ajukan Peminjaman di atas atau langsung menuju meja layanan sirkulasi perpustakaan.' }}
                    </p>
                    <div class="pt-2 border-t border-gray-200/60 flex flex-wrap items-center gap-x-6 gap-y-1 text-[11px] text-gray-500 font-medium">
                        <span>Jam Layanan: <strong class="text-gray-800">{{ $pengaturan['jam_operasional'] ?? 'Senin - Jumat: 07.00 - 15.30 WIB' }}</strong></span>
                        <span>Maksimal Pinjam: <strong class="text-gray-800">{{ $pengaturan['max_buku_pinjam'] ?? 2 }} Buku</strong></span>
                    </div>
                </div>
            </div>

        </div>

        <div class="pt-6 border-t border-gray-100 space-y-4">
            <h3 class="text-sm font-bold text-gray-900">Spesifikasi Metadata Katalog</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border border-gray-200 rounded-xl overflow-hidden">
                    <tbody class="divide-y divide-gray-100">
                        <tr class="bg-gray-50/50">
                            <td class="px-4 py-2.5 font-bold text-gray-500 w-1/3">Judul Lengkap</td>
                            <td class="px-4 py-2.5 text-gray-900 font-semibold">{{ $buku->judul }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 font-bold text-gray-500">Nomor Standar (ISBN)</td>
                            <td class="px-4 py-2.5 text-gray-900 font-mono font-medium">{{ $buku->isbn ?? '-' }}</td>
                        </tr>
                        <tr class="bg-gray-50/50">
                            <td class="px-4 py-2.5 font-bold text-gray-500">Penulis / Pengarang</td>
                            <td class="px-4 py-2.5 text-gray-900 font-medium">{{ $buku->penulis->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 font-bold text-gray-500">Penerbit</td>
                            <td class="px-4 py-2.5 text-gray-900 font-medium">{{ $buku->penerbit->nama ?? '-' }}</td>
                        </tr>
                        <tr class="bg-gray-50/50">
                            <td class="px-4 py-2.5 font-bold text-gray-500">Tahun Terbit</td>
                            <td class="px-4 py-2.5 text-gray-900 font-medium">{{ $buku->tahun_terbit ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 font-bold text-gray-500">Kategori Subjek</td>
                            <td class="px-4 py-2.5 text-gray-900 font-medium">{{ $buku->kategori->nama ?? '-' }}</td>
                        </tr>
                        <tr class="bg-gray-50/50">
                            <td class="px-4 py-2.5 font-bold text-gray-500">Peruntukan Kelas</td>
                            <td class="px-4 py-2.5 text-gray-900 font-medium">{{ $buku->kelas->nama_kelas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 font-bold text-gray-500">Lokasi Penempatan</td>
                            <td class="px-4 py-2.5 text-gray-900 font-medium">{{ $buku->lokasi_lengkap }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if($relatedBooks->isNotEmpty())
            <div class="pt-6 border-t border-gray-100 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Rekomendasi Buku Terkait</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Koleksi lainnya dalam kategori {{ $buku->kategori->nama ?? 'serupa' }}</p>
                    </div>
                    <a href="{{ route('katalog', ['kategori' => $buku->kategori_id]) }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 transition flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach($relatedBooks as $rb)
                        <a href="{{ route('buku.detail', $rb->id) }}" class="group p-3 bg-gray-50 hover:bg-brand-50/40 border border-gray-200 hover:border-brand-200 rounded-2xl transition space-y-2.5 flex flex-col justify-between">
                            <div class="aspect-3/4 bg-gray-200 rounded-xl overflow-hidden shadow-2xs relative">
                                @if($rb->cover_url)
                                    <img src="{{ $rb->cover_card_url }}" alt="{{ $rb->judul }}" width="200" height="267" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-brand-900 via-brand-800 to-red-950 text-white p-2 border-l-[3px] border-amber-400/50 flex flex-col justify-between select-none relative overflow-hidden text-center">
                                        <span class="text-[6.5px] font-black text-amber-300/90 uppercase tracking-wider">{{ substr($rb->kategori->nama ?? 'Buku', 0, 8) }}</span>
                                        <div class="my-auto">
                                            <i class="fa-solid fa-book text-white/30 text-xs mb-0.5"></i>
                                            <p class="text-[8px] font-bold text-white line-clamp-2 leading-tight">{{ $rb->judul }}</p>
                                        </div>
                                        <span class="text-[6px] font-bold text-white/50 uppercase">SMK PGRI</span>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <span class="text-[9px] font-bold text-brand-700 block uppercase">{{ $rb->kategori->nama ?? 'Umum' }}</span>
                                <h4 class="text-xs font-bold text-gray-900 line-clamp-2 leading-snug group-hover:text-brand-700 transition">{{ $rb->judul }}</h4>
                                <p class="text-[10px] text-gray-500 truncate">{{ $rb->penulis->nama ?? '-' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <div x-show="openLoanModal" @click.self="openLoanModal = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 backdrop-blur-xs p-4 overflow-y-auto"          x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 space-y-5 shadow-2xl border-2 border-gray-200 transform transition-all my-8 relative">
            <button @click="openLoanModal = false" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-900 flex items-center justify-center transition font-bold">&times;</button>

            <div class="space-y-1">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-brand-50 text-brand-800 text-[10px] font-black uppercase tracking-wider border border-brand-200">
                    <i class="fa-solid fa-file-signature text-brand-700"></i>
                    <span>Formulir Pengajuan Peminjaman</span>
                </div>
                <h3 class="text-base font-black text-gray-900">Form Pengajuan Peminjaman Buku</h3>
                <p class="text-[11px] text-gray-500">Lengkapi data diri Anda untuk konfirmasi peminjaman buku ke petugas perpustakaan.</p>
            </div>

            <div class="p-3 bg-gray-50 rounded-2xl border border-gray-200 flex items-center gap-3">
                <div class="w-12 h-16 bg-gray-200 rounded-lg overflow-hidden shrink-0 border border-gray-300 flex items-center justify-center shadow-2xs">
                    @if($buku->cover_url)
                        <img src="{{ $buku->cover_thumb_url }}" alt="Cover" width="48" height="64" loading="lazy" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-brand-800 text-white font-bold flex items-center justify-center text-xs">
                            <i class="fa-solid fa-book"></i>
                        </div>
                    @endif
                </div>
                <div class="min-w-0 flex-1 text-xs">
                    <p class="font-extrabold text-gray-900 line-clamp-1">{{ $buku->judul }}</p>
                    <p class="text-[11px] text-gray-500 mt-0.5">Penulis: <span class="font-bold text-gray-700">{{ $buku->penulis->nama ?? '-' }}</span></p>
                    <p class="text-[10px] text-brand-700 font-mono font-bold mt-0.5">Lokasi Rak: <span>{{ $buku->rak->kode_rak ?? '-' }} ({{ $buku->laci->nama_laci ?? 'Laci 1' }})</span></p>
                </div>
            </div>

            <form @submit.prevent="submitLoanRequest()" class="space-y-3 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama Lengkap Siswa / Peminjam <span class="text-rose-600">*</span></label>
                    <input type="text" x-model="loanData.nama_peminjam" required placeholder="Contoh: Muhammad Ihwal" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Kelas &amp; Jurusan <span class="text-rose-600">*</span></label>
                        <input type="text" x-model="loanData.jurusan" required placeholder="Contoh: XII RPL 1 / XI TKJ 2" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">NISN / Nomor Induk (Opsional)</label>
                        <input type="text" x-model="loanData.nomor_induk" placeholder="Contoh: 0065123489" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">No. WhatsApp Aktif (Opsional)</label>
                    <input type="text" x-model="loanData.no_wa" placeholder="Contoh: 081234567890" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Catatan / Keterangan Tambahan</label>
                    <textarea x-model="loanData.catatan" rows="2" placeholder="Contoh: Untuk keperluan tugas akhir / ujian kejuruan..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button" @click="openLoanModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="submit" :disabled="submittingLoan" class="px-6 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold rounded-xl text-xs transition shadow-md flex items-center gap-1.5 disabled:opacity-50">
                        <template x-if="submittingLoan">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </template>
                        <template x-if="!submittingLoan">
                            <i class="fa-solid fa-paper-plane"></i>
                        </template>
                        <span x-text="submittingLoan ? 'Mengirim...' : 'Kirim Pengajuan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openPetaRak" @click.self="openPetaRak = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 backdrop-blur-xs p-4 overflow-y-auto"          x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-3xl max-w-4xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border-2 border-gray-200 transform transition-all my-8">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div>
                    <h3 class="text-base font-black text-gray-900">Peta Denah Rak Buku Perpustakaan</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Panduan penempatan rak dan nomor laci penyimpanan buku di ruangan perpustakaan.</p>
                </div>
                <button type="button" @click="openPetaRak = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-sm transition">
                    &times;
                </button>
            </div>

            <div class="space-y-4">
                <div class="p-3 bg-brand-50/70 border border-brand-200 rounded-2xl flex items-center gap-3 text-xs text-brand-900">
                    <span class="w-3 h-3 rounded-full bg-brand-700 shrink-0"></span>
                    <span>Rak dengan sorotan warna merah adalah posisi rak target: <strong>{{ $buku->rak->nama_rak ?? '-' }} ({{ $buku->rak->kode_rak ?? '-' }})</strong> pada <strong>{{ $buku->laci->nama_laci ?? 'Laci Standar' }}</strong>.</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-[50vh] overflow-y-auto p-1">
                    @foreach($allRaks ?? [] as $rk)
                        @php
                            $isTargetRak = ($rk->id === $buku->rak_id);
                        @endphp
                        <div class="p-3.5 rounded-2xl border transition {{ $isTargetRak ? 'bg-brand-50/80 border-2 border-brand-700 shadow-sm ring-2 ring-brand-100' : 'bg-gray-50 border-gray-200 opacity-80 hover:opacity-100' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-0.5 rounded font-mono font-bold text-[11px] {{ $isTargetRak ? 'bg-brand-700 text-white' : 'bg-gray-200 text-gray-800' }}">
                                    {{ $rk->kode_rak }}
                                </span>
                                @if($isTargetRak)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-brand-700 text-white uppercase tracking-wider">
                                        Posisi Buku
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs font-bold text-gray-900 leading-snug">{{ $rk->nama_rak }}</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">{{ $rk->lokasi ?? 'Lantai 1' }}</p>

                            <div class="mt-2.5 pt-2 border-t {{ $isTargetRak ? 'border-brand-200' : 'border-gray-200' }} space-y-1">
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block">Tingkat Laci:</span>
                                <div class="flex flex-wrap gap-1">
                                    @forelse($rk->laci as $lc)
                                        @php
                                            $isTargetLaci = ($isTargetRak && $buku->rak_laci_id === $lc->id);
                                        @endphp
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $isTargetLaci ? 'bg-emerald-600 text-white ring-1 ring-emerald-700' : 'bg-white text-gray-700 border border-gray-200' }}">
                                            {{ $lc->nama_laci }}
                                        </span>
                                    @empty
                                        <span class="text-[10px] text-gray-400 italic">Standar</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-500">Perpustakaan SMK PGRI Pekanbaru</span>
                <button type="button" @click="openPetaRak = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl text-xs transition">
                    Tutup Peta
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function detailBukuPage() {
    return {
        openPetaRak: false,
        openLoanModal: false,
        init() {
            this.$watch('openLoanModal', (val) => this.syncBodyScrollLock());
            this.$watch('openPetaRak', (val) => this.syncBodyScrollLock());
        },
        syncBodyScrollLock() {
            document.body.style.overflow = (this.openLoanModal || this.openPetaRak) ? 'hidden' : '';
        },
        loanData: {
            buku_id: '{{ $buku->id }}',
            nama_peminjam: '',
            jurusan: '',
            nomor_induk: '',
            no_wa: '',
            catatan: ''
        },
        submittingLoan: false,
        startLoan() {
            @if($buku->available_quantity <= 0)
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stok Buku Habis',
                        text: 'Seluruh eksemplar buku ini sedang dipinjam oleh murid lain.',
                        confirmButtonColor: '#991b1b'
                    });
                } else {
                    alert('Maaf, seluruh stok buku ini sedang habis dipinjam.');
                }
                return;
            @else
                this.openLoanModal = true;
            @endif
        },
        submitLoanRequest() {
            if (!this.loanData.nama_peminjam || !this.loanData.jurusan) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Belum Lengkap',
                        text: 'Mohon isi Nama Siswa dan Kelas/Jurusan.',
                        confirmButtonColor: '#991b1b'
                    });
                } else {
                    alert('Mohon isi Nama Siswa dan Kelas/Jurusan.');
                }
                return;
            }
            this.submittingLoan = true;
            const token = document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '{{ csrf_token() }}';
            const payload = Object.assign({}, this.loanData, { _token: token });
            fetch('{{ route('katalog.ajukan') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(res => {
                this.submittingLoan = false;
                if (res.status === 200 && res.body.success) {
                    this.openLoanModal = false;
                    this.loanData.nama_peminjam = '';
                    this.loanData.jurusan = '';
                    this.loanData.nomor_induk = '';
                    this.loanData.no_wa = '';
                    this.loanData.catatan = '';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Pengajuan Terkirim!',
                            html: '<p class="text-xs text-gray-600 mb-2">Pengajuan peminjaman buku berhasil dikirim ke Admin Perpustakaan.</p><p class="text-sm font-mono font-bold text-brand-800 bg-gray-100 py-1.5 px-3 rounded-lg border border-gray-200">Kode Ref: ' + res.body.kode + '</p><p class="text-[11px] text-gray-500 mt-2">Silakan konfirmasi ke meja sirkulasi perpustakaan saat mengambil buku fisik.</p>',
                            confirmButtonColor: '#991b1b'
                        });
                    } else {
                        alert('Pengajuan peminjaman berhasil dikirim! Kode Referensi: ' + res.body.kode);
                    }
                } else {
                    const msg = res.body.message || 'Gagal mengajukan peminjaman buku.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Pengajuan Gagal',
                            text: msg,
                            confirmButtonColor: '#991b1b'
                        });
                    } else {
                        alert(msg);
                    }
                }
            })
            .catch(() => {
                this.submittingLoan = false;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Koneksi jaringan terputus atau server sedang sibuk.',
                        confirmButtonColor: '#991b1b'
                    });
                } else {
                    alert('Terjadi kesalahan jaringan.');
                }
            });
        }
    };
}
</script>
@endsection
