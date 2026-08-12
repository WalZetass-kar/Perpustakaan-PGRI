@extends('layouts.dashboard')

@section('title', 'Eksemplar & Barcode')
@section('page_heading', 'Eksemplar & Barcode Buku')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100">
        <h2 class="text-base font-bold text-gray-900">Daftar Fisik Eksemplar & Barcode Sirkulasi</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-5 font-semibold">Kode Eksemplar</th>
                    <th class="py-3 px-5 font-semibold">Barcode ID</th>
                    <th class="py-3 px-5 font-semibold">Judul Buku</th>
                    <th class="py-3 px-5 font-semibold">Kondisi</th>
                    <th class="py-3 px-5 font-semibold">Rak</th>
                    <th class="py-3 px-5 font-semibold">Status Eksemplar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @foreach($eksemplarList as $ex)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-5 font-mono font-bold text-gray-900">{{ $ex->kode_eksemplar }}</td>
                        <td class="py-3.5 px-5 font-mono text-gray-600 bg-gray-50 px-2 py-1 rounded w-max">{{ $ex->barcode }}</td>
                        <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $ex->buku->judul ?? '-' }}</td>
                        <td class="py-3.5 px-5 uppercase text-[10px] font-bold text-gray-600">{{ $ex->kondisi }}</td>
                        <td class="py-3.5 px-5 font-mono">{{ $ex->rak->kode_rak ?? '-' }}</td>
                        <td class="py-3.5 px-5">
                            @if($ex->status === 'tersedia')
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Tersedia</span>
                            @elseif($ex->status === 'dipinjam')
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">Dipinjam</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-100 uppercase">{{ $ex->status }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $eksemplarList->links() }}
    </div>
</div>
@endsection
