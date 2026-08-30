<?php

use App\Models\Kelas;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Larang dua kelas dengan tingkat DAN nama yang sama.
     *
     * Satu jurusan wajar dimiliki beberapa angkatan, jadi "10 DKV" dan
     * "11 DKV" tetap sah berdampingan; yang dilarang hanya pengulangan pada
     * tingkat yang sama. Perbandingannya tidak bisa diserahkan langsung ke
     * kolom `nama_kelas`: di data lapangan sudah tercatat "11 dkv" dan
     * "11dkv" sebagai dua baris berbeda, padahal petugas memaksudkan satu
     * kelas. Karena itu dibuat kolom pembanding `kunci_unik` yang berisi
     * bentuk bakunya (tanpa spasi, huruf disamakan), dan indeks uniknya
     * dipasang di situ.
     *
     * Kolom ini diisi ulang otomatis oleh event `saving` pada model Kelas,
     * sehingga tidak pernah perlu — dan tidak boleh — diisi dari formulir.
     */
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            // Diberi default '' supaya penambahan kolom ini tidak menggagalkan
            // baris yang sudah ada, dan tidak perlu ->change() menyusul yang
            // pada SQLite berarti membangun ulang seluruh tabel.
            $table->string('kunci_unik')->default('')->after('nama_kelas');
        });

        $this->isiKunciUntukDataLama();

        Schema::table('kelas', function (Blueprint $table) {
            $table->unique('kunci_unik', 'kelas_kunci_unik_unique');
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropUnique('kelas_kunci_unik_unique');
            $table->dropColumn('kunci_unik');
        });
    }

    /**
     * Hitung kunci untuk baris yang sudah telanjur ada.
     *
     * Aturan baru ini menjaga isian berikutnya, bukan membereskan masa lalu —
     * dan data lama memang sudah memuat kelas kembar. Menghapus atau
     * menggabungkannya di sini terlalu berbahaya: kelas bisa saja sudah
     * dipakai buku, dan keputusan mana yang benar hanya petugas yang tahu.
     *
     * Jadi yang kembar dibiarkan hidup, tetapi kuncinya diberi akhiran nomor
     * barisnya supaya indeks uniknya tetap bisa terpasang. Akibatnya baris
     * kembar itu tidak bisa disimpan ulang tanpa diperbaiki lebih dulu —
     * petugas akan melihat pesan penolakan saat membukanya, dan itu memang
     * yang diinginkan.
     */
    private function isiKunciUntukDataLama(): void
    {
        $sudahDipakai = [];

        // Yang dipanggil dari model hanyalah fungsi murni penghitung kunci —
        // bukan penyimpanan lewat Eloquent — supaya bentuk bakunya dijamin
        // persis sama dengan yang dipakai aplikasi, tanpa memicu event apa pun.
        foreach (DB::table('kelas')->orderBy('id')->get() as $baris) {
            $kunci = Kelas::kunciUnik($baris->tingkat, $baris->nama_kelas);

            if (isset($sudahDipakai[$kunci])) {
                // Nomor baris dijamin unik, jadi kunci ini pasti tidak bentrok.
                $kunci .= '#' . $baris->id;
            } else {
                $sudahDipakai[$kunci] = true;
            }

            DB::table('kelas')->where('id', $baris->id)->update(['kunci_unik' => $kunci]);
        }
    }
};
