@extends('layouts.dashboard')

@section('title', 'Pengembalian Buku Cepat')
@section('page_heading', 'Pengembalian Cepat (Scan & Otomatisasi Denda)')

@section('content')
<script src="https://unpkg.com/html5-qrcode"></script>

<div class="max-w-4xl mx-auto space-y-6" x-data="{ openScanner: false, html5QrCode: null }">

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm p-6 space-y-6">
        
        <div class="border-b border-gray-100 pb-4 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-gray-900">Form Pengembalian Buku &amp; Hitung Denda</h2>
                <p class="text-xs text-gray-500">Scan Barcode → Cari Transaksi Aktif → Hitung Terlambat &amp; Denda Otomatis → Konfirmasi</p>
            </div>
            <button @click="openScanner = true; setTimeout(() => initCameraScanner(), 300)" class="px-3.5 py-2 bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-xs rounded-xl transition shadow-2xs flex items-center gap-1.5 shrink-0">
                <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Buka Kamera Scanner</span>
            </button>
        </div>

        <!-- Scan Barcode Search -->
        <form action="{{ route('pustakawan.pengembalian') }}" method="GET" id="formReturnScan" class="space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-200">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-gray-900">Scan / Input Barcode Eksemplar Buku Yang Dikembalikan</label>
                <button type="button" @click="openScanner = true; setTimeout(() => initCameraScanner(), 300)" class="text-[10px] font-extrabold text-brand-700 hover:underline flex items-center gap-1">
                    <span>Scan Camera</span> &raquo;
                </button>
            </div>
            <div class="flex gap-2">
                <input type="text" name="scan_barcode" id="inputReturnBarcode" value="{{ request('scan_barcode') }}" placeholder="Contoh: BC882001" required autofocus
                    class="flex-1 px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg text-xs font-mono focus:ring-2 focus:ring-brand-700 focus:outline-none">
                <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white font-medium text-xs rounded-lg hover:bg-gray-800 transition">Cari Transaksi</button>
            </div>
        </form>

        @if($peminjaman)
            <!-- Found Loan Details & Confirmation Form -->
            <form action="{{ route('pustakawan.pengembalian') }}" method="POST" class="pt-4 border-t border-gray-200 space-y-6">
                @csrf
                <input type="hidden" name="peminjaman_id" value="{{ $peminjaman->id }}">

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5 space-y-4 text-xs">
                    <h3 class="text-sm font-bold text-gray-900 border-b border-gray-200 pb-2">Informasi Transaksi Peminjaman</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <span class="text-gray-500 block">Kode Transaksi</span>
                            <strong class="font-mono text-gray-900">{{ $peminjaman->kode_peminjaman }}</strong>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Nama Peminjam</span>
                            <strong class="text-gray-900">{{ $peminjaman->user->name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Judul Buku</span>
                            <strong class="text-gray-900">{{ $peminjaman->buku->judul ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Tanggal Pinjam</span>
                            <span class="text-gray-900 font-medium">{{ $peminjaman->tanggal_pinjam }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Tanggal Jatuh Tempo</span>
                            <span class="text-gray-900 font-medium">{{ $peminjaman->tanggal_jatuh_tempo }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Status Keterlambatan</span>
                            @if($hariTerlambat > 0)
                                <span class="font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded">Terlambat {{ $hariTerlambat }} Hari</span>
                            @else
                                <span class="font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">Tepat Waktu</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Fine Calculation Preview -->
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 space-y-3">
                    <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider">Perhitungan Denda Otomatis System</h3>
                    <div class="flex flex-col sm:flex-row items-center justify-between text-xs gap-4">
                        <div>
                            <span class="text-gray-600 block">Denda Keterlambatan Fisik:</span>
                            <strong class="text-lg text-rose-700 font-extrabold">Rp {{ number_format($dendaEstimasi, 0, ',', '.') }}</strong>
                            <span class="text-[11px] text-gray-500 block">({{ $hariTerlambat }} hari × Rp 2.000 / hari)</span>
                        </div>

                        <div class="w-full sm:w-auto">
                            <label class="block font-semibold text-gray-800 mb-1">Kondisi Fisik Buku Saat Dikembalikan</label>
                            <select name="kondisi_buku" class="w-full px-3.5 py-2 bg-white border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-brand-700 focus:outline-none">
                                <option value="baik">Baik (Normal)</option>
                                <option value="rusak">Rusak (Denda Tambahan Rp 50.000)</option>
                                <option value="hilang">Hilang (Denda Ganti Rugi Rp 150.000)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('pustakawan.pengembalian') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-700 text-white font-bold text-xs rounded-lg hover:bg-emerald-800 transition shadow-sm">
                        Konfirmasi Pengembalian Buku
                    </button>
                </div>
            </form>
        @elseif(request('scan_barcode'))
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700">
                Tidak ditemukan transaksi peminjaman aktif untuk barcode <strong>{{ request('scan_barcode') }}</strong>. Pastikan barcode benar atau eksemplar tidak sedang tersedia.
            </div>
        @endif

    </div>

    <!-- HTML5 Live Camera QR Scanner Modal -->
    <div x-show="openScanner" @click.self="stopCameraScanner(); openScanner = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/70 backdrop-blur-xs p-4" x-cloak>
        <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border-2 border-gray-200 text-center relative" @click.stop>
            <button @click="stopCameraScanner(); openScanner = false" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 text-gray-600 font-bold">&times;</button>
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">Kamera Web Scanner HP/Laptop</h3>
            <p class="text-xs text-gray-500">Arahkan kamera ke Barcode / QR Code Stiker Buku Yang Dikembalikan.</p>

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
        if (scannedVal.includes("BARCODE:")) {
            let match = scannedVal.match(/BARCODE:([^|]+)/);
            if (match) scannedVal = match[1];
        }

        const inputBarcode = document.getElementById('inputReturnBarcode');
        if (inputBarcode) {
            inputBarcode.value = scannedVal;
            document.getElementById('formReturnScan').submit();
        }
    }

    function stopCameraScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
    }
</script>
@endsection
