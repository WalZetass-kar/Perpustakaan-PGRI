@extends('layouts.dashboard')

@section('title', 'Pengembalian Buku Cepat')
@section('page_heading', 'Pengembalian Cepat (Otomasasi Denda)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">
        
        <div class="border-b border-gray-100 pb-4">
            <h2 class="text-base font-bold text-gray-900">Form Pengembalian Buku & Hitung Denda</h2>
            <p class="text-xs text-gray-500">Scan Barcode → Cari Transaksi Active → Hitung Terlambat & Denda Otomatis → Konfirmasi</p>
        </div>

        <!-- Scan Barcode Search -->
        <form action="{{ route('pustakawan.pengembalian') }}" method="GET" class="space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-200">
            <label class="block text-xs font-bold text-gray-900">Scan / Input Barcode Eksemplar Buku Yang Dikembalikan</label>
            <div class="flex gap-2">
                <input type="text" name="scan_barcode" value="{{ request('scan_barcode') }}" placeholder="Contoh: BC882001" required autofocus
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

</div>
@endsection
