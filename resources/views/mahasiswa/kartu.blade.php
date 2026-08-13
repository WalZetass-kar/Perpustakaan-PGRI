@extends('layouts.dashboard')

@section('title', 'Kartu Perpustakaan Digital Siswa')
@section('page_heading', 'Kartu Tanda Anggota Digital')

@section('content')
<!-- Print Specific CSS Styling - Preserves Exact CR80 / KTP Dimension & Aspect Ratio -->
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
            width: 420px !important;
            height: 260px !important;
            max-width: 420px !important;
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
            <h2 class="text-sm font-black text-gray-900">Kartu Anggota Presisi KTP / CR80</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Rasio dimensi standar KTP (85.6 x 54 mm) dengan QR Code terintegrasi.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <!-- Custom Upload Photo Form (Permanent DB Storage) -->
            <form id="fotoForm" action="{{ route('mahasiswa.profil.update') }}" method="POST" enctype="multipart/form-data" class="inline">
                @csrf
                <input type="hidden" name="name" value="{{ $user->name }}">
                <label class="px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs rounded-xl transition cursor-pointer flex items-center gap-2 border border-gray-300 shadow-2xs">
                    <svg class="w-4 h-4 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Ganti Pas Foto</span>
                    <input type="file" name="foto" accept="image/*" class="hidden" onchange="document.getElementById('fotoForm').submit()">
                </label>
            </form>

            <!-- Print Card PDF Button -->
            <button onclick="window.print()" class="px-4 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-md hover:shadow-lg transform active:scale-95 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / PDF</span>
            </button>
        </div>
    </div>

    <!-- DIGITAL LIBRARY CARD FRONT SIDE (Exact KTP CR80 Ratio 420px x 260px) -->
    <div class="w-full max-w-[420px] h-[260px] mx-auto bg-white rounded-2xl border-2 border-brand-700 shadow-2xl overflow-hidden flex flex-col justify-between relative" id="printable-card">
        
        <!-- 1. Compact Header Strip -->
        <div class="bg-gradient-to-r from-brand-900 via-brand-700 to-red-800 text-white px-3.5 py-2 flex items-center justify-between border-b-2 border-amber-400 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white p-0.5 shadow-xs shrink-0 flex items-center justify-center border border-amber-300">
                    <img src="https://simpeg.smkpgripekanbaru.sch.id/images/logo.png" alt="Logo SMK PGRI" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="text-[10px] font-black tracking-wider leading-none uppercase text-amber-300">PERPUSTAKAAN SMK PGRI</h2>
                    <p class="text-[7.5px] font-extrabold text-red-100 uppercase tracking-widest mt-0.5">KARTU ANGGOTA RESMI</p>
                </div>
            </div>
            <span class="px-2 py-0.5 rounded-md bg-amber-400 text-brand-900 text-[8px] font-black uppercase shadow-2xs border border-amber-200">
                {{ strtoupper($anggota->status ?? 'Aktif') }}
            </span>
        </div>

        <!-- 2. Compact Body Content (3 Columns: Photo | Details | QR Code) -->
        <div class="p-3 bg-white flex items-center justify-between gap-2.5 flex-1 min-h-0">
            
            <!-- Left Column: Student Photo (Compact 3x4 Aspect) -->
            <div class="shrink-0">
                <div class="w-20 h-26 bg-gradient-to-b from-gray-50 to-gray-100 border-2 border-brand-700 rounded-lg overflow-hidden flex flex-col items-center justify-center p-0.5 shadow-xs relative group">
                    @if($anggota && $anggota->foto)
                        <img src="{{ asset('storage/' . $anggota->foto) }}" class="w-full h-full object-cover rounded-md" alt="Pas Foto {{ $user->name }}">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-brand-50/50 text-center p-1 rounded-md">
                            <div class="w-10 h-10 rounded-full bg-brand-700 text-white flex items-center justify-center font-black text-lg shadow-2xs mb-0.5">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <span class="text-[7px] font-black text-brand-700 uppercase tracking-wider block">FOTO 3x4</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Middle Column: Student Information Details -->
            <div class="flex-1 min-w-0 space-y-1.5 text-[9.5px]">
                <div>
                    <span class="text-[7px] font-black text-gray-400 uppercase tracking-widest block leading-none">Nama Lengkap Siswa</span>
                    <span class="text-[11px] font-black text-gray-900 block truncate leading-tight mt-0.5">{{ $user->name }}</span>
                </div>

                <div class="grid grid-cols-2 gap-1 border-t border-b border-gray-100 py-1">
                    <div>
                        <span class="text-[7px] font-black text-gray-400 uppercase tracking-widest block leading-none">NISN / NIS</span>
                        <span class="font-mono text-gray-900 font-extrabold text-[9px] block truncate mt-0.5">{{ $anggota->nim ?? '1022014001' }}</span>
                    </div>
                    <div>
                        <span class="text-[7px] font-black text-gray-400 uppercase tracking-widest block leading-none">ID Anggota</span>
                        <span class="font-mono text-brand-700 font-black text-[9px] block truncate mt-0.5">{{ $anggota->nomor_anggota ?? 'LIB-2026-001' }}</span>
                    </div>
                </div>

                <div>
                    <span class="text-[7px] font-black text-gray-400 uppercase tracking-widest block leading-none">Program / Jurusan</span>
                    <span class="text-gray-900 font-bold text-[9px] block truncate leading-tight mt-0.5">{{ $anggota->program_studi ?? 'Teknik Komputer & Jaringan' }}</span>
                </div>
            </div>

            <!-- Right Column: REAL SCANNABLE QR CODE -->
            @php
                $qrContent = urlencode("PERPUS-SMK-PGRI|NISN:" . ($anggota->nim ?? '1022014001') . "|MEMBER:" . ($anggota->nomor_anggota ?? 'LIB-2026-001') . "|NAME:" . $user->name);
                $qrApiUrl = "https://quickchart.io/qr?text={$qrContent}&size=140&margin=1";
            @endphp
            <div class="shrink-0 flex flex-col items-center justify-center p-1.5 bg-gray-50 rounded-xl border border-gray-200 text-center space-y-1">
                <div class="w-14 h-14 bg-white p-0.5 rounded-lg border border-gray-300 shadow-2xs flex items-center justify-center">
                    <img src="{{ $qrApiUrl }}" alt="QR Code ID" class="w-full h-full object-contain">
                </div>
                <span class="text-[7px] font-black text-emerald-600 block uppercase tracking-wider">VALID QR</span>
            </div>

        </div>

        <!-- 3. Compact Footer Ribbon -->
        <div class="bg-gray-100 border-t border-gray-200 px-3 py-1 flex items-center justify-between text-[8px] text-gray-500 font-semibold shrink-0">
            <span>Berlaku selama menjadi siswa SMK PGRI</span>
            <span class="font-black text-brand-700">Official KTP ID Card</span>
        </div>

    </div>

</div>
@endsection
