<?php

namespace App\Http\Laporan;

use Illuminate\Http\Response;

/**
 * Pembungkus respons unduhan laporan Excel.
 *
 * Berkas yang diunduh sebenarnya bukan .xlsx sungguhan, melainkan tabel HTML
 * ber-CSS yang dibuka Excel sebagai lembar kerja — cara ini dipilih supaya
 * sistem tidak perlu memasang pustaka spreadsheet tambahan di hosting sekolah.
 *
 * Kelas ini murni urusan penyajian: isi laporannya disusun service masing-masing
 * domain, tata letaknya di Blade (resources/views/admin/laporan/excel), dan di
 * sini hanya perakitan respons beserta header unduhannya.
 */
class LaporanExcel
{
    /**
     * Blok pengaturan lembar kerja yang hanya dibaca Excel (lewat conditional
     * comment; browser mengabaikannya). Isinya menamai tab lembar kerja.
     *
     * Dibangun di sini, bukan di Blade, karena Blade menyangka tag <x:Name>
     * dan <x:ExcelWorkbook> adalah komponen Blade lalu gagal mencarinya.
     */
    public static function blokLembarKerja(string $namaLembar): string
    {
        return '<!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>' . e($namaLembar) . '</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->';
    }

    /**
     * Nama berkas unduhan, mis. Laporan_Data_Buku_Perpustakaan_SMK_20260827_101500.xls
     */
    public static function namaBerkas(string $prefix, string $namaPerpustakaan): string
    {
        return $prefix . '_' . preg_replace('/[^A-Za-z0-9]/', '_', $namaPerpustakaan) . '_' . date('Ymd_His') . '.xls';
    }

    /**
     * Render sebuah view laporan menjadi respons unduhan Excel.
     *
     * @param array  $data       isi laporan dari service, termasuk kunci `identitas`
     * @param string $namaLembar nama tab lembar kerja di Excel
     */
    public static function unduh(string $view, array $data, string $prefixBerkas, string $namaLembar): Response
    {
        $namaBerkas = self::namaBerkas($prefixBerkas, $data['identitas']['nama_perpustakaan']);
        $data['blokLembarKerja'] = self::blokLembarKerja($namaLembar);

        return response(view($view, $data)->render(), 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $namaBerkas . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }
}
