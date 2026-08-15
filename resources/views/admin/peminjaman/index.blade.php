@extends('layouts.dashboard')

@section('title', 'Sirkulasi Peminjaman Aktif')
@section('page_heading', 'Sirkulasi Peminjaman Buku')

@section('content')
<div class="space-y-5" x-data="{ openAddModal: false, selectedBookQty: 1 }" x-init="openAddModal = false">
    
    <!-- Top Action Toolbar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-black text-gray-900">Daftar Peminjaman Sedang Berlangsung</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Catatan buku yang sedang dipinjam dan belum dikembalikan hari ini</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <form action="{{ route('admin.peminjaman') }}" method="GET" class="relative flex-1 sm:w-72">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama siswa, jurusan, NIS..." 
                       class="w-full pl-8 pr-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:outline-none font-medium">
                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
            <button @click="openAddModal = true" class="px-3.5 py-1.5 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center gap-1.5 shrink-0">
                <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Peminjaman Baru</span>
            </button>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
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
                                    <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-lg text-[11px] transition shadow-2xs flex items-center gap-1 ml-auto">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
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
        <div class="p-3 border-t border-gray-100">
            {{ $activeLoans->links() }}
        </div>
    </div>

    <!-- Modal Form Peminjaman Baru (Direct Input Form) -->
    <div x-show="openAddModal" @click.self="openAddModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto" x-cloak>
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
                
                <!-- Nama Siswa / Peminjam (Text Input Langsung) -->
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama Lengkap Siswa / Peminjam <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="nama_peminjam" required placeholder="Contoh: Muhammad Ihwal Maulana" autofocus
                               class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Jurusan / Kelas (Text Input) -->
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Jurusan / Kelas <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="jurusan" required placeholder="Contoh: XII RPL 1, XI TKJ..."
                                   class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                    </div>

                    <!-- NIS / NIP / No. Identitas (Opsional) -->
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">NIS / NIP <span class="text-gray-400 font-normal">(Opsional)</span></label>
                        <div class="relative">
                            <input type="text" name="nomor_induk" placeholder="Nomor induk (opsional)"
                                   class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Pilih Judul Buku -->
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Pilih Buku <span class="text-rose-500">*</span></label>
                    <select name="buku_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                        <option value="">-- Pilih Judul Buku --</option>
                        @foreach($booksList as $bk)
                            <option value="{{ $bk->id }}">
                                {{ $bk->judul }} (Tersedia: {{ $bk->available_quantity }} Eks)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jumlah Buku Dipinjam -->
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
