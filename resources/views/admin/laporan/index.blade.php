@extends('layouts.dashboard')

@section('title', 'Laporan Perpustakaan')
@section('page_heading', 'Laporan & Analytics System')

@section('content')
<div class="space-y-6">

    <!-- Filter Bar -->
    <form action="{{ route('admin.laporan') }}" method="GET" class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col sm:flex-row items-end gap-4 text-xs">
        <div class="w-full sm:w-auto flex-1">
            <label class="block font-semibold text-gray-700 mb-1">Jenis Laporan</label>
            <select name="type" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-700">
                <option value="peminjaman" {{ $type === 'peminjaman' ? 'selected' : '' }}>Laporan Peminjaman Buku</option>
                <option value="pengembalian" {{ $type === 'pengembalian' ? 'selected' : '' }}>Laporan Pengembalian Buku</option>
                <option value="denda" {{ $type === 'denda' ? 'selected' : '' }}>Laporan Denda & Sanksi</option>
            </select>
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg">
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="px-5 py-2.5 bg-brand-700 text-white font-bold rounded-xl hover:bg-brand-800 transition">Filter Laporan</button>
            <a href="{{ route('admin.laporan.cetak', ['type' => $type, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="px-4 py-2.5 bg-amber-400 hover:bg-amber-300 text-brand-950 font-extrabold rounded-xl transition shadow-2xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Laporan Resmi (A4)</span>
            </a>
        </div>
    </form>

    <!-- Report Output Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden p-6 space-y-4">
        <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Hasil Laporan {{ ucfirst($type) }} Periode {{ $startDate }} s/d {{ $endDate }}</h3>
            <span class="text-xs text-gray-500">Total Record: {{ count($reportData) }} Data</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                        @if($type === 'peminjaman')
                            <th class="py-3 px-4 font-semibold">Kode Peminjaman</th>
                            <th class="py-3 px-4 font-semibold">Peminjam</th>
                            <th class="py-3 px-4 font-semibold">Buku</th>
                            <th class="py-3 px-4 font-semibold">Tgl Pinjam</th>
                            <th class="py-3 px-4 font-semibold">Jatuh Tempo</th>
                            <th class="py-3 px-4 font-semibold">Status</th>
                        @elseif($type === 'pengembalian')
                            <th class="py-3 px-4 font-semibold">Kode Transaksi</th>
                            <th class="py-3 px-4 font-semibold">Peminjam</th>
                            <th class="py-3 px-4 font-semibold">Buku</th>
                            <th class="py-3 px-4 font-semibold">Tgl Kembali</th>
                            <th class="py-3 px-4 font-semibold">Terlambat</th>
                            <th class="py-3 px-4 font-semibold">Total Denda</th>
                        @else
                            <th class="py-3 px-4 font-semibold">Nama Anggota</th>
                            <th class="py-3 px-4 font-semibold">Buku Terkait</th>
                            <th class="py-3 px-4 font-semibold">Alasan</th>
                            <th class="py-3 px-4 font-semibold">Nominal Denda</th>
                            <th class="py-3 px-4 font-semibold">Status Pembayaran</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($reportData as $row)
                        <tr class="hover:bg-gray-50/50">
                            @if($type === 'peminjaman')
                                <td class="py-3 px-4 font-mono font-bold text-gray-900">{{ $row->kode_peminjaman }}</td>
                                <td class="py-3 px-4 font-semibold text-gray-900">{{ $row->user->name ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $row->buku->judul ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $row->tanggal_pinjam }}</td>
                                <td class="py-3 px-4">{{ $row->tanggal_jatuh_tempo }}</td>
                                <td class="py-3 px-4 uppercase font-bold text-brand-700">{{ $row->status }}</td>
                            @elseif($type === 'pengembalian')
                                <td class="py-3 px-4 font-mono font-bold text-gray-900">{{ $row->peminjaman->kode_peminjaman ?? '-' }}</td>
                                <td class="py-3 px-4 font-semibold text-gray-900">{{ $row->peminjaman->user->name ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $row->peminjaman->buku->judul ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $row->tanggal_kembali }}</td>
                                <td class="py-3 px-4 font-bold text-rose-700">{{ $row->hari_keterlambatan }} Hari</td>
                                <td class="py-3 px-4 font-bold text-gray-900">Rp {{ number_format($row->total_denda, 0, ',', '.') }}</td>
                            @else
                                <td class="py-3 px-4 font-semibold text-gray-900">{{ $row->user->name ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $row->peminjaman->buku->judul ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $row->alasan }}</td>
                                <td class="py-3 px-4 font-bold text-rose-700">Rp {{ number_format($row->jumlah_denda, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 uppercase font-bold text-emerald-700">{{ $row->status_pembayaran }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">Tidak ada data untuk periode tanggal yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
