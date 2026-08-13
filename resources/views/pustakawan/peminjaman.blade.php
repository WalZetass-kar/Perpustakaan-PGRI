@extends('layouts.dashboard')

@section('title', 'Peminjaman Buku Cepat')
@section('page_heading', 'Peminjaman Cepat (Scan QR / Barcode)')

@section('content')
<script src="https://unpkg.com/html5-qrcode"></script>

<div class="max-w-4xl mx-auto space-y-6" x-data="{ openScanner: false, targetField: 'nim', html5QrCode: null }">

    <!-- Step-by-Step Interactive Workflow Card -->
    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm p-6 space-y-6">
        
        <div class="border-b border-gray-100 pb-4 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-gray-900">Form Transaksi Peminjaman Baru</h2>
                <p class="text-xs text-gray-500">Scan NIM Siswa → Scan Barcode Eksemplar Buku → Tentukan Tanggal Tempo → Konfirmasi</p>
            </div>
            <button @click="targetField = 'nim'; openScanner = true; setTimeout(() => initCameraScanner(), 300)" class="px-3.5 py-2 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-2xs flex items-center gap-1.5 shrink-0">
                <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Buka Kamera Scanner</span>
            </button>
        </div>

        <!-- Step 1 & 2 Inputs -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Scan NIM Mahasiswa / Siswa -->
            <form action="{{ route('pustakawan.peminjaman') }}" method="GET" id="formNim" class="space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-gray-900">Langkah 1: Scan / Input NIM Siswa</label>
                    <button type="button" @click="targetField = 'nim'; openScanner = true; setTimeout(() => initCameraScanner(), 300)" class="text-[10px] font-extrabold text-brand-700 hover:underline flex items-center gap-1">
                        <span>Scan Camera</span> &raquo;
                    </button>
                </div>
                <div class="flex gap-2">
                    <input type="text" name="scan_nim" id="inputNim" value="{{ request('scan_nim') }}" placeholder="Contoh: 2022014001 atau Scan QR Kartu" required
                        class="flex-1 px-3.5 py-2 bg-white border border-gray-300 rounded-lg text-xs font-mono focus:ring-2 focus:ring-brand-700 focus:outline-none">
                    <button type="submit" class="px-4 py-2 bg-gray-900 text-white font-medium text-xs rounded-lg hover:bg-gray-800 transition">Cari</button>
                </div>
                
                @if($selectedUser)
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs space-y-1">
                        <p class="font-bold text-emerald-900 flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>{{ $selectedUser->name }}</span></p>
                        <p class="text-emerald-700">NIM: {{ $selectedUser->anggota->nim ?? '-' }} | Prodi: {{ $selectedUser->anggota->program_studi ?? '-' }}</p>
                    </div>
                @elseif(request('scan_nim'))
                    <p class="text-xs text-rose-600 font-semibold">Anggota dengan NIM tersebut tidak ditemukan.</p>
                @endif
            </form>

            <!-- Scan Barcode Buku -->
            <form action="{{ route('pustakawan.peminjaman') }}" method="GET" id="formBarcode" class="space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <input type="hidden" name="scan_nim" value="{{ request('scan_nim') }}">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-gray-900">Langkah 2: Scan / Input Barcode Eksemplar</label>
                    <button type="button" @click="targetField = 'barcode'; openScanner = true; setTimeout(() => initCameraScanner(), 300)" class="text-[10px] font-extrabold text-brand-700 hover:underline flex items-center gap-1">
                        <span>Scan Camera</span> &raquo;
                    </button>
                </div>
                <div class="flex gap-2">
                    <input type="text" name="scan_barcode" id="inputBarcode" value="{{ request('scan_barcode') }}" placeholder="Contoh: BC882002" required
                        class="flex-1 px-3.5 py-2 bg-white border border-gray-300 rounded-lg text-xs font-mono focus:ring-2 focus:ring-brand-700 focus:outline-none">
                    <button type="submit" class="px-4 py-2 bg-gray-900 text-white font-medium text-xs rounded-lg hover:bg-gray-800 transition">Cari</button>
                </div>

                @if($selectedExemplar)
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs space-y-1">
                        <p class="font-bold text-emerald-900 flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>{{ $selectedExemplar->buku->judul ?? '-' }}</span></p>
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
                <input type="hidden" name="buku_id" value="{{ $selectedExemplar->buku_id }}">
                <input type="hidden" name="eksemplar_id" value="{{ $selectedExemplar->id }}">

                <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="space-y-1">
                        <h4 class="text-xs font-black text-emerald-900">Langkah 3: Konfirmasi Tanggal Jatuh Tempo Peminjaman</h4>
                        <p class="text-xs text-emerald-700">Peminjaman otomatis diset 7 hari dari hari ini.</p>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <div>
                            <label class="block text-[10px] font-bold text-emerald-800 uppercase">Jatuh Tempo</label>
                            <input type="date" name="tanggal_jatuh_tempo" value="{{ \Carbon\Carbon::now()->addDays(7)->toDateString() }}" required
                                class="px-3 py-1.5 bg-white border border-emerald-300 rounded-lg text-xs font-bold font-mono focus:ring-2 focus:ring-emerald-600">
                        </div>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl transition shadow-md hover:shadow-lg transform active:scale-95 shrink-0">
                            Proses Peminjaman Fisik
                        </button>
                    </div>
                </div>
            </form>
        @endif

    </div>

    <!-- HTML5 Live Camera QR Scanner Modal -->
    <div x-show="openScanner" @click.self="stopCameraScanner(); openScanner = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/70 backdrop-blur-xs p-4" x-cloak>
        <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border-2 border-gray-200 text-center relative" @click.stop>
            <button @click="stopCameraScanner(); openScanner = false" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 text-gray-600 font-bold">&times;</button>
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">Kamera Web Scanner HP/Laptop</h3>
            <p class="text-xs text-gray-500">Arahkan kamera ke QR Code Kartu Siswa atau Barcode Stiker Buku.</p>

            <div id="reader" class="w-full h-64 bg-gray-100 rounded-2xl overflow-hidden border-2 border-gray-300"></div>

            <button @click="stopCameraScanner(); openScanner = false" class="w-full py-2.5 bg-gray-100 text-gray-700 font-extrabold text-xs rounded-xl hover:bg-gray-200 transition">
                Tutup Scanner
            </button>
        </div>
    </div>

</div>

<script>
    let html5QrcodeScanner = null;

    function initCameraScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
        html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
        
        let scannedVal = decodedText;
        if (scannedVal.includes("NISN:")) {
            let match = scannedVal.match(/NISN:([^|]+)/);
            if (match) scannedVal = match[1];
        } else if (scannedVal.includes("BARCODE:")) {
            let match = scannedVal.match(/BARCODE:([^|]+)/);
            if (match) scannedVal = match[1];
        }

        const inputNim = document.getElementById('inputNim');
        const inputBarcode = document.getElementById('inputBarcode');

        if (inputNim && !inputNim.value) {
            inputNim.value = scannedVal;
            document.getElementById('formNim').submit();
        } else if (inputBarcode) {
            inputBarcode.value = scannedVal;
            document.getElementById('formBarcode').submit();
        }
    }

    function stopCameraScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
    }
</script>
@endsection
