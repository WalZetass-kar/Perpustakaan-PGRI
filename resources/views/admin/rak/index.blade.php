@extends('layouts.dashboard')

@section('title', 'Manajemen Rak')
@section('page_heading', 'Manajemen Rak Perpustakaan')

@section('content')
<div class="space-y-6" x-data="{ openAddModal: false }">

    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <h2 class="text-base font-bold text-gray-900">Daftar Rak & Lokasi Penyimpanan</h2>
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
                    <th class="py-3 px-5 font-semibold">Lokasi</th>
                    <th class="py-3 px-5 font-semibold">Kategori Spesifik</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @foreach($rakList as $rk)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-5 font-mono font-bold text-gray-900">{{ $rk->kode_rak }}</td>
                        <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $rk->nama_rak }}</td>
                        <td class="py-3.5 px-5">{{ $rk->lokasi }}</td>
                        <td class="py-3.5 px-5 text-gray-500">{{ $rk->kategori->nama ?? 'Umum' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Form Tambah Rak (Popup) -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4" x-transition>
        <div class="bg-white rounded-xl max-w-md w-full p-6 space-y-4 shadow-xl border border-gray-200" @click.away="openAddModal = false">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-900">Tambah Rak Baru</h3>
                <button @click="openAddModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('admin.rak.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Kode Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_rak" required placeholder="Contoh: RAK-C1" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_rak" required placeholder="Contoh: Rak Jurnal & Sains" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Lokasi Gedung/Lantai <span class="text-rose-500">*</span></label>
                    <input type="text" name="lokasi" required placeholder="Contoh: Lantai 2 - Sayap Selatan" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700">
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

</div>
@endsection
