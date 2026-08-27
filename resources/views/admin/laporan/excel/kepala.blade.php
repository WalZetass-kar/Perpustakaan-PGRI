{{-- Pembuka dokumen Excel: pengaturan lembar kerja + gaya bersama.
     $blokLembarKerja disiapkan App\Http\Laporan\LaporanExcel. --}}
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    {!! $blokLembarKerja !!}
    @include('admin.laporan.excel.gaya')
</head>
