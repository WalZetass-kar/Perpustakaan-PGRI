@extends('layouts.dashboard')

@section('title', 'Manajemen Kategori Buku')
@section('page_heading', 'Manajemen Kategori Kejuruan')

@section('content')
<div class="space-y-5" x-data="{ openAddModal: false, openEditModal: false, editData: {} }" x-init="openAddModal = false; openEditModal = false; editData = {}">

    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-black text-gray-900">Daftar Kategori Kejuruan Terdaftar</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Kelola pengelompokan klasifikasi modul dan bidang keahlian di SMK PGRI Pekanbaru</p>
        </div>
        <button @click="openAddModal = true" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition duration-300 shadow-md hover:shadow-lg transform active:scale-95 flex items-center gap-2">
            <i class="fa-solid fa-plus text-emerald-400"></i>
            <span>Tambah Kategori Baru</span>
        </button>
    </div>

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider font-extrabold">
                        <th class="py-3 px-4 font-bold">No</th>
                        <th class="py-3 px-4 font-bold">Nama Kategori</th>
                        <th class="py-3 px-4 font-bold">Deskripsi Ringkas</th>
                        <th class="py-3 px-4 font-bold text-center">Jumlah Buku</th>
                        <th class="py-3 px-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    @forelse($kategoriList as $index => $kat)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3 px-4 font-mono text-gray-400 font-bold">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900">{{ $kat->nama }}</td>
                            <td class="py-3 px-4 text-gray-600 max-w-md truncate">{{ $kat->deskripsi ?? '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-brand-50 text-brand-700 border border-brand-200">
                                    {{ $kat->buku_count ?? 0 }} Judul
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="relative flex items-center justify-end" x-data="{ open: false }">
                                    <button @click.stop="open = !open" type="button" class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 transition">
                                        <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                                    </button>
                                    <div x-show="open" x-cloak @click.outside="open = false"
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute right-0 top-8 z-50 w-36 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                                        <button type="button" @click="editData = {{ json_encode([
                                            'id' => $kat->id,
                                            'nama' => $kat->nama,
                                            'deskripsi' => $kat->deskripsi ?? ''
                                        ]) }}; openEditModal = true; open = false" class="w-full flex items-center gap-2 px-3 py-2 text-[11px] font-bold text-amber-700 hover:bg-amber-50 transition">
                                            <i class="fa-solid fa-pen-to-square w-3.5 text-center"></i>
                                            <span>Edit</span>
                                        </button>
                                        <form action="{{ route('admin.kategori.delete', $kat->id) }}" method="POST" onsubmit="return confirmDelete(event, 'Hapus Kategori?', 'Klasifikasi kategori ini akan dihapus.')">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-[11px] font-bold text-rose-600 hover:bg-rose-50 transition">
                                                <i class="fa-solid fa-trash-can w-3.5 text-center"></i>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 font-medium">Belum ada data kategori kejuruan terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="openAddModal" @click.self="openAddModal = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">

            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/70">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Tambah Kategori Baru</h3>
                        <p class="text-[10px] text-gray-500">Masukkan nama bidang klasifikasi</p>
                    </div>
                </div>
                <button @click="openAddModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 hover:text-gray-900 flex items-center justify-center transition font-bold text-sm">&times;</button>
            </div>

            <form action="{{ route('admin.kategori.store') }}" method="POST" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" required placeholder="Contoh: Pemrograman & Web (RPL)" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Deskripsi Ringkas</label>
                    <textarea name="deskripsi" rows="2" placeholder="Deskripsi rincian bidang keahlian..." class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2 shrink-0">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 transition shadow-md hover:shadow-lg transform active:scale-95 text-xs">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openEditModal" @click.self="openEditModal = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">

            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-amber-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Edit Data Kategori</h3>
                        <p class="text-[10px] text-gray-500">Perbarui rincian klasifikasi</p>
                    </div>
                </div>
                <button @click="openEditModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 hover:text-gray-900 flex items-center justify-center transition font-bold text-sm">&times;</button>
            </div>

            <form :action="'{{ url('/admin/kategori/update') }}/' + editData.id" method="POST" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" x-model="editData.nama" required class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Deskripsi Ringkas</label>
                    <textarea name="deskripsi" x-model="editData.deskripsi" rows="2" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2 shrink-0">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 transition shadow-md hover:shadow-lg transform active:scale-95 text-xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
