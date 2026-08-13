@extends('layouts.dashboard')

@section('title', 'Denda & Pembayaran')
@section('page_heading', 'Kelola Denda & Kas Pembayaran')

@section('content')
<div class="space-y-6" x-data="{ openDendaModal: false }">

    <!-- Action Bar -->
    <div class="bg-white p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-black text-gray-900">Daftar Tagihan Denda &amp; Konfirmasi Pelunasan</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Kelola denda keterlambatan, kerusakan modul, atau kehilangan buku</p>
        </div>
        <button @click="openDendaModal = true" class="px-4 py-2.5 bg-red-700 hover:bg-red-800 text-white font-extrabold text-xs rounded-xl transition shadow-md hover:shadow-lg transform active:scale-95 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <span>+ Tetapkan Denda Manual</span>
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider font-extrabold">
                        <th class="py-3.5 px-5 font-bold">Anggota / User</th>
                        <th class="py-3.5 px-5 font-bold">Buku Terkait</th>
                        <th class="py-3.5 px-5 font-bold">Alasan Keterlambatan/Rusak</th>
                        <th class="py-3.5 px-5 font-bold">Jumlah Denda</th>
                        <th class="py-3.5 px-5 font-bold">Status Pembayaran</th>
                        <th class="py-3.5 px-5 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    @foreach($dendaList as $denda)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3.5 px-5 font-bold text-gray-900">
                                <span>{{ $denda->user->name ?? '-' }}</span>
                                <span class="block text-[10px] text-gray-400 font-mono">{{ $denda->user->email ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-5 font-semibold text-gray-800">{{ $denda->peminjaman->buku->judul ?? 'Peminjaman Umum' }}</td>
                            <td class="py-3.5 px-5 text-gray-600">{{ $denda->alasan }}</td>
                            <td class="py-3.5 px-5 font-black text-rose-700">Rp {{ number_format($denda->jumlah_denda, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-5">
                                @if($denda->status_pembayaran === 'lunas')
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">LUNAS</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">BELUM LUNAS</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                @if($denda->status_pembayaran === 'belum_lunas')
                                    <form action="{{ route('pustakawan.denda.bayar', $denda->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Konfirmasi penerimaan pembayaran denda ini?')"
                                            class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-lg text-[11px] transition shadow-2xs">
                                            Terima Pembayaran
                                        </button>
                                    </form>
                                @else
                                    <span class="text-emerald-600 text-[11px] font-extrabold flex items-center justify-end gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>Selesai</span></span>
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

    <!-- Modal Form Tetapkan Denda Manual -->
    <div x-show="openDendaModal" @click.self="openDendaModal = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-4 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 space-y-5 shadow-2xl border-2 border-gray-200 transform transition-all my-8">
            <div class="flex items-center justify-between border-b-2 border-gray-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-red-50 border border-red-200 text-red-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Tetapkan Denda Manual</h3>
                        <p class="text-[11px] text-gray-500">Pilih anggota dan masukkan nominal denda</p>
                    </div>
                </div>
                <button @click="openDendaModal = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-900 flex items-center justify-center transition font-bold">&times;</button>
            </div>

            @php
                $userList = \App\Models\User::whereHas('role', function($q) {
                    $q->whereNotIn('name', ['admin', 'pustakawan']);
                })->orderBy('name', 'asc')->get();
            @endphp

            <form action="{{ route('pustakawan.denda.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Pilih Anggota Siswa <span class="text-rose-500">*</span></label>
                    <select name="user_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-600 focus:border-red-600 focus:bg-white focus:outline-none font-bold text-gray-900">
                        <option value="">-- Pilih Nama Siswa --</option>
                        @foreach($userList as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Nominal Denda (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="jumlah_denda" required min="500" step="500" placeholder="Contoh: 5000" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-600 focus:border-red-600 focus:bg-white focus:outline-none font-bold text-gray-900">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Alasan / Keterangan Penalti <span class="text-rose-500">*</span></label>
                    <input type="text" name="alasan" required placeholder="Contoh: Terlambat pengembalian modul 5 hari / Kerusakan cover" class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-600 focus:border-red-600 focus:bg-white focus:outline-none font-medium">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Status Pembayaran Denda <span class="text-rose-500">*</span></label>
                    <select name="status_pembayaran" required class="w-full px-3.5 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-600 focus:border-red-600 focus:bg-white focus:outline-none font-medium">
                        <option value="belum_lunas">Belum Lunas (Tanggungan Siswa)</option>
                        <option value="lunas">Lunas (Langsung Dibayar di Kasir)</option>
                    </select>
                </div>

                <div class="pt-4 border-t-2 border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="openDendaModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-red-700 text-white font-extrabold rounded-xl hover:bg-red-800 transition shadow-md hover:shadow-lg transform active:scale-95">Tetapkan Denda</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
