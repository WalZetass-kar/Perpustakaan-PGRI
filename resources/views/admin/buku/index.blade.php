@extends('layouts.dashboard')

@section('title', 'Manajemen Koleksi Buku')
@section('page_heading', 'Koleksi Buku Perpustakaan')

@push('styles')
<style>
    .dataTables_wrapper {
        padding: 1rem;
        font-size: 0.75rem;
    }
    .dataTables_wrapper .dataTables_length {
        margin-bottom: 0.75rem;
    }
    .dataTables_wrapper .dataTables_length select {
        padding: 0.35rem 1.75rem 0.35rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: #374151;
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        outline: none;
    }
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 0.75rem;
    }
    .dataTables_wrapper .dataTables_filter input {
        padding: 0.35rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
        color: #111827;
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        outline: none;
        min-width: 220px;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #b91c1c;
        background-color: #ffffff;
    }
    .dataTables_wrapper .dataTables_info {
        padding-top: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
    }
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 0.75rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        display: inline-block;
        padding: 0.35rem 0.65rem;
        margin-left: 0.25rem;
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #374151 !important;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #fee2e2 !important;
        border-color: #fca5a5 !important;
        color: #b91c1c !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #b91c1c !important;
        border-color: #b91c1c !important;
        color: #ffffff !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        opacity: 0.4;
        cursor: not-allowed;
        background: #f9fafb !important;
        border-color: #e5e7eb !important;
        color: #9ca3af !important;
    }
    table.dataTable no-footer {
        border-bottom: 1px solid #e5e7eb;
    }
</style>
@endpush

@section('content')
<div class="space-y-5" x-data="{ 
    openAddModal: false, 
    openEditModal: false, 
    editData: {}, 
    allRaks: {{ json_encode($rakList) }},
    addRakId: '',
    addLaciId: '',
    editRakId: '',
    editLaciId: '',
    get addAvailableLacis() {
        const found = this.allRaks.find(r => r.id == this.addRakId);
        return found ? found.laci : [];
    },
    get editAvailableLacis() {
        const found = this.allRaks.find(r => r.id == this.editRakId);
        return found ? found.laci : [];
    }
}" x-init="openAddModal = false; openEditModal = false; editData = {}" id="buku-container">

    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-black text-gray-900">Katalog Judul Buku & Stok Fisik</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Kelola data buku, jumlah stok fisik, serta nomor rak dan laci penyimpanan</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <button @click="addRakId = ''; addLaciId = ''; openAddModal = true" class="px-4 py-2 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center gap-2 shrink-0">
                <i class="fa-solid fa-plus text-emerald-300"></i>
                <span>Tambah Buku</span>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table id="tabel-buku" class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider font-extrabold">
                        <th class="py-3 px-4 font-bold">Buku & Cover</th>
                        <th class="py-3 px-4 font-bold">Penulis / Penerbit</th>
                        <th class="py-3 px-4 font-bold">Kategori & Lokasi Rak/Laci</th>
                        <th class="py-3 px-4 font-bold text-center">Stok Fisik</th>
                        <th class="py-3 px-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                </tbody>
            </table>
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
                        <i class="fa-solid fa-book text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Tambah Koleksi Buku Baru</h3>
                        <p class="text-[10px] text-gray-500">Masukkan detail buku, lokasi rak & laci, serta jumlah stok fisik</p>
                    </div>
                </div>
                <button @click="openAddModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm">&times;</button>
            </div>

            <form action="{{ route('admin.buku.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Judul Buku <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" required placeholder="Contoh: Pemrograman Web Dasar" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="grid grid-cols-3 gap-2.5">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">ISBN (Opsional)</label>
                        <input type="text" name="isbn" placeholder="978-602-xxx" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Tahun Terbit <span class="text-rose-500">*</span></label>
                        <input type="number" name="tahun_terbit" value="{{ date('Y') }}" required min="1900" max="{{ date('Y') + 1 }}" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Jumlah Stok <span class="text-rose-500">*</span></label>
                        <input type="number" name="total_quantity" value="1" required min="1" max="1000" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium font-bold text-brand-700">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Penulis Buku</label>
                        <select name="penulis_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Penulis ▼ --</option>
                            @foreach($penulisList as $pn)
                                <option value="{{ $pn->id }}">{{ $pn->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Penerbit Buku</label>
                        <select name="penerbit_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Penerbit ▼ --</option>
                            @foreach($penerbitList as $pb)
                                <option value="{{ $pb->id }}">{{ $pb->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2.5">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Kategori Buku</label>
                        <select name="kategori_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Kategori ▼ --</option>
                            @foreach($kategoriList as $kt)
                                <option value="{{ $kt->id }}">{{ $kt->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Lokasi Rak</label>
                        <select name="rak_id" x-model="addRakId" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Rak ▼ --</option>
                            @foreach($rakList as $rk)
                                <option value="{{ $rk->id }}">{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Tingkat / Laci</label>
                        <select name="rak_laci_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Laci ▼ --</option>
                            <template x-for="laci in addAvailableLacis" :key="laci.id">
                                <option :value="laci.id" x-text="laci.nama_laci"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Cover Buku (Opsional)</label>
                    <input type="file" name="cover" accept="image/*" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none text-[11px] file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[10px] file:font-bold file:bg-brand-700 file:text-white">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Sinopsis / Ringkasan (Opsional)</label>
                    <textarea name="sinopsis" rows="2" placeholder="Ringkasan materi atau buku..." class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium resize-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">
                        Keterangan Posisi Buku (Opsional)
                        <span class="text-[10px] font-normal text-gray-400 ml-1">— posisi fisik buku di dalam laci/rak</span>
                    </label>
                    <textarea name="keterangan_posisi" rows="2"
                              placeholder="Contoh: Baris ke-2 dari depan, urutan ke-5 dari kiri. Atau: Di pojok kanan laci bagian bawah."
                              class="w-full px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-lg focus:ring-1.5 focus:ring-amber-500 focus:bg-white focus:outline-none font-medium text-gray-700 placeholder-gray-400 resize-none"></textarea>
                    <p class="text-[10px] text-amber-700 mt-0.5 font-medium">
                        <i class="fa-solid fa-circle-info mr-0.5"></i>
                        Membantu petugas menemukan buku secara lebih presisi di dalam laci.
                    </p>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2 shrink-0">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 text-xs flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Buku</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openEditModal" @click.self="openEditModal = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto"          x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-amber-50/50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Edit Data Buku</h3>
                        <p class="text-[10px] text-gray-500">Perbarui rincian, lokasi rak & laci, serta stok buku</p>
                    </div>
                </div>
                <button @click="openEditModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm">&times;</button>
            </div>

            <form :action="'{{ url('/admin/buku/update') }}/' + editData.id" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Judul Buku <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" x-model="editData.judul" required class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="grid grid-cols-3 gap-2.5">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">ISBN (Opsional)</label>
                        <input type="text" name="isbn" x-model="editData.isbn" placeholder="978-602-xxx" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Tahun Terbit <span class="text-rose-500">*</span></label>
                        <input type="number" name="tahun_terbit" x-model="editData.tahun_terbit" required min="1900" max="{{ date('Y') + 1 }}" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Jumlah Stok <span class="text-rose-500">*</span></label>
                        <input type="number" name="total_quantity" x-model="editData.total_quantity" required min="1" max="1000" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium font-bold text-brand-700">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Penulis Buku</label>
                        <select name="penulis_id" x-model="editData.penulis_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Penulis ▼ --</option>
                            @foreach($penulisList as $pn)
                                <option value="{{ $pn->id }}">{{ $pn->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Penerbit Buku</label>
                        <select name="penerbit_id" x-model="editData.penerbit_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Penerbit ▼ --</option>
                            @foreach($penerbitList as $pb)
                                <option value="{{ $pb->id }}">{{ $pb->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2.5">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Kategori Buku</label>
                        <select name="kategori_id" x-model="editData.kategori_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Kategori ▼ --</option>
                            @foreach($kategoriList as $kt)
                                <option value="{{ $kt->id }}">{{ $kt->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Lokasi Rak</label>
                        <select name="rak_id" x-model="editRakId" @change="editData.rak_id = editRakId" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Rak ▼ --</option>
                            @foreach($rakList as $rk)
                                <option value="{{ $rk->id }}">{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Tingkat / Laci</label>
                        <select name="rak_laci_id" x-model="editData.rak_laci_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Laci ▼ --</option>
                            <template x-for="laci in editAvailableLacis" :key="laci.id">
                                <option :value="laci.id" x-text="laci.nama_laci" :selected="laci.id == editData.rak_laci_id"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Ganti Cover Buku (Opsional)</label>
                    <input type="file" name="cover" accept="image/*" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none text-[11px] file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[10px] file:font-bold file:bg-brand-700 file:text-white">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Sinopsis / Ringkasan (Opsional)</label>
                    <textarea name="sinopsis" x-model="editData.sinopsis" rows="2" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium resize-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">
                        Keterangan Posisi Buku (Opsional)
                        <span class="text-[10px] font-normal text-gray-400 ml-1">— posisi fisik buku di dalam laci/rak</span>
                    </label>
                    <textarea name="keterangan_posisi" x-model="editData.keterangan_posisi" rows="2"
                              placeholder="Contoh: Baris ke-2 dari depan, urutan ke-5 dari kiri. Atau: Di pojok kanan laci bagian bawah."
                              class="w-full px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-lg focus:ring-1.5 focus:ring-amber-500 focus:bg-white focus:outline-none font-medium text-gray-700 placeholder-gray-400 resize-none"></textarea>
                    <p class="text-[10px] text-amber-700 mt-0.5 font-medium">
                        <i class="fa-solid fa-circle-info mr-0.5"></i>
                        Membantu petugas menemukan buku secara lebih presisi di dalam laci.
                    </p>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2 shrink-0">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 text-xs flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const tabelBuku = $('#tabel-buku').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.buku') }}",
                type: "GET"
            },
            columns: [
                { data: 'buku', name: 'judul', orderable: true, searchable: true },
                { data: 'penulis', name: 'penulis_id', orderable: true, searchable: true },
                { data: 'kategori', name: 'kategori_id', orderable: true, searchable: true },
                { data: 'stok', name: 'available_quantity', orderable: true, searchable: false, className: 'text-center' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-right' }
            ],
            order: [[0, 'asc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                processing: '<div class="py-4 text-center text-xs font-bold text-brand-700 flex items-center justify-center gap-2"><i class="fa-solid fa-spinner fa-spin text-sm"></i> Memuat data koleksi...</div>',
                search: '',
                searchPlaceholder: 'Cari judul, ISBN, penulis...',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ buku',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 buku',
                infoFiltered: '(disaring dari _MAX_ total)',
                zeroRecords: 'Tidak ditemukan data buku yang sesuai',
                paginate: {
                    first: '<i class="fa-solid fa-angles-left"></i>',
                    previous: '<i class="fa-solid fa-chevron-left"></i>',
                    next: '<i class="fa-solid fa-chevron-right"></i>',
                    last: '<i class="fa-solid fa-angles-right"></i>'
                }
            }
        });

        $(document).on('click', '.btn-edit-buku', function() {
            const rawData = $(this).attr('data-buku');
            if (rawData) {
                const parsed = JSON.parse(rawData);
                const container = document.getElementById('buku-container');
                if (container && container._x_dataStack) {
                    const alpineData = container._x_dataStack[0];
                    alpineData.editData = parsed;
                    alpineData.editRakId = parsed.rak_id ? String(parsed.rak_id) : '';
                    alpineData.editLaciId = parsed.rak_laci_id ? String(parsed.rak_laci_id) : '';
                    alpineData.openEditModal = true;
                }
            }
        });
    });
</script>
@endpush
