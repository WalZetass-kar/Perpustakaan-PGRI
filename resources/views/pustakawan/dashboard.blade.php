@extends('layouts.dashboard')

@section('title', 'Dashboard Layanan Pustakawan')
@section('page_heading', 'Dashboard Pustakawan')

@section('content')
<div class="space-y-6">
    
    <!-- Quick Actions Banner -->
    <div class="bg-brand-700 text-white rounded-xl p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-extrabold">Modul Sirkulasi Cepat Petugas</h2>
            <p class="text-xs text-brand-100 mt-1">Lakukan peminjaman dan pengembalian buku dengan pemindaian barcode / QR code instan.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('pustakawan.peminjaman') }}" class="px-4 py-2 bg-white text-brand-700 font-bold text-xs rounded-lg hover:bg-brand-50 transition shadow-sm">
                + Peminjaman Cepat
            </a>
            <a href="{{ route('pustakawan.pengembalian') }}" class="px-4 py-2 bg-brand-800 text-white font-bold text-xs rounded-lg hover:bg-brand-900 border border-brand-600 transition">
                &larr; Pengembalian Cepat
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-[11px] font-semibold text-gray-500 uppercase block">Pinjam Hari Ini</span>
            <span class="text-xl font-bold text-gray-900 mt-1 block">{{ $stats['peminjaman_hari_ini'] }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-[11px] font-semibold text-gray-500 uppercase block">Kembali Hari Ini</span>
            <span class="text-xl font-bold text-gray-900 mt-1 block">{{ $stats['pengembalian_hari_ini'] }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-[11px] font-semibold text-gray-500 uppercase block">Terlambat</span>
            <span class="text-xl font-bold text-rose-700 mt-1 block">{{ $stats['buku_terlambat'] }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-[11px] font-semibold text-gray-500 uppercase block">Denda Unpaid</span>
            <span class="text-xl font-bold text-amber-700 mt-1 block">Rp {{ number_format($stats['denda_belum_lunas'], 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-[11px] font-semibold text-gray-500 uppercase block">Total Buku</span>
            <span class="text-xl font-bold text-gray-900 mt-1 block">{{ $stats['total_buku'] }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-[11px] font-semibold text-gray-500 uppercase block">Ready Eksemplar</span>
            <span class="text-xl font-bold text-emerald-700 mt-1 block">{{ $stats['buku_tersedia'] }}</span>
        </div>
    </div>

    <!-- Recent Loans Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Transaksi Sirkulasi Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-5 font-semibold">Kode TRX</th>
                        <th class="py-3 px-5 font-semibold">Nama Anggota</th>
                        <th class="py-3 px-5 font-semibold">Judul Buku</th>
                        <th class="py-3 px-5 font-semibold">Barcode</th>
                        <th class="py-3 px-5 font-semibold">Pinjam</th>
                        <th class="py-3 px-5 font-semibold">Jatuh Tempo</th>
                        <th class="py-3 px-5 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @foreach($recentLoans as $rLoan)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-3.5 px-5 font-mono font-medium text-gray-900">{{ $rLoan->kode_peminjaman }}</td>
                            <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $rLoan->user->name ?? '-' }}</td>
                            <td class="py-3.5 px-5">{{ $rLoan->buku->judul ?? '-' }}</td>
                            <td class="py-3.5 px-5 font-mono text-gray-500">{{ $rLoan->eksemplar->barcode ?? '-' }}</td>
                            <td class="py-3.5 px-5">{{ $rLoan->tanggal_pinjam }}</td>
                            <td class="py-3.5 px-5">{{ $rLoan->tanggal_jatuh_tempo }}</td>
                            <td class="py-3.5 px-5">
                                @if($rLoan->status === 'dipinjam')
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">Dipinjam</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
