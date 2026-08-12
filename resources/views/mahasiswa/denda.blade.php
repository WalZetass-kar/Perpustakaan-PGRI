@extends('layouts.dashboard')

@section('title', 'Catatan Denda')
@section('page_heading', 'Catatan Denda')

@section('content')
<div class="space-y-6">

    <!-- Summary Box -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Denda Belum Lunas</span>
            <span class="text-2xl font-extrabold text-brand-700 mt-1 block">Rp {{ number_format($totalActive, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Total Denda Pernah Dibayar</span>
            <span class="text-2xl font-extrabold text-emerald-700 mt-1 block">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Fine List -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900">Rincian Tagihan & Riwayat Pembayaran Denda</h2>
            <p class="text-xs text-gray-500 mt-0.5">Pembayaran denda dilakukan langsung secara tunai/QRIS melalui meja layanan Pustakawan.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-5 font-semibold">Buku Terkait</th>
                        <th class="py-3 px-5 font-semibold">Alasan Denda</th>
                        <th class="py-3 px-5 font-semibold">Jumlah Denda</th>
                        <th class="py-3 px-5 font-semibold">Status Pembayaran</th>
                        <th class="py-3 px-5 font-semibold">Tanggal Dicatat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($fines as $fine)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $fine->peminjaman->buku->judul ?? '-' }}</td>
                            <td class="py-3.5 px-5">{{ $fine->alasan }}</td>
                            <td class="py-3.5 px-5 font-bold text-gray-900">Rp {{ number_format($fine->jumlah_denda, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-5">
                                @if($fine->status_pembayaran === 'lunas')
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Lunas</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-100">Belum Lunas</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-gray-500">{{ $fine->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400">Tidak ada catatan denda. Selamat, Anda anggota yang disiplin!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
