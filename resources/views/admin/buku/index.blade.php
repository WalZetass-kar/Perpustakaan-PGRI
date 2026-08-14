@extends('layouts.dashboard')

@section('title', 'Manajemen Koleksi Buku')
@section('page_heading', 'Koleksi Buku Perpustakaan')

@section('content')
<div class="space-y-5" x-data="{ openAddModal: false, openEditModal: false, editData: {} }" x-init="openAddModal = false; openEditModal = false; editData = {}">
    
    <!-- Top Action Toolbar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-black text-gray-900">Katalog Judul Buku & Stok Fisik</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Kelola data buku, jumlah stok fisik, dan lokasi rak</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <form action="{{ route('admin.buku') }}" method="GET" class="relative flex-1 sm:w-72">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, ISBN, atau penulis..." 
                       class="w-full pl-8 pr-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:outline-none">
                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
            <button @click="openAddModal = true" class="px-3.5 py-1.5 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center gap-1.5 shrink-0">
                <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Tambah Buku</span>
            </button>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider font-extrabold">
                        <th class="py-3 px-4 font-bold">Buku & Cover</th>
                        <th class="py-3 px-4 font-bold">Penulis / Penerbit</th>
                        <th class="py-3 px-4 font-bold">Kategori & Rak</th>
                        <th class="py-3 px-4 font-bold text-center">Stok Fisik</th>
                        <th class="py-3 px-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    @forelse($bukuList as $buku)
                        @php
                            $coverUrl = $buku->cover_url;
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-14 bg-gray-100 rounded-lg overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center">
                                        @if($coverUrl)
                                            <img src="{{ $coverUrl }}" alt="Cover" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center bg-brand-700 text-white font-black text-xs">
                                                {{ substr($buku->judul, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 line-clamp-2">{{ $buku->judul }}</p>
                                        <div class="flex items-center gap-2 mt-0.5 text-[10px] text-gray-500 font-mono">
                                            <span>ISBN: {{ $buku->isbn ?? 'Tanpa ISBN' }}</span>
                                            <span>•</span>
                                            <span>Tahun {{ $buku->tahun_terbit }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-gray-800">{{ $buku->penulis->nama ?? '-' }}</p>
                                <p class="text-[10.5px] text-gray-500">{{ $buku->penerbit->nama ?? '-' }}</p>
                            </td>
                            <td class="py-3 px-4">
                                <div class="space-y-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-brand-50 text-brand-700 border border-brand-200 inline-block">
                                        {{ $buku->kategori->nama ?? 'Umum' }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-gray-100 text-gray-700 border border-gray-200 inline-block">
                                        {{ $buku->rak->kode_rak ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-black {{ $buku->available_quantity > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        {{ $buku->available_quantity }} / {{ $buku->total_quantity }} Eks
                                    </span>
                                    <span class="text-[9.5px] text-gray-400 font-medium mt-0.5">
                                        {{ $buku->available_quantity > 0 ? 'Tersedia' : 'Habis Dipinjam' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                                <button type="button" @click="editData = {{ json_encode([
                                    'id' => $buku->id,
                                    'isbn' => $buku->isbn ?? '',
                                    'judul' => $buku->judul,
                                    'tahun_terbit' => $buku->tahun_terbit,
                                    'total_quantity' => $buku->total_quantity,
                                    'penulis_id' => $buku->penulis_id,
                                    'penerbit_id' => $buku->penerbit_id,
                                    'kategori_id' => $buku->kategori_id,
                                    'rak_id' => $buku->rak_id,
                                    'sinopsis' => $buku->sinopsis ?? '',
                                    'cover_url' => $coverUrl
                                ]) }}; openEditModal = true" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white font-extrabold rounded-lg text-[10px] transition shadow-2xs">
                                    Edit
                                </button>
                                <form action="{{ route('admin.buku.delete', $buku->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Hapus Judul Buku?', 'Master buku ini akan dihapus dari katalog.')">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-lg text-[10px] transition shadow-2xs">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 font-medium">Belum ada buku terdaftar di katalog.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">
            {{ $bukuList->links() }}
        </div>
    </div>

    <!-- Modal Form Tambah Buku (Compact & Clean) -->
    <div x-show="openAddModal" @click.self="openAddModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto" x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/70">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Tambah Koleksi Buku Baru</h3>
                        <p class="text-[10px] text-gray-500">Masukkan detail buku dan jumlah stok fisik</p>
                    </div>
                </div>
                <button @click="openAddModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm">&times;</button>
            </div>

            <form action="{{ route('admin.buku.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Judul Buku <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" required placeholder="Contoh: Pemrograman Web Kelas XI" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
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

                <div class="grid grid-cols-2 gap-2.5">
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
                        <select name="rak_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Rak ▼ --</option>
                            @foreach($rakList as $rk)
                                <option value="{{ $rk->id }}">{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Cover Buku (Opsional)</label>
                    <input type="file" name="cover" accept="image/*" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none text-[11px] file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[10px] file:font-bold file:bg-brand-700 file:text-white">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Sinopsis / Ringkasan (Opsional)</label>
                    <textarea name="sinopsis" rows="2" placeholder="Ringkasan materi atau buku..." class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2 shrink-0">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 text-xs">Simpan Buku</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Form Edit Buku -->
    <div x-show="openEditModal" @click.self="openEditModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto" x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-amber-50/50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Edit Data Buku</h3>
                        <p class="text-[10px] text-gray-500">Perbarui rincian dan stok buku</p>
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

                <div class="grid grid-cols-2 gap-2.5">
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
                        <select name="rak_id" x-model="editData.rak_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="">-- Pilih Rak ▼ --</option>
                            @foreach($rakList as $rk)
                                <option value="{{ $rk->id }}">{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Ganti Cover Buku (Opsional)</label>
                    <input type="file" name="cover" accept="image/*" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none text-[11px] file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[10px] file:font-bold file:bg-brand-700 file:text-white">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Sinopsis / Ringkasan (Opsional)</label>
                    <textarea name="sinopsis" x-model="editData.sinopsis" rows="2" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2 shrink-0">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 text-xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
