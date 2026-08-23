<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master Kelas awalnya hanya punya nama_kelas + keterangan. Laporan
     * inventaris butuh tingkat (10/11/12) sebagai kolom tersendiri supaya bisa
     * direkap per angkatan, dan penamaan "deskripsi" diselaraskan dengan tabel
     * kategori yang sudah lebih dulu memakai istilah itu.
     */
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->string('tingkat', 10)->nullable()->after('id');
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->renameColumn('keterangan', 'deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->renameColumn('deskripsi', 'keterangan');
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn('tingkat');
        });
    }
};
