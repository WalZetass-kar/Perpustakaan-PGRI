@extends('layouts.dashboard')

@section('title', 'Request Peminjaman Siswa')
@section('page_heading', 'Request Peminjaman Buku')

@section('content')
<div class="space-y-5" x-data="{ 
    openRejectModal: false, 
    rejectId: null, 
    rejectNama: '',
    rejectJudul: '',
    alasanText: ''
}">

    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-black text-gray-900">Pengajuan Peminjaman Buku Daring (OPAC)</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Daftar permintaan peminjaman mandiri yang diajukan oleh siswa melalui Katalog OPAC</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('admin.peminjaman') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-extrabold rounded-xl transition flex items-center gap-1.5">
                <i class="fa-solid fa-list-check text-gray-500"></i>
                <span>Sirkulasi Aktif</span>
            </a>
            <a href="{{ route('admin.riwayat') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-extrabold rounded-xl transition flex items-center gap-1.5">
                <i class="fa-solid fa-clock-rotate-left text-gray-500"></i>
                <span>Riwayat Sirkulasi</span>
            </a>
        </div>
    </div>

    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0">
            <a href="{{ route('admin.peminjaman.request', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap {{ $status === 'pending' ? 'bg-brand-700 text-white shadow-sm' : 'bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                <span>Menunggu Persetujuan</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $status === 'pending' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-700 font-black' }}">{{ $counts['pending'] }}</span>
            </a>
            <a href="{{ route('admin.peminjaman.request', ['status' => 'dipinjam']) }}" class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap {{ $status === 'dipinjam' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                <span>Disetujui</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $status === 'dipinjam' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700 font-black' }}">{{ $counts['dipinjam'] }}</span>
            </a>
            <a href="{{ route('admin.peminjaman.request', ['status' => 'ditolak']) }}" class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap {{ $status === 'ditolak' ? 'bg-rose-700 text-white shadow-sm' : 'bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                <span>Ditolak</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $status === 'ditolak' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700 font-black' }}">{{ $counts['ditolak'] }}</span>
            </a>
            <a href="{{ route('admin.peminjaman.request', ['status' => 'all']) }}" class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap {{ $status === 'all' ? 'bg-gray-800 text-white shadow-sm' : 'bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                <span>Semua</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] {{ $status === 'all' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700 font-black' }}">{{ $counts['all'] }}</span>
            </a>
        </div>

        <form action="{{ route('admin.peminjaman.request') }}" method="GET" class="relative w-full md:w-80 shrink-0">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari siswa, NISN, WA, buku..." class="w-full pl-9 pr-4 py-2 bg-white border-2 border-gray-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-brand-700 focus:border-brand-700 focus:outline-none transition shadow-2xs">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-xs"></i>
            @if(request('search'))
                <a href="{{ route('admin.peminjaman.request', ['status' => $status]) }}" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 text-xs font-bold">&times;</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase tracking-wider font-extrabold text-[11px]">
                        <th class="py-3.5 px-4">Ref & Tanggal</th>
                        <th class="py-3.5 px-4">Data Pemohon</th>
                        <th class="py-3.5 px-4">Buku & Lokasi</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    @forelse($requestList as $req)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-mono font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded border border-gray-200 text-[10.5px] block w-fit">
                                    {{ $req->kode_peminjaman }}
                                </span>
                                <span class="text-[10px] text-gray-400 font-mono block mt-1">
                                    {{ $req->created_at ? $req->created_at->format('d M Y, H:i') : '-' }} WIB
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-black text-gray-900 text-xs">{{ $req->nama_peminjam }}</p>
                                <div class="flex items-center gap-1.5 text-[10.5px] text-gray-500 font-medium mt-0.5">
                                    <span class="font-bold text-brand-700">{{ $req->jurusan }}</span>
                                    <span>&bull;</span>
                                    <span>NISN: {{ $req->nomor_induk ?: '-' }}</span>
                                </div>
                                @if($req->no_wa)
                                    <p class="text-[10px] text-emerald-700 font-mono font-bold mt-0.5 flex items-center gap-1">
                                        <i class="fa-brands fa-whatsapp text-xs text-emerald-600"></i>
                                        <span>{{ $req->no_wa }}</span>
                                    </p>
                                @endif
                                @if($req->catatan)
                                    <p class="text-[10px] text-gray-500 italic mt-1 bg-amber-50/70 border border-amber-200/60 p-1.5 rounded-lg">
                                        "{{ $req->catatan }}"
                                    </p>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($req->buku)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-12 bg-gray-100 rounded-lg overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center">
                                            @if($req->buku->cover_url)
                                                <img src="{{ $req->buku->cover_url }}" alt="Cover" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-brand-700 text-white font-bold flex items-center justify-center text-xs">
                                                    {{ substr($req->buku->judul, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-gray-900 line-clamp-1 text-xs">{{ $req->buku->judul }}</p>
                                            <div class="flex items-center gap-1.5 text-[10px] text-gray-500 mt-0.5">
                                                <span class="px-1.5 py-0.5 rounded bg-gray-100 font-mono text-gray-800 font-bold">{{ $req->buku->rak->kode_rak ?? '-' }}</span>
                                                <span>&bull;</span>
                                                <span class="text-amber-700 font-bold">{{ $req->buku->laci->nama_laci ?? ($req->buku->rak ? 'Laci 1' : '-') }}</span>
                                            </div>
                                            <p class="text-[10px] text-gray-400 font-bold mt-0.5">
                                                Sisa Stok: <span class="{{ $req->buku->available_quantity > 0 ? 'text-emerald-700' : 'text-rose-600' }}">{{ $req->buku->available_quantity }} / {{ $req->buku->total_quantity }}</span>
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Buku telah dihapus</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($req->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-clock text-amber-600"></i>
                                        <span>Menunggu</span>
                                    </span>
                                @elseif($req->status === 'dipinjam')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                        <span>Disetujui</span>
                                    </span>
                                    @if($req->petugas)
                                        <span class="block text-[9.5px] text-gray-400 mt-0.5">Oleh: {{ $req->petugas->name }}</span>
                                    @endif
                                @elseif($req->status === 'ditolak')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-xmark text-rose-600"></i>
                                        <span>Ditolak</span>
                                    </span>
                                    @if($req->alasan_penolakan)
                                        <p class="text-[9.5px] text-rose-600 mt-1 max-w-xs truncate" title="{{ $req->alasan_penolakan }}">
                                            "{{ $req->alasan_penolakan }}"
                                        </p>
                                    @endif
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-gray-100 text-gray-700 border border-gray-200">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                @if($req->status === 'pending')
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form action="{{ route('admin.peminjaman.request.approve', $req->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(event, 'Setujui Peminjaman?', 'Buku ini akan resmi dicatat dipinjam dan stok fisik otomatis dipotong.')">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-lg text-xs transition shadow-xs flex items-center gap-1">
                                                <i class="fa-solid fa-check"></i>
                                                <span>Setujui</span>
                                            </button>
                                        </form>
                                        <button type="button" @click="rejectId = {{ $req->id }}; rejectNama = '{{ addslashes($req->nama_peminjam) }}'; rejectJudul = '{{ addslashes($req->buku->judul ?? 'Buku') }}'; alasanText = ''; openRejectModal = true" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-lg text-xs transition shadow-xs flex items-center gap-1">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>Tolak</span>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400 font-mono text-[10px]">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center mx-auto mb-2 text-gray-400">
                                    <i class="fa-solid fa-inbox text-xl"></i>
                                </div>
                                <p class="text-xs font-bold text-gray-700">Tidak ada data request peminjaman</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">Pengajuan baru dari siswa melalui OPAC akan muncul di sini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requestList->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                {{ $requestList->links() }}
            </div>
        @endif
    </div>

    <div x-show="openRejectModal" @click.self="openRejectModal = false" class="fixed inset-0 z-[100] !mt-0 flex items-center justify-center bg-gray-950/60 backdrop-blur-sm p-4 overflow-y-auto"          x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div @click.stop class="bg-white rounded-2xl max-w-md w-full shadow-2xl border-2 border-gray-200 overflow-hidden transform transition-all my-auto">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between bg-rose-50/70">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-circle-xmark text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Tolak Pengajuan Peminjaman</h3>
                        <p class="text-[10px] text-gray-500">Konfirmasi pembatalan request peminjaman buku</p>
                    </div>
                </div>
                <button @click="openRejectModal = false" class="w-7 h-7 rounded-full bg-gray-200/70 hover:bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-sm">&times;</button>
            </div>

            <form :action="'{{ url('/admin/peminjaman/request') }}/' + rejectId + '/reject'" method="POST" class="p-5 space-y-4 text-xs">
                @csrf
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 space-y-1">
                    <p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider">Pemohon</p>
                    <p class="text-xs font-black text-gray-900" x-text="rejectNama"></p>
                    <p class="text-[11px] text-brand-700 font-bold line-clamp-1" x-text="rejectJudul"></p>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Alasan Penolakan (Opsional)</label>
                    <textarea name="alasan_penolakan" x-model="alasanText" rows="3" placeholder="Contoh: Buku fisik sedang dalam perbaikan / siswa memiliki pinjaman tertunggak..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-700 focus:bg-white focus:outline-none font-medium"></textarea>
                </div>

                <div class="pt-2 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="openRejectModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-rose-600 text-white font-extrabold rounded-xl hover:bg-rose-700 text-xs flex items-center gap-1.5">
                        <i class="fa-solid fa-ban"></i>
                        <span>Tolak Pengajuan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
