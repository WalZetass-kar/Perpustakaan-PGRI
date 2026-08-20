@extends('layouts.dashboard')

@section('title', 'Riwayat Sirkulasi Peminjaman')
@section('page_heading', 'Riwayat Transaksi Peminjaman')

@section('content')
<div class="space-y-5">

    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-black text-gray-900">Rekapitulasi Sirkulasi Peminjaman</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Arsip seluruh riwayat transaksi peminjaman dan pengembalian buku</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.peminjaman.export.excel', request()->all()) }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center gap-1.5 shrink-0" title="Export Seluruh Riwayat ke Excel">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('admin.peminjaman.export.pdf', request()->all()) }}" target="_blank" class="px-3.5 py-2 bg-rose-700 hover:bg-rose-800 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center gap-1.5 shrink-0" title="Cetak / Simpan Laporan PDF Resmi">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Cetak / PDF</span>
            </a>
        </div>
    </div>

    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-gray-200 shadow-sm">
        <form action="{{ route('admin.riwayat') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-bold text-gray-700 mb-1">Cari Transaksi</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode, nama siswa, jurusan, judul..."
                       class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:outline-none font-medium">
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Status Transaksi</label>
                <select name="status" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:outline-none font-medium">
                    <option value="">Semua Status</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam (Aktif)</option>
                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Sudah Dikembalikan</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Tanggal Pinjam</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                       class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1.5 focus:ring-brand-700 focus:outline-none font-medium">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-1.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold rounded-xl transition text-xs">
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'tanggal']))
                    <a href="{{ route('admin.riwayat') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider font-extrabold">
                        <th class="py-3 px-4 font-bold">Kode Pinjam</th>
                        <th class="py-3 px-4 font-bold">Nama Peminjam</th>
                        <th class="py-3 px-4 font-bold">Jurusan / Kelas</th>
                        <th class="py-3 px-4 font-bold">Judul Buku</th>
                        <th class="py-3 px-4 font-bold text-center">Jumlah</th>
                        <th class="py-3 px-4 font-bold">Waktu Pinjam</th>
                        <th class="py-3 px-4 font-bold">Waktu Kembali</th>
                        <th class="py-3 px-4 font-bold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    @forelse($riwayatList as $trx)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-900 font-mono font-bold text-[11px] border border-gray-200">
                                    {{ $trx->kode_peminjaman }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-gray-900">{{ $trx->nama_peminjam ?: ($trx->user->name ?? '-') }}</p>
                                @if($trx->nomor_induk)
                                    <p class="text-[10px] text-gray-500 font-mono">NIS/NIP: {{ $trx->nomor_induk }}</p>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-gray-100 text-gray-800 border border-gray-200">
                                    {{ $trx->jurusan ?: '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold text-gray-900 max-w-xs truncate">{{ $trx->buku->judul ?? '-' }}</td>
                            <td class="py-3 px-4 text-center font-bold">{{ $trx->jumlah }} Buku</td>
                            <td class="py-3 px-4 text-gray-700">
                                <span class="font-bold">{{ \Carbon\Carbon::parse($trx->tanggal_pinjam)->format('d M Y') }}</span>
                                <span class="text-[10px] text-gray-400 block">{{ $trx->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="py-3 px-4 text-gray-700">
                                @if($trx->waktu_kembali)
                                    <span class="font-bold text-emerald-700">{{ \Carbon\Carbon::parse($trx->waktu_kembali)->format('d M Y') }}</span>
                                    <span class="text-[10px] text-gray-400 block">{{ \Carbon\Carbon::parse($trx->waktu_kembali)->format('H:i') }} WIB</span>
                                @else
                                    <span class="text-gray-400 font-medium">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($trx->status === 'dikembalikan')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                        Dikembalikan
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200 uppercase">
                                        Dipinjam
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400 font-medium">Tidak ada data riwayat peminjaman yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-gray-100">
            {{ $riwayatList->links() }}
        </div>
    </div>

</div>
@endsection
