<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Riwayat peminjaman adalah catatan resmi sirkulasi perpustakaan, tetapi dua
 * relasinya semula ber-ON DELETE CASCADE:
 *
 *   peminjaman.user_id -> users.id   CASCADE
 *   peminjaman.buku_id -> buku.id    CASCADE
 *
 * Akibatnya menghapus satu akun petugas ikut memusnahkan SELURUH transaksi yang
 * pernah dicatatnya (peminjamanStore menyimpan auth()->id() ke user_id), dan
 * menghapus satu judul buku memusnahkan seluruh riwayat peminjaman buku itu.
 * Penjaga di aplikasi hanya menahan peminjaman yang masih berstatus `dipinjam`,
 * sehingga transaksi yang sudah dikembalikan — yaitu justru seluruh arsipnya —
 * terhapus tanpa peringatan apa pun.
 *
 * Sesudah migrasi ini:
 *   user_id -> SET NULL. Identitas peminjam tetap terbaca karena nama, jurusan,
 *              nomor induk, dan nomor WhatsApp-nya disimpan langsung di baris
 *              peminjaman, bukan lewat relasi.
 *   buku_id -> RESTRICT. Baris riwayat tanpa buku kehilangan maknanya, jadi
 *              penghapusannya ditolak di tingkat database sebagai jaring
 *              terakhir bila penjaga di aplikasi terlewat.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('peminjaman')) {
            return;
        }

        // Baris yatim dari penghapusan sebelum migrasi ini tidak boleh
        // menggagalkan pemasangan RESTRICT di bawah.
        DB::table('peminjaman')
            ->whereNotNull('buku_id')
            ->whereNotIn('buku_id', function ($q) {
                $q->select('id')->from('buku');
            })
            ->delete();

        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['buku_id']);

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('buku_id')->references('id')->on('buku')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('peminjaman')) {
            return;
        }

        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['buku_id']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('buku_id')->references('id')->on('buku')->cascadeOnDelete();
        });
    }
};
