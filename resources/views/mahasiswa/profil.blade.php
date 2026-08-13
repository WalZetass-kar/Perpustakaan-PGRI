@extends('layouts.dashboard')

@section('title', 'Profil Saya & Pengaturan Akun')
@section('page_heading', 'Profil Pengguna Siswa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-2 border-emerald-200 rounded-2xl text-xs font-bold text-emerald-700 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 border-2 border-rose-200 rounded-2xl text-xs font-bold text-rose-700 space-y-1 shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Gagal Memperbarui Profil:</span>
            </div>
            <ul class="list-disc pl-6 font-medium text-[11px]">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profile Overview Card Banner -->
    <div class="bg-gradient-to-r from-brand-900 via-brand-700 to-red-800 text-white rounded-3xl p-6 sm:p-8 shadow-xl border-2 border-brand-700 relative overflow-hidden flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-5 z-10">
            <!-- Pas Foto Avatar Frame -->
            <div class="w-24 h-28 rounded-2xl bg-white p-1 border-2 border-amber-400 shadow-md shrink-0 overflow-hidden relative group">
                @if($anggota && $anggota->foto)
                    <img src="{{ asset('storage/' . $anggota->foto) }}" class="w-full h-full object-cover rounded-xl" alt="{{ $user->name }}">
                @else
                    <div class="w-full h-full bg-brand-50 text-brand-700 flex flex-col items-center justify-center rounded-xl text-center">
                        <span class="text-3xl font-black">{{ substr($user->name, 0, 1) }}</span>
                        <span class="text-[8px] font-bold text-gray-400 mt-1 uppercase">Belum Foto</span>
                    </div>
                @endif
            </div>

            <div class="space-y-1 text-center sm:text-left">
                <span class="inline-block px-3 py-1 bg-amber-400 text-brand-900 rounded-full text-[10px] font-black uppercase tracking-wider">
                    {{ strtoupper($anggota->status ?? 'Aktif') }}
                </span>
                <h2 class="text-xl sm:text-2xl font-black tracking-tight leading-snug">{{ $user->name }}</h2>
                <p class="text-xs text-red-100 font-medium">
                    NISN: <span class="font-mono font-bold">{{ $anggota->nim ?? '1022014001' }}</span> | ID: <span class="font-mono font-bold text-amber-300">{{ $anggota->nomor_anggota ?? 'LIB-2026-001' }}</span>
                </p>
                <p class="text-xs text-red-200 font-semibold">{{ $anggota->program_studi ?? 'Teknik Komputer & Jaringan' }}</p>
            </div>
        </div>

        <div class="z-10 shrink-0">
            <a href="{{ route('mahasiswa.kartu') }}" class="px-5 py-3 bg-white text-brand-700 font-extrabold text-xs rounded-xl hover:bg-gray-100 transition shadow-lg flex items-center gap-2 transform active:scale-95">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6"/></svg>
                <span>Lihat Kartu Digital</span>
            </a>
        </div>
    </div>

    <!-- Main Profile Edit Form -->
    <div class="bg-white rounded-3xl border-2 border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
        
        <div class="border-b-2 border-gray-100 pb-4">
            <h3 class="text-base font-black text-gray-900">Form Pengaturan Informasi Pengguna</h3>
            <p class="text-xs text-gray-500">Perbarui identitas, pas foto resmi 3x4, nomor kontak, atau kata sandi akun Anda.</p>
        </div>

        <form action="{{ route('mahasiswa.profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs" x-data="{ previewPhoto: null }">
            @csrf

            <!-- Upload Pas Foto Field -->
            <div class="p-4 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-300 flex flex-col sm:flex-row items-center gap-4">
                <div class="w-16 h-20 bg-gray-200 rounded-xl border border-gray-300 overflow-hidden shrink-0 flex items-center justify-center">
                    <template x-if="previewPhoto">
                        <img :src="previewPhoto" class="w-full h-full object-cover rounded-xl">
                    </template>
                    <template x-if="!previewPhoto">
                        @if($anggota && $anggota->foto)
                            <img src="{{ asset('storage/' . $anggota->foto) }}" class="w-full h-full object-cover rounded-xl">
                        @else
                            <span class="text-[9px] font-bold text-gray-400 text-center">FOTO 3x4</span>
                        @endif
                    </template>
                </div>
                <div class="space-y-1.5 flex-1 text-center sm:text-left">
                    <label class="block font-bold text-gray-800">Unggah Pas Foto Resmi Siswa (3x4 CM)</label>
                    <p class="text-[11px] text-gray-500">Format gambar JPG, PNG, atau WEBP. Ukuran maksimal 2 MB.</p>
                    <label class="inline-flex items-center gap-2 px-3.5 py-2 bg-brand-700 text-white font-bold rounded-xl hover:bg-brand-800 transition cursor-pointer shadow-2xs mt-1">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Pilih Foto dari Perangkat</span>
                        <input type="file" name="foto" accept="image/*" class="hidden" @change="
                            const file = $event.target.files[0];
                            if(file) {
                                const reader = new FileReader();
                                reader.onload = (e) => { previewPhoto = e.target.result };
                                reader.readAsDataURL(file);
                            }
                        ">
                    </label>
                </div>
            </div>

            <!-- Identity Fields Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-xs font-bold text-gray-900 focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none transition">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Email Sekolah (Terdaftar)</label>
                    <input type="email" value="{{ $user->email }}" disabled
                        class="w-full px-4 py-3 bg-gray-100 border-2 border-gray-200 text-gray-500 font-bold rounded-xl text-xs cursor-not-allowed">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">NISN / Nomor Induk Siswa <span class="text-rose-500">*</span></label>
                    <input type="text" name="nim" value="{{ old('nim', $anggota->nim ?? '') }}" required placeholder="Contoh: 0081234567"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-xs font-mono font-bold text-gray-900 focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none transition">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Program / Jurusan Keahlian <span class="text-rose-500">*</span></label>
                    <input type="text" name="program_studi" value="{{ old('program_studi', $anggota->program_studi ?? '') }}" required placeholder="Contoh: Teknik Komputer & Jaringan"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-xs font-bold text-gray-900 focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none transition">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 mb-1.5">Nomor WhatsApp / Telepon Aktif</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 0812-3456-7890"
                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-xs font-bold text-gray-900 focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:bg-white focus:outline-none transition">
                </div>
            </div>

            <!-- Optional Password Update Section -->
            <div class="pt-4 border-t-2 border-gray-100 space-y-4">
                <h4 class="text-xs font-extrabold text-gray-900">Keamanan &amp; Ubah Kata Sandi (Opsional)</h4>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Kata Sandi Lama</label>
                        <input type="password" name="current_password" placeholder="Sandi saat ini"
                            class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Kata Sandi Baru</label>
                        <input type="password" name="password" placeholder="Minimal 6 karakter"
                            class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Konfirmasi Sandi Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi sandi baru"
                            class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="pt-4 border-t-2 border-gray-100 flex items-center justify-between">
                <span class="text-[11px] text-gray-400 font-medium">* Perubahan tersimpan permanen di database perpustakaan.</span>
                <button type="submit" class="px-7 py-3 bg-brand-700 text-white font-extrabold text-xs rounded-xl hover:bg-brand-800 transition shadow-md hover:shadow-lg transform active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan Profil</span>
                </button>
            </div>
        </form>

    </div>

</div>
@endsection
