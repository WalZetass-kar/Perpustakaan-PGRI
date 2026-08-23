<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Sirkulasi Peminjaman - {{ $pengaturan['nama_sekolah'] ?? 'Perpustakaan Sekolah' }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 28mm 15mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #1e293b;
            background-color: #f1f5f9;
            margin: 0;
            padding: 24px;
            font-size: 9.5pt;
            line-height: 1.35;
        }

        .no-print-bar {
            position: sticky;
            top: 16px;
            z-index: 1000;
            max-width: 820px;
            margin: 0 auto 20px auto;
            background: #ffffff;
            border: 2px solid #e2e8f0;
            padding: 12px 20px;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-print {
            background-color: #881337;
            color: #ffffff;
        }
        .btn-print:hover {
            background-color: #9f1239;
        }

        .btn-excel {
            background-color: #059669;
            color: #ffffff;
        }
        .btn-excel:hover {
            background-color: #047857;
        }

        .btn-back {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-back:hover {
            background-color: #e2e8f0;
        }

        .paper-page {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            padding: 28px 32px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .header-kop {
            text-align: center;
            border-bottom: 3px double #0f172a;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .kop-text h1 {
            font-size: 14pt;
            font-weight: 900;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }

        .kop-text h2 {
            font-size: 11pt;
            font-weight: 700;
            margin: 2px 0 0 0;
            color: #881337;
            text-transform: uppercase;
        }

        .kop-text p {
            font-size: 8pt;
            margin: 2px 0 0 0;
            color: #475569;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .report-title-box {
            text-align: center;
            margin-bottom: 14px;
        }

        .report-title-box h3 {
            font-size: 12pt;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
            text-decoration: underline;
        }

        .report-title-box span {
            font-size: 8.5pt;
            color: #64748b;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: block;
            margin-top: 3px;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 14px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .stat-card {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            border-radius: 8px;
            padding: 6px 8px;
            text-align: center;
        }

        .stat-card .label {
            font-size: 7.5pt;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .value {
            font-size: 13pt;
            font-weight: 900;
            color: #881337;
            margin-top: 1px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        table.data-table th {
            background-color: #881337 !important;
            color: #ffffff !important;
            border: 1px solid #6b0c2a;
            padding: 5px 4px;
            font-weight: 800;
            text-align: center;
            font-size: 7.5pt;
            text-transform: uppercase;
        }

        table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
            vertical-align: middle;
            color: #1e293b;
        }

        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .badge-status {
            display: inline-block;
            padding: 1.5px 5px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: 800;
            text-transform: uppercase;
        }
        .badge-dipinjam {
            background-color: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .badge-kembali {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .signature-section {
            margin-top: 36px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            page-break-inside: avoid;
            font-family: 'Times New Roman', Times, serif;
            font-size: 9.5pt;
        }

        .sig-col {
            text-align: center;
            width: 260px;
        }

        .sig-header {
            height: 54px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            line-height: 1.35;
        }

        .sig-space {
            height: 60px;
        }

        .sig-footer {
            line-height: 1.3;
        }

        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            display: block;
            font-size: 9.5pt;
        }

        .sig-nip {
            font-size: 8.5pt;
            color: #334155;
            display: block;
            margin-top: 2px;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .paper-page {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            table.data-table tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('admin.peminjaman') }}" class="btn-action btn-back">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Sirkulasi</span>
            </a>
            <span style="font-size: 11px; color: #64748b; font-weight: 600;">| Format Dokumen: A4 Portrait</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('admin.peminjaman.export.excel', request()->all()) }}" class="btn-action btn-excel">
                <i class="fa-solid fa-file-excel"></i>
                <span>Download Excel</span>
            </a>
            <button type="button" onclick="window.print()" class="btn-action btn-print">
                <i class="fa-solid fa-print"></i>
                <span>Cetak / PDF</span>
            </button>
        </div>
    </div>

    <div class="paper-page">
        <div class="header-kop">
            <div class="kop-text">
                <h1>{{ $pengaturan['nama_sekolah'] ?? 'PERPUSTAKAAN SEKOLAH' }}</h1>
                <h2>{{ $pengaturan['nama_perpustakaan'] ?? 'UNIT PERPUSTAKAAN & PUSAT SUMBER BELAJAR' }}</h2>
                <p>NPSN: {{ $pengaturan['npsn'] ?? '-' }} | Alamat: {{ $pengaturan['alamat'] ?? 'Gedung Perpustakaan Sekolah' }}</p>
                <p>Telepon: {{ $pengaturan['telepon'] ?? '-' }} | Email: {{ $pengaturan['email_perpustakaan'] ?? 'perpustakaan@sekolah.sch.id' }}</p>
            </div>
        </div>

        <div class="report-title-box">
            <h3>Laporan Rekapitulasi Sirkulasi &amp; Peminjaman Buku</h3>
            <span>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB | Oleh Petugas: {{ auth()->user()->name ?? 'Administrator' }}</span>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="label">Total Transaksi</div>
                <div class="value">{{ number_format($totalTransaksi, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Sedang Dipinjam</div>
                <div class="value" style="color: #d97706;">{{ number_format($totalDipinjam, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Sudah Kembali</div>
                <div class="value" style="color: #059669;">{{ number_format($totalKembali, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Total Buku Terlibat</div>
                <div class="value" style="color: #4338ca;">{{ number_format($totalBukuPinjam, 0, ',', '.') }}</div>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 70px;">Kode</th>
                    <th style="text-align: left;">Nama Peminjam / Jurusan</th>
                    <th style="text-align: left;">Judul Buku &amp; Lokasi</th>
                    <th style="width: 30px;">Jml</th>
                    <th style="width: 70px;">Tgl Pinjam</th>
                    <th style="width: 75px;">Tgl Kembali</th>
                    <th style="width: 65px;">Status</th>
                    <th style="width: 75px; text-align: left;">Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loanItems as $idx => $loan)
                    @php
                        $isReturned = ($loan->status === 'dikembalikan');
                        $rakInfo = $loan->buku && $loan->buku->rak ? $loan->buku->rak->kode_rak : '-';
                    @endphp
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $idx + 1 }}</td>
                        <td style="text-align: center; font-family: monospace; font-size: 7.5pt; font-weight: bold;">{{ $loan->kode_peminjaman }}</td>
                        <td>
                            <div style="font-weight: bold; color: #0f172a;">{{ $loan->nama_peminjam }}</div>
                            <div style="font-size: 7.5pt; color: #64748b;">{{ $loan->jurusan }} {{ $loan->nomor_induk ? '• NISN: ' . $loan->nomor_induk : '' }}</div>
                        </td>
                        <td>
                            <div style="font-weight: bold; color: #0f172a;">{{ $loan->buku->judul ?? '-' }}</div>
                            <div style="font-size: 7.5pt; color: #881337;">Rak: {{ $rakInfo }}</div>
                        </td>
                        <td style="text-align: center; font-weight: bold;">{{ $loan->jumlah }}</td>
                        <td style="text-align: center;">
                            <div>{{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d/m/Y') }}</div>
                        </td>
                        <td style="text-align: center;">
                            @if($loan->waktu_kembali)
                                <div>{{ \Carbon\Carbon::parse($loan->waktu_kembali)->format('d/m/Y') }}</div>
                                <div style="font-size: 7pt; color: #64748b;">{{ \Carbon\Carbon::parse($loan->waktu_kembali)->format('H:i') }} WIB</div>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($isReturned)
                                <span class="badge-status badge-kembali">Kembali</span>
                            @else
                                <span class="badge-status badge-dipinjam">Dipinjam</span>
                            @endif
                        </td>
                        <td>{{ $loan->petugas->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 20px; color: #94a3b8;">Tidak ada data sirkulasi peminjaman yang tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="signature-section">
            <div class="sig-col">
                <div class="sig-header">
                    <div>Petugas Administrasi Perpustakaan,</div>
                </div>
                <div class="sig-space"></div>
                <div class="sig-footer">
                    <span class="sig-name">{{ auth()->user()->name ?? 'Administrator Sekolah' }}</span>
                    <span class="sig-nip">Admin Sirkulasi</span>
                </div>
            </div>

            <div class="sig-col">
                <div class="sig-header">
                    <div>{{ $pengaturan['kota'] ?? 'Pekanbaru' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                    <div>Mengetahui,</div>
                    <div style="font-weight: bold;">Kepala Perpustakaan</div>
                </div>
                <div class="sig-space"></div>
                <div class="sig-footer">
                    <span class="sig-name">{{ $pengaturan['kepala_perpustakaan'] ?? 'Dra. Hj. Nurhayati, M.Pd' }}</span>
                    <span class="sig-nip">NIP. {{ $pengaturan['nip_kepala_perpustakaan'] ?? '19750812 200212 2 003' }}</span>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
