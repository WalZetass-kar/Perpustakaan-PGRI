@extends('layouts.dashboard')

@section('title', 'Manajemen Anggota & Pengguna')
@section('page_heading', 'Manajemen Anggota & Pengaturan Hak Akses')

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
            <h2 class="text-base font-bold text-gray-900">Daftar Anggota Terdaftar & Hak Akses User</h2>
            <p class="text-xs text-gray-500">Kelola pendaftaran akun Siswa, Pustakawan, maupun Administrator</p>
        </div>
        <button @click="openAddModal = true" class="px-4 py-2 bg-brand-700 text-white font-semibold text-xs rounded-lg hover:bg-brand-800 transition shadow-2xs">
            + Tambah Anggota / User Baru
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-5 font-semibold">No. Anggota ID</th>
                        <th class="py-3 px-5 font-semibold">Nama Pengguna</th>
                        <th class="py-3 px-5 font-semibold">NISN / Identitas</th>
                        <th class="py-3 px-5 font-semibold">Jurusan / Unit</th>
                        <th class="py-3 px-5 font-semibold">Email</th>
                        <th class="py-3 px-5 font-semibold">Role Akses</th>
                        <th class="py-3 px-5 font-semibold">Status Member</th>
                        <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @foreach($anggotaList as $member)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-3.5 px-5 font-mono font-bold text-gray-900">{{ $member->nomor_anggota }}</td>
                            <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $member->user->name ?? '-' }}</td>
                            <td class="py-3.5 px-5 font-mono">{{ $member->nim ?? '-' }}</td>
                            <td class="py-3.5 px-5">{{ $member->program_studi ?? '-' }}</td>
                            <td class="py-3.5 px-5 text-gray-500">{{ $member->user->email ?? '-' }}</td>
                            <td class="py-3.5 px-5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase">
                                    {{ $member->user->role->display_name ?? $member->user->role->name ?? 'User' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5">
                                @if($member->status === 'aktif')
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase">Aktif</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-100 uppercase">{{ $member->status }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right space-x-2">
                                <button @click="editData = {
                                    id: {{ $member->id }},
                                    name: '{{ addslashes($member->user->name ?? '') }}',
                                    email: '{{ $member->user->email ?? '' }}',
                                    role_id: '{{ $member->user->role_id ?? '' }}',
                                    phone: '{{ $member->user->phone ?? '' }}',
                                    nim: '{{ $member->nim ?? '' }}',
                                    program_studi: '{{ addslashes($member->program_studi ?? '') }}',
                                    status: '{{ $member->status }}'
                                }; openEditModal = true" class="text-brand-700 font-semibold hover:underline">Edit</button>
                                <form action="{{ route('admin.anggota.delete', $member->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Hapus data anggota dan akun user ini?')" class="text-rose-600 font-semibold hover:underline">Hapus</button>
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

    <!-- Modal Form Tambah Anggota / User Baru -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4" x-transition x-cloak>
        <div class="bg-white rounded-xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-gray-200" @click.away="openAddModal = false">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-900">Tambah Anggota / User Akses Baru</h3>
                <button @click="openAddModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('admin.anggota.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Rian Pratama" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Email Sekolah <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="rian@smkpgri.sch.id" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kata Sandi Default <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Hak Akses Role <span class="text-rose-500">*</span></label>
                        <select name="role_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                            @foreach($roles as $rl)
                                <option value="{{ $rl->id }}">{{ $rl->display_name ?? $rl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">NISN / Nomor Induk <span class="text-rose-500">*</span></label>
                        <input type="text" name="nim" required placeholder="1022014009" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Program / Jurusan <span class="text-rose-500">*</span></label>
                        <input type="text" name="program_studi" required placeholder="Teknik Komputer & Jaringan" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">No. WhatsApp / Telepon</label>
                    <input type="text" name="phone" placeholder="081234567890" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-700 text-white font-bold rounded-lg hover:bg-brand-800">Daftarkan Anggota</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Form Edit Anggota / User -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4" x-transition x-cloak>
        <div class="bg-white rounded-xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-gray-200" @click.away="openEditModal = false">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-gray-900">Edit Data Anggota & User</h3>
                <button @click="openEditModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form :action="'{{ url('/admin/anggota/update') }}/' + editData.id" method="POST" class="space-y-3 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" x-model="editData.name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Email Sekolah</label>
                        <input type="email" name="email" x-model="editData.email" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Ubah Sandi (Opsional)</label>
                        <input type="password" name="password" placeholder="Biarkan kosong jika tidak diubah" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Hak Akses Role</label>
                        <select name="role_id" x-model="editData.role_id" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                            @foreach($roles as $rl)
                                <option value="{{ $rl->id }}">{{ $rl->display_name ?? $rl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">NISN / Nomor Induk</label>
                        <input type="text" name="nim" x-model="editData.nim" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Program / Jurusan</label>
                        <input type="text" name="program_studi" x-model="editData.program_studi" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">No. WhatsApp / Telepon</label>
                        <input type="text" name="phone" x-model="editData.phone" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Status Keanggotaan</label>
                        <select name="status" x-model="editData.status" required class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                            <option value="dibekukan">Dibekukan</option>
                        </select>
                    </div>
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
