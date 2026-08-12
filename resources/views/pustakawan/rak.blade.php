@extends('layouts.dashboard')

@section('title', 'Data Rak Perpustakaan')
@section('page_heading', 'Rak Perpustakaan')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-base font-bold text-gray-900">Daftar Rak Penyimpanan Buku</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-5 font-semibold">Kode Rak</th>
                    <th class="py-3 px-5 font-semibold">Nama Rak</th>
                    <th class="py-3 px-5 font-semibold">Lokasi Gedung / Lantai</th>
                    <th class="py-3 px-5 font-semibold">Kategori Spesifik</th>
                    <th class="py-3 px-5 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @foreach($rakList as $rk)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-5 font-mono font-bold text-gray-900">{{ $rk->kode_rak }}</td>
                        <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $rk->nama_rak }}</td>
                        <td class="py-3.5 px-5">{{ $rk->lokasi }}</td>
                        <td class="py-3.5 px-5 text-gray-500">{{ $rk->kategori->nama ?? 'Umum' }}</td>
                        <td class="py-3.5 px-5">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase">{{ $rk->status }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
