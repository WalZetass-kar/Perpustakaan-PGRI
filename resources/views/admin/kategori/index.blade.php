@extends('layouts.dashboard')

@section('title', 'Manajemen Kategori')
@section('page_heading', 'Manajemen Kategori Buku')

@section('content')
<div class="space-y-6" x-data="{ openAddModal: false, openEditModal: false, editData: {} }">

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-xl flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <h2 class="text-base font-bold text-gray-900">Daftar Kategori Klasifikasi Buku</h2>
        <button @click="openAddModal = true" class="px-4 py-2 bg-brand-700 text-white font-semibold text-xs rounded-lg hover:bg-brand-800 transition">
            + Tambah Kategori Baru
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-5 font-semibold">Nama Kategori</th>
                    <th class="py-3 px-5 font-semibold">Slug System</th>
                    <th class="py-3 px-5 font-semibold">Deskripsi</th>
                    <th class="py-3 px-5 font-semibold">Jumlah Koleksi</th>
                    <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @foreach($kategoriList as $kat)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-5 font-bold text-gray-900">{{ $kat->nama }}</td>
                        <td class="py-3.5 px-5 font-mono text-gray-500">{{ $kat->slug }}</td>
                        <td class="py-3.5 px-5 text-gray-600 max-w-xs truncate">{{ $kat->deskripsi ?? '-' }}</td>
                        <td class="py-3.5 px-5 font-bold text-brand-700">{{ $kat->buku_count }} Judul</td>
                        <td class="py-3.5 px-5 text-right space-x-2">
                            <button @click="editData = {
                                id: {{ $kat->id }},
                                nama: '{{ addslashes($kat->nama) }}',
                                deskripsi: '{{ addslashes($kat->deskripsi ?? '') }}'
                            }; openEditModal = true" class="text-brand-700 font-semibold hover:underline">Edit</button>
                            <form action="{{ route('admin.kategori.delete', $kat->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" onclick="return confirm('Hapus kategori ini?')" class="text-rose-600 font-semibold hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Form Tambah Kategori (Popup) -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4" x-transition x-cloak>
        <div class="bg-white rounded-xl max-w-md w-full p-6 space-y-4 shadow-xl border border-gray-200" @click.away="openAddModal = false">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-900">Tambah Kategori Baru</h3>
                <button @click="openAddModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('admin.kategori.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" required placeholder="Contoh: Pemrograman & Web" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Deskripsi Ringkas</label>
                    <textarea name="deskripsi" rows="3" placeholder="Deskripsi atau rincian bidang kategori..." class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700"></textarea>
                </div>
                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-bold rounded-lg hover:bg-brand-800 transition">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Form Edit Kategori (Popup) -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4" x-transition x-cloak>
        <div class="bg-white rounded-xl max-w-md w-full p-6 space-y-4 shadow-xl border border-gray-200" @click.away="openEditModal = false">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-900">Edit Data Kategori</h3>
                <button @click="openEditModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form :action="'{{ url('/admin/kategori/update') }}/' + editData.id" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" x-model="editData.nama" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Deskripsi Ringkas</label>
                    <textarea name="deskripsi" x-model="editData.deskripsi" rows="3" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700"></textarea>
                </div>
                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-bold rounded-lg hover:bg-brand-800 transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
