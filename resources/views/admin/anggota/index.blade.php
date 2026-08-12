@extends('layouts.dashboard')

@section('title', 'Manajemen Anggota & User')
@section('page_heading', 'Manajemen Pengguna & Anggota Perpustakaan')

@section('content')
<div class="space-y-6" x-data="{ openAddModal: false, openEditModal: false, editData: {} }">
    
    <!-- Top Action Toolbar -->
    <div class="bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-black text-gray-900">Daftar Pengguna &amp; Anggota Terdaftar</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Kelola akun Siswa, Pustakawan, Administrator, dan nomor kartu perpustakaan</p>
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
            <span class="text-xs font-bold text-gray-500">Total: {{ $anggotaList->total() }} User</span>
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
                                <span class="font-mono font-bold text-brand-700 block">{{ $anggota->nomor_anggota ?? 'NON-ANGGOTA' }}</span>
                                <span class="text-[10px] text-gray-400 font-medium">NISN: {{ $anggota->nim ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-5 font-bold text-gray-900">{{ $user->name }}</td>
                            <td class="py-3.5 px-5">
                                <span class="block text-gray-900 font-medium">{{ $user->email }}</span>
                                <span class="text-[10px] text-gray-400">{{ $user->phone ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase {{ ($user->role->name ?? '') === 'admin' ? 'bg-rose-50 text-rose-700 border border-rose-200' : (($user->role->name ?? '') === 'pustakawan' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200') }}">
                                    {{ $user->role->display_name ?? ($user->role->name ?? 'Anggota') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 font-bold text-gray-700">{{ $anggota->program_studi ?? '-' }}</td>
                            <td class="py-3.5 px-5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold capitalize {{ ($anggota->status ?? 'aktif') === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                    {{ $anggota->status ?? 'aktif' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right space-x-1.5">
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

    <!-- Premium Backdrop Glass Modal Form Tambah User -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 space-y-5 shadow-2xl border-2 border-gray-200 transform transition-all my-8">
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
                        <input type="text" name="nim" required placeholder="0081234567" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Program / Jurusan <span class="text-rose-500">*</span></label>
                        <input type="text" name="program_studi" required placeholder="Teknik Komputer & Jaringan" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">No. WhatsApp / Telepon</label>
                    <input type="text" name="phone" placeholder="0812-3456-7890" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="pt-4 border-t-2 border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="openAddModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-brand-700 text-white font-extrabold rounded-xl hover:bg-brand-800 transition shadow-md hover:shadow-lg transform active:scale-95">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Premium Backdrop Glass Modal Form Edit User -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 space-y-5 shadow-2xl border-2 border-gray-200 transform transition-all my-8">
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

</div>
@endsection
