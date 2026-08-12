@extends('layouts.dashboard')

@section('title', 'Data Buku')
@section('page_heading', 'Data Buku')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-base font-bold text-gray-900">Koleksi Judul Buku Perpustakaan</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-5 font-semibold">ISBN</th>
                    <th class="py-3 px-5 font-semibold">Judul Buku</th>
                    <th class="py-3 px-5 font-semibold">Penulis</th>
                    <th class="py-3 px-5 font-semibold">Kategori</th>
                    <th class="py-3 px-5 font-semibold">Rak</th>
                    <th class="py-3 px-5 font-semibold">Tersedia / Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @foreach($bukuList as $buku)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-5 font-mono text-gray-900">{{ $buku->isbn }}</td>
                        <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $buku->judul }}</td>
                        <td class="py-3.5 px-5">{{ $buku->penulis->nama ?? '-' }}</td>
                        <td class="py-3.5 px-5">{{ $buku->kategori->nama ?? '-' }}</td>
                        <td class="py-3.5 px-5 font-mono text-gray-600">{{ $buku->rak->kode_rak ?? '-' }}</td>
                        <td class="py-3.5 px-5 font-bold text-emerald-700">{{ $buku->jumlah_tersedia }} / {{ $buku->jumlah_eksemplar }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $bukuList->links() }}
    </div>
</div>
@endsection
