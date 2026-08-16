<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('peminjaman', 'eksemplar_id')) {
            Schema::table('peminjaman', function (Blueprint $table) {
                $table->dropForeign(['eksemplar_id']);
                $table->dropColumn('eksemplar_id');
            });
        }

        Schema::dropIfExists('pembayaran_denda');
        Schema::dropIfExists('denda');
        Schema::dropIfExists('pengembalian');
        Schema::dropIfExists('detail_peminjaman');
        Schema::dropIfExists('reservasi');
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('eksemplar');
    }

    public function down(): void
    {
    }
};
