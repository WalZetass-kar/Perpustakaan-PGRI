@extends('layouts.dashboard')

@section('title', 'Manajemen Anggota & User')
@section('page_heading', 'Manajemen Pengguna & Anggota Perpustakaan')

@section('content')
<div class="space-y-6" x-data="{ openAddModal: false, openEditModal: false, openDendaModal: false, editData: {}, dendaData: {} }" x-init="openAddModal = false; openEditModal = false; openDendaModal = false; editData = {}; dendaData = {}">
    
    <!-- Top Action Toolbar -->
    <div class="bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-black text-gray-900">Daftar Pengguna &amp; Anggota Terdaftar</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Kelola akun Siswa, Pustakawan, Administrator, kartu anggota, serta penetapan denda</p>
        </div>
        <button @click="openAddModal = true" class="px-4 py-2.5 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition duration-300 shadow-md hover:shadow-lg transform active:scale-95 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <span>+ Tambah Anggota / User Baru</span>
        </button>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b-2 border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <form action="{{ route('admin.anggota') }}" method="GET" class="w-full sm:w-80 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, nomor anggota, NISN..."
                    class="w-full pl-9 pr-3 py-2 bg-gray-50 border-2 border-gray-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
            <span class="text-xs font-bold text-gray-500">Total: {{ $anggotaList->total() }} User Terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider font-extrabold">
                        <th class="py-3.5 px-5 font-bold">No. Anggota &amp; NISN</th>
                        <th class="py-3.5 px-5 font-bold">Nama User</th>
                        <th class="py-3.5 px-5 font-bold">Email &amp; Telepon</th>
                        <th class="py-3.5 px-5 font-bold">Role Akses</th>
                        <th class="py-3.5 px-5 font-bold">Jurusan</th>
                        <th class="py-3.5 px-5 font-bold">Status</th>
                        <th class="py-3.5 px-5 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    @foreach($anggotaList as $user)
                        @php $anggota = $user->anggota; @endphp
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3.5 px-5">
                                <span class="font-mono font-bold text-brand-700 block">{{ $anggota->nomor_anggota ?? 'LIB-2026-000' }}</span>
                                <span class="text-[10px] text-gray-400 font-medium">NISN: {{ $anggota->nim ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-5 font-bold text-gray-900 flex items-center gap-2">
                                @if($anggota && $anggota->foto)
                                    <img src="{{ asset('storage/' . $anggota->foto) }}" alt="Foto" class="w-7 h-7 rounded-full object-cover border border-gray-300 shrink-0">
                                @else
                                    <div class="w-7 h-7 rounded-full bg-brand-700 text-white font-black text-xs flex items-center justify-center shrink-0">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                                <span>{{ $user->name }}</span>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="block text-gray-900 font-medium">{{ $user->email }}</span>
                                <span class="text-[10px] text-gray-400">{{ $user->phone ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase {{ ($user->role->name ?? '') === 'admin' ? 'bg-rose-50 text-rose-700 border border-rose-200' : (($user->role->name ?? '') === 'pustakawan' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200') }}">
                                    {{ $user->role->display_name ?? ($user->role->name ?? 'Anggota') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 font-bold text-gray-700">{{ $anggota->program_studi ?? 'Umum' }}</td>
                            <td class="py-3.5 px-5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold capitalize {{ ($anggota->status ?? 'aktif') === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                    {{ $anggota->status ?? 'aktif' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right space-x-1 whitespace-nowrap">
                                <!-- Tombol Beri Denda -->
                                <button @click="dendaData = {
                                    user_id: {{ $user->id }},
                                    user_name: '{{ addslashes($user->name) }}'
                                }; openDendaModal = true" class="px-2.5 py-1 bg-red-600 hover:bg-red-700 text-white font-extrabold rounded-lg text-[10px] transition shadow-2xs inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Beri Denda</span>
                                </button>

                                <!-- Tombol Edit User/Anggota -->
                                <button @click="editData = {
                                    id: {{ $user->id }},
                                    name: '{{ addslashes($user->name) }}',
                                    email: '{{ addslashes($user->email) }}',
                                    phone: '{{ addslashes($user->phone ?? '') }}',
                                    role_id: {{ $user->role_id }},
                                    nim: '{{ addslashes($anggota->nim ?? '') }}',
                                    program_studi: '{{ addslashes($anggota->program_studi ?? '') }}',
                                    status: '{{ $anggota->status ?? 'aktif' }}'
                                }; openEditModal = true" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white font-extrabold rounded-lg text-[10px] transition shadow-2xs">
                                    Edit
                                </button>

                                <!-- Tombol Hapus User/Anggota -->
                                <form action="{{ route('admin.anggota.delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Hapus Pengguna?', 'Akun user beserta data anggotanya akan dihapus.')">
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
            {{ $anggotaList->links() }}
        </div>
    </div>

    <!-- Modal Form Tambah User/Anggota Baru -->
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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Registrasi Pengguna Baru</h3>
                        <p class="text-[11px] text-gray-500">Daftarkan akun Siswa, Pustakawan, atau Admin baru</p>
                    </div>
                </div>
                <button @click="openAddModal = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-900 flex items-center justify-center transition font-bold">&times;</button>
            </div>

            <form action="{{ route('admin.anggota.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Budi Santoso" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Email Sekolah <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="budi@smkpgri.sch.id" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Kata Sandi <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Hak Akses Role <span class="text-rose-500">*</span></label>
                        <select name="role_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            @foreach($roles as $rl)
                                <option value="{{ $rl->id }}">{{ $rl->display_name ?? $rl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">NISN / Nomor Induk <span class="text-rose-500">*</span></label>
                        <input type="text" name="nim" required placeholder="Contoh: 0081234567" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Program / Jurusan <span class="text-rose-500">*</span></label>
                        <input type="text" name="program_studi" required placeholder="Teknik Komputer & Jaringan" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">No. WhatsApp / Telepon</label>
                    <input type="text" name="phone" placeholder="08123456789" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="pt-4 border-t-2 border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="openAddModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 transition shadow-md hover:shadow-lg transform active:scale-95">Simpan User Baru</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Form Edit User/Anggota -->
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
                        <h3 class="text-base font-black text-gray-900">Edit Data Anggota &amp; User</h3>
                        <p class="text-[11px] text-gray-500">Perbarui profil, role, atau kata sandi</p>
                    </div>
                </div>
                <button @click="openEditModal = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-900 flex items-center justify-center transition font-bold">&times;</button>
            </div>

            <form :action="'{{ url('/admin/anggota/update') }}/' + editData.id" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="editData.name" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Email Sekolah <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" x-model="editData.email" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Ubah Sandi (Opsional)</label>
                        <input type="password" name="password" placeholder="Biarkan kosong jika tidak diubah" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Hak Akses Role <span class="text-rose-500">*</span></label>
                        <select name="role_id" x-model="editData.role_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            @foreach($roles as $rl)
                                <option value="{{ $rl->id }}">{{ $rl->display_name ?? $rl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">NISN / Nomor Induk <span class="text-rose-500">*</span></label>
                        <input type="text" name="nim" x-model="editData.nim" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Program / Jurusan <span class="text-rose-500">*</span></label>
                        <input type="text" name="program_studi" x-model="editData.program_studi" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">No. WhatsApp / Telepon</label>
                        <input type="text" name="phone" x-model="editData.phone" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Status Keanggotaan <span class="text-rose-500">*</span></label>
                        <select name="status" x-model="editData.status" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                            <option value="dibekukan">Dibekukan</option>
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

    <!-- Modal Form Penetapan Denda Siswa / Anggota -->
    <div x-show="openDendaModal" @click.self="openDendaModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 space-y-5 shadow-2xl border-2 border-gray-200 transform transition-all my-8">
            <div class="flex items-center justify-between border-b-2 border-gray-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-red-50 border border-red-200 text-red-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Beri Denda Anggota</h3>
                        <p class="text-[11px] text-gray-500">Tetapkan penalti / denda untuk <strong class="text-gray-900" x-text="dendaData.user_name"></strong></p>
                    </div>
                </div>
                <button @click="openDendaModal = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-900 flex items-center justify-center transition font-bold">&times;</button>
            </div>

            <form action="{{ route('admin.denda.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="user_id" :value="dendaData.user_id">

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Nominal Denda (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="jumlah_denda" required min="500" step="500" placeholder="Contoh: 5000" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-600 focus:border-red-600 focus:bg-white focus:outline-none font-bold text-gray-900">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Alasan / Keterangan Penalti <span class="text-rose-500">*</span></label>
                    <input type="text" name="alasan" required placeholder="Contoh: Terlambat pengembalian modul 5 hari / Kerusakan cover" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-600 focus:border-red-600 focus:bg-white focus:outline-none font-medium">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Status Pembayaran Denda <span class="text-rose-500">*</span></label>
                    <select name="status_pembayaran" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-600 focus:border-red-600 focus:bg-white focus:outline-none font-medium">
                        <option value="belum_lunas">Belum Lunas (Menjadi Tanggungan Siswa)</option>
                        <option value="lunas">Lunas (Langsung Dibayar di Kasir Pustakawan)</option>
                    </select>
                </div>

                <div class="pt-4 border-t-2 border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="openDendaModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-red-600 text-white font-extrabold rounded-xl hover:bg-red-700 transition shadow-md hover:shadow-lg transform active:scale-95">Tetapkan Denda</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
