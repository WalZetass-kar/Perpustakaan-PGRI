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
        // 1. Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->timestamps();
        });

        // 2. Permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->timestamps();
        });

        // 3. Role Permissions
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();
        });

        // Update Users Table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->onDelete('set null');
            $table->string('phone')->nullable()->after('email');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('password');
        });

        // 4. Anggota
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nomor_anggota')->unique();
            $table->string('nim')->unique()->nullable();
            $table->string('program_studi')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['aktif', 'nonaktif', 'suspend'])->default('aktif');
            $table->timestamps();
        });

        // 5. Penulis
        Schema::create('penulis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('biografi')->nullable();
            $table->timestamps();
        });

        // 6. Penerbit
        Schema::create('penerbit', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kota')->nullable();
            $table->timestamps();
        });

        // 7. Kategori
        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 8. Rak
        Schema::create('rak', function (Blueprint $table) {
            $table->id();
            $table->string('kode_rak')->unique();
            $table->string('nama_rak');
            $table->string('lokasi');
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->onDelete('set null');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        // 9. Buku
        Schema::create('buku', function (Blueprint $table) {
            $table->id();
            $table->string('isbn')->unique();
            $table->string('judul');
            $table->foreignId('penulis_id')->nullable()->constrained('penulis')->onDelete('set null');
            $table->foreignId('penerbit_id')->nullable()->constrained('penerbit')->onDelete('set null');
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->onDelete('set null');
            $table->foreignId('rak_id')->nullable()->constrained('rak')->onDelete('set null');
            $table->integer('tahun_terbit');
            $table->text('sinopsis')->nullable();
            $table->string('cover')->nullable();
            $table->integer('view_count')->default(0);
            $table->enum('status', ['tersedia', 'tidak_tersedia'])->default('tersedia');
            $table->timestamps();
        });

        // 10. Eksemplar
        Schema::create('eksemplar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buku_id')->constrained('buku')->onDelete('cascade');
            $table->string('kode_eksemplar')->unique();
            $table->string('barcode')->unique();
            $table->string('qr_code')->nullable();
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->foreignId('rak_id')->nullable()->constrained('rak')->onDelete('set null');
            $table->enum('status', ['tersedia', 'dipinjam', 'rusak', 'hilang', 'tidak_tersedia'])->default('tersedia');
            $table->timestamps();
        });

        // 11. Peminjaman
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode_peminjaman')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('buku_id')->constrained('buku')->onDelete('cascade');
            $table->foreignId('eksemplar_id')->constrained('eksemplar')->onDelete('cascade');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_jatuh_tempo');
            $table->integer('jumlah_perpanjangan')->default(0);
            $table->enum('status', ['dipinjam', 'dikembalikan', 'terlambat'])->default('dipinjam');
            $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 12. Detail Peminjaman
        Schema::create('detail_peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->onDelete('cascade');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 13. Pengembalian
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->onDelete('cascade');
            $table->date('tanggal_kembali');
            $table->integer('hari_keterlambatan')->default(0);
            $table->decimal('denda_keterlambatan', 10, 2)->default(0);
            $table->decimal('denda_kerusakan_kehilangan', 10, 2)->default(0);
            $table->decimal('total_denda', 10, 2)->default(0);
            $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 14. Denda
        Schema::create('denda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->nullable()->constrained('peminjaman')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('jumlah_denda', 10, 2);
            $table->string('alasan');
            $table->enum('status_pembayaran', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->timestamps();
        });

        // 15. Pembayaran Denda
        Schema::create('pembayaran_denda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('denda_id')->constrained('denda')->onDelete('cascade');
            $table->decimal('jumlah_bayar', 10, 2);
            $table->date('tanggal_bayar');
            $table->string('metode_pembayaran')->default('tunai');
            $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 16. Reservasi
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_reservasi')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('buku_id')->constrained('buku')->onDelete('cascade');
            $table->integer('posisi_antrean')->default(1);
            $table->enum('status', ['menunggu', 'tersedia', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->date('tanggal_reservasi');
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->timestamps();
        });

        // 17. Notifikasi
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->text('pesan');
            $table->string('tipe');
            $table->boolean('dibaca')->default(false);
            $table->timestamps();
        });

        // 18. Audit Logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name')->nullable();
            $table->string('aktivitas');
            $table->text('deskripsi')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        // 19. Pengaturan
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label');
            $table->string('tipe')->default('text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('reservasi');
        Schema::dropIfExists('pembayaran_denda');
        Schema::dropIfExists('denda');
        Schema::dropIfExists('pengembalian');
        Schema::dropIfExists('detail_peminjaman');
        Schema::dropIfExists('peminjaman');
        Schema::dropIfExists('eksemplar');
        Schema::dropIfExists('buku');
        Schema::dropIfExists('rak');
        Schema::dropIfExists('kategori');
        Schema::dropIfExists('penerbit');
        Schema::dropIfExists('penulis');
        Schema::dropIfExists('anggota');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'phone', 'status']);
        });
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
