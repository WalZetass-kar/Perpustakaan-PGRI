<?php

namespace App\Rules;

use App\Models\Kelas;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Pastikan tidak ada dua kelas dengan tingkat DAN nama yang sama.
 *
 * Satu jurusan wajar dimiliki beberapa angkatan, jadi "10 DKV" dan "11 DKV"
 * tetap boleh berdampingan. Yang ditolak hanyalah pengulangan pada tingkat
 * yang sama — termasuk yang hanya berbeda spasi atau huruf besar/kecil,
 * karena "11 dkv" dan "11dkv" merujuk kelas yang sama di mata petugas namun
 * tersimpan sebagai dua baris berbeda kalau dibandingkan apa adanya.
 *
 * Aturan ini adalah lapisan yang pesannya dibaca petugas di layar. Penjagaan
 * yang sama diulang di KelasService dan oleh indeks unik `kunci_unik` di
 * database, sebagai jaring bagi jalur penyimpanan yang tidak lewat formulir.
 */
class KelasBelumTerdaftar implements ValidationRule
{
    public function __construct(private mixed $tingkat, private ?int $kecualiId = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $kembar = Kelas::kembarDengan(
            is_string($this->tingkat) ? $this->tingkat : null,
            is_string($value) ? $value : null,
            $this->kecualiId
        );

        if (!$kembar) {
            return;
        }

        // Nama kelas yang sudah tersimpan ikut disebut supaya petugas paham
        // mengapa isiannya ditolak walau tulisannya tidak persis sama.
        $fail(
            'Kelas "' . $kembar->label_lengkap . '" sudah terdaftar. '
            . 'Nama kelas tidak boleh sama pada tingkat yang sama — termasuk bila hanya berbeda '
            . 'spasi, huruf besar/kecil, atau penulisan tingkatnya (X sama dengan 10). '
            . 'Pakai nama lain, atau ubah data kelas yang sudah ada.'
        );
    }
}
