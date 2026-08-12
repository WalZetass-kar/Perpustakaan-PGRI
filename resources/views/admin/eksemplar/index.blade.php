@extends('layouts.dashboard')

@section('title', 'Manajemen Eksemplar')
@section('page_heading', 'Manajemen Fisik Eksemplar')

@section('content')
<div class="space-y-6" x-data="{ openAddModal: false }">

    <div class="flex items-center justify-between">
        <h2 class="text-base font-bold text-gray-900">Registrasi Fisik & Barcode Eksemplar</h2>
        <button @click="openAddModal = true" class="px-4 py-2 bg-brand-700 text-white font-semibold text-xs rounded-lg hover:bg-brand-800 transition">
            + Tambah Eksemplar Baru
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-5 font-semibold">Kode Eksemplar</th>
                    <th class="py-3 px-5 font-semibold">Barcode</th>
                    <th class="py-3 px-5 font-semibold">Judul Buku</th>
                    <th class="py-3 px-5 font-semibold">Lokasi Rak</th>
                    <th class="py-3 px-5 font-semibold">Kondisi</th>
                    <th class="py-3 px-5 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @foreach($eksemplarList as $ex)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-5 font-mono font-bold text-gray-900">{{ $ex->kode_eksemplar }}</td>
                        <td class="py-3.5 px-5 font-mono text-gray-600 bg-gray-50 px-2 py-0.5 rounded w-max">{{ $ex->barcode }}</td>
                        <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $ex->buku->judul ?? '-' }}</td>
                        <td class="py-3.5 px-5 font-mono">{{ $ex->rak->kode_rak ?? '-' }}</td>
                        <td class="py-3.5 px-5 uppercase text-[10px] font-bold text-gray-600">{{ $ex->kondisi }}</td>
                        <td class="py-3.5 px-5">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase">{{ $ex->status }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-100">
            {{ $eksemplarList->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4" x-transition>
        <div class="bg-white rounded-xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-gray-200">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-900">Registrasi Eksemplar Baru</h3>
                <button @click="openAddModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('admin.eksemplar.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Pilih Master Buku</label>
                    <select name="buku_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                        @foreach($bukuList as $bk)
                            <option value="{{ $bk->id }}">{{ $bk->judul }} (ISBN: {{ $bk->isbn }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kode Eksemplar</label>
                        <input type="text" name="kode_eksemplar" required placeholder="EX-xxx-001" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Barcode ID</label>
                        <input type="text" name="barcode" required placeholder="BC10001" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kondisi Buku</label>
                        <select name="kondisi" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                            <option value="baik">Baik</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Pilih Rak</label>
                        <select name="rak_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                            @foreach($rakList as $rk)
                                <option value="{{ $rk->id }}">{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-bold rounded-lg hover:bg-brand-800">Simpan Eksemplar</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
