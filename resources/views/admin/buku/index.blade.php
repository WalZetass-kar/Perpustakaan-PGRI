@extends('layouts.dashboard')

@section('title', 'Manajemen Koleksi Buku')
@section('page_heading', 'Manajemen Master Koleksi Buku')

@section('content')
<div class="space-y-6" x-data="{ openAddModal: false, openEditModal: false, editData: {} }">
    
    <!-- Top Action Toolbar -->
    <div class="bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-black text-gray-900">Daftar Judul Buku Terdaftar</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Kelola katalog master buku, ISBN, penulis, penerbit, dan rak lokasi</p>
        </div>
        <button @click="openAddModal = true" class="px-4 py-2.5 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition duration-300 shadow-md hover:shadow-lg transform active:scale-95 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Tambah Buku Baru</span>
        </button>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b-2 border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <form action="{{ route('admin.buku') }}" method="GET" class="w-full sm:w-80 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, ISBN, penulis..."
                    class="w-full pl-9 pr-3 py-2 bg-gray-50 border-2 border-gray-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
            <span class="text-xs font-bold text-gray-500">Total: {{ $bukuList->total() }} Judul</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider font-extrabold">
                        <th class="py-3.5 px-5 font-bold">ISBN &amp; Tahun</th>
                        <th class="py-3.5 px-5 font-bold">Judul Buku</th>
                        <th class="py-3.5 px-5 font-bold">Kategori</th>
                        <th class="py-3.5 px-5 font-bold">Penulis / Penerbit</th>
                        <th class="py-3.5 px-5 font-bold">Lokasi Rak</th>
                        <th class="py-3.5 px-5 font-bold text-center">Eksemplar</th>
                        <th class="py-3.5 px-5 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    @forelse($bukuList as $buku)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3.5 px-5">
                                <span class="font-mono font-bold text-gray-900 block">{{ $buku->isbn }}</span>
                                <span class="text-[10px] text-gray-400 font-medium">Th. {{ $buku->tahun_terbit }}</span>
                            </td>
                            <td class="py-3.5 px-5 font-bold text-gray-900 max-w-xs truncate">{{ $buku->judul }}</td>
                            <td class="py-3.5 px-5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-brand-50 text-brand-700 border border-brand-200">
                                    {{ $buku->kategori->nama ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="block text-gray-900 font-bold">{{ $buku->penulis->nama ?? '-' }}</span>
                                <span class="text-[10px] text-gray-400">{{ $buku->penerbit->nama ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-mono text-[10px] border border-gray-200">
                                    {{ $buku->rak->kode_rak ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $buku->eksemplar_count ?? 0 }} Fisik
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right space-x-1.5">
                                <button @click="editData = {
                                    id: {{ $buku->id }},
                                    isbn: '{{ $buku->isbn }}',
                                    judul: '{{ addslashes($buku->judul) }}',
                                    tahun_terbit: {{ $buku->tahun_terbit }},
                                    penulis_id: {{ $buku->penulis_id ?? 'null' }},
                                    penerbit_id: {{ $buku->penerbit_id ?? 'null' }},
                                    kategori_id: {{ $buku->kategori_id ?? 'null' }},
                                    rak_id: {{ $buku->rak_id ?? 'null' }},
                                    sinopsis: '{{ addslashes($buku->sinopsis) }}'
                                }; openEditModal = true" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white font-extrabold rounded-lg text-[10px] transition shadow-2xs">
                                    Edit
                                </button>
                                <form action="{{ route('admin.buku.delete', $buku->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Hapus Judul Buku?', 'Master buku beserta seluruh eksemplar fisiknya akan dihapus.')">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-lg text-[10px] transition shadow-2xs">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-gray-400 font-medium">Tidak ada data buku terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $bukuList->links() }}
        </div>
    </div>

    <!-- Premium Backdrop Glass Modal Form Tambah Buku -->
    <div x-show="openAddModal" @click.self="openAddModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 space-y-5 shadow-2xl border-2 border-gray-200 transform transition-all my-8">
            <div class="flex items-center justify-between border-b-2 border-gray-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Tambah Judul Buku Baru</h3>
                        <p class="text-[11px] text-gray-500">Masukkan metadata buku master ke dalam pangkalan data</p>
                    </div>
                </div>
                <button @click="openAddModal = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-900 flex items-center justify-center transition font-bold">&times;</button>
            </div>

            <form action="{{ route('admin.buku.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">ISBN <span class="text-rose-500">*</span></label>
                        <input type="text" name="isbn" required placeholder="contoh: 978-602-1234-56-7" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Tahun Terbit <span class="text-rose-500">*</span></label>
                        <input type="number" name="tahun_terbit" value="{{ date('Y') }}" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Judul Lengkap Buku <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" required placeholder="Masukkan judul utama buku..." class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Penulis <span class="text-rose-500">*</span></label>
                        <select name="penulis_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Penulis --</option>
                            @foreach($penulisList as $pen)
                                <option value="{{ $pen->id }}">{{ $pen->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Penerbit <span class="text-rose-500">*</span></label>
                        <select name="penerbit_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Penerbit --</option>
                            @foreach($penerbitList as $pub)
                                <option value="{{ $pub->id }}">{{ $pub->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Kategori Kejuruan <span class="text-rose-500">*</span></label>
                        <select name="kategori_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Posisi Rak Utama <span class="text-rose-500">*</span></label>
                        <select name="rak_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Rak Lokasi --</option>
                            @foreach($rakList as $rk)
                                <option value="{{ $rk->id }}">{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Jumlah Eksemplar Fisik Awal <span class="text-rose-500">*</span></label>
                    <input type="number" name="jumlah_eksemplar" value="1" min="1" max="50" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    <span class="text-[10px] text-gray-400 mt-1 block">Sistem akan otomatis menggenerasi kode barcode ID eksemplar fisik.</span>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Sinopsis / Deskripsi Ringkas</label>
                    <textarea name="sinopsis" rows="3" placeholder="Tuliskan sinopsis modul atau ringkasan isi buku..." class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium"></textarea>
                </div>

                <div class="pt-4 border-t-2 border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="openAddModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 transition shadow-md hover:shadow-lg transform active:scale-95">Simpan Buku Master</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Premium Backdrop Glass Modal Form Edit Buku -->
    <div x-show="openEditModal" @click.self="openEditModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 space-y-5 shadow-2xl border-2 border-gray-200 transform transition-all my-8">
            <div class="flex items-center justify-between border-b-2 border-gray-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Edit Data Buku</h3>
                        <p class="text-[11px] text-gray-500">Perbarui rincian metadata buku master</p>
                    </div>
                </div>
                <button @click="openEditModal = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-900 flex items-center justify-center transition font-bold">&times;</button>
            </div>

            <form :action="'{{ url('/admin/buku/update') }}/' + editData.id" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">ISBN <span class="text-rose-500">*</span></label>
                        <input type="text" name="isbn" x-model="editData.isbn" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Tahun Terbit <span class="text-rose-500">*</span></label>
                        <input type="number" name="tahun_terbit" x-model="editData.tahun_terbit" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Judul Buku <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" x-model="editData.judul" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Penulis <span class="text-rose-500">*</span></label>
                        <select name="penulis_id" x-model="editData.penulis_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            @foreach($penulisList as $pen)
                                <option value="{{ $pen->id }}">{{ $pen->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Penerbit <span class="text-rose-500">*</span></label>
                        <select name="penerbit_id" x-model="editData.penerbit_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            @foreach($penerbitList as $pub)
                                <option value="{{ $pub->id }}">{{ $pub->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Kategori Kejuruan <span class="text-rose-500">*</span></label>
                        <select name="kategori_id" x-model="editData.kategori_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Rak Lokasi <span class="text-rose-500">*</span></label>
                        <select name="rak_id" x-model="editData.rak_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            @foreach($rakList as $rk)
                                <option value="{{ $rk->id }}">{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Sinopsis / Ringkasan</label>
                    <textarea name="sinopsis" x-model="editData.sinopsis" rows="3" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium"></textarea>
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
