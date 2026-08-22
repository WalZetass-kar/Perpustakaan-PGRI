<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Inventaris Buku - {{ $pengaturan['nama_sekolah'] ?? 'SMK PGRI PEKANBARU' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 12mm;
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

        .signature-section {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            page-break-inside: avoid;
            font-family: 'Times New Roman', Times, serif;
            font-size: 9.5pt;
        }

        .sig-box {
            text-align: center;
            width: 220px;
        }

        .sig-space {
            height: 55px;
        }

        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            display: block;
        }

        .sig-nip {
            font-size: 8.5pt;
            color: #334155;
            display: block;
            margin-top: 1px;
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
            <a href="{{ route('admin.buku') }}" class="btn-action btn-back">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Master Buku</span>
            </a>
            <span style="font-size: 11px; color: #64748b; font-weight: 600;">| Format Dokumen: A4 Portrait</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('admin.buku.export.excel', request()->all()) }}" class="btn-action btn-excel">
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
                <h1>{{ $pengaturan['nama_sekolah'] ?? 'SMK PGRI PEKANBARU' }}</h1>
                <h2>{{ $pengaturan['nama_perpustakaan'] ?? 'UNIT PERPUSTAKAAN & PUSAT SUMBER BELAJAR' }}</h2>
                <p>NPSN: {{ $pengaturan['npsn'] ?? '10404457' }} | Alamat: {{ $pengaturan['alamat'] ?? 'Jl. Bangau No. 16 Sukajadi, Pekanbaru, Riau' }}</p>
                <p>Telepon: {{ $pengaturan['telepon'] ?? '(0761) 23456' }} | Email: {{ $pengaturan['email_perpustakaan'] ?? 'perpustakaan@smkpgri-pku.sch.id' }}</p>
            </div>
        </div>

        <div class="report-title-box">
            <h3>Laporan Rekapitulasi Inventaris Koleksi Buku</h3>
            <span>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB | Oleh Petugas: {{ auth()->user()->name ?? 'Administrator' }}</span>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="label">Total Judul Buku</div>
                <div class="value">{{ number_format($totalJudul, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Total Eksemplar Fisik</div>
                <div class="value">{{ number_format($totalEksemplar, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Eksemplar Tersedia</div>
                <div class="value" style="color: #059669;">{{ number_format($totalTersedia, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Sedang Dipinjam</div>
                <div class="value" style="color: #d97706;">{{ number_format($totalDipinjam, 0, ',', '.') }}</div>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="text-align: left;">Judul Buku</th>
                    <th style="width: 80px;">ISBN</th>
                    <th style="width: 110px; text-align: left;">Penulis</th>
                    <th style="width: 100px; text-align: left;">Penerbit</th>
                    <th style="width: 45px;">Tahun</th>
                    <th style="width: 85px; text-align: left;">Kategori</th>
                    <th style="width: 40px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bukuItems as $idx => $buku)
                    @php
                        $total = (int) $buku->total_quantity;
                    @endphp
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $idx + 1 }}</td>
                        <td style="font-weight: bold; color: #0f172a;">{{ $buku->judul }}</td>
                        <td style="text-align: center; font-family: monospace; font-size: 7.5pt;">{{ $buku->isbn ?? '-' }}</td>
                        <td>{{ $buku->penulis->nama ?? '-' }}</td>
                        <td>{{ $buku->penerbit->nama ?? '-' }}</td>
                        <td style="text-align: center;">{{ $buku->tahun_terbit ?? '-' }}</td>
                        <td>{{ $buku->kategori->nama ?? 'Umum' }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $total }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px; color: #94a3b8;">Tidak ada data buku yang terdaftar di sistem.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="signature-section">
            <div class="sig-box">
                <span>Petugas Administrasi Perpustakaan,</span>
                <div class="sig-space"></div>
                <span class="sig-name">{{ auth()->user()->name ?? 'Petugas Perpustakaan' }}</span>
                <span class="sig-nip">Admin Sirkulasi</span>
            </div>

            <div class="sig-box">
                <span>Pekanbaru, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                <span style="display: block; margin-top: 2px;">Mengetahui,</span>
                <strong style="display: block; margin-top: 2px;">Kepala Perpustakaan</strong>
                <div class="sig-space"></div>
                <span class="sig-name">{{ $pengaturan['kepala_perpustakaan'] ?? 'Dra. Hj. Perpustakaan' }}</span>
                <span class="sig-nip">NIP. {{ $pengaturan['nip_kepala_perpustakaan'] ?? '-' }}</span>
            </div>
        </div>
    </div>

</body>
</html>
