@extends('layouts.dashboard')

@section('title', 'Riwayat Peminjaman')
@section('page_heading', 'Riwayat Peminjaman')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100">
        <h2 class="text-base font-bold text-gray-900">Arsip Transaksi Peminjaman Buku</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-5 font-semibold">Kode Transaksi</th>
                    <th class="py-3 px-5 font-semibold">Judul Buku</th>
                    <th class="py-3 px-5 font-semibold">Tgl Pinjam</th>
                    <th class="py-3 px-5 font-semibold">Jatuh Tempo</th>
                    <th class="py-3 px-5 font-semibold">Tgl Dikembalikan</th>
                    <th class="py-3 px-5 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse($history as $item)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-5 font-mono font-medium text-gray-900">{{ $item->kode_peminjaman }}</td>
                        <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $item->buku->judul ?? '-' }}</td>
                        <td class="py-3.5 px-5">{{ $item->tanggal_pinjam }}</td>
                        <td class="py-3.5 px-5">{{ $item->tanggal_jatuh_tempo }}</td>
                        <td class="py-3.5 px-5">{{ $item->pengembalian->tanggal_kembali ?? '-' }}</td>
                        <td class="py-3.5 px-5">
                            @if($item->status === 'dikembalikan')
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Dikembalikan</span>
                            @elseif($item->status === 'dipinjam')
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">Dipinjam</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-100">Terlambat</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">Belum ada riwayat transaksi peminjaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $history->links() }}
    </div>
</div>
@endsection
