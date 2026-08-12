@extends('layouts.dashboard')

@section('title', 'Manajemen Rak')
@section('page_heading', 'Manajemen Rak Perpustakaan')

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
        <div>
            <h2 class="text-base font-bold text-gray-900">Daftar Rak Penyimpanan Buku</h2>
            <p class="text-xs text-gray-500">Penataan lokasi boks rak dalam Ruang Utama Perpustakaan SMK PGRI</p>
        </div>
        <button @click="openAddModal = true" class="px-4 py-2 bg-brand-700 text-white font-semibold text-xs rounded-lg hover:bg-brand-800 transition">
            + Tambah Rak Baru
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-5 font-semibold">Kode Rak</th>
                    <th class="py-3 px-5 font-semibold">Nama Rak</th>
                    <th class="py-3 px-5 font-semibold">Posisi Ruang Perpustakaan</th>
                    <th class="py-3 px-5 font-semibold">Kategori Spesifik</th>
                    <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @foreach($rakList as $rk)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-5 font-mono font-bold text-gray-900">{{ $rk->kode_rak }}</td>
                        <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $rk->nama_rak }}</td>
                        <td class="py-3.5 px-5">{{ $rk->lokasi }}</td>
                        <td class="py-3.5 px-5 text-gray-500">{{ $rk->kategori->nama ?? 'Umum' }}</td>
                        <td class="py-3.5 px-5 text-right space-x-2">
                            <button @click="editData = {
                                id: {{ $rk->id }},
                                kode_rak: '{{ $rk->kode_rak }}',
                                nama_rak: '{{ addslashes($rk->nama_rak) }}',
                                lokasi: '{{ addslashes($rk->lokasi) }}',
                                kategori_id: '{{ $rk->kategori_id }}'
                            }; openEditModal = true" class="text-brand-700 font-semibold hover:underline">Edit</button>
                            <form action="{{ route('admin.rak.delete', $rk->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" onclick="return confirm('Hapus data rak ini?')" class="text-rose-600 font-semibold hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Form Tambah Rak (Popup) -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4" x-transition x-cloak>
        <div class="bg-white rounded-xl max-w-md w-full p-6 space-y-4 shadow-xl border border-gray-200" @click.away="openAddModal = false">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-900">Tambah Rak Baru</h3>
                <button @click="openAddModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('admin.rak.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Kode Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_rak" required placeholder="Contoh: RAK-RPL-01" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_rak" required placeholder="Contoh: Rak Pemrograman & Web" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Posisi Ruang Perpustakaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="lokasi" required placeholder="Contoh: Baris Depan - Samping Meja Pustakawan" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Kategori Spesifik (Opsional)</label>
                    <select name="kategori_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-bold rounded-lg hover:bg-brand-800 transition">Simpan Rak</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Form Edit Rak (Popup) -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4" x-transition x-cloak>
        <div class="bg-white rounded-xl max-w-md w-full p-6 space-y-4 shadow-xl border border-gray-200" @click.away="openEditModal = false">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-900">Edit Data Rak</h3>
                <button @click="openEditModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form :action="'{{ url('/admin/rak/update') }}/' + editData.id" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Kode Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_rak" x-model="editData.kode_rak" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_rak" x-model="editData.nama_rak" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Posisi Ruang Perpustakaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="lokasi" x-model="editData.lokasi" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Kategori Spesifik (Opsional)</label>
                    <select name="kategori_id" x-model="editData.kategori_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                        @endforeach
                    </select>
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
