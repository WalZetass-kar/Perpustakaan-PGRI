@extends('layouts.dashboard')

@section('title', 'Kartu Perpustakaan Digital Siswa')
@section('page_heading', 'Kartu Perpustakaan Digital')

@section('content')
<!-- Print Specific CSS Styling for perfect ID Card Dimension -->
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printable-card, #printable-card * {
            visibility: visible;
        }
        #printable-card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100% !important;
            max-width: 450px !important;
            border: 2px solid #000 !important;
            box-shadow: none !important;
        }
    }
</style>

<div class="max-w-2xl mx-auto space-y-6">

    <!-- Action Bar (Print & Download Options) -->
    <div class="flex items-center justify-between bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-xs">
        <div>
            <h2 class="text-sm font-black text-gray-900">Kartu Tanda Anggota Digital Resmi</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Tunjukkan QR Code / Barcode ID ini ke Pustakawan untuk transaksi pinjam buku cepat.</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-md hover:shadow-lg transform active:scale-95 flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>

    <!-- DIGITAL LIBRARY CARD FRONT SIDE -->
    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-md overflow-hidden relative" id="printable-card">
        
        <!-- Header Banner (Red Brand Primary #B91C1C) -->
        <div class="bg-brand-700 text-white p-4 sm:p-5 flex items-center justify-between border-b-2 border-brand-800">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-white p-1 shadow-md shrink-0 flex items-center justify-center">
                    <img src="https://simpeg.smkpgripekanbaru.sch.id/images/logo.png" alt="Logo SMK PGRI" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="text-xs sm:text-sm font-black tracking-wider leading-tight uppercase">PERPUSTAKAAN SMK PGRI</h2>
                    <p class="text-[10px] sm:text-[11px] font-semibold text-brand-100 uppercase tracking-widest">KARTU TANDA ANGGOTA PERPUSTAKAAN</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-lg bg-white text-brand-700 text-[10px] font-black uppercase shadow-xs">
                    {{ strtoupper($anggota->status ?? 'Aktif') }}
                </span>
            </div>
        </div>

        <!-- Card Body Content -->
        <div class="p-6 bg-white space-y-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                
                <!-- Student Photo Placeholder with Badge -->
                <div class="relative shrink-0">
                    <div class="w-32 h-40 bg-gray-50 border-2 border-gray-200 rounded-xl flex flex-col items-center justify-center p-2 text-center shadow-2xs">
                        <div class="w-16 h-16 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center font-bold text-xl mb-1">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">PAS FOTO SISWA</span>
                        <span class="text-[9px] text-gray-500 font-mono mt-0.5">RESMI PGRI</span>
                    </div>
                </div>

                <!-- Detailed Information Grid -->
                <div class="flex-1 w-full space-y-3 text-xs">
                    <div class="border-b border-gray-100 pb-2">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Nama Lengkap Siswa</span>
                        <span class="text-base font-extrabold text-gray-900 block leading-tight">{{ $user->name }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 border-b border-gray-100 pb-2">
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">NISN / NIS</span>
                            <span class="font-mono text-gray-900 font-bold text-xs block">{{ $anggota->nim ?? '1022014001' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Nomor Anggota ID</span>
                            <span class="font-mono text-brand-700 font-extrabold text-xs block">{{ $anggota->nomor_anggota ?? 'LIB-2026-001' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Program / Jurusan</span>
                            <span class="text-gray-800 font-bold text-xs block truncate">{{ $anggota->program_studi ?? 'Teknik Komputer & Jaringan' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Email Terdaftar</span>
                            <span class="text-gray-600 font-medium text-[11px] block truncate">{{ $user->email }}</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Scannable Barcode & QR Code Section -->
            <div class="pt-4 border-t-2 border-dashed border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/70 p-4 rounded-xl">
                <!-- Barcode Lines Visual Simulation -->
                <div class="space-y-1 text-center sm:text-left">
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block">BARCODE SIRKULASI PERPUSTAKAAN</span>
                    <div class="h-9 bg-gray-900 w-48 rounded px-2 py-1 flex items-center justify-between gap-0.5 mx-auto sm:mx-0">
                        <div class="w-1 h-full bg-white"></div>
                        <div class="w-0.5 h-full bg-white"></div>
                        <div class="w-1.5 h-full bg-white"></div>
                        <div class="w-0.5 h-full bg-white"></div>
                        <div class="w-1 h-full bg-white"></div>
                        <div class="w-2 h-full bg-white"></div>
                        <div class="w-0.5 h-full bg-white"></div>
                        <div class="w-1 h-full bg-white"></div>
                        <div class="w-1.5 h-full bg-white"></div>
                        <div class="w-0.5 h-full bg-white"></div>
                        <div class="w-1 h-full bg-white"></div>
                        <div class="w-2 h-full bg-white"></div>
                    </div>
                    <span class="font-mono text-[10px] font-bold text-gray-700 tracking-widest block">{{ $anggota->nomor_anggota ?? 'LIB-2026-001' }}</span>
                </div>

                <!-- QR Code Visual Box -->
                <div class="flex items-center gap-3 bg-white p-2.5 rounded-lg border border-gray-200 shadow-2xs shrink-0">
                    <div class="w-12 h-12 bg-gray-900 flex items-center justify-center rounded p-1">
                        <svg class="w-full h-full text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm-2 12h8v8H2v-8zm2 2v4h4v-4H4zm12-16h8v8h-8V2zm2 2v4h4V4h-4zM14 14h2v2h-2v-2zm2 2h2v2h-2v-2zm-2 2h2v2h-2v-2zm4-2h2v2h-2v-2zm2 2h2v2h-2v-2zm0-4h2v2h-2v-2z"/></svg>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-gray-400 block uppercase">SCAN QR</span>
                        <span class="font-mono text-[11px] font-extrabold text-brand-700 block">{{ $anggota->nim ?? '1022014001' }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer Card Ribbon -->
        <div class="bg-gray-100 border-t border-gray-200 px-6 py-2.5 flex items-center justify-between text-[10px] text-gray-500">
            <span>Berlaku selama menjadi siswa aktif SMK PGRI</span>
            <span class="font-bold text-brand-700">Official Digital ID Card</span>
        </div>

    </div>

</div>
@endsection
