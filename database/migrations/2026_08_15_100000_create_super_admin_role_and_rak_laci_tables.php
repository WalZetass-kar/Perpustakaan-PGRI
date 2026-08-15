<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->where('id', 1)->update([
            'name'         => 'super_admin',
            'display_name' => 'Super Administrator',
            'updated_at'   => now(),
        ]);

        if (!DB::table('roles')->where('name', 'admin')->exists()) {
            DB::table('roles')->insert([
                'name'         => 'admin',
                'display_name' => 'Admin Perpustakaan',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        if (!Schema::hasTable('rak_laci')) {
            Schema::create('rak_laci', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rak_id')->constrained('rak')->onDelete('cascade');
                $table->integer('nomor_laci')->default(1);
                $table->string('nama_laci');
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('buku', function (Blueprint $table) {
            if (!Schema::hasColumn('buku', 'rak_laci_id')) {
                $table->foreignId('rak_laci_id')->nullable()->after('rak_id')->constrained('rak_laci')->onDelete('set null');
            }
        });

        $existingRaks = DB::table('rak')->get();
        foreach ($existingRaks as $rak) {
            $laciIds = [];
            for ($i = 1; $i <= 3; $i++) {
                $laciId = DB::table('rak_laci')->insertGetId([
                    'rak_id'      => $rak->id,
                    'nomor_laci'  => $i,
                    'nama_laci'   => 'Laci ' . $i,
                    'keterangan'  => 'Tingkat ' . $i . ' pada ' . $rak->nama_rak,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
                $laciIds[$i] = $laciId;
            }

            if (!empty($laciIds[1])) {
                DB::table('buku')->where('rak_id', $rak->id)->whereNull('rak_laci_id')->update([
                    'rak_laci_id' => $laciIds[1],
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            if (Schema::hasColumn('buku', 'rak_laci_id')) {
                $table->dropForeign(['rak_laci_id']);
                $table->dropColumn('rak_laci_id');
            }
        });

        Schema::dropIfExists('rak_laci');
    }
};
