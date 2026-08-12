@extends('layouts.dashboard')

@section('title', 'Peminjaman Saya')
@section('page_heading', 'Peminjaman Saya')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-gray-900">Buku Sedang Dipinjam</h2>
            <p class="text-xs text-gray-500 mt-0.5">Batas maksimal perpanjangan online: {{ $maxPerpanjangan }}x</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-5 font-semibold">Kode Peminjaman</th>
                    <th class="py-3 px-5 font-semibold">Judul Buku</th>
                    <th class="py-3 px-5 font-semibold">Kode Eksemplar</th>
                    <th class="py-3 px-5 font-semibold">Tgl Pinjam</th>
                    <th class="py-3 px-5 font-semibold">Tgl Jatuh Tempo</th>
                    <th class="py-3 px-5 font-semibold">Perpanjangan</th>
                    <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse($loans as $loan)
                    @php
                        $today = \Carbon\Carbon::today();
                        $due = \Carbon\Carbon::parse($loan->tanggal_jatuh_tempo);
                        $diffDays = (int) $today->diffInDays($due, false);
                    @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-5 font-mono font-medium text-gray-900">{{ $loan->kode_peminjaman }}</td>
                        <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $loan->buku->judul ?? '-' }}</td>
                        <td class="py-3.5 px-5 font-mono text-gray-500">{{ $loan->eksemplar->kode_eksemplar ?? '-' }}</td>
                        <td class="py-3.5 px-5">{{ $loan->tanggal_pinjam }}</td>
                        <td class="py-3.5 px-5">
                            <span class="{{ $diffDays < 0 ? 'text-rose-600 font-bold' : 'text-gray-900 font-medium' }}">{{ $loan->tanggal_jatuh_tempo }}</span>
                        </td>
                        <td class="py-3.5 px-5">{{ $loan->jumlah_perpanjangan }} / {{ $maxPerpanjangan }}</td>
                        <td class="py-3.5 px-5 text-right">
                            @if($diffDays >= 0 && $loan->jumlah_perpanjangan < $maxPerpanjangan)
                                <form action="{{ route('mahasiswa.peminjaman.perpanjang', $loan->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-brand-700 text-white font-medium rounded-lg text-xs hover:bg-brand-800 transition">Perpanjang 7 Hari</button>
                                </form>
                            @else
                                <span class="text-gray-400">Tidak Memenuhi Syarat</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-gray-400">Tidak ada data peminjaman aktif saat ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
