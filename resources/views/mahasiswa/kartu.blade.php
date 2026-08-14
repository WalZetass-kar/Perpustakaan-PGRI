@extends('layouts.dashboard')

@section('title', 'Kartu Perpustakaan Digital Siswa')
@section('page_heading', 'Kartu Tanda Anggota Digital')

@section('content')
<!-- Print Specific CSS Stylesheet - Preserves Physical CR80 Dimensions (85.6mm x 54mm) -->
<style>
    @media print {
        /* Single CR80 Card Page Settings */
        @page {
            size: 85.6mm 54mm;
            margin: 0;
        }

        html, body {
            width: 85.6mm !important;
            height: 54mm !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        /* Hide all non-printable Dashboard UI elements */
        header, nav, aside, sidebar, button, form, .no-print, .dashboard-header, #app-header {
            display: none !important;
        }

        /* Single CR80 Card Output Selector */
        #printable-card {
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            position: fixed !important;
            left: 0 !important;
            top: 0 !important;
            width: 85.6mm !important;
            height: 54mm !important;
            max-width: 85.6mm !important;
            max-height: 54mm !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
            border: 0.5mm solid #b91c1c !important;
            border-radius: 2.5mm !important;
            overflow: hidden !important;
            background: #ffffff !important;
            page-break-after: avoid !important;
            break-after: avoid !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Print Typography & Element Resizing for 85.6mm x 54mm */
        #printable-card * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Header Strip Print Layout */
        #printable-card > div:nth-child(1) {
            height: 8.5mm !important;
            padding: 1mm 2mm !important;
            border-bottom-width: 0.4mm !important;
        }
        #printable-card > div:nth-child(1) img {
            width: 5.5mm !important;
            height: 5.5mm !important;
        }
        #printable-card > div:nth-child(1) h2 {
            font-size: 6.5pt !important;
            line-height: 1 !important;
        }
        #printable-card > div:nth-child(1) p {
            font-size: 4.5pt !important;
            line-height: 1 !important;
        }
        #printable-card > div:nth-child(1) span {
            font-size: 4.5pt !important;
            padding: 0.4mm 1.2mm !important;
        }

        /* Body Strip Print Layout */
        #printable-card > div:nth-child(2) {
            height: 40mm !important;
            padding: 1.5mm 2mm !important;
            gap: 2mm !important;
        }
        /* Photo Frame Print */
        #printable-card .card-photo-frame {
            width: 21mm !important;
            height: 26.5mm !important;
            border-width: 0.4mm !important;
            border-radius: 1.5mm !important;
        }
        /* Details Text Print */
        #printable-card .card-info-container {
            gap: 1mm !important;
        }
        #printable-card .card-label {
            font-size: 4.5pt !important;
            line-height: 1 !important;
        }
        #printable-card .card-name-val {
            font-size: 7.5pt !important;
            line-height: 1.1 !important;
        }
        #printable-card .card-data-val {
            font-size: 6pt !important;
            line-height: 1.1 !important;
        }

        /* QR Code Container Print */
        #printable-card .card-qr-box {
            padding: 1mm !important;
            border-radius: 1.5mm !important;
        }
        #printable-card .card-qr-img-wrap {
            width: 14.5mm !important;
            height: 14.5mm !important;
        }
        #printable-card .card-qr-label {
            font-size: 4.5pt !important;
        }
        #printable-card .card-qr-subtext {
            font-size: 3.8pt !important;
            max-width: 16mm !important;
        }

        /* Footer Strip Print Layout */
        #printable-card > div:nth-child(3) {
            height: 5.5mm !important;
            padding: 0.8mm 2mm !important;
            font-size: 4.5pt !important;
            border-top-width: 0.3mm !important;
        }

        /* A4 Sheet Multi-Card Print Settings */
        .sheet-print-mode @page {
            size: A4 portrait;
            margin: 10mm;
        }
        .sheet-print-mode html, .sheet-print-mode body {
            width: 210mm !important;
            height: 297mm !important;
        }
    }
</style>

<div class="max-w-4xl mx-auto space-y-6" x-data="{ printMode: 'single' }">

    <!-- Action Bar (Print & Upload Photo - Hidden on Print) -->
    <div class="no-print bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="space-y-1 flex-1 min-w-0">
            <h2 class="text-base font-black text-gray-900 leading-snug">Sistem Cetak Kartu Anggota Presisi (CR80)</h2>
            <p class="text-xs text-gray-500">Ukuran cetak otomatis disesuaikan ke standar fisik <strong class="text-gray-900">85.6 mm × 54 mm</strong>.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <!-- Mode Toggle Option -->
            <select x-model="printMode" class="px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-700">
                <option value="single">Mode 1 Kartu CR80 (85.6 x 54 mm)</option>
                <option value="sheet">Mode Lembar A4 (Multi-Kartu Grid)</option>
            </select>

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
            <button @click="
                if (printMode === 'sheet') {
                    document.body.classList.add('sheet-print-mode');
                } else {
                    document.body.classList.remove('sheet-print-mode');
                }
                window.print();
            " class="px-5 py-2.5 bg-red-700 hover:bg-red-800 text-white font-extrabold text-xs rounded-xl transition shadow-md hover:shadow-lg transform active:scale-95 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / PDF</span>
            </button>
        </div>
    </div>

    <!-- OFFICIAL INSTITUTIONAL LIBRARY CARD FRONT SIDE -->
    <div class="w-full max-w-[500px] mx-auto bg-white rounded-2xl border-2 border-red-700 shadow-xl overflow-hidden flex flex-col justify-between relative" id="printable-card">
        
        <!-- 1. Institutional Header Banner -->
        <div class="bg-red-700 text-white px-4 py-3 flex items-center justify-between border-b-2 border-amber-400 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-white p-1 shadow-xs shrink-0 flex items-center justify-center border border-amber-300">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SMK PGRI" class="w-full h-full object-contain">
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
        <div class="p-4 sm:p-5 bg-white flex items-center justify-between gap-3.5 flex-1 min-h-0">
            
            <!-- Area 1: Student Photo (Pas Foto 4:5 Ratio) -->
            <div class="shrink-0">
                <div class="card-photo-frame w-28 h-36 bg-gray-50 border-2 border-red-700 rounded-xl overflow-hidden flex flex-col items-center justify-center shadow-xs relative group">
                    @if($anggota && $anggota->foto)
                        <img src="{{ asset('storage/' . $anggota->foto) }}" class="w-full h-full object-cover rounded-lg" alt="Pas Foto {{ $user->name }}">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-red-50/50 text-center p-2 rounded-lg">
                            <div class="w-12 h-12 rounded-full bg-red-700 text-white flex items-center justify-center font-black text-xl shadow-xs mb-1">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <span class="text-[8px] font-black text-red-700 uppercase tracking-wider block">FOTO 3x4</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Area 2: Student Information Details (Focal Point) -->
            <div class="card-info-container flex-1 min-w-0 space-y-2 text-[10px]">
                <div>
                    <span class="card-label text-[8px] font-extrabold text-gray-500 uppercase tracking-wider block leading-none">NAMA LENGKAP SISWA</span>
                    <span class="card-name-val text-xs sm:text-sm font-extrabold text-gray-900 block truncate leading-tight mt-0.5">{{ $user->name }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2 border-t border-b border-gray-100 py-1.5">
                    <div>
                        <span class="card-label text-[8px] font-extrabold text-gray-500 uppercase tracking-wider block leading-none">NISN / NIS</span>
                        <span class="card-data-val font-mono text-gray-800 font-extrabold text-[10px] block truncate mt-0.5">{{ $anggota->nim ?? '0877667687' }}</span>
                    </div>
                    <div>
                        <span class="card-label text-[8px] font-extrabold text-gray-500 uppercase tracking-wider block leading-none">ID ANGGOTA</span>
                        <span class="card-data-val font-mono text-red-700 font-extrabold text-[10px] block truncate mt-0.5">{{ $anggota->nomor_anggota ?? 'LIB-2026-005' }}</span>
                    </div>
                </div>

                <div>
                    <span class="card-label text-[8px] font-extrabold text-gray-500 uppercase tracking-wider block leading-none">PROGRAM / JURUSAN</span>
                    <span class="card-data-val text-gray-900 font-bold text-[10px] block truncate leading-tight mt-0.5">{{ $anggota->program_studi ?? 'Teknik Komputer & Jaringan (TKJ)' }}</span>
                </div>
            </div>

            <!-- Area 3: Proportional & Clean QR Code -->
            @php
                $qrContent = urlencode("PERPUS-SMK-PGRI|NISN:" . ($anggota->nim ?? '0877667687') . "|MEMBER:" . ($anggota->nomor_anggota ?? 'LIB-2026-005') . "|NAME:" . $user->name);
                $qrApiUrl = "https://quickchart.io/qr?text={$qrContent}&size=150&margin=1";
            @endphp
            <div class="card-qr-box shrink-0 flex flex-col items-center justify-center p-2.5 bg-white rounded-xl border border-gray-200 shadow-2xs text-center space-y-1">
                <div class="card-qr-img-wrap w-16 h-16 bg-white p-0.5 rounded-lg border border-gray-200 flex items-center justify-center">
                    <img src="{{ $qrApiUrl }}" alt="QR Code Verifikasi" class="w-full h-full object-contain">
                </div>
                <span class="card-qr-label text-[8px] font-black text-emerald-600 block uppercase tracking-wider">VALID QR</span>
                <span class="card-qr-subtext text-[7px] font-bold text-gray-400 block max-w-[80px] leading-tight">Scan untuk verifikasi anggota</span>
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
