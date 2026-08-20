<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::table('buku', function (Blueprint $table) {
            $table->foreignId('kelas_id')->nullable()->after('kategori_id')->constrained('kelas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kelas_id');
        });

        Schema::dropIfExists('kelas');
    }
};
