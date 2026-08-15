@extends('layouts.dashboard')

@section('title', 'Manajemen Rak & Laci Perpustakaan')
@section('page_heading', 'Manajemen Lokasi Rak & Laci Buku')

@section('content')
<div class="space-y-5" x-data="{ 
    openAddModal: false, 
    openEditModal: false, 
    openAddLaciModal: false, 
    openEditLaciModal: false, 
    editData: {}, 
    laciData: {} 
}" x-init="openAddModal = false; openEditModal = false; openAddLaciModal = false; openEditLaciModal = false; editData = {}; laciData = {}">

    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-black text-gray-900">Struktur Rak & Laci Tingkat Perpustakaan</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Kelola lemari rak dan pembagian laci fleksibel (satu laci satu kategori atau campuran beberapa kategori)</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <form action="{{ route('admin.rak') }}" method="GET" class="relative flex-1 sm:w-72">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode rak, nama rak..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:outline-none">
                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
            <button @click="openAddModal = true" class="px-3.5 py-1.5 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center gap-1.5 shrink-0">
                <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Tambah Rak Baru</span>
            </button>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($rakList as $rak)
            <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 sm:p-5 bg-gray-50/80 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-brand-700 text-white flex items-center justify-center font-mono font-black text-xs shadow-xs">
                            {{ $rak->kode_rak }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-black text-sm text-gray-900">{{ $rak->nama_rak }}</h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $rak->buku_count ?? 0 }} Judul Buku
                                </span>
                            </div>
                            <p class="text-[11px] text-gray-500 flex items-center gap-1 mt-0.5">
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $rak->lokasi ?? 'Lokasi umum perpustakaan' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-end sm:self-center">
                        <button type="button" @click="laciData = { rak_id: {{ $rak->id }}, rak_nama: '{{ $rak->nama_rak }}' }; openAddLaciModal = true" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl transition shadow-2xs flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Tambah Laci</span>
                        </button>
                        <button type="button" @click="editData = {{ json_encode([
                            'id' => $rak->id,
                            'kode_rak' => $rak->kode_rak,
                            'nama_rak' => $rak->nama_rak,
                            'lokasi' => $rak->lokasi ?? '',
                            'kategori_id' => $rak->kategori_id
                        ]) }}; openEditModal = true" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs rounded-xl transition shadow-2xs">
                            Edit Rak
                        </button>
                        <form action="{{ route('admin.rak.delete', $rak->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Hapus Rak?', 'Rak ini beserta seluruh susunan lacinya akan dihapus.')">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl transition shadow-2xs">
                                Hapus Rak
                            </button>
                        </form>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    <h4 class="text-[11px] font-extrabold uppercase tracking-wider text-gray-400 mb-3 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        <span>Tingkat / Laci di dalam {{ $rak->nama_rak }} ({{ $rak->laci->count() }} Laci Terpasang)</span>
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @forelse($rak->laci as $laci)
                            @php
                                $categoriesInLaci = $laci->buku->pluck('kategori.nama')->filter()->unique()->values();
                            @endphp
                            <div class="p-3.5 rounded-xl border border-gray-200 bg-gray-50/50 hover:bg-white hover:border-brand-200 hover:shadow-sm transition space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-gray-200/80 text-gray-800 font-mono font-black text-xs flex items-center justify-center">
                                            {{ $laci->nomor_laci }}
                                        </span>
                                        <span class="font-bold text-xs text-gray-900">{{ $laci->nama_laci }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="laciData = {{ json_encode([
                                            'id' => $laci->id,
                                            'nomor_laci' => $laci->nomor_laci,
                                            'nama_laci' => $laci->nama_laci,
                                            'keterangan' => $laci->keterangan ?? ''
                                        ]) }}; openEditLaciModal = true" class="p-1 text-gray-400 hover:text-amber-600 transition" title="Edit Laci">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                        <form action="{{ route('admin.rak.laci.delete', $laci->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Hapus Laci?', 'Laci ini akan dihapus dari rak.')">
                                            @csrf
                                            <button type="submit" class="p-1 text-gray-400 hover:text-rose-600 transition" title="Hapus Laci">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="text-[11px] text-gray-600">
                                    <span class="font-semibold block text-gray-400 text-[10px] uppercase">Kategori Buku di Laci ini:</span>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @if($categoriesInLaci->isNotEmpty())
                                            @foreach($categoriesInLaci as $catName)
                                                <span class="px-2 py-0.5 rounded-md text-[9.5px] font-extrabold bg-brand-50 text-brand-700 border border-brand-200">
                                                    {{ $catName }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-[10px] text-gray-400 italic">Belum ada buku di laci ini</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-gray-200/60 flex items-center justify-between text-[10.5px]">
                                    <span class="text-gray-500 font-medium">{{ $laci->keterangan ?? 'Laci Tingkat ' . $laci->nomor_laci }}</span>
                                    <span class="font-black text-gray-900">{{ $laci->buku->count() }} Judul</span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-4 text-center text-gray-400 text-xs italic bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                Belum ada laci pada rak ini. Silakan klik "+ Tambah Laci".
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border-2 border-gray-200 p-8 text-center text-gray-400 font-medium">
                Belum ada rak perpustakaan terdaftar. Silakan klik "+ Tambah Rak Baru".
            </div>
        @endforelse

        <div class="p-3">
            {{ $rakList->links() }}
        </div>
    </div>

    <div x-show="openAddModal" @click.self="openAddModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto" x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/70">
                <h3 class="text-sm font-black text-gray-900">Tambah Rak Buku Baru</h3>
                <button @click="openAddModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm">&times;</button>
            </div>

            <form action="{{ route('admin.rak.store') }}" method="POST" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Kode Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_rak" required placeholder="Contoh: RAK-01" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_rak" required placeholder="Contoh: Rak Kejuruan Rekayasa Perangkat Lunak" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Lokasi Ruang Perpustakaan</label>
                    <input type="text" name="lokasi" placeholder="Contoh: Sayap Kiri Gedung Utama" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Jumlah Laci Awal</label>
                    <input type="number" name="jumlah_laci" value="3" min="1" max="20" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    <p class="text-[10px] text-gray-400 mt-1">Sistem otomatis membuatkan Laci 1, Laci 2, dst. sesuai jumlah ini.</p>
                </div>
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                    <button type="button" @click="openAddModal = false" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold rounded-lg transition shadow-sm">Simpan Rak</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openEditModal" @click.self="openEditModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto" x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/70">
                <h3 class="text-sm font-black text-gray-900">Edit Rak Lokasi</h3>
                <button @click="openEditModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm">&times;</button>
            </div>

            <form :action="'/admin/rak/update/' + editData.id" method="POST" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Kode Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_rak" x-model="editData.kode_rak" required class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_rak" x-model="editData.nama_rak" required class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Lokasi Ruang Perpustakaan</label>
                    <input type="text" name="lokasi" x-model="editData.lokasi" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                    <button type="button" @click="openEditModal = false" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold rounded-lg transition shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openAddLaciModal" @click.self="openAddLaciModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto" x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/70">
                <div>
                    <h3 class="text-sm font-black text-gray-900">Tambah Laci / Tingkat Baru</h3>
                    <p class="text-[10px] text-gray-500">Menambahkan laci pada <span class="font-bold text-gray-800" x-text="laciData.rak_nama"></span></p>
                </div>
                <button @click="openAddLaciModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm">&times;</button>
            </div>

            <form :action="'/admin/rak/' + laciData.rak_id + '/laci'" method="POST" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama / Label Laci <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_laci" required placeholder="Contoh: Laci 4 - Tingkat Bawah" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nomor Urut Tingkat (Opsional)</label>
                    <input type="number" name="nomor_laci" min="1" placeholder="Auto nomor berikutnya" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Keterangan / Catatan Penempatan</label>
                    <textarea name="keterangan" rows="2" placeholder="Contoh: Khusus modul kejuruan kelas 10 & 11" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium"></textarea>
                </div>
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                    <button type="button" @click="openAddLaciModal = false" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-lg transition shadow-sm">Tambah Laci</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openEditLaciModal" @click.self="openEditLaciModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto" x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/70">
                <h3 class="text-sm font-black text-gray-900">Edit Data Laci Rak</h3>
                <button @click="openEditLaciModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm">&times;</button>
            </div>

            <form :action="'/admin/rak/laci/update/' + laciData.id" method="POST" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama / Label Laci <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_laci" x-model="laciData.nama_laci" required class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nomor Urut Tingkat <span class="text-rose-500">*</span></label>
                    <input type="number" name="nomor_laci" x-model="laciData.nomor_laci" required min="1" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Keterangan Penempatan</label>
                    <textarea name="keterangan" x-model="laciData.keterangan" rows="2" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium"></textarea>
                </div>
                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                    <button type="button" @click="openEditLaciModal = false" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold rounded-lg transition shadow-sm">Simpan Laci</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
