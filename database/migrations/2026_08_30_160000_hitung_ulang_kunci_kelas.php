<?php

use App\Models\Kelas;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Hitung ulang `kunci_unik` setelah cara membandingkan kelas diperluas.
     *
     * Sebelumnya "tingkat 11 + nama 11 RPL" dan "tingkat 11 + nama RPL"
     * menghasilkan dua kunci berbeda, padahal petugas memaksudkan kelas yang
     * sama — hanya yang kedua tidak ikut menuliskan angka jenjangnya. Kunci
     * sekarang membuang jenjang yang cuma mengulang tingkatnya, sehingga
     * keduanya bertemu.
     *
     * Kunci yang tersimpan dihitung dengan aturan lama, jadi harus disusun
     * ulang. Tanpa ini penjagaannya bocor untuk seluruh baris yang sudah ada:
     * kunci basi tidak akan pernah bertemu kunci baru.
     */
    public function up(): void
    {
        $this->tulisUlangKunci();
    }

    /**
     * Membalikkannya berarti menghitung ulang dengan aturan yang berlaku saat
     * itu — dan itu persis yang dilakukan fungsi yang sama. Jadi arah baliknya
     * cukup menjalankan hal serupa, bukan mengembalikan nilai lama satu per
     * satu yang justru akan menanam kunci yang tidak cocok dengan kodenya.
     */
    public function down(): void
    {
        $this->tulisUlangKunci();
    }

    private function tulisUlangKunci(): void
    {
        $baris = DB::table('kelas')->orderBy('id')->get();

        if ($baris->isEmpty()) {
            return;
        }

        $sudahDipakai = [];
        $final = [];

        foreach ($baris as $satu) {
            $kunci = Kelas::kunciUnik($satu->tingkat, $satu->nama_kelas);

            // Aturan baru bisa mempertemukan dua baris lama yang dulu terpisah.
            // Keduanya tetap dibiarkan hidup — hanya petugas yang tahu mana
            // yang benar — dengan kunci yang dibedakan nomor barisnya supaya
            // indeks uniknya tetap terpasang. Barisnya akan menolak disimpan
            // ulang sampai diperbaiki, dan memang itu yang diinginkan.
            if (isset($sudahDipakai[$kunci])) {
                $kunci .= '#' . $satu->id;
            } else {
                $sudahDipakai[$kunci] = true;
            }

            $final[$satu->id] = $kunci;
        }

        DB::transaction(function () use ($final) {
            // Dua tahap: kunci lama dan kunci baru bisa saling bertukar antar
            // baris, dan menulisnya langsung akan menabrak indeks unik di
            // tengah jalan walau susunan akhirnya sah.
            foreach (array_keys($final) as $id) {
                DB::table('kelas')->where('id', $id)->update(['kunci_unik' => '#sementara-' . $id]);
            }

            foreach ($final as $id => $kunci) {
                DB::table('kelas')->where('id', $id)->update(['kunci_unik' => $kunci]);
            }
        });
    }
};
