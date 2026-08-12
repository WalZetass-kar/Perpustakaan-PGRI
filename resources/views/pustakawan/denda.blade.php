@extends('layouts.dashboard')

@section('title', 'Denda & Pembayaran')
@section('page_heading', 'Kelola Denda & Kas Pembayaran')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100">
        <h2 class="text-base font-bold text-gray-900">Daftar Tagihan Denda & Konfirmasi Pelunasan</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-5 font-semibold">Anggota / User</th>
                    <th class="py-3 px-5 font-semibold">Buku Terkait</th>
                    <th class="py-3 px-5 font-semibold">Alasan Keterlambatan/Rusak</th>
                    <th class="py-3 px-5 font-semibold">Jumlah Denda</th>
                    <th class="py-3 px-5 font-semibold">Status Pembayaran</th>
                    <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @foreach($dendaList as $denda)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $denda->user->name ?? '-' }}</td>
                        <td class="py-3.5 px-5">{{ $denda->peminjaman->buku->judul ?? '-' }}</td>
                        <td class="py-3.5 px-5">{{ $denda->alasan }}</td>
                        <td class="py-3.5 px-5 font-bold text-gray-900">Rp {{ number_format($denda->jumlah_denda, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-5">
                            @if($denda->status_pembayaran === 'lunas')
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Lunas</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-100">Belum Lunas</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-5 text-right">
                            @if($denda->status_pembayaran === 'belum_lunas')
                                <form action="{{ route('pustakawan.denda.bayar', $denda->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Konfirmasi penerimaan pembayaran tunai denda ini?')"
                                        class="px-3 py-1 bg-emerald-700 text-white font-medium rounded-lg text-[11px] hover:bg-emerald-800 transition">
                                        Terima Bayar
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-[11px]">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $dendaList->links() }}
    </div>
</div>
@endsection
