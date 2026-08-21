@extends('layouts.dashboard')

@section('title', 'Riwayat Sirkulasi Peminjaman')
@section('page_heading', 'Riwayat Transaksi Peminjaman')

@section('content')
<div class="space-y-5">

    {{-- Satu kartu gabungan — panah kiri (buka search & filter), Export Excel/PDF kanan --}}
    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-gray-200 shadow-sm"
         x-data="{ toolOpen: {{ request()->hasAny(['search', 'status', 'tanggal']) ? 'true' : 'false' }} }">
        <div class="flex items-center justify-between gap-2">
            <button type="button" @click="toolOpen = !toolOpen"
                    class="relative w-9 h-9 shrink-0 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 flex items-center justify-center transition"
                    title="Tampilkan/Sembunyikan Pencarian & Filter" aria-label="Toggle Pencarian & Filter">
                <svg class="w-4 h-4 transition-transform duration-200" :class="toolOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                @if(request()->hasAny(['search', 'status', 'tanggal']))
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-brand-700 rounded-full border-2 border-white"></span>
                @endif
            </button>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.peminjaman.export.excel', request()->all()) }}" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center gap-1.5 shrink-0" title="Export Seluruh Riwayat ke Excel">
                    <i class="fa-solid fa-file-excel"></i>
                    <span>Excel</span>
                </a>
                <a href="{{ route('admin.peminjaman.export.pdf', request()->all()) }}" target="_blank" class="px-3 py-2 bg-rose-700 hover:bg-rose-800 text-white text-xs font-extrabold rounded-xl transition duration-200 shadow-sm flex items-center gap-1.5 shrink-0" title="Cetak / Simpan Laporan PDF Resmi">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>PDF</span>
                </a>
            </div>
        </div>

        <form action="{{ route('admin.riwayat') }}" method="GET" x-show="toolOpen" x-cloak
              x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="opacity-0 -translate-y-1"
              x-transition:enter-end="opacity-100 translate-y-0"
              x-transition:leave="transition ease-in duration-150"
              x-transition:leave-start="opacity-100 translate-y-0"
              x-transition:leave-end="opacity-0 -translate-y-1"
              class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs mt-3 pt-3 border-t border-gray-100">
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
            <div class="flex items-center sm:items-end gap-2">
                <button type="submit" class="w-full py-1.5 bg-brand-700 hover:bg-brand-800 text-white font-extrabold rounded-xl transition text-xs">
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'tanggal']))
                    <a href="{{ route('admin.riwayat') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs shrink-0">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="hidden lg:block overflow-x-auto">
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

        <div class="lg:hidden divide-y divide-gray-100">
            @forelse($riwayatList as $trx)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2">
                        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-900 font-mono font-bold text-[11px] border border-gray-200">
                            {{ $trx->kode_peminjaman }}
                        </span>
                        @if($trx->status === 'dikembalikan')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase shrink-0">
                                Dikembalikan
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200 uppercase shrink-0">
                                Dipinjam
                            </span>
                        @endif
                    </div>

                    <div class="mt-2.5">
                        <p class="font-bold text-gray-900 text-xs">{{ $trx->nama_peminjam ?: ($trx->user->name ?? '-') }}</p>
                        <div class="flex items-center gap-1.5 text-[10.5px] text-gray-500 font-medium mt-0.5">
                            <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-800 border border-gray-200 font-bold">{{ $trx->jurusan ?: '-' }}</span>
                            @if($trx->nomor_induk)
                                <span>&bull;</span>
                                <span class="font-mono">NIS: {{ $trx->nomor_induk }}</span>
                            @endif
                        </div>
                    </div>

                    <p class="flex items-start gap-1.5 text-[11px] text-gray-700 font-bold mt-2">
                        <i class="fa-solid fa-book text-gray-300 text-[10px] mt-0.5 shrink-0"></i>
                        <span class="line-clamp-2">{{ $trx->buku->judul ?? '-' }}</span>
                        <span class="shrink-0 px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 font-mono text-[10px] ml-auto">{{ $trx->jumlah }}x</span>
                    </p>

                    <div class="flex items-center gap-3 mt-2.5 pt-2.5 border-t border-gray-100 text-[10px] text-gray-400 font-mono">
                        <span class="flex items-center gap-1">
                            <i class="fa-regular fa-clock text-gray-300"></i>
                            {{ \Carbon\Carbon::parse($trx->tanggal_pinjam)->format('d M Y') }}, {{ $trx->created_at->format('H:i') }}
                        </span>
                        @if($trx->waktu_kembali)
                            <span class="flex items-center gap-1 text-emerald-700">
                                <i class="fa-solid fa-arrow-rotate-left text-emerald-400"></i>
                                {{ \Carbon\Carbon::parse($trx->waktu_kembali)->format('d M Y, H:i') }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-gray-400 font-medium text-xs px-4">Tidak ada data riwayat peminjaman yang cocok.</div>
            @endforelse
        </div>

        <div class="p-3 border-t border-gray-100">
            {{ $riwayatList->links() }}
        </div>
    </div>

</div>
@endsection
