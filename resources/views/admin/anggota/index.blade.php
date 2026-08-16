@extends('layouts.dashboard')

@section('title', 'Manajemen Pengelola & Akun Admin')
@section('page_heading', 'Manajemen Hak Akses & Akun Pengelola')

@section('content')
<div class="space-y-5" x-data="{ openAddModal: false, openEditModal: false, openPasswordModal: false, editData: {}, passwordData: {} }" x-init="openAddModal = false; openEditModal = false; openPasswordModal = false; editData = {}; passwordData = {}">

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border-2 border-gray-200 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Super Admin</span>
                <span class="text-lg font-black text-gray-900">{{ $anggotaList->where('role.name', 'super_admin')->count() }} Akun</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border-2 border-gray-200 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Admin Perpustakaan</span>
                <span class="text-lg font-black text-gray-900">{{ $anggotaList->where('role.name', 'admin')->count() }} Staf</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border-2 border-gray-200 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Status Aktif</span>
                <span class="text-lg font-black text-emerald-700">{{ $anggotaList->where('status', 'active')->count() }} Akun</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border-2 border-gray-200 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Status Nonaktif</span>
                <span class="text-lg font-black text-rose-700">{{ $anggotaList->where('status', 'inactive')->count() }} Akun</span>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-black text-gray-900">Daftar Akun Pengelola & Staf Perpustakaan</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Kelola data login, hak akses Super Admin vs Admin, dan reset password</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <form action="{{ route('admin.anggota') }}" method="GET" class="relative flex-1 sm:w-72">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pengelola, email..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:outline-none">
                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
            @if(auth()->user()->isAdmin())
                <button @click="openAddModal = true" class="px-3.5 py-1.5 bg-brand-700 hover:bg-brand-800 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center gap-1.5 shrink-0">
                    <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>+ Tambah Admin</span>
                </button>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider font-extrabold">
                        <th class="py-3 px-4 font-bold">Identitas Staf / Pengelola</th>
                        <th class="py-3 px-4 font-bold">Kontak Telepon / WA</th>
                        <th class="py-3 px-4 font-bold">Tingkat Hak Akses</th>
                        <th class="py-3 px-4 font-bold">Status Akun</th>
                        <th class="py-3 px-4 font-bold text-right">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    @forelse($anggotaList as $user)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl {{ $user->isSuperAdmin() ? 'bg-amber-100 border border-amber-300 text-amber-800' : 'bg-brand-50 border border-brand-200 text-brand-700' }} font-black flex items-center justify-center text-xs shrink-0 shadow-2xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <p class="font-bold text-gray-900 truncate">{{ $user->name }}</p>
                                            @if($user->id === auth()->id())
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-blue-50 text-blue-700 border border-blue-200">Akun Anda</span>
                                            @endif
                                        </div>
                                        <p class="text-[10.5px] text-gray-500 font-mono">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-gray-700">
                                {{ $user->phone ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($user->isSuperAdmin())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black bg-amber-50 text-amber-800 border border-amber-300 shadow-2xs">
                                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        <span>Super Administrator</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black bg-brand-50 text-brand-700 border border-brand-200">
                                        <svg class="w-3 h-3 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <span>Admin Perpustakaan</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Dinonaktifkan / Diblokir</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1 whitespace-nowrap">
                                @if(auth()->user()->isSuperAdmin() || auth()->id() === $user->id)
                                    <button type="button" @click="editData = {{ json_encode([
                                        'id' => $user->id,
                                        'name' => $user->name,
                                        'email' => $user->email,
                                        'phone' => $user->phone ?? '',
                                        'role_id' => $user->role_id,
                                        'status' => $user->status
                                    ]) }}; openEditModal = true" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white font-extrabold rounded-lg text-[10px] transition shadow-2xs">
                                        Edit
                                    </button>
                                @endif

                                @if(auth()->user()->isAdmin())
                                    <button type="button" @click="passwordData = {{ json_encode([
                                        'id' => $user->id,
                                        'name' => $user->name,
                                        'email' => $user->email
                                    ]) }}; openPasswordModal = true" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white font-extrabold rounded-lg text-[10px] transition shadow-2xs" title="Reset Password Akun">
                                        Ubah Password
                                    </button>

                                    @if($user->id !== 1 && $user->id !== auth()->id())
                                        <form action="{{ route('admin.anggota.toggle-status', $user->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Ubah Status Akun?', 'Status akses login akun ini akan dialihkan.')">
                                            @csrf
                                            @if($user->status === 'active')
                                                <button type="submit" class="px-2.5 py-1 bg-gray-600 hover:bg-gray-700 text-white font-extrabold rounded-lg text-[10px] transition shadow-2xs" title="Blokir / Nonaktifkan">
                                                    Blokir
                                                </button>
                                            @else
                                                <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-lg text-[10px] transition shadow-2xs" title="Aktifkan Kembali">
                                                    Aktifkan
                                                </button>
                                            @endif
                                        </form>

                                        <form action="{{ route('admin.anggota.delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Hapus Akun Pengelola?', 'Akun admin ini akan dihapus permanen dari sistem.')">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-lg text-[10px] transition shadow-2xs">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 font-medium">Belum ada akun pengelola terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">
            {{ $anggotaList->links() }}
        </div>
    </div>

    @if(auth()->user()->isAdmin())
        <div x-show="openAddModal" @click.self="openAddModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.stop class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/70">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-gray-900">Tambah Akun Pengelola Baru</h3>
                            <p class="text-[10px] text-gray-500">Daftarkan akun Super Admin atau Admin Perpustakaan</p>
                        </div>
                    </div>
                    <button @click="openAddModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm">&times;</button>
                </div>

                <form action="{{ route('admin.anggota.store') }}" method="POST" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nama Lengkap Pengelola <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Budi Santoso, S.Kom" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Email Akun (Login ID) <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="Contoh: budi@smkpgri.sch.id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Password Baru <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="phone" placeholder="Contoh: 081234567890" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Tingkat Hak Akses <span class="text-rose-500">*</span></label>
                            <select name="role_id" required class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $role->name === 'admin' ? 'selected' : '' }}>{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Status Akun <span class="text-rose-500">*</span></label>
                            <select name="status" required class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="openAddModal = false" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-1.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold rounded-lg transition shadow-sm">Simpan Akun</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div x-show="openEditModal" @click.self="openEditModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto" x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/70">
                <h3 class="text-sm font-black text-gray-900">Edit Akun Pengelola</h3>
                <button @click="openEditModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm">&times;</button>
            </div>

            <form :action="'/admin/anggota/update/' + editData.id" method="POST" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="editData.name" required class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Email Login <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" x-model="editData.email" required class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nomor Telepon / WA</label>
                    <input type="text" name="phone" x-model="editData.phone" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                @if(auth()->user()->isAdmin())
                    <template x-if="editData.id !== 1">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-gray-700 mb-1">Tingkat Hak Akses</label>
                                <select name="role_id" x-model="editData.role_id" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-gray-700 mb-1">Status Akun</label>
                                <select name="status" x-model="editData.status" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </template>
                @endif

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                    <button type="button" @click="openEditModal = false" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold rounded-lg transition shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    @if(auth()->user()->isAdmin())
        <div x-show="openPasswordModal" @click.self="openPasswordModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.stop class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/70">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-gray-900">Ubah Password Admin</h3>
                            <p class="text-[10px] text-gray-500">Atur kata sandi baru untuk <span class="font-bold text-gray-800" x-text="passwordData.name"></span></p>
                        </div>
                    </div>
                    <button @click="openPasswordModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm">&times;</button>
                </div>

                <form :action="'/admin/anggota/reset-password/' + passwordData.id" method="POST" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Password Baru <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Ulangi Password Baru <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" required placeholder="Ketik ulang password baru" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="openPasswordModal = false" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold rounded-lg transition shadow-sm">Simpan Password Baru</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
