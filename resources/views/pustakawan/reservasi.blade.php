@extends('layouts.dashboard')

@section('title', 'Daftar Reservasi Online')
@section('page_heading', 'Kelola Reservasi & Booking Buku Online')

@section('content')
<div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden space-y-4">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-gray-900">Antrean Booking Buku Mandiri oleh Siswa</h2>
            <p class="text-xs text-gray-500">Klik "Setujui &amp; Serahkan Buku" untuk memproses booking menjadi peminjaman fisik aktif.</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider">
                    <th class="py-3 px-5 font-bold">Kode Reservasi</th>
                    <th class="py-3 px-5 font-bold">Nama Pemohon (Siswa)</th>
                    <th class="py-3 px-5 font-bold">Buku Direservasi</th>
                    <th class="py-3 px-5 font-bold">Tanggal Booking</th>
                    <th class="py-3 px-5 font-bold">Status</th>
                    <th class="py-3 px-5 font-bold text-right">Aksi Pustakawan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse($reservasiList as $res)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-5 font-mono font-extrabold text-brand-700">{{ $res->kode_reservasi }}</td>
                        <td class="py-3.5 px-5 font-bold text-gray-900">{{ $res->user->name ?? '-' }}</td>
                        <td class="py-3.5 px-5 font-medium text-gray-900 max-w-xs truncate">{{ $res->buku->judul ?? '-' }}</td>
                        <td class="py-3.5 px-5 text-gray-500">{{ $res->tanggal_reservasi }}</td>
                        <td class="py-3.5 px-5">
                            @if($res->status === 'menunggu')
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200 uppercase">Menunggu Pengambilan</span>
                            @elseif($res->status === 'selesai')
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">Selesai Dipinjamkan</span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200 uppercase">{{ $res->status }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-5 text-right">
                            @if($res->status === 'menunggu')
                                <form action="{{ route('pustakawan.reservasi.proses', $res->id) }}" method="POST" class="inline" onsubmit="return confirmAction(event, 'Setujui Reservasi?', 'Aktifkan transaksi peminjaman fisik untuk buku ini.', 'Ya, Setujui &amp; Serahkan!')">
                                    @csrf
                                    <button type="submit"
                                        class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs transition shadow-2xs">
                                        Setujui &amp; Serahkan Buku
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-xs font-medium">Proses Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500 font-medium">Belum ada antrean reservasi online yang masuk saat ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $reservasiList->links() }}
    </div>
</div>
@endsection
