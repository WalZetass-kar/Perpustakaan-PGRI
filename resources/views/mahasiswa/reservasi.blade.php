@extends('layouts.dashboard')

@section('title', 'Reservasi Antrean Buku')
@section('page_heading', 'Reservasi Antrean Buku')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100">
        <h2 class="text-base font-bold text-gray-900">Daftar Antrean Reservasi Saya</h2>
        <p class="text-xs text-gray-500 mt-0.5">Reservasi akan diproses otomatis saat buku fisik dikembalikan oleh peminjam sebelumnya.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-5 font-semibold">Kode Reservasi</th>
                    <th class="py-3 px-5 font-semibold">Judul Buku</th>
                    <th class="py-3 px-5 font-semibold">Tanggal Reservasi</th>
                    <th class="py-3 px-5 font-semibold">Posisi Antrean</th>
                    <th class="py-3 px-5 font-semibold">Status</th>
                    <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse($reservations as $res)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-5 font-mono font-medium text-gray-900">{{ $res->kode_reservasi }}</td>
                        <td class="py-3.5 px-5 font-semibold text-gray-900">{{ $res->buku->judul ?? '-' }}</td>
                        <td class="py-3.5 px-5">{{ $res->tanggal_reservasi }}</td>
                        <td class="py-3.5 px-5 font-bold text-gray-900">Posisi #{{ $res->posisi_antrean }}</td>
                        <td class="py-3.5 px-5">
                            @if($res->status === 'menunggu')
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-100">Menunggu Buku</span>
                            @elseif($res->status === 'tersedia')
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Siap Diambil</span>
                            @elseif($res->status === 'selesai')
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">Selesai</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">Dibatalkan</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-5 text-right">
                            @if(in_array($res->status, ['menunggu', 'tersedia']))
                                <form action="{{ route('mahasiswa.reservasi.batalkan', $res->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Yakin ingin membatalkan antrean reservasi ini?')" 
                                        class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200 font-medium rounded-lg text-xs hover:bg-rose-100 transition">
                                        Batalkan
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-[11px]">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">Anda tidak memiliki daftar reservasi aktif saat ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
