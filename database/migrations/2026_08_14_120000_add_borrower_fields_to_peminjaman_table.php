<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (Schema::hasColumn('peminjaman', 'user_id')) {
                $table->foreignId('user_id')->nullable()->change();
            }
            if (!Schema::hasColumn('peminjaman', 'nama_peminjam')) {
                $table->string('nama_peminjam')->nullable()->after('kode_peminjaman');
            }
            if (!Schema::hasColumn('peminjaman', 'jurusan')) {
                $table->string('jurusan')->nullable()->after('nama_peminjam');
            }
            if (!Schema::hasColumn('peminjaman', 'nomor_induk')) {
                $table->string('nomor_induk')->nullable()->after('jurusan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (Schema::hasColumn('peminjaman', 'nomor_induk')) {
                $table->dropColumn('nomor_induk');
            }
            if (Schema::hasColumn('peminjaman', 'jurusan')) {
                $table->dropColumn('jurusan');
            }
            if (Schema::hasColumn('peminjaman', 'nama_peminjam')) {
                $table->dropColumn('nama_peminjam');
            }
        });
    }
};
