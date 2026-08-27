{{-- Laporan sirkulasi peminjaman dalam format Excel. Lihat App\Services\Laporan\LaporanExcel. --}}
@include('admin.laporan.excel.kepala')
<body>
    <table>
        @include('admin.laporan.excel.kop', [
            'kolom' => 10,
            'judul' => 'LAPORAN REKAPITULASI SIRKULASI PEMINJAMAN &amp; PENGEMBALIAN BUKU',
        ])

        @include('admin.laporan.excel.kpi', ['kartu' => [
            ['label' => 'TOTAL TRANSAKSI SIRKULASI', 'nilai' => $totalTransaksi,  'satuan' => 'Transaksi', 'warna' => 'blue',   'kolom' => 3],
            ['label' => 'SEDANG DIPINJAM',           'nilai' => $totalDipinjam,   'satuan' => 'Transaksi', 'warna' => 'amber',  'kolom' => 2],
            ['label' => 'SUDAH DIKEMBALIKAN',        'nilai' => $totalKembali,    'satuan' => 'Transaksi', 'warna' => 'green',  'kolom' => 2],
            ['label' => 'TOTAL BUKU TERLIBAT',       'nilai' => $totalBukuPinjam, 'satuan' => 'Buku',      'warna' => 'purple', 'kolom' => 3],
        ]])

        @include('admin.laporan.excel.meta', ['kolom' => 10])
    </table>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 32px;">No</th>
                <th style="width: 100px;">Kode Pinjam</th>
                <th style="width: 160px;">Nama Peminjam</th>
                <th style="width: 130px;">Jurusan / NISN</th>
                <th style="width: 240px;">Judul Buku &amp; Lokasi</th>
                <th style="width: 45px;">Jml</th>
                <th style="width: 85px;">Tgl Pinjam</th>
                <th style="width: 95px;">Tgl Kembali</th>
                <th style="width: 85px;">Status</th>
                <th style="width: 110px;">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loanItems as $idx => $loan)
                @php
                    $sudahKembali = $loan->status === 'dikembalikan';
                @endphp
                <tr @class(['row-even' => $idx % 2 === 1])>
                    <td class="text-center font-bold" style="color: #64748b;">{{ $idx + 1 }}</td>
                    <td class="text-center font-bold mso-text" style="font-family: monospace; font-size: 8.5pt;">{{ $loan->kode_peminjaman }}</td>
                    <td class="text-left font-bold" style="color: #0f172a;">{{ $loan->nama_peminjam }}</td>
                    <td class="text-left">{{ $loan->jurusan }}{{ $loan->nomor_induk ? ' (' . $loan->nomor_induk . ')' : '' }}</td>
                    <td class="text-left">
                        <div style="font-weight: bold; color: #0f172a;">{{ $loan->buku->judul ?? '-' }}</div>
                        <div style="font-size: 8pt; color: #881337;">Rak: {{ $loan->buku->rak->kode_rak ?? '-' }}</div>
                    </td>
                    <td class="text-center font-bold mso-num">{{ (int) $loan->jumlah }}</td>
                    <td class="text-center mso-text">{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d/m/Y') }}</td>
                    <td class="text-center mso-text">{{ $loan->waktu_kembali ? \Carbon\Carbon::parse($loan->waktu_kembali)->format('d/m/Y H:i') : '-' }}</td>
                    <td class="{{ $sudahKembali ? 'badge-kembali' : 'badge-dipinjam' }}">{{ $sudahKembali ? 'Dikembalikan' : 'Sedang Dipinjam' }}</td>
                    <td class="text-left">{{ $loan->petugas->name ?? '-' }}</td>
                </tr>
            @endforeach

            <tr class="row-total">
                <td colspan="5" class="text-center font-bold" style="font-size: 9.5pt; color: #0f172a;">
                    TOTAL REKAPITULASI SIRKULASI ({{ $totalTransaksi }} TRANSAKSI)
                </td>
                <td class="text-center font-bold mso-num" style="font-size: 10pt;">{{ $totalBukuPinjam }}</td>
                <td colspan="2" class="text-center font-bold" style="color: #065f46; font-size: 8.5pt;">{{ $totalKembali }} Selesai / {{ $totalDipinjam }} Aktif</td>
                <td colspan="2" class="text-center font-bold" style="color: #475569; font-size: 8.5pt;">Terverifikasi</td>
            </tr>
        </tbody>
    </table>

    @include('admin.laporan.excel.ttd', ['kolom' => 10, 'awal' => 1, 'kiri' => 4, 'tengah' => 1, 'kanan' => 4])
</body>
</html>
