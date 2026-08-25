<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menandai asal setiap peminjaman: diajukan sendiri lewat katalog OPAC, atau
 * dicatat petugas di meja sirkulasi.
 *
 * Sebelumnya asal-usul ini bisa ditebak dari awalan kode `REQ-`, tapi tebakan
 * itu hilang begitu pengajuan disetujui: peminjamanRequestApprove menimpa
 * kode_peminjaman menjadi `PJ-...`. Jadi penandanya harus disimpan sendiri.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (!Schema::hasColumn('peminjaman', 'sumber')) {
                $table->string('sumber', 20)->default('petugas')->after('kode_peminjaman');
            }
        });

        // Data lama: petugas tidak pernah mengisi no_wa maupun catatan lewat
        // form meja sirkulasi, dan pengajuan yang belum disetujui masih memakai
        // awalan REQ-. Ketiganya hanya mungkin berasal dari katalog OPAC.
        DB::table('peminjaman')
            ->where('kode_peminjaman', 'like', 'REQ-%')
            ->orWhereNotNull('no_wa')
            ->orWhereNotNull('catatan')
            ->update(['sumber' => 'opac']);
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (Schema::hasColumn('peminjaman', 'sumber')) {
                $table->dropColumn('sumber');
            }
        });
    }
};
