{{-- Gaya bersama seluruh laporan Excel. Excel hanya mengenali sebagian kecil
     CSS, jadi ukuran ditulis dalam pt dan warna sebagai hex penuh. --}}
<style>
    body { font-family: "Segoe UI", Calibri, Arial, sans-serif; font-size: 10pt; color: #0f172a; margin: 0; padding: 0; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-left { text-align: left; }
    .font-bold { font-weight: bold; }

    .banner-top { background-color: #881337; color: #ffffff; font-size: 15pt; font-weight: bold; text-align: center; height: 38px; vertical-align: middle; border: 2pt solid #4c0519; }
    .banner-sub { background-color: #9f1239; color: #ffe4e6; font-size: 9.5pt; font-weight: bold; text-align: center; height: 22px; vertical-align: middle; }
    .banner-ribbon { background-color: #b45309; height: 4px; }
    .banner-title { background-color: #f8fafc; color: #0f172a; font-size: 12.5pt; font-weight: bold; text-align: center; height: 32px; vertical-align: middle; border-bottom: 2pt solid #881337; }

    .kpi-head-blue { background-color: #eff6ff; color: #1e40af; border: 1pt solid #93c5fd; font-size: 8pt; font-weight: bold; text-align: center; height: 20px; }
    .kpi-val-blue { background-color: #ffffff; color: #1e3a8a; border: 1pt solid #93c5fd; font-size: 13pt; font-weight: bold; text-align: center; height: 28px; }

    .kpi-head-purple { background-color: #f5f3ff; color: #5b21b6; border: 1pt solid #c4b5fd; font-size: 8pt; font-weight: bold; text-align: center; height: 20px; }
    .kpi-val-purple { background-color: #ffffff; color: #4c1d95; border: 1pt solid #c4b5fd; font-size: 13pt; font-weight: bold; text-align: center; height: 28px; }

    .kpi-head-green { background-color: #ecfdf5; color: #065f46; border: 1pt solid #a7f3d0; font-size: 8pt; font-weight: bold; text-align: center; height: 20px; }
    .kpi-val-green { background-color: #f0fdf4; color: #047857; border: 1pt solid #a7f3d0; font-size: 13pt; font-weight: bold; text-align: center; height: 28px; }

    .kpi-head-amber { background-color: #fffbeb; color: #92400e; border: 1pt solid #fde68a; font-size: 8pt; font-weight: bold; text-align: center; height: 20px; }
    .kpi-val-amber { background-color: #fefce8; color: #b45309; border: 1pt solid #fde68a; font-size: 13pt; font-weight: bold; text-align: center; height: 28px; }

    .meta-strip { background-color: #f1f5f9; color: #475569; font-size: 8.5pt; padding: 6px 10px; border: 0.5pt solid #cbd5e1; height: 24px; vertical-align: middle; }

    .table-data { border-collapse: collapse; width: 100%; }
    .table-data th { background-color: #881337; color: #ffffff; font-weight: bold; text-align: center; border: 1.5pt solid #4c0519; padding: 8px 5px; font-size: 9pt; height: 28px; vertical-align: middle; text-transform: uppercase; }
    .table-data td { border: 0.5pt solid #cbd5e1; padding: 6px 5px; font-size: 9pt; vertical-align: middle; }
    .row-even { background-color: #f8fafc; }

    .badge-kembali { background-color: #ecfdf5; color: #065f46; font-weight: bold; border: 1pt solid #a7f3d0; text-align: center; }
    .badge-dipinjam { background-color: #fffbeb; color: #92400e; font-weight: bold; border: 1pt solid #fde68a; text-align: center; }

    .row-total { background-color: #e2e8f0; font-weight: bold; border-top: 2pt solid #881337; border-bottom: 2pt solid #881337; height: 26px; vertical-align: middle; }

    .mso-text { mso-number-format:"\@"; }
    .mso-num { mso-number-format:"\#\,\#\#0"; }
</style>
