<?php

use App\Models\Kelas;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Urutkan daftar kelas menurut jenjangnya yang sebenarnya.
     *
     * Pengurutan sebelumnya memakai `CAST(tingkat AS UNSIGNED)`, dan SQL tidak
     * mengenal angka Romawi: `CAST('XI' AS UNSIGNED)` bernilai 0, sehingga
     * kelas XI selalu terlempar ke urutan paling atas — sebelum kelas 9.
     * Sejak sistem menyetarakan "XI" dengan 11 pada penjagaan nama kembar,
     * petugas wajar saja menulis tingkat dengan angka Romawi, jadi kekeliruan
     * ini tinggal menunggu waktu untuk terlihat.
     *
     * Penyelesaiannya bukan mengubah SQL-nya — SQL memang tidak akan pernah
     * paham Romawi — melainkan menyimpan hasil terjemahannya di kolom
     * tersendiri. Kolom ini diisi ulang otomatis oleh event `saving` pada model
     * Kelas, sama seperti `kunci_unik`, sehingga tidak pernah perlu diisi dari
     * formulir dan tidak bisa basi.
     */
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->unsignedSmallInteger('tingkat_angka')->nullable()->after('tingkat');
        });

        // Kolom baru untuk baris yang sudah ada. Yang dipanggil dari model
        // hanyalah fungsi murni penerjemahnya, bukan penyimpanan lewat Eloquent,
        // supaya hasilnya dijamin sama dengan yang dipakai aplikasi.
        foreach (DB::table('kelas')->orderBy('id')->get() as $baris) {
            DB::table('kelas')
                ->where('id', $baris->id)
                ->update(['tingkat_angka' => Kelas::angkaTingkat($baris->tingkat)]);
        }

        Schema::table('kelas', function (Blueprint $table) {
            $table->index(['tingkat_angka', 'nama_kelas'], 'kelas_urutan_tingkat_index');
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropIndex('kelas_urutan_tingkat_index');
            $table->dropColumn('tingkat_angka');
        });
    }
};
