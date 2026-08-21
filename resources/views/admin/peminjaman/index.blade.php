@extends('layouts.dashboard')

@section('title', 'Sirkulasi Peminjaman Aktif')
@section('page_heading', 'Sirkulasi Peminjaman Buku')

@section('content')
<div class="space-y-5" x-data="{ openAddModal: false, selectedBookQty: 1 }" x-init="openAddModal = false">

    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-gray-200 shadow-sm"
         x-data="{ toolOpen: {{ request()->filled('search') ? 'true' : 'false' }} }">

        {{-- Desktop/tablet: search kiri, Export & Peminjaman Baru kanan --}}
        <div class="hidden sm:flex sm:flex-row items-center justify-between gap-3">
            <form action="{{ route('admin.peminjaman') }}" method="GET" class="relative w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, siswa, NIS..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:outline-none font-medium">
                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
            <div class="flex flex-wrap items-center justify-end gap-2 shrink-0">
                <a href="{{ route('admin.peminjaman.export.excel', array_merge(request()->all(), ['status' => 'dipinjam'])) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center gap-1.5 shrink-0" title="Export Excel">
                    <i class="fa-solid fa-file-excel"></i>
                    <span>Excel</span>
                </a>
                <a href="{{ route('admin.peminjaman.export.pdf', array_merge(request()->all(), ['status' => 'dipinjam'])) }}" target="_blank" class="px-3 py-1.5 bg-rose-700 hover:bg-rose-800 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center gap-1.5 shrink-0" title="Cetak / PDF">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>Cetak / PDF</span>
                </a>
                <button @click="openAddModal = true" class="px-3.5 py-1.5 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center gap-1.5 shrink-0">
                    <i class="fa-solid fa-plus text-emerald-300"></i>
                    <span>Peminjaman Baru</span>
                </button>
            </div>
        </div>

        {{-- Mobile: panah kiri (toggle search & export) — tombol Peminjaman Baru kanan --}}
        <div class="sm:hidden">
            <div class="flex items-center justify-between gap-2">
                <button type="button" @click="toolOpen = !toolOpen"
                        class="relative w-9 h-9 shrink-0 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 flex items-center justify-center transition"
                        title="Tampilkan/Sembunyikan Pencarian & Export" aria-label="Toggle Pencarian & Export">
                    <svg class="w-4 h-4 transition-transform duration-200" :class="toolOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    @if(request()->filled('search'))
                        <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-brand-700 rounded-full border-2 border-white"></span>
                    @endif
                </button>

                <button @click="openAddModal = true" class="px-3.5 py-1.5 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center gap-1.5 shrink-0">
                    <i class="fa-solid fa-plus text-emerald-300"></i>
                    <span>Peminjaman Baru</span>
                </button>
            </div>

            {{-- Panel collapsible: search di atas, Export Excel/PDF di bawahnya --}}
            <div x-show="toolOpen" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="mt-3 space-y-2">
                <form action="{{ route('admin.peminjaman') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, siswa, NIS..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:outline-none font-medium">
                    <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </form>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('admin.peminjaman.export.excel', array_merge(request()->all(), ['status' => 'dipinjam'])) }}" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center justify-center gap-1.5" title="Export Excel">
                        <i class="fa-solid fa-file-excel"></i>
                        <span>Excel</span>
                    </a>
                    <a href="{{ route('admin.peminjaman.export.pdf', array_merge(request()->all(), ['status' => 'dipinjam'])) }}" target="_blank" class="px-3 py-2 bg-rose-700 hover:bg-rose-800 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center justify-center gap-1.5" title="Cetak / PDF">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Cetak / PDF</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider font-extrabold">
                        <th class="py-3 px-4 font-bold">Kode Pinjam</th>
                        <th class="py-3 px-4 font-bold">Nama Peminjam / Siswa</th>
                        <th class="py-3 px-4 font-bold">Jurusan / Kelas</th>
                        <th class="py-3 px-4 font-bold">Judul Buku</th>
                        <th class="py-3 px-4 font-bold text-center">Jumlah</th>
                        <th class="py-3 px-4 font-bold">Tanggal & Waktu Pinjam</th>
                        <th class="py-3 px-4 font-bold">Petugas</th>
                        <th class="py-3 px-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    @forelse($activeLoans as $loan)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-brand-700 font-mono font-black text-[11px] border border-gray-200">
                                    {{ $loan->kode_peminjaman }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-gray-900">{{ $loan->nama_peminjam ?: ($loan->user->name ?? '-') }}</p>
                                @if($loan->nomor_induk)
                                    <p class="text-[10px] text-gray-500 font-mono">NIS/NIP: {{ $loan->nomor_induk }}</p>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-gray-100 text-gray-800 border border-gray-200">
                                    {{ $loan->jurusan ?: '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold text-gray-900 max-w-xs truncate">
                                {{ $loan->buku->judul ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-amber-50 text-amber-800 border border-amber-200">
                                    {{ $loan->jumlah }} Buku
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}</span>
                                <span class="text-[10px] text-gray-400 block">{{ $loan->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="py-3 px-4 text-gray-600 font-medium">
                                {{ $loan->petugas->name ?? 'Admin' }}
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <form action="{{ route('admin.peminjaman.kembali', $loan->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Proses Pengembalian?', 'Buku akan dikembalikan ke stok fisik perpustakaan.')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-lg text-[11px] transition shadow-2xs flex items-center gap-1.5 ml-auto">
                                        <i class="fa-solid fa-arrow-rotate-left text-xs"></i>
                                        <span>Pengembalian</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400 font-medium">Tidak ada buku yang sedang dipinjam saat ini. Semua buku berada di rak.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="lg:hidden divide-y divide-gray-100">
            @forelse($activeLoans as $loan)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2">
                        <span class="px-2 py-0.5 rounded bg-gray-100 text-brand-700 font-mono font-black text-[11px] border border-gray-200">
                            {{ $loan->kode_peminjaman }}
                        </span>
                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-amber-50 text-amber-800 border border-amber-200 shrink-0">
                            {{ $loan->jumlah }} Buku
                        </span>
                    </div>

                    <div class="mt-2.5">
                        <p class="font-bold text-gray-900 text-xs">{{ $loan->nama_peminjam ?: ($loan->user->name ?? '-') }}</p>
                        <div class="flex items-center gap-1.5 text-[10.5px] text-gray-500 font-medium mt-0.5">
                            <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-800 border border-gray-200 font-bold">{{ $loan->jurusan ?: '-' }}</span>
                            @if($loan->nomor_induk)
                                <span>&bull;</span>
                                <span class="font-mono">NIS: {{ $loan->nomor_induk }}</span>
                            @endif
                        </div>
                    </div>

                    <p class="flex items-start gap-1.5 text-[11px] text-gray-700 font-bold mt-2">
                        <i class="fa-solid fa-book text-gray-300 text-[10px] mt-0.5 shrink-0"></i>
                        <span class="line-clamp-2">{{ $loan->buku->judul ?? '-' }}</span>
                    </p>

                    <div class="flex items-center gap-3 mt-2 text-[10px] text-gray-400 font-mono">
                        <span class="flex items-center gap-1"><i class="fa-regular fa-clock text-gray-300"></i>{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d M Y') }}, {{ $loan->created_at->format('H:i') }}</span>
                        <span class="flex items-center gap-1"><i class="fa-solid fa-user-tie text-gray-300"></i>{{ $loan->petugas->name ?? 'Admin' }}</span>
                    </div>

                    <form action="{{ route('admin.peminjaman.kembali', $loan->id) }}" method="POST" class="mt-3 pt-3 border-t border-gray-100" onsubmit="return confirmDelete(event, 'Proses Pengembalian?', 'Buku akan dikembalikan ke stok fisik perpustakaan.')">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-lg text-xs transition shadow-2xs flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-arrow-rotate-left text-xs"></i>
                            <span>Proses Pengembalian</span>
                        </button>
                    </form>
                </div>
            @empty
                <div class="py-10 text-center text-gray-400 font-medium text-xs px-4">Tidak ada buku yang sedang dipinjam saat ini. Semua buku berada di rak.</div>
            @endforelse
        </div>

        <div class="p-3 border-t border-gray-100">
            {{ $activeLoans->links() }}
        </div>
    </div>

    <div x-show="openAddModal" @click.self="openAddModal = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto"          x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/70">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Catat Peminjaman Baru</h3>
                        <p class="text-[10px] text-gray-500">Peminjaman dan pengembalian fisik hari ini</p>
                    </div>
                </div>
                <button @click="openAddModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm">&times;</button>
            </div>

            <form action="{{ route('admin.peminjaman.store') }}" method="POST" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3.5 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama Lengkap Siswa / Peminjam <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="nama_peminjam" required placeholder="Contoh: Muhammad Ihwal Maulana" autofocus
                               class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Jurusan / Kelas <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="jurusan" required placeholder="Contoh: XII RPL 1, XI TKJ..."
                                   class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">NIS / NIP <span class="text-gray-400 font-normal">(Opsional)</span></label>
                        <div class="relative">
                            <input type="text" name="nomor_induk" placeholder="Nomor induk (opsional)"
                                   class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Pilih Buku <span class="text-rose-500">*</span></label>
                    <select name="buku_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                        <option value="">-- Pilih Judul Buku --</option>
                        @foreach(($booksList ?? $bukuList ?? []) as $bk)
                            <option value="{{ $bk->id }}">
                                {{ $bk->judul }} (Tersedia: {{ $bk->available_quantity }} Eks)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Jumlah Buku Dipinjam <span class="text-rose-500">*</span></label>
                    <input type="number" name="jumlah" value="1" required min="1" max="10" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-black text-brand-700">
                </div>

                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[11px] leading-relaxed">
                    <p class="font-bold flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Aturan Operasional Sirkulasi:</span>
                    </p>
                    <p class="mt-0.5 text-amber-800">Buku dipinjam untuk kegiatan pembelajaran di perpustakaan/sekolah dan harus dikembalikan sebelum jam operasional berakhir.</p>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2 shrink-0">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 text-xs">Simpan Peminjaman</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
