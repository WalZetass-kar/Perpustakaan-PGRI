<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Sirkulasi Perpustakaan SMK PGRI Pekanbaru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm;
            }
            body {
                background: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 p-6 font-sans text-gray-900">

    <!-- Action Bar (Hidden on Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white p-4 rounded-2xl border-2 border-gray-200 shadow-sm flex items-center justify-between gap-4">
        <div>
            <h1 class="text-sm font-black text-gray-900 uppercase">Cetak Laporan Resm Rekapitulasi Perpustakaan</h1>
            <p class="text-xs text-gray-500">Laporan fisik siap serah kepada Kepala Sekolah SMK PGRI Pekanbaru.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-5 py-2.5 bg-red-700 hover:bg-red-800 text-white font-extrabold text-xs rounded-xl transition shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 012-2v-4a2 2 0 01-2-2H5a2 2 0 01-2 2v4a2 2 0 012 2h2m2 4h6a2 2 0 012-2v-4a2 2 0 01-2-2H9a2 2 0 01-2 2v4a2 2 0 012 2zm8-12V5a2 2 0 01-2-2H9a2 2 0 01-2 2v4h10z"/></svg>
                <span>Cetak Dokumen (A4)</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- Printable Official Document Container -->
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-200 space-y-6">
        
        <!-- Official Kop Surat Header -->
        <div class="flex items-center justify-between border-b-4 border-double border-gray-900 pb-4">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SMK PGRI" class="w-20 h-20 object-contain shrink-0">
            <div class="text-center flex-1 px-4 space-y-0.5">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700">YAYASAN PERGURUAN PGRI PEKANBARU</h2>
                <h1 class="text-xl font-black uppercase text-gray-900 tracking-tight">SMK PGRI PEKANBARU</h1>
                <p class="text-xs font-semibold text-gray-600">PERPUSTAKAAN SEKOLAH TERPADU &amp; KANTONG INFORMASI DIGITALLY INTEGRATED</p>
                <p class="text-[11px] text-gray-500">Jl. Jend. Sudirman No. 45 Pekanbaru, Riau | Telp: (0761) 123456 | Website: smkpgripekanbaru.sch.id</p>
            </div>
            <div class="w-20 shrink-0 text-right">
                <span class="text-[10px] font-mono font-bold text-gray-400 block">DOK-REP</span>
                <span class="text-[10px] font-mono font-bold text-gray-900 block">{{ date('Y/m') }}</span>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center space-y-1 py-2">
            <h3 class="text-base font-black text-gray-900 uppercase underline decoration-2 underline-offset-4">
                LAPORAN REKAPITULASI {{ strtoupper($type) }} PERPUSTAKAAN
            </h3>
            <p class="text-xs font-bold text-gray-600">Periode: {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</p>
        </div>

        <!-- Summary Stats Grid -->
        <div class="grid grid-cols-3 gap-4 text-xs font-bold p-4 bg-gray-50 rounded-xl border border-gray-200">
            <div>
                <span class="text-gray-500 uppercase block text-[10px]">Jenis Laporan</span>
                <span class="text-gray-900 uppercase font-black text-sm">{{ $type }}</span>
            </div>
            <div>
                <span class="text-gray-500 uppercase block text-[10px]">Total Catatan Transaksi</span>
                <span class="text-gray-900 font-black text-sm">{{ count($reportData) }} Data</span>
            </div>
            <div>
                <span class="text-gray-500 uppercase block text-[10px]">Tanggal Dicetak</span>
                <span class="text-gray-900 font-bold text-sm">{{ date('d F Y') }}</span>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-300 text-gray-800 uppercase font-black">
                        <th class="py-2.5 px-3 border-r border-gray-300 w-10 text-center">No</th>
                        @if($type === 'peminjaman')
                            <th class="py-2.5 px-3 border-r border-gray-300">Kode Pinjam</th>
                            <th class="py-2.5 px-3 border-r border-gray-300">Nama Peminjam</th>
                            <th class="py-2.5 px-3 border-r border-gray-300">Judul Buku</th>
                            <th class="py-2.5 px-3 border-r border-gray-300">Tanggal Pinjam</th>
                            <th class="py-2.5 px-3">Jatuh Tempo</th>
                        @elseif($type === 'pengembalian')
                            <th class="py-2.5 px-3 border-r border-gray-300">Nama Peminjam</th>
                            <th class="py-2.5 px-3 border-r border-gray-300">Judul Buku</th>
                            <th class="py-2.5 px-3 border-r border-gray-300">Tgl Pinjam</th>
                            <th class="py-2.5 px-3 border-r border-gray-300">Tgl Kembali</th>
                            <th class="py-2.5 px-3">Denda Paid</th>
                        @else
                            <th class="py-2.5 px-3 border-r border-gray-300">Nama Peminjam</th>
                            <th class="py-2.5 px-3 border-r border-gray-300">Alasan Keterlambatan</th>
                            <th class="py-2.5 px-3 border-r border-gray-300">Jumlah Denda</th>
                            <th class="py-2.5 px-3">Status Bayar</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($reportData as $index => $row)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-2 px-3 border-r border-gray-200 text-center font-bold">{{ $index + 1 }}</td>
                            @if($type === 'peminjaman')
                                <td class="py-2 px-3 border-r border-gray-200 font-mono font-bold">{{ $row->kode_peminjaman }}</td>
                                <td class="py-2 px-3 border-r border-gray-200 font-semibold">{{ $row->user->name ?? '-' }}</td>
                                <td class="py-2 px-3 border-r border-gray-200">{{ $row->buku->judul ?? '-' }}</td>
                                <td class="py-2 px-3 border-r border-gray-200 font-mono">{{ $row->tanggal_pinjam }}</td>
                                <td class="py-2 px-3 font-mono">{{ $row->tanggal_jatuh_tempo }}</td>
                            @elseif($type === 'pengembalian')
                                <td class="py-2 px-3 border-r border-gray-200 font-semibold">{{ $row->peminjaman->user->name ?? '-' }}</td>
                                <td class="py-2 px-3 border-r border-gray-200">{{ $row->peminjaman->buku->judul ?? '-' }}</td>
                                <td class="py-2 px-3 border-r border-gray-200 font-mono">{{ $row->peminjaman->tanggal_pinjam ?? '-' }}</td>
                                <td class="py-2 px-3 border-r border-gray-200 font-mono">{{ $row->tanggal_kembali }}</td>
                                <td class="py-2 px-3 font-mono font-bold">Rp {{ number_format($row->denda_terlambat + $row->denda_kondisi, 0, ',', '.') }}</td>
                            @else
                                <td class="py-2 px-3 border-r border-gray-200 font-semibold">{{ $row->user->name ?? '-' }}</td>
                                <td class="py-2 px-3 border-r border-gray-200 capitalize">{{ $row->alasan }}</td>
                                <td class="py-2 px-3 border-r border-gray-200 font-mono font-bold">Rp {{ number_format($row->jumlah_denda, 0, ',', '.') }}</td>
                                <td class="py-2 px-3 font-bold uppercase">{{ $row->status_pembayaran }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500 font-medium">Tidak ada data sirkulasi untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Official Signatures Block -->
        <div class="pt-8 grid grid-cols-2 gap-8 text-center text-xs font-medium">
            <div class="space-y-16">
                <div>
                    <p class="font-bold text-gray-700">Mengetahui,</p>
                    <p class="font-black text-gray-900 uppercase">Kepala Perpustakaan SMK PGRI</p>
                </div>
                <div>
                    <p class="font-black text-gray-900 underline decoration-1">Nurbaiti, S.Pd., M.IP</p>
                    <p class="text-[10px] text-gray-500 font-mono">NIP. 19850412 201001 2 004</p>
                </div>
            </div>

            <div class="space-y-16">
                <div>
                    <p class="font-bold text-gray-700">Pekanbaru, {{ date('d F Y') }}</p>
                    <p class="font-black text-gray-900 uppercase">Kepala Sekolah SMK PGRI Pekanbaru</p>
                </div>
                <div>
                    <p class="font-black text-gray-900 underline decoration-1">Drs. H. Mohamad Syafi'i, M.Pd</p>
                    <p class="text-[10px] text-gray-500 font-mono">NIP. 19680815 199403 1 008</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
