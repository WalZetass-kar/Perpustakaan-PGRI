@extends('layouts.dashboard')

@section('title', 'Kartu Perpustakaan Digital Siswa')
@section('page_heading', 'Kartu Tanda Anggota Digital')

@section('content')
<!-- Print Specific CSS Styling - Preserves Exact Web Design & Colors -->
<style>
    @media print {
        @page {
            size: auto;
            margin: 10mm;
        }
        body {
            background-color: #ffffff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body * {
            visibility: hidden !important;
        }
        #printable-card, #printable-card * {
            visibility: visible !important;
        }
        #printable-card {
            position: absolute !important;
            left: 50% !important;
            top: 20px !important;
            transform: translateX(-50%) !important;
            width: 100% !important;
            max-width: 600px !important;
            margin: 0 auto !important;
            box-shadow: none !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

<div class="max-w-2xl mx-auto space-y-6" x-data="{ photoUrl: null }">

    <!-- Action Bar (Print & Upload Photo) -->
    <div class="bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-black text-gray-900">Kartu Tanda Anggota Digital Resmi</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Unggah pas foto siswa dan tunjukkan QR Code / Barcode ini ke Pustakawan.</p>
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

    <!-- DIGITAL LIBRARY CARD FRONT SIDE -->
    <div class="bg-white rounded-3xl border-2 border-brand-700 shadow-xl overflow-hidden relative transform transition duration-300" id="printable-card">
        
        <!-- Header Banner (Red Brand Primary #B91C1C & Gold Trim) -->
        <div class="bg-gradient-to-r from-brand-900 via-brand-700 to-brand-800 text-white p-5 flex items-center justify-between border-b-4 border-amber-400 relative overflow-hidden">
            <!-- Subtle Watermark Background -->
            <div class="absolute -right-6 -bottom-6 w-28 h-28 opacity-10 rounded-full bg-white pointer-events-none"></div>

            <div class="flex items-center gap-3.5 z-10">
                <div class="w-12 h-12 rounded-2xl bg-white p-1.5 shadow-lg shrink-0 flex items-center justify-center border border-amber-300">
                    <img src="https://simpeg.smkpgripekanbaru.sch.id/images/logo.png" alt="Logo SMK PGRI" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="text-sm font-black tracking-wider leading-tight uppercase text-amber-300">PERPUSTAKAAN SMK PGRI</h2>
                    <p class="text-[10px] font-bold text-red-100 uppercase tracking-widest mt-0.5">KARTU TANDA ANGGOTA RESMI</p>
                </div>
            </div>
            <div class="text-right z-10">
                <span class="inline-block px-3 py-1 rounded-xl bg-amber-400 text-brand-900 text-[10px] font-black uppercase shadow-xs border border-amber-200">
                    {{ strtoupper($anggota->status ?? 'Aktif') }}
                </span>
            </div>
        </div>

        <!-- Card Body Content -->
        <div class="p-6 bg-white space-y-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                
                <!-- Student Photo Frame (With Permanent DB Photo Rendering) -->
                <div class="relative shrink-0">
                    <div class="w-32 h-40 bg-gradient-to-b from-gray-50 to-gray-100 border-2 border-brand-700 rounded-2xl overflow-hidden flex flex-col items-center justify-center p-1 shadow-md relative group">
                        
                        @if($anggota && $anggota->foto)
                            <img src="{{ asset('storage/' . $anggota->foto) }}" class="w-full h-full object-cover rounded-xl" alt="Pas Foto Siswa {{ $user->name }}">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-brand-50/50 text-center p-2 rounded-xl">
                                <div class="w-16 h-16 rounded-full bg-brand-700 text-white flex items-center justify-center font-black text-2xl shadow-md mb-2">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="text-[9px] font-black text-brand-700 uppercase tracking-wider block">PAS FOTO SISWA</span>
                                <span class="text-[8px] text-gray-400 font-mono">3 x 4 CM</span>
                            </div>
                        @endif
                        
                        <!-- Overlay Hint -->
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[9px] font-bold text-center p-1">
                            Klik Ganti Pas Foto di Atas
                        </div>
                    </div>
                </div>

                <!-- Detailed Information Grid -->
                <div class="flex-1 w-full space-y-3.5 text-xs">
                    <div class="border-b-2 border-gray-100 pb-2">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Nama Lengkap Siswa</span>
                        <span class="text-base font-black text-gray-900 block leading-tight tracking-tight">{{ $user->name }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 border-b-2 border-gray-100 pb-2">
                        <div>
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">NISN / Nomor Induk</span>
                            <span class="font-mono text-gray-900 font-extrabold text-xs block">{{ $anggota->nim ?? '1022014001' }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Nomor Anggota ID</span>
                            <span class="font-mono text-brand-700 font-black text-xs block">{{ $anggota->nomor_anggota ?? 'LIB-2026-001' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Program / Jurusan</span>
                            <span class="text-gray-900 font-bold text-xs block truncate">{{ $anggota->program_studi ?? 'Teknik Komputer & Jaringan' }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Email Terdaftar</span>
                            <span class="text-gray-600 font-medium text-[11px] block truncate">{{ $user->email }}</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Real Scannable QR Code & Barcode Section -->
            <div class="pt-4 border-t-2 border-dashed border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50 p-4 rounded-2xl border-2 border-gray-200/80">
                
                <!-- Barcode Simulation -->
                <div class="space-y-1 text-center sm:text-left">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">BARCODE SIRKULASI MEJA PUSTAKAWAN</span>
                    <div class="h-10 bg-gray-950 w-48 rounded-xl px-2.5 py-1.5 flex items-center justify-between gap-0.5 mx-auto sm:mx-0 shadow-inner">
                        <div class="w-1.5 h-full bg-white"></div>
                        <div class="w-0.5 h-full bg-white"></div>
                        <div class="w-2 h-full bg-white"></div>
                        <div class="w-0.5 h-full bg-white"></div>
                        <div class="w-1 h-full bg-white"></div>
                        <div class="w-2.5 h-full bg-white"></div>
                        <div class="w-0.5 h-full bg-white"></div>
                        <div class="w-1.5 h-full bg-white"></div>
                        <div class="w-2 h-full bg-white"></div>
                        <div class="w-0.5 h-full bg-white"></div>
                        <div class="w-1.5 h-full bg-white"></div>
                        <div class="w-2 h-full bg-white"></div>
                    </div>
                    <span class="font-mono text-[10px] font-black text-gray-800 tracking-widest block">{{ $anggota->nomor_anggota ?? 'LIB-2026-001' }}</span>
                </div>

                <!-- REAL SCANNABLE QR CODE GENERATOR (via QuickChart Realtime API) -->
                @php
                    $qrContent = urlencode("PERPUS-SMK-PGRI|NISN:" . ($anggota->nim ?? '1022014001') . "|MEMBER:" . ($anggota->nomor_anggota ?? 'LIB-2026-001') . "|NAME:" . $user->name);
                    $qrApiUrl = "https://quickchart.io/qr?text={$qrContent}&size=150&margin=1";
                @endphp
                <div class="flex items-center gap-3 bg-white p-2 rounded-xl border-2 border-gray-200 shadow-sm shrink-0">
                    <div class="w-14 h-14 bg-white p-0.5 rounded-lg border border-gray-300">
                        <img src="{{ $qrApiUrl }}" alt="Real Scannable QR Code" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="text-[9px] font-black text-emerald-600 block uppercase flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>VALID SCANNABLE</span>
                        </span>
                        <span class="font-mono text-[11px] font-black text-brand-700 block mt-0.5">{{ $anggota->nim ?? '1022014001' }}</span>
                    </div>
                </div>

            </div>

        </div>

        <!-- Footer Card Ribbon -->
        <div class="bg-gray-100 border-t-2 border-gray-200 px-6 py-3 flex items-center justify-between text-[10px] text-gray-500 font-semibold">
            <span>Kartu ini berlaku selama menjadi siswa aktif SMK PGRI Pekanbaru</span>
            <span class="font-black text-brand-700">Official Digital ID Card</span>
        </div>

    </div>

</div>
@endsection
