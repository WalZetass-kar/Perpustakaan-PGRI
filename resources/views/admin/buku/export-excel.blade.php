{{-- Laporan inventaris buku dalam format Excel. Lihat App\Services\Laporan\LaporanExcel. --}}
@include('admin.laporan.excel.kepala')
<body>
    <table>
        @include('admin.laporan.excel.kop', [
            'kolom' => 9,
            'judul' => 'LAPORAN DATA INVENTARIS BUKU &amp; KOLEKSI PERPUSTAKAAN',
        ])

        @include('admin.laporan.excel.kpi', ['kartu' => [
            ['label' => 'TOTAL JUDUL BUKU',      'nilai' => $totalJudul,     'satuan' => 'Judul',     'warna' => 'blue',   'kolom' => 3],
            ['label' => 'TOTAL EKSEMPLAR FISIK', 'nilai' => $totalEksemplar, 'satuan' => 'Eksemplar', 'warna' => 'purple', 'kolom' => 2],
            ['label' => 'TERSEDIA DI RAK',       'nilai' => $totalTersedia,  'satuan' => 'Eksemplar', 'warna' => 'green',  'kolom' => 2],
            ['label' => 'SEDANG DIPINJAM',       'nilai' => $totalDipinjam,  'satuan' => 'Eksemplar', 'warna' => 'amber',  'kolom' => 2],
        ]])

        @include('admin.laporan.excel.meta', ['kolom' => 9])
    </table>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 280px;">Judul Buku</th>
                <th style="width: 120px;">ISBN</th>
                <th style="width: 160px;">Penulis</th>
                <th style="width: 150px;">Penerbit</th>
                <th style="width: 60px;">Tahun</th>
                <th style="width: 140px;">Kategori</th>
                <th style="width: 90px;">Kelas</th>
                <th style="width: 80px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bukuItems as $idx => $buku)
                <tr @class(['row-even' => $idx % 2 === 1])>
                    <td class="text-center font-bold" style="color: #64748b;">{{ $idx + 1 }}</td>
                    <td class="text-left font-bold" style="color: #0f172a;">{{ $buku->judul }}</td>
                    <td class="text-center mso-text" style="font-family: monospace; font-size: 8.5pt;">{{ $buku->isbn ?? '-' }}</td>
                    <td class="text-left">{{ $buku->penulis->nama ?? '-' }}</td>
                    <td class="text-left">{{ $buku->penerbit->nama ?? '-' }}</td>
                    <td class="text-center mso-text">{{ $buku->tahun_terbit ?? '-' }}</td>
                    <td class="text-left">{{ $buku->kategori->nama ?? 'Umum' }}</td>
                    <td class="text-center">{{ $buku->kelas->label_lengkap ?? '-' }}</td>
                    <td class="text-center font-bold mso-num" style="color: #0f172a;">{{ (int) $buku->total_quantity }}</td>
                </tr>
            @endforeach

            <tr class="row-total">
                <td colspan="8" class="text-center font-bold" style="font-size: 9.5pt; color: #0f172a;">
                    TOTAL REKAPITULASI KOLEKSI BUKU ({{ $totalJudul }} JUDUL)
                </td>
                <td class="text-center font-bold mso-num" style="font-size: 10pt; color: #881337;">{{ $totalEksemplar }}</td>
            </tr>
        </tbody>
    </table>

    @include('admin.laporan.excel.ttd', ['kolom' => 9, 'awal' => 0, 'kiri' => 3, 'tengah' => 3, 'kanan' => 3])
</body>
</html>
