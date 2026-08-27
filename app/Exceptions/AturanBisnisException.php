<?php

namespace App\Exceptions;

use Exception;

/**
 * Operasi ditolak karena melanggar aturan perpustakaan — bukan karena sistem
 * bermasalah. Misalnya: stok buku tidak cukup, pengajuan sudah diproses
 * petugas lain, atau buku masih tercatat di riwayat peminjaman.
 *
 * Pesannya ditulis untuk dibaca petugas, sehingga controller cukup
 * meneruskannya apa adanya ke halaman lewat `->with('error', ...)`.
 * Lapisan service melempar ini; lapisan HTTP yang memutuskan tampilannya.
 */
class AturanBisnisException extends Exception
{
}
