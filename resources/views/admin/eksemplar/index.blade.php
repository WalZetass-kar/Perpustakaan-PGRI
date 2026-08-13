@extends('layouts.dashboard')

@section('title', 'Manajemen Eksemplar Buku')
@section('page_heading', 'Manajemen Eksemplar & Barcode ID')

@section('content')
<div class="space-y-6" x-data="{ openAddModal: false, openEditModal: false, editData: {} }" x-init="openAddModal = false; openEditModal = false; editData = {}">

    <!-- Information Banner -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-brand-900 to-brand-800 text-white border-2 border-brand-950 shadow-md flex items-center justify-between gap-4">
        <div class="space-y-1">
            <h2 class="text-sm font-black text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Fungsi Modul Eksemplar Fisik</span>
            </h2>
            <p class="text-xs text-red-100 max-w-3xl leading-relaxed font-medium">
                Setiap 1 Master Judul Buku dapat memiliki banyak fisik buku (Eksemplar). Setiap fisik buku ditempeli stiker **Barcode ID unik** untuk keperluan scanning cepat sirkulasi pinjam/kembali di meja pustakawan.
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('admin.eksemplar.cetak_barcode') }}" target="_blank" class="px-4 py-2.5 bg-amber-400 hover:bg-amber-300 text-brand-950 font-extrabold text-xs rounded-xl transition shadow-md flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Stiker Barcode (A4)</span>
            </a>
            <button @click="openAddModal = true" class="px-4 py-2.5 bg-white text-brand-700 font-extrabold text-xs rounded-xl hover:bg-gray-100 transition shadow-md hover:shadow-lg transform active:scale-95 shrink-0 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Registrasi Eksemplar Baru</span>
            </button>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b-2 border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <form action="{{ route('admin.eksemplar') }}" method="GET" class="w-full sm:w-80 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barcode ID, kode eksemplar, judul..."
                    class="w-full pl-9 pr-3 py-2 bg-gray-50 border-2 border-gray-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
            <span class="text-xs font-bold text-gray-500">Total: {{ $eksemplarList->total() }} Fisik Eksemplar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider font-extrabold">
                        <th class="py-3.5 px-5 font-bold">Barcode ID</th>
                        <th class="py-3.5 px-5 font-bold">Kode Eksemplar</th>
                        <th class="py-3.5 px-5 font-bold">Master Judul Buku</th>
                        <th class="py-3.5 px-5 font-bold">Posisi Rak</th>
                        <th class="py-3.5 px-5 font-bold">Kondisi Fisik</th>
                        <th class="py-3.5 px-5 font-bold">Status Sirkulasi</th>
                        <th class="py-3.5 px-5 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    @foreach($eksemplarList as $ex)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3.5 px-5 font-mono font-black text-brand-700">{{ $ex->barcode }}</td>
                            <td class="py-3.5 px-5 font-mono text-gray-900 font-bold">{{ $ex->kode_eksemplar }}</td>
                            <td class="py-3.5 px-5 font-bold text-gray-900 max-w-xs truncate">{{ $ex->buku->judul ?? '-' }}</td>
                            <td class="py-3.5 px-5">
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-800 font-mono text-[10px] border border-gray-200">
                                    {{ $ex->rak->kode_rak ?? '-' }} - {{ $ex->rak->nama_rak ?? '' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 capitalize font-bold">
                                @if($ex->kondisi === 'baik')
                                    <span class="text-emerald-700 font-bold">Baik</span>
                                @elseif($ex->kondisi === 'rusak_ringan')
                                    <span class="text-amber-600 font-bold">Rusak Ringan</span>
                                @else
                                    <span class="text-rose-600 font-bold">Rusak Berat</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5">
                                @if($ex->status === 'tersedia')
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">Tersedia</span>
                                @elseif($ex->status === 'dipinjam')
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200 uppercase">Dipinjam</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200 uppercase">{{ $ex->status }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right space-x-1.5">
                                <button @click="editData = {
                                    id: {{ $ex->id }},
                                    kode_eksemplar: '{{ addslashes($ex->kode_eksemplar) }}',
                                    barcode: '{{ addslashes($ex->barcode) }}',
                                    kondisi: '{{ $ex->kondisi }}',
                                    rak_id: '{{ $ex->rak_id }}',
                                    status: '{{ $ex->status }}'
                                }; openEditModal = true" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white font-extrabold rounded-lg text-[10px] transition shadow-2xs">
                                    Edit
                                </button>
                                <form action="{{ route('admin.eksemplar.delete', $ex->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Hapus Eksemplar Buku?', 'Fisik eksemplar ini akan dihapus permanen dari perpustakaan.')">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-lg text-[10px] transition shadow-2xs">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $eksemplarList->links() }}
        </div>
    </div>

    <!-- Premium Backdrop Glass Modal Form Tambah Eksemplar -->
    <div x-show="openAddModal" @click.self="openAddModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 space-y-5 shadow-2xl border-2 border-gray-200 transform transition-all my-8">
            <div class="flex items-center justify-between border-b-2 border-gray-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Registrasi Eksemplar Baru</h3>
                        <p class="text-[11px] text-gray-500">Daftarkan fisik buku baru ke dalam koleksi</p>
                    </div>
                </div>
                <button @click="openAddModal = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-900 flex items-center justify-center transition font-bold">&times;</button>
            </div>

            <form action="{{ route('admin.eksemplar.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Pilih Master Buku <span class="text-rose-500">*</span></label>
                    <select name="buku_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                        <option value="">-- Pilih Buku Master --</option>
                        @foreach($bukuList as $bk)
                            <option value="{{ $bk->id }}">{{ $bk->judul }} (ISBN: {{ $bk->isbn }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Kode Eksemplar <span class="text-rose-500">*</span></label>
                        <input type="text" name="kode_eksemplar" required placeholder="EX-WEB-003" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Barcode ID <span class="text-rose-500">*</span></label>
                        <input type="text" name="barcode" required placeholder="BC882003" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Kondisi Buku <span class="text-rose-500">*</span></label>
                        <select name="kondisi" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="baik">Baik</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Pilih Rak <span class="text-rose-500">*</span></label>
                        <select name="rak_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Rak Lokasi --</option>
                            @foreach($rakList as $rk)
                                <option value="{{ $rk->id }}">{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t-2 border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="openAddModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 transition shadow-md hover:shadow-lg transform active:scale-95">Simpan Eksemplar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Premium Backdrop Glass Modal Form Edit Eksemplar -->
    <div x-show="openEditModal" @click.self="openEditModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 space-y-5 shadow-2xl border-2 border-gray-200 transform transition-all my-8">
            <div class="flex items-center justify-between border-b-2 border-gray-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Edit Data Eksemplar</h3>
                        <p class="text-[11px] text-gray-500">Perbarui rincian kondisi fisik & status sirkulasi</p>
                    </div>
                </div>
                <button @click="openEditModal = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-900 flex items-center justify-center transition font-bold">&times;</button>
            </div>

            <form :action="'{{ url('/admin/eksemplar/update') }}/' + editData.id" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Kode Eksemplar <span class="text-rose-500">*</span></label>
                        <input type="text" name="kode_eksemplar" x-model="editData.kode_eksemplar" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Barcode ID <span class="text-rose-500">*</span></label>
                        <input type="text" name="barcode" x-model="editData.barcode" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Kondisi Buku <span class="text-rose-500">*</span></label>
                        <select name="kondisi" x-model="editData.kondisi" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="baik">Baik</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Pilih Rak <span class="text-rose-500">*</span></label>
                        <select name="rak_id" x-model="editData.rak_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            @foreach($rakList as $rk)
                                <option value="{{ $rk->id }}">{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Status <span class="text-rose-500">*</span></label>
                        <select name="status" x-model="editData.status" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="tersedia">Tersedia</option>
                            <option value="dipinjam">Dipinjam</option>
                            <option value="rusak">Rusak</option>
                            <option value="hilang">Hilang</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t-2 border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="openEditModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 transition shadow-md hover:shadow-lg transform active:scale-95">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
