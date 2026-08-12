@extends('layouts.dashboard')

@section('title', 'Manajemen Buku')
@section('page_heading', 'Manajemen Koleksi Buku')

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
        <h2 class="text-base font-bold text-gray-900">Daftar Master Koleksi Buku</h2>
        <button @click="openAddModal = true" class="px-4 py-2 bg-brand-700 text-white font-semibold text-xs rounded-lg hover:bg-brand-800 transition shadow-2xs">
            + Tambah Buku Baru
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-5 font-semibold">ISBN</th>
                        <th class="py-3 px-5 font-semibold">Judul Buku</th>
                        <th class="py-3 px-5 font-semibold">Penulis</th>
                        <th class="py-3 px-5 font-semibold">Kategori</th>
                        <th class="py-3 px-5 font-semibold">Rak</th>
                        <th class="py-3 px-5 font-semibold">Stok Fisik</th>
                        <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @foreach($bukuList as $buku)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-3.5 px-5 font-mono text-gray-900">{{ $buku->isbn }}</td>
                            <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $buku->judul }}</td>
                            <td class="py-3.5 px-5">{{ $buku->penulis->nama ?? '-' }}</td>
                            <td class="py-3.5 px-5">{{ $buku->kategori->nama ?? '-' }}</td>
                            <td class="py-3.5 px-5 font-mono">{{ $buku->rak->kode_rak ?? '-' }}</td>
                            <td class="py-3.5 px-5 font-bold text-emerald-700">{{ $buku->jumlah_tersedia }} / {{ $buku->jumlah_eksemplar }}</td>
                            <td class="py-3.5 px-5 text-right space-x-2">
                                <button @click="editData = {
                                    id: {{ $buku->id }},
                                    isbn: '{{ $buku->isbn }}',
                                    judul: '{{ addslashes($buku->judul) }}',
                                    penulis_id: '{{ $buku->penulis_id }}',
                                    penerbit_id: '{{ $buku->penerbit_id }}',
                                    kategori_id: '{{ $buku->kategori_id }}',
                                    rak_id: '{{ $buku->rak_id }}',
                                    tahun_terbit: '{{ $buku->tahun_terbit }}',
                                    sinopsis: '{{ addslashes($buku->sinopsis ?? '') }}'
                                }; openEditModal = true" class="text-brand-700 font-semibold hover:underline">Edit</button>
                                <form action="{{ route('admin.buku.delete', $buku->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Hapus master data buku ini beserta seluruh eksemplar fisiknya?')" class="text-rose-600 font-semibold hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $bukuList->links() }}
        </div>
    </div>

    <!-- Modal Form Add Book -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4" x-transition x-cloak>
        <div class="bg-white rounded-xl max-w-xl w-full p-6 space-y-4 shadow-xl border border-gray-200">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-900">Tambah Master Buku Baru</h3>
                <button @click="openAddModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('admin.buku.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">ISBN</label>
                        <input type="text" name="isbn" required placeholder="978-xxx-xxx" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Tahun Terbit</label>
                        <input type="number" name="tahun_terbit" value="2024" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Judul Buku</label>
                    <input type="text" name="judul" required placeholder="Judul lengkap buku" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Penulis</label>
                        <select name="penulis_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                            @foreach($penulisList as $pen)
                                <option value="{{ $pen->id }}">{{ $pen->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Penerbit</label>
                        <select name="penerbit_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                            @foreach($penerbitList as $pub)
                                <option value="{{ $pub->id }}">{{ $pub->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kategori</label>
                        <select name="kategori_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Rak Lokasi</label>
                        <select name="rak_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                            @foreach($rakList as $rk)
                                <option value="{{ $rk->id }}">{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Sinopsis / Ringkasan</label>
                    <textarea name="sinopsis" rows="3" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-bold rounded-lg hover:bg-brand-800">Simpan Buku</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Form Edit Book -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4" x-transition x-cloak>
        <div class="bg-white rounded-xl max-w-xl w-full p-6 space-y-4 shadow-xl border border-gray-200">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-900">Edit Data Buku</h3>
                <button @click="openEditModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form :action="'{{ url('/admin/buku/update') }}/' + editData.id" method="POST" class="space-y-3 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">ISBN</label>
                        <input type="text" name="isbn" x-model="editData.isbn" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Tahun Terbit</label>
                        <input type="number" name="tahun_terbit" x-model="editData.tahun_terbit" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Judul Buku</label>
                    <input type="text" name="judul" x-model="editData.judul" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Penulis</label>
                        <select name="penulis_id" x-model="editData.penulis_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                            @foreach($penulisList as $pen)
                                <option value="{{ $pen->id }}">{{ $pen->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Penerbit</label>
                        <select name="penerbit_id" x-model="editData.penerbit_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                            @foreach($penerbitList as $pub)
                                <option value="{{ $pub->id }}">{{ $pub->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kategori</label>
                        <select name="kategori_id" x-model="editData.kategori_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Rak Lokasi</label>
                        <select name="rak_id" x-model="editData.rak_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                            @foreach($rakList as $rk)
                                <option value="{{ $rk->id }}">{{ $rk->kode_rak }} - {{ $rk->nama_rak }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Sinopsis / Ringkasan</label>
                    <textarea name="sinopsis" x-model="editData.sinopsis" rows="3" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-bold rounded-lg hover:bg-brand-800">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
