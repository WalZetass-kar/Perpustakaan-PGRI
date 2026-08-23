@extends('layouts.dashboard')

@section('title', 'Pengaturan Sistem Perpustakaan')
@section('page_heading', 'Pengaturan Sistem & Identitas Perpustakaan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ activeTab: 'identitas' }">

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-black text-gray-900">Pusat Konfigurasi & Kebijakan Perpustakaan</h2>
            <p class="text-xs text-gray-500 mt-0.5">Sesuaikan identitas resmi sekolah, jam layanan, batasan peminjaman, dan preferensi portal OPAC</p>
        </div>
        <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl border border-gray-200 text-xs font-bold w-full sm:w-auto overflow-x-auto">
            <button type="button" @click="activeTab = 'identitas'" :class="activeTab === 'identitas' ? 'bg-white text-brand-700 shadow-xs' : 'text-gray-600 hover:text-gray-900'" class="px-3 py-1.5 rounded-lg transition shrink-0">
                Identitas Lembaga
            </button>
            <button type="button" @click="activeTab = 'kontak'" :class="activeTab === 'kontak' ? 'bg-white text-brand-700 shadow-xs' : 'text-gray-600 hover:text-gray-900'" class="px-3 py-1.5 rounded-lg transition shrink-0">
                Kontak & Lokasi
            </button>
            <button type="button" @click="activeTab = 'sirkulasi'" :class="activeTab === 'sirkulasi' ? 'bg-white text-brand-700 shadow-xs' : 'text-gray-600 hover:text-gray-900'" class="px-3 py-1.5 rounded-lg transition shrink-0">
                Jam & Sirkulasi
            </button>
            <button type="button" @click="activeTab = 'tampilan'" :class="activeTab === 'tampilan' ? 'bg-white text-brand-700 shadow-xs' : 'text-gray-600 hover:text-gray-900'" class="px-3 py-1.5 rounded-lg transition shrink-0">
                Tampilan OPAC
            </button>
            <button type="button" @click="activeTab = 'server'" :class="activeTab === 'server' ? 'bg-white text-brand-700 shadow-xs' : 'text-gray-600 hover:text-gray-900'" class="px-3 py-1.5 rounded-lg transition shrink-0">
                Info Sistem
            </button>
        </div>
    </div>

    <form action="{{ route('admin.pengaturan.update') }}" method="POST" class="space-y-6 text-xs">
        @csrf

        <div x-show="activeTab === 'identitas'" class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="border-b border-gray-100 pb-3 flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-gray-900">1. Identitas & Kepengurusan Perpustakaan</h3>
                    <p class="text-[11px] text-gray-500">Profil resmi institusi sekolah dan penanggung jawab perpustakaan</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div class="space-y-1 sm:col-span-2">
                    <label class="block font-bold text-gray-800">Nama Resmi Perpustakaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_perpustakaan" value="{{ old('nama_perpustakaan', $pengaturan['nama_perpustakaan'] ?? 'Sistem Perpustakaan Sekolah') }}" required
                           placeholder="Contoh: Perpustakaan SMK Negeri 1 / SMA Nusantara"
                           class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-bold text-gray-900">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-gray-800">Nama Lembaga / Sekolah</label>
                    <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $pengaturan['nama_sekolah'] ?? '') }}"
                           placeholder="Contoh: SMK Negeri / Swasta"
                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-gray-800">Nomor Pokok Sekolah Nasional (NPSN)</label>
                    <input type="text" name="npsn" value="{{ old('npsn', $pengaturan['npsn'] ?? '') }}"
                           placeholder="Contoh: 10404456"
                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-mono font-bold">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-gray-800">Nama Kepala Perpustakaan / Pengelola</label>
                    <input type="text" name="kepala_perpustakaan" value="{{ old('kepala_perpustakaan', $pengaturan['kepala_perpustakaan'] ?? '') }}"
                           placeholder="Contoh: Nama Kepala Perpustakaan, M.Pd"
                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-gray-800">NIP / NUPTK Kepala Perpustakaan</label>
                    <input type="text" name="nip_kepala_perpustakaan" value="{{ old('nip_kepala_perpustakaan', $pengaturan['nip_kepala_perpustakaan'] ?? '') }}"
                           placeholder="Contoh: 19750812 200212 2 003"
                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-mono">
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'kontak'" x-cloak class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="border-b border-gray-100 pb-3 flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-gray-900">2. Kontak, Alamat & Lokasi Gedung</h3>
                    <p class="text-[11px] text-gray-500">Informasi kontak yang ditampilkan pada footer portal publik</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div class="space-y-1 sm:col-span-2">
                    <label class="block font-bold text-gray-800">Alamat Lengkap & Posisi Gedung</label>
                    <textarea name="alamat" rows="2" placeholder="Contoh: Jl. Pendidikan No. 45, Gedung Perpustakaan Lt. 2."
                              class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">{{ old('alamat', $pengaturan['alamat'] ?? '') }}</textarea>
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-gray-800">Email Resmi Layanan</label>
                    <input type="email" name="email_perpustakaan" value="{{ old('email_perpustakaan', $pengaturan['email_perpustakaan'] ?? '') }}"
                           placeholder="Contoh: perpustakaan@sekolah.sch.id"
                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-gray-800">Nomor Telepon / WhatsApp Layanan</label>
                    <input type="text" name="telepon" value="{{ old('telepon', $pengaturan['telepon'] ?? '') }}"
                           placeholder="Contoh: (021) 123456 / 0812-3456-7890"
                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="block font-bold text-gray-800">Website Resmi Sekolah</label>
                    <input type="text" name="website_sekolah" value="{{ old('website_sekolah', $pengaturan['website_sekolah'] ?? '') }}"
                           placeholder="Contoh: https://sekolah.sch.id"
                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-mono">
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'sirkulasi'" x-cloak class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="border-b border-gray-100 pb-3 flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-gray-900">3. Jam Operasional Layanan & Aturan Sirkulasi</h3>
                    <p class="text-[11px] text-gray-500">Kebijakan durasi peminjaman dan batas maksimal buku</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div class="space-y-1">
                    <label class="block font-bold text-gray-800">Jam Layanan (Senin - Kamis) <span class="text-rose-500">*</span></label>
                    <input type="text" name="jam_operasional" value="{{ old('jam_operasional', $pengaturan['jam_operasional'] ?? 'Senin - Kamis: 07.00 - 15.30 WIB') }}" required
                           placeholder="Contoh: Senin - Kamis: 07.00 - 15.30 WIB"
                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-semibold">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-gray-800">Jam Layanan Khusus (Jumat)</label>
                    <input type="text" name="jam_operasional_jumat" value="{{ old('jam_operasional_jumat', $pengaturan['jam_operasional_jumat'] ?? 'Jumat: 07.00 - 11.30 WIB') }}"
                           placeholder="Contoh: Jumat: 07.00 - 11.30 WIB"
                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-semibold">
                </div>

                <div class="space-y-1 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <label class="block font-bold text-gray-900">Batas Maksimal Buku Dipinjam <span class="text-rose-500">*</span></label>
                    <p class="text-[10.5px] text-gray-500 mb-2">Batas maksimal buku yang dapat dipinjam siswa sekaligus</p>
                    <div class="flex items-center gap-2">
                        <input type="number" name="max_buku_pinjam" value="{{ old('max_buku_pinjam', $pengaturan['max_buku_pinjam'] ?? 3) }}" required min="1" max="20"
                               class="w-24 px-3 py-1.5 bg-white border border-gray-300 rounded-lg focus:ring-1.5 focus:ring-brand-700 font-mono font-bold text-sm">
                        <span class="font-bold text-gray-700">Buku / Transaksi</span>
                    </div>
                </div>

                <div class="space-y-1 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <label class="block font-bold text-gray-900">Durasi Pinjam Standar <span class="text-rose-500">*</span></label>
                    <p class="text-[10.5px] text-gray-500 mb-2">Durasi peminjaman sebelum tanggal jatuh tempo</p>
                    <div class="flex items-center gap-2">
                        <input type="number" name="durasi_pinjam_hari" value="{{ old('durasi_pinjam_hari', $pengaturan['durasi_pinjam_hari'] ?? 7) }}" required min="1" max="30"
                               class="w-24 px-3 py-1.5 bg-white border border-gray-300 rounded-lg focus:ring-1.5 focus:ring-brand-700 font-mono font-bold text-sm">
                        <span class="font-bold text-gray-700">Hari</span>
                    </div>
                </div>

                <div class="space-y-1 bg-gray-50 p-4 rounded-xl border border-gray-200 sm:col-span-2">
                    <label class="block font-bold text-gray-900">Maksimal Perpanjangan Online</label>
                    <p class="text-[10.5px] text-gray-500 mb-2">Batas frekuensi perpanjangan durasi pinjam yang diizinkan</p>
                    <div class="flex items-center gap-2">
                        <input type="number" name="max_perpanjangan" value="{{ old('max_perpanjangan', $pengaturan['max_perpanjangan'] ?? 2) }}" min="0" max="10"
                               class="w-24 px-3 py-1.5 bg-white border border-gray-300 rounded-lg focus:ring-1.5 focus:ring-brand-700 font-mono font-bold text-sm">
                        <span class="font-bold text-gray-700">Kali</span>
                    </div>
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="block font-bold text-gray-800">Pesan Pengingat Sirkulasi di Detail Buku</label>
                    <textarea name="pesan_sirkulasi" rows="2" placeholder="Pesan petunjuk yang ditampilkan di halaman detail buku untuk siswa..."
                              class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">{{ old('pesan_sirkulasi', $pengaturan['pesan_sirkulasi'] ?? 'Untuk meminjam buku fisik, silakan datangi meja pengelola perpustakaan.') }}</textarea>
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="block font-bold text-gray-800">Ketentuan & Syarat Peminjam</label>
                    <input type="text" name="syarat_peminjaman" value="{{ old('syarat_peminjaman', $pengaturan['syarat_peminjaman'] ?? 'Siswa aktif sekolah dengan menyebutkan nama lengkap & kelas.') }}"
                           placeholder="Contoh: Siswa aktif sekolah dengan menyebutkan nama lengkap & kelas."
                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'tampilan'" x-cloak class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="border-b border-gray-100 pb-3 flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-purple-50 border border-purple-200 text-purple-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-gray-900">4. Tampilan & Preferensi Katalog Publik (OPAC)</h3>
                    <p class="text-[11px] text-gray-500">Pengaturan konten teks hero banner dan jumlah item per halaman katalog</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div class="space-y-1 sm:col-span-2">
                    <label class="block font-bold text-gray-800">Judul Utama Banner Beranda (Hero Title)</label>
                    <input type="text" name="judul_hero" value="{{ old('judul_hero', $pengaturan['judul_hero'] ?? 'Sistem Informasi Perpustakaan') }}"
                           placeholder="Contoh: Perpustakaan Digital Sekolah"
                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-bold">
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="block font-bold text-gray-800">Subjudul / Deskripsi Banner Beranda</label>
                    <textarea name="subjudul_hero" rows="2" placeholder="Deskripsi ringkas yang tampil di bawah judul hero..."
                              class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium">{{ old('subjudul_hero', $pengaturan['subjudul_hero'] ?? 'Katalog buku, modul pelajaran, dan sirkulasi peminjaman digital.') }}</textarea>
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-gray-800">Jumlah Koleksi Buku Per Halaman Katalog</label>
                    <input type="number" name="buku_per_halaman" value="{{ old('buku_per_halaman', $pengaturan['buku_per_halaman'] ?? 12) }}" min="4" max="48"
                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:bg-white focus:outline-none font-mono font-bold">
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'server'" x-cloak class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">5. Informasi Diagnostik Sistem & Server</h3>
                        <p class="text-[11px] text-gray-500">Status lingkungan server perpustakaan dan cadangan basis data</p>
                    </div>
                </div>

                @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.pengaturan.backup') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs flex items-center gap-2 shadow-xs transition">
                        <i class="fa-solid fa-download"></i>
                        <span>Unduh Backup Basis Data (.SQL)</span>
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 text-center">
                    <span class="text-[10px] text-gray-400 font-bold uppercase block">Framework</span>
                    <span class="text-xs font-black text-brand-700 mt-1 block font-mono">Laravel {{ $systemInfo['laravel_version'] ?? '11.x' }}</span>
                </div>
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 text-center">
                    <span class="text-[10px] text-gray-400 font-bold uppercase block">Versi PHP</span>
                    <span class="text-xs font-black text-gray-900 mt-1 block font-mono">PHP {{ $systemInfo['php_version'] ?? '8.4' }}</span>
                </div>
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 text-center">
                    <span class="text-[10px] text-gray-400 font-bold uppercase block">Database Engine</span>
                    <span class="text-xs font-black text-emerald-700 mt-1 block font-mono uppercase">{{ $systemInfo['db_driver'] ?? 'MySQL' }}</span>
                </div>
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 text-center">
                    <span class="text-[10px] text-gray-400 font-bold uppercase block">Environment</span>
                    <span class="text-xs font-black text-gray-900 mt-1 block uppercase font-mono">{{ $systemInfo['app_env'] ?? 'local' }}</span>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 space-y-2">
                <h4 class="font-bold text-gray-900 text-xs">Statistik Data Master:</h4>
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-2xs">
                        <span class="text-gray-400 text-[10px] block font-bold">Total Judul Buku</span>
                        <span class="font-black text-gray-900 text-sm mt-0.5 block">{{ number_format($systemInfo['total_buku'] ?? 0) }}</span>
                    </div>
                    <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-2xs">
                        <span class="text-gray-400 text-[10px] block font-bold">Total Transaksi</span>
                        <span class="font-black text-brand-700 text-sm mt-0.5 block">{{ number_format($systemInfo['total_pinjam'] ?? 0) }}</span>
                    </div>
                    <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-2xs">
                        <span class="text-gray-400 text-[10px] block font-bold">Total Pengguna</span>
                        <span class="font-black text-emerald-700 text-sm mt-0.5 block">{{ number_format($systemInfo['total_pengguna'] ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border-2 border-gray-200 shadow-sm flex items-center justify-between">
            <p class="text-[11px] text-gray-500 font-medium">Pastikan data identitas dan kebijakan sirkulasi sudah benar sebelum disimpan.</p>
            <button type="submit" class="px-7 py-2.5 bg-brand-700 text-white font-extrabold text-xs rounded-xl hover:bg-brand-800 transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Simpan Perubahan Pengaturan</span>
            </button>
        </div>

    </form>

</div>
@endsection
