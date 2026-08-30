<?php

namespace App\Rules;

use App\Models\Kelas;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Tolak kelas yang tingkatnya bertengkar dengan namanya sendiri.
 *
 * Petugas terbiasa mengulang tingkat di dalam nama kelas ("11 DKV",
 * "xii Mesin"), sehingga satu baris menyimpan jenjang di dua tempat. Ketika
 * keduanya berbeda — tingkat 11 tetapi namanya "XII DKV" — tidak ada cara
 * menebak mana yang benar, dan akibatnya nyata: buku yang ditandai untuk kelas
 * itu jadi salah sasaran, sementara laporan per angkatan merekapnya ke jenjang
 * yang lain lagi.
 *
 * Yang diperiksa hanya angka di awal nama. Nama tanpa penunjuk jenjang ("DKV")
 * dibiarkan, begitu pula kelas yang kolom tingkatnya memang sengaja
 * dikosongkan — keduanya tidak menyimpan pertentangan apa pun.
 */
class TingkatSelarasDenganNama implements ValidationRule
{
    public function __construct(private mixed $tingkat)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $tingkatDipilih = Kelas::angkaTingkat(is_string($this->tingkat) ? $this->tingkat : null);
        $tingkatDiNama = Kelas::tingkatDiAwalNama(is_string($value) ? $value : null);

        // Tidak ada yang bisa bertentangan kalau salah satunya tidak menyebut
        // jenjang sama sekali.
        if ($tingkatDipilih === null || $tingkatDiNama === null || $tingkatDipilih === $tingkatDiNama) {
            return;
        }

        $fail(
            'Tingkat yang dipilih (' . $tingkatDipilih . ') tidak cocok dengan nama kelas "' . $value
            . '" yang menunjuk tingkat ' . $tingkatDiNama . '. Angka Romawi dihitung sama dengan '
            . 'angka biasa (X = 10, XI = 11, XII = 12). Samakan keduanya: perbaiki tingkatnya, '
            . 'atau hapus angka tingkat dari nama kelas.'
        );
    }
}
