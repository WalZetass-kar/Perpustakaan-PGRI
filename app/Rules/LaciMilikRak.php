<?php

namespace App\Rules;

use App\Models\RakLaci;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Pastikan laci yang dipilih benar-benar berada di rak yang dipilih.
 *
 * Sistem ini dipakai untuk MENEMUKAN buku di rak. Laci milik rak lain akan
 * tersimpan diam-diam dan membuat halaman Temukan Buku menunjukkan lokasi
 * yang salah — kesalahan yang baru ketahuan setelah petugas mencari ke rak
 * yang keliru.
 */
class LaciMilikRak implements ValidationRule
{
    public function __construct(private mixed $rakId)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value) {
            return;
        }

        $laci = RakLaci::find($value);

        if ($laci && (int) $laci->rak_id !== (int) $this->rakId) {
            $fail('Laci yang dipilih bukan milik rak tersebut. Pilih laci yang tersedia pada rak yang sama.');
        }
    }
}
