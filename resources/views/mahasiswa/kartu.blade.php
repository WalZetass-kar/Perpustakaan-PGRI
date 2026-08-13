@extends('layouts.dashboard')

@section('title', 'Kartu Perpustakaan Digital Siswa')
@section('page_heading', 'Kartu Tanda Anggota Digital')

@section('content')
<!-- Print Specific CSS Styling - Preserves Exact Institutional Colors & Print Quality -->
<style>
    @media print {
        @page {
            size: auto;
            margin: 15mm;
        }
        html, body {
            background-color: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        body * {
            visibility: hidden !important;
        }
        #printable-card, #printable-card * {
            visibility: visible !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        #printable-card {
            position: absolute !important;
            left: 50% !important;
            top: 20px !important;
            transform: translateX(-50%) !important;
            width: 480px !important;
            max-width: 480px !important;
            margin: 0 auto !important;
            box-shadow: none !important;
            border: 2px solid #b91c1c !important;
            border-radius: 16px !important;
            overflow: hidden !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
    }
</style>

<div class="max-w-xl mx-auto space-y-6">

    <!-- Action Bar (Print & Upload Photo) -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-black text-gray-900">Kartu Tanda Anggota Perpustakaan Resmi</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Format kartu identitas sekolah resmi dengan QR Code verifikasi.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <!-- Custom Upload Photo Form (Permanent DB Storage) -->
            <form id="fotoForm" action="{{ route('mahasiswa.profil.update') }}" method="POST" enctype="multipart/form-data" class="inline">
                @csrf
                <input type="hidden" name="name" value="{{ $user->name }}">
                <label class="px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs rounded-xl transition cursor-pointer flex items-center gap-2 border border-gray-300 shadow-2xs">
                    <svg class="w-4 h-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Ganti Pas Foto</span>
                    <input type="file" name="foto" accept="image/*" class="hidden" onchange="document.getElementById('fotoForm').submit()">
                </label>
            </form>

            <!-- Print Card PDF Button -->
            <button onclick="window.print()" class="px-4 py-2.5 bg-red-700 hover:bg-red-800 text-white font-extrabold text-xs rounded-xl transition shadow-md hover:shadow-lg transform active:scale-95 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / PDF</span>
            </button>
        </div>
    </div>

    <!-- OFFICIAL INSTITUTIONAL LIBRARY CARD FRONT SIDE -->
    <div class="w-full max-w-[540px] mx-auto bg-white rounded-2xl border-2 border-red-700 shadow-xl overflow-hidden flex flex-col justify-between relative" id="printable-card">
        
        <!-- 1. Institutional Header Banner -->
        <div class="bg-red-700 text-white px-4 py-3 flex items-center justify-between border-b-2 border-amber-400 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-white p-1 shadow-xs shrink-0 flex items-center justify-center border border-amber-300">
                    <img src="https://simpeg.smkpgripekanbaru.sch.id/images/logo.png" alt="Logo SMK PGRI" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="text-xs sm:text-sm font-black tracking-wide leading-tight uppercase text-white">PERPUSTAKAAN SMK PGRI</h2>
                    <p class="text-[9px] sm:text-[10px] font-bold text-amber-300 uppercase tracking-wider mt-0.5">KARTU ANGGOTA PERPUSTAKAAN</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-md bg-amber-400 text-red-950 text-[9px] font-black uppercase tracking-wide flex items-center gap-1 shadow-2xs border border-amber-300 shrink-0">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                <span>AKTIF</span>
            </span>
        </div>

        <!-- 2. Clean Institutional Body Content (Photo | Information | QR Code) -->
        <div class="p-4 sm:p-5 bg-white flex items-center justify-between gap-4 flex-1">
            
            <!-- Area 1: Student Photo (Enlarged Pas Foto 4:5 Ratio - w-32 h-40) -->
            <div class="shrink-0">
                <div class="w-32 h-40 sm:w-34 sm:h-42 bg-gray-50 border-2 border-red-700 rounded-xl overflow-hidden flex flex-col items-center justify-center shadow-xs relative group">
                    @if($anggota && $anggota->foto)
                        <img src="{{ asset('storage/' . $anggota->foto) }}" class="w-full h-full object-cover rounded-lg" alt="Pas Foto {{ $user->name }}">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-red-50/50 text-center p-2 rounded-lg">
                            <div class="w-14 h-14 rounded-full bg-red-700 text-white flex items-center justify-center font-black text-2xl shadow-xs mb-1.5">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <span class="text-[9px] font-black text-red-700 uppercase tracking-wider block">FOTO SISWA 3x4</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Area 2: Student Information Details (Focal Point) -->
            <div class="flex-1 min-w-0 space-y-2.5 text-[10.5px]">
                <div>
                    <span class="text-[8.5px] font-extrabold text-gray-500 uppercase tracking-wider block leading-none">NAMA LENGKAP SISWA</span>
                    <span class="text-xs sm:text-sm font-extrabold text-gray-900 block truncate leading-tight mt-0.5">{{ $user->name }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2 border-t border-b border-gray-100 py-2">
                    <div>
                        <span class="text-[8.5px] font-extrabold text-gray-500 uppercase tracking-wider block leading-none">NISN / NIS</span>
                        <span class="font-mono text-gray-800 font-extrabold text-[10.5px] block truncate mt-0.5">{{ $anggota->nim ?? '0877667687' }}</span>
                    </div>
                    <div>
                        <span class="text-[8.5px] font-extrabold text-gray-500 uppercase tracking-wider block leading-none">ID ANGGOTA</span>
                        <span class="font-mono text-red-700 font-extrabold text-[10.5px] block truncate mt-0.5">{{ $anggota->nomor_anggota ?? 'LIB-2026-005' }}</span>
                    </div>
                </div>

                <div>
                    <span class="text-[8.5px] font-extrabold text-gray-500 uppercase tracking-wider block leading-none">PROGRAM / JURUSAN</span>
                    <span class="text-gray-900 font-bold text-[10.5px] block truncate leading-tight mt-0.5">{{ $anggota->program_studi ?? 'Teknik Komputer & Jaringan (TKJ)' }}</span>
                </div>
            </div>

            <!-- Area 3: Proportional & Clean QR Code -->
            @php
                $qrContent = urlencode("PERPUS-SMK-PGRI|NISN:" . ($anggota->nim ?? '0877667687') . "|MEMBER:" . ($anggota->nomor_anggota ?? 'LIB-2026-005') . "|NAME:" . $user->name);
                $qrApiUrl = "https://quickchart.io/qr?text={$qrContent}&size=150&margin=1";
            @endphp
            <div class="shrink-0 flex flex-col items-center justify-center p-2.5 bg-white rounded-xl border border-gray-200 shadow-2xs text-center space-y-1">
                <div class="w-18 h-18 bg-white p-0.5 rounded-lg border border-gray-200 flex items-center justify-center">
                    <img src="{{ $qrApiUrl }}" alt="QR Code Verifikasi" class="w-full h-full object-contain">
                </div>
                <span class="text-[8.5px] font-black text-emerald-600 block uppercase tracking-wider">VALID QR</span>
                <span class="text-[7.5px] font-bold text-gray-400 block max-w-[85px] leading-tight">Scan untuk verifikasi anggota</span>
            </div>

        </div>

        <!-- 3. Official Institutional Footer Banner -->
        <div class="bg-gray-100 border-t border-gray-200 px-4 py-2 flex items-center justify-between text-[9px] text-gray-600 font-semibold shrink-0">
            <span>Kartu berlaku selama menjadi siswa SMK PGRI</span>
            <span class="font-bold text-red-700">Kartu Anggota Perpustakaan Resmi</span>
        </div>

    </div>

</div>
@endsection
