@extends('layouts.dashboard')

@section('title', 'Daftar Reservasi')
@section('page_heading', 'Daftar Reservasi Antrean Buku')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100">
        <h2 class="text-base font-bold text-gray-900">Kelola Antrean Reservasi Anggota</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-5 font-semibold">Kode Reservasi</th>
                    <th class="py-3 px-5 font-semibold">Nama Pemohon</th>
                    <th class="py-3 px-5 font-semibold">Buku Yang Direservasi</th>
                    <th class="py-3 px-5 font-semibold">Tanggal</th>
                    <th class="py-3 px-5 font-semibold">Antrean</th>
                    <th class="py-3 px-5 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @foreach($reservasiList as $res)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-5 font-mono font-bold text-gray-900">{{ $res->kode_reservasi }}</td>
                        <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $res->user->name ?? '-' }}</td>
                        <td class="py-3.5 px-5">{{ $res->buku->judul ?? '-' }}</td>
                        <td class="py-3.5 px-5">{{ $res->tanggal_reservasi }}</td>
                        <td class="py-3.5 px-5 font-bold text-gray-900">Posisi #{{ $res->posisi_antrean }}</td>
                        <td class="py-3.5 px-5">
                            @if($res->status === 'menunggu')
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-100">Menunggu</span>
                            @elseif($res->status === 'tersedia')
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Siap Diambil</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600 border border-gray-200 uppercase">{{ $res->status }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $reservasiList->links() }}
    </div>
</div>
@endsection
