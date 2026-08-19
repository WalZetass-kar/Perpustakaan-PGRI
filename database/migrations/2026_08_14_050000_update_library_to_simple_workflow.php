<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up(): void
    {

        Schema::table('buku', function (Blueprint $table) {
            $table->string('isbn')->nullable()->change();
            if (!Schema::hasColumn('buku', 'total_quantity')) {
                $table->integer('total_quantity')->default(1)->after('tahun_terbit');
            }
            if (!Schema::hasColumn('buku', 'available_quantity')) {
                $table->integer('available_quantity')->default(1)->after('total_quantity');
            }
        });

        Schema::table('rak', function (Blueprint $table) {
            $table->string('lokasi')->nullable()->change();
        });

        Schema::table('peminjaman', function (Blueprint $table) {
            if (Schema::hasColumn('peminjaman', 'eksemplar_id')) {
                $table->foreignId('eksemplar_id')->nullable()->change();
            }
            if (!Schema::hasColumn('peminjaman', 'jumlah')) {
                $table->integer('jumlah')->default(1)->after('buku_id');
            }
            if (!Schema::hasColumn('peminjaman', 'waktu_kembali')) {
                $table->timestamp('waktu_kembali')->nullable()->after('status');
            }
        });

        $bukuList = DB::table('buku')->get();
        foreach ($bukuList as $buku) {
            $countEksemplar = DB::table('eksemplar')->where('buku_id', $buku->id)->count();
            $totalQty = $countEksemplar > 0 ? $countEksemplar : 1;
            
            $borrowedCount = DB::table('peminjaman')
                ->where('buku_id', $buku->id)
                ->where('status', 'dipinjam')
                ->count();

            $availQty = max(0, $totalQty - $borrowedCount);

            DB::table('buku')->where('id', $buku->id)->update([
                'total_quantity' => $totalQty,
                'available_quantity' => $availQty,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (Schema::hasColumn('peminjaman', 'jumlah')) {
                $table->dropColumn('jumlah');
            }
            if (Schema::hasColumn('peminjaman', 'waktu_kembali')) {
                $table->dropColumn('waktu_kembali');
            }
        });

        Schema::table('buku', function (Blueprint $table) {
            if (Schema::hasColumn('buku', 'total_quantity')) {
                $table->dropColumn('total_quantity');
            }
            if (Schema::hasColumn('buku', 'available_quantity')) {
                $table->dropColumn('available_quantity');
            }
        });
    }
};
