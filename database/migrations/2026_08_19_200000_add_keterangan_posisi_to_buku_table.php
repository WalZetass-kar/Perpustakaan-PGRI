<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->text('keterangan_posisi')->nullable()->after('sinopsis')
                  ->comment('Catatan posisi fisik buku dalam laci/rak, misal: Baris ke-2 dari depan');
        });
    }

    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropColumn('keterangan_posisi');
        });
    }
};
