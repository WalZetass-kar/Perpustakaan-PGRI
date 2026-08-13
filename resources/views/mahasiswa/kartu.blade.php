@extends('layouts.dashboard')

@section('title', 'Kartu Perpustakaan Digital Siswa')
@section('page_heading', 'Kartu Tanda Anggota Digital')

@section('content')
<!-- Print Specific CSS Styling - Preserves Exact Web Design, Colors & Dimensions -->
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
            width: 460px !important;
            max-width: 460px !important;
            margin: 0 auto !important;
            box-shadow: none !important;
            border: 2px solid #b91c1c !important;
            border-radius: 20px !important;
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
            <h2 class="text-sm font-black text-gray-900">Kartu Tanda Anggota Standard KTP / CR80</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Format kartu identitas sekolah resmi dengan QR Code scannable.</p>
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

    <!-- DIGITAL LIBRARY CARD FRONT SIDE (Standard KTP Aspect Ratio ~480px width) -->
    <div class="w-full max-w-[480px] mx-auto bg-white rounded-3xl border-2 border-brand-700 shadow-2xl overflow-hidden relative" id="printable-card">
        
        <!-- Header Banner (Red Brand & Gold Trim) -->
        <div class="bg-gradient-to-r from-brand-900 via-brand-700 to-red-800 text-white p-3.5 sm:p-4 flex items-center justify-between border-b-4 border-amber-400 relative">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white p-1 shadow-md shrink-0 flex items-center justify-center border border-amber-300">
                    <img src="https://simpeg.smkpgripekanbaru.sch.id/images/logo.png" alt="Logo SMK PGRI" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="text-xs sm:text-sm font-black tracking-wider leading-tight uppercase text-amber-300">PERPUSTAKAAN SMK PGRI</h2>
                    <p class="text-[9px] font-extrabold text-red-100 uppercase tracking-widest">KARTU ANGGOTA RESMI</p>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-lg bg-amber-400 text-brand-900 text-[9px] font-black uppercase shadow-2xs border border-amber-200">
                {{ strtoupper($anggota->status ?? 'Aktif') }}
            </span>
        </div>

        <!-- Card Body Content -->
        <div class="p-4 sm:p-5 bg-white space-y-4">
            
            <div class="flex items-start gap-4">
                
                <!-- Student Photo Frame (Compact 3x4 Ratio) -->
                <div class="shrink-0">
                    <div class="w-24 h-32 bg-gradient-to-b from-gray-50 to-gray-100 border-2 border-brand-700 rounded-xl overflow-hidden flex flex-col items-center justify-center p-0.5 shadow-sm relative group">
                        @if($anggota && $anggota->foto)
                            <img src="{{ asset('storage/' . $anggota->foto) }}" class="w-full h-full object-cover rounded-lg" alt="Pas Foto {{ $user->name }}">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-brand-50/50 text-center p-1 rounded-lg">
                                <div class="w-12 h-12 rounded-full bg-brand-700 text-white flex items-center justify-center font-black text-xl shadow-xs mb-1">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="text-[8px] font-black text-brand-700 uppercase tracking-wider block">PAS FOTO</span>
                                <span class="text-[7px] text-gray-400 font-mono">3 x 4</span>
                            </div>
                        @endif
                        <!-- Hover hint -->
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[8px] font-bold text-center p-1">
                            Ganti Foto
                        </div>
                    </div>
                </div>

                <!-- Detailed Information Grid -->
                <div class="flex-1 min-w-0 space-y-2 text-[11px]">
                    <div class="border-b border-gray-100 pb-1">
                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest block">Nama Lengkap</span>
                        <span class="text-xs font-black text-gray-900 block truncate leading-snug">{{ $user->name }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-1">
                        <div>
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest block">NISN / NIS</span>
                            <span class="font-mono text-gray-900 font-extrabold text-[10px] block truncate">{{ $anggota->nim ?? '1022014001' }}</span>
                        </div>
                        <div>
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest block">No. Anggota</span>
                            <span class="font-mono text-brand-700 font-black text-[10px] block truncate">{{ $anggota->nomor_anggota ?? 'LIB-2026-001' }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest block">Jurusan / Keahlian</span>
                        <span class="text-gray-900 font-bold text-[10px] block truncate leading-tight">{{ $anggota->program_studi ?? 'Teknik Komputer & Jaringan' }}</span>
                    </div>
                </div>

            </div>

            <!-- Bottom QR Code Section (No Linear Barcode Box) -->
            @php
                $qrContent = urlencode("PERPUS-SMK-PGRI|NISN:" . ($anggota->nim ?? '1022014001') . "|MEMBER:" . ($anggota->nomor_anggota ?? 'LIB-2026-001') . "|NAME:" . $user->name);
                $qrApiUrl = "https://quickchart.io/qr?text={$qrContent}&size=140&margin=1";
            @endphp
            <div class="pt-3 border-t-2 border-dashed border-gray-200 flex items-center justify-between gap-3 bg-gray-50 p-2.5 rounded-xl border border-gray-200">
                <div class="space-y-0.5">
                    <span class="text-[8px] font-black text-emerald-600 uppercase flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>VALID QR CODE ID</span>
                    </span>
                    <span class="text-[9px] text-gray-500 font-medium block">Scan QR ke meja Pustakawan untuk sirkulasi buku.</span>
                </div>

                <div class="w-12 h-12 bg-white p-0.5 rounded-lg border border-gray-300 shadow-2xs shrink-0 flex items-center justify-center">
                    <img src="{{ $qrApiUrl }}" alt="QR Code Anggota" class="w-full h-full object-contain">
                </div>
            </div>

        </div>

        <!-- Footer Ribbon -->
        <div class="bg-gray-100 border-t border-gray-200 px-4 py-1.5 flex items-center justify-between text-[9px] text-gray-500 font-medium">
            <span>Berlaku selama menjadi siswa SMK PGRI</span>
            <span class="font-bold text-brand-700">Official ID Card</span>
        </div>

    </div>

</div>
@endsection
