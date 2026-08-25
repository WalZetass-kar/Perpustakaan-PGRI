<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Membuang dua setelan yang tidak lagi dipakai sistem.
 *
 * - max_buku_pinjam: pembatasan jumlah pinjam per siswa dihapus. Kini satu-satunya
 *   pembatas adalah stok fisik yang tersedia.
 * - max_perpanjangan: perpanjangan online tidak pernah diimplementasikan. Siswa
 *   yang ingin memperpanjang datang langsung ke perpustakaan.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('pengaturan')
            ->whereIn('key', ['max_buku_pinjam', 'max_perpanjangan'])
            ->delete();
    }

    public function down(): void
    {
        foreach ([
            ['key' => 'max_buku_pinjam',  'value' => '3', 'label' => 'Maksimal Buku Dipinjam Per Siswa', 'tipe' => 'number'],
            ['key' => 'max_perpanjangan', 'value' => '2', 'label' => 'Maksimal Perpanjangan Online',     'tipe' => 'number'],
        ] as $setelan) {
            DB::table('pengaturan')->updateOrInsert(
                ['key' => $setelan['key']],
                $setelan + ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
};
