<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (!Schema::hasColumn('peminjaman', 'no_wa')) {
                $table->string('no_wa')->nullable()->after('nomor_induk');
            }
            if (!Schema::hasColumn('peminjaman', 'catatan')) {
                $table->text('catatan')->nullable()->after('status');
            }
            if (!Schema::hasColumn('peminjaman', 'alasan_penolakan')) {
                $table->text('alasan_penolakan')->nullable()->after('catatan');
            }
        });

        // `MODIFY COLUMN` hanya dikenal MySQL. Pernyataan aslinya dipertahankan
        // apa adanya untuk MySQL supaya hasil di server produksi tidak berubah,
        // sementara driver lain (sqlite yang dipakai test) memakai padanan
        // portabel dari Schema builder.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'dipinjam'");
        } else {
            Schema::table('peminjaman', function (Blueprint $table) {
                $table->string('status', 50)->nullable(false)->default('dipinjam')->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (Schema::hasColumn('peminjaman', 'alasan_penolakan')) {
                $table->dropColumn('alasan_penolakan');
            }
            if (Schema::hasColumn('peminjaman', 'catatan')) {
                $table->dropColumn('catatan');
            }
            if (Schema::hasColumn('peminjaman', 'no_wa')) {
                $table->dropColumn('no_wa');
            }
        });
    }
};
