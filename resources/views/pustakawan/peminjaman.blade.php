@extends('layouts.dashboard')

@section('title', 'Peminjaman Buku Cepat')
@section('page_heading', 'Peminjaman Cepat (Scan Barcode)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Step-by-Step Interactive Workflow Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">
        
        <div class="border-b border-gray-100 pb-4">
            <h2 class="text-base font-bold text-gray-900">Form Transaksi Peminjaman Baru</h2>
            <p class="text-xs text-gray-500">Scan NIM Mahasiswa → Scan Barcode Eksemplar Buku → Tentukan Tanggal Tempo → Konfirmasi</p>
        </div>

        <!-- Step 1 & 2 Inputs -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Scan NIM Mahasiswa -->
            <form action="{{ route('pustakawan.peminjaman') }}" method="GET" class="space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <label class="block text-xs font-bold text-gray-900">Langkah 1: Scan / Input NIM Mahasiswa</label>
                <div class="flex gap-2">
                    <input type="text" name="scan_nim" value="{{ request('scan_nim') }}" placeholder="Contoh: 2022014001" required
                        class="flex-1 px-3.5 py-2 bg-white border border-gray-300 rounded-lg text-xs font-mono focus:ring-2 focus:ring-brand-700 focus:outline-none">
                    <button type="submit" class="px-4 py-2 bg-gray-900 text-white font-medium text-xs rounded-lg hover:bg-gray-800 transition">Cari</button>
                </div>
                
                @if($selectedUser)
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs space-y-1">
                        <p class="font-bold text-emerald-900">✓ {{ $selectedUser->name }}</p>
                        <p class="text-emerald-700">NIM: {{ $selectedUser->anggota->nim ?? '-' }} | Prodi: {{ $selectedUser->anggota->program_studi ?? '-' }}</p>
                    </div>
                @elseif(request('scan_nim'))
                    <p class="text-xs text-rose-600 font-semibold">Anggota dengan NIM tersebut tidak ditemukan.</p>
                @endif
            </form>

            <!-- Scan Barcode Buku -->
            <form action="{{ route('pustakawan.peminjaman') }}" method="GET" class="space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <input type="hidden" name="scan_nim" value="{{ request('scan_nim') }}">
                <label class="block text-xs font-bold text-gray-900">Langkah 2: Scan / Input Barcode Eksemplar</label>
                <div class="flex gap-2">
                    <input type="text" name="scan_barcode" value="{{ request('scan_barcode') }}" placeholder="Contoh: BC882002" required
                        class="flex-1 px-3.5 py-2 bg-white border border-gray-300 rounded-lg text-xs font-mono focus:ring-2 focus:ring-brand-700 focus:outline-none">
                    <button type="submit" class="px-4 py-2 bg-gray-900 text-white font-medium text-xs rounded-lg hover:bg-gray-800 transition">Cari</button>
                </div>

                @if($selectedExemplar)
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs space-y-1">
                        <p class="font-bold text-emerald-900">✓ {{ $selectedExemplar->buku->judul ?? '-' }}</p>
                        <p class="text-emerald-700">Kode: {{ $selectedExemplar->kode_eksemplar }} | Status: <strong class="uppercase">{{ $selectedExemplar->status }}</strong></p>
                    </div>
                @elseif(request('scan_barcode'))
                    <p class="text-xs text-rose-600 font-semibold">Eksemplar dengan barcode tersebut tidak ditemukan.</p>
                @endif
            </form>

        </div>

        <!-- Step 3 & Final Confirmation -->
        @if($selectedUser && $selectedExemplar && $selectedExemplar->status === 'tersedia')
            <form action="{{ route('pustakawan.peminjaman') }}" method="POST" class="pt-4 border-t border-gray-200 space-y-4">
                @csrf
                <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                <input type="hidden" name="barcode" value="{{ $selectedExemplar->barcode }}">

                <div class="bg-brand-50 border border-brand-200 rounded-xl p-4 space-y-3">
                    <h3 class="text-xs font-bold text-brand-900 uppercase tracking-wider">Langkah 3: Konfirmasi Peminjaman Buku</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-gray-500 block">Peminjam:</span>
                            <strong class="text-gray-900">{{ $selectedUser->name }} ({{ $selectedUser->email }})</strong>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Buku & Barcode:</span>
                            <strong class="text-gray-900">{{ $selectedExemplar->buku->judul }} [{{ $selectedExemplar->barcode }}]</strong>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Jatuh Tempo Ditentukan</label>
                        <input type="date" name="tanggal_jatuh_tempo" value="{{ $defaultDueDate }}" required
                            class="px-3.5 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold focus:ring-2 focus:ring-brand-700 focus:outline-none">
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('pustakawan.peminjaman') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200">Reset</a>
                    <button type="submit" class="px-6 py-2.5 bg-brand-700 text-white font-bold text-xs rounded-lg hover:bg-brand-800 transition shadow-sm">
                        Konfirmasi Peminjaman Sekarang
                    </button>
                </div>
            </form>
        @endif

    </div>

</div>
@endsection
