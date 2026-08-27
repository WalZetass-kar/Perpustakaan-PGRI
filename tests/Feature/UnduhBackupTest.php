<?php

namespace Tests\Feature;

use App\Models\{AuditLog, Buku, Kategori, Rak, Role, User};
use App\Services\DatabaseBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tombol "Unduh Backup Database (.SQL)" di Pengaturan → Informasi Diagnostik.
 *
 * Cadangan yang tidak bisa dipulihkan sama saja dengan tidak punya cadangan,
 * jadi yang diuji bukan sekadar berkasnya terunduh, melainkan isinya benar
 * dan benar-benar bisa dimuat kembali.
 */
class UnduhBackupTest extends TestCase
{
    use RefreshDatabase;

    private function login(string $peran): User
    {
        $role = Role::firstOrCreate(['name' => $peran], ['display_name' => $peran]);
        $u = User::create([
            'name' => 'Uji ' . $peran, 'email' => $peran . '@u.test',
            'password' => Hash::make('rahasia123'), 'role_id' => $role->id, 'status' => 'active',
        ]);
        $this->actingAs($u);

        return $u;
    }

    private function isiData(): Buku
    {
        $rak = Rak::create(['kode_rak' => 'R1', 'nama_rak' => 'Rak Sastra', 'lokasi' => 'L1', 'status' => 'aktif']);
        $kat = Kategori::create(['nama' => 'Sastra', 'slug' => 'sastra']);

        return Buku::create([
            // Sengaja memakai tanda kutip, baris baru, backslash, dan huruf non-latin:
            // inilah yang merusak dump buatan sendiri kalau escaping-nya keliru.
            'judul' => "Bumi Manusia 'Cetakan\\ Ke-2\"",
            'sinopsis' => "Baris satu\nBaris dua — dengan tanda pisah panjang & émoji 📚",
            'isbn' => 'ISBN-001', 'rak_id' => $rak->id, 'kategori_id' => $kat->id,
            'tahun_terbit' => 2024, 'total_quantity' => 3, 'available_quantity' => 3, 'status' => 'tersedia',
        ]);
    }

    // ------------------------------------------------------------- hak akses

    public function test_super_admin_bisa_mengunduh(): void
    {
        $this->isiData();
        $this->login('super_admin');

        $r = $this->get(route('admin.pengaturan.backup'));

        $r->assertOk();
        $r->assertHeader('Content-Type', 'application/sql');
        $this->assertStringContainsString('.sql', $r->headers->get('Content-Disposition'));
        $this->assertStringContainsString('attachment', $r->headers->get('Content-Disposition'));
    }

    public function test_petugas_biasa_ditolak(): void
    {
        $this->login('admin');
        $this->get(route('admin.pengaturan.backup'))->assertForbidden();
    }

    public function test_tamu_tidak_bisa_mengunduh(): void
    {
        $this->get(route('admin.pengaturan.backup'))->assertRedirect();
    }

    public function test_pengunduhan_tercatat_di_audit_log(): void
    {
        $this->login('super_admin');
        $this->get(route('admin.pengaturan.backup'))->assertOk();

        $this->assertSame(1, AuditLog::where('aktivitas', 'DOWNLOAD_BACKUP_SQL')->count());
    }

    // ------------------------------------------------------------- isi dump

    public function test_dump_memuat_seluruh_tabel_dan_datanya(): void
    {
        $buku = $this->isiData();
        $this->login('super_admin');

        $sql = $this->get(route('admin.pengaturan.backup'))->getContent();

        foreach (['buku', 'users', 'rak', 'kategori', 'peminjaman', 'pengaturan', 'roles'] as $tabel) {
            $this->assertStringContainsString($tabel, $sql, "Tabel {$tabel} tidak ikut dicadangkan.");
        }
        $this->assertStringContainsString('Bumi Manusia', $sql, 'Isi tabel tidak ikut, hanya strukturnya.');
    }

    /**
     * Ini inti persoalannya: dump dimuat ulang ke database kosong, lalu datanya
     * harus kembali persis seperti semula — termasuk teks bertanda kutip,
     * baris baru, dan huruf non-latin.
     */
    public function test_dump_benar_benar_bisa_dipulihkan(): void
    {
        $asli = $this->isiData();
        $sql = app(DatabaseBackupService::class)->generateSqlDump();

        // Kosongkan tabelnya, lalu pulihkan dari dump.
        Buku::query()->delete();
        $this->assertSame(0, Buku::count());

        DB::unprepared($sql);

        $pulih = Buku::find($asli->id);
        $this->assertNotNull($pulih, 'Data tidak kembali setelah dump dimuat ulang.');
        $this->assertSame($asli->judul, $pulih->judul, 'Teks bertanda kutip rusak saat dipulihkan.');
        $this->assertSame($asli->sinopsis, $pulih->sinopsis, 'Baris baru / huruf non-latin rusak saat dipulihkan.');
        $this->assertSame($asli->total_quantity, $pulih->total_quantity);
    }

    /**
     * Satu INSERT raksasa berisi seluruh isi tabel akan melewati
     * max_allowed_packet MySQL (bawaannya 64 MB) begitu perpustakaan berjalan
     * beberapa tahun — dan yang gagal adalah PEMULIHANNYA, tepat pada saat
     * cadangan itu paling dibutuhkan. Jadi barisnya harus dipecah.
     */
    public function test_insert_dipecah_agar_tidak_melewati_batas_paket(): void
    {
        $baris = [];
        for ($i = 0; $i < 1200; $i++) {
            $baris[] = [
                'user_id' => null, 'user_name' => "Petugas {$i}",
                'aktivitas' => 'UJI', 'deskripsi' => "Baris ke-{$i}",
                'ip_address' => '127.0.0.1',
                'created_at' => now(), 'updated_at' => now(),
            ];
        }
        foreach (array_chunk($baris, 300) as $potongan) {
            DB::table('audit_logs')->insert($potongan);
        }

        $sql = app(DatabaseBackupService::class)->generateSqlDump();

        $jumlahInsert = substr_count($sql, 'INSERT INTO "audit_logs"')
            + substr_count($sql, 'INSERT INTO `audit_logs`');

        $this->assertGreaterThan(1, $jumlahInsert,
            'Seluruh baris masih ditulis sebagai satu INSERT tunggal.');
        $this->assertSame(1200, substr_count($sql, "'UJI'"),
            'Pemecahan tidak boleh sampai menghilangkan atau menggandakan baris.');
    }

    /** Semua baris tetap kembali utuh walau INSERT-nya terpecah. */
    public function test_dump_terpecah_tetap_pulih_utuh(): void
    {
        $this->isiData();
        for ($i = 0; $i < 700; $i++) {
            Kategori::create(['nama' => "Kategori {$i}", 'slug' => "kategori-{$i}"]);
        }
        $sebelum = Kategori::count();

        $sql = app(DatabaseBackupService::class)->generateSqlDump();
        Kategori::query()->delete();
        DB::unprepared($sql);

        $this->assertSame($sebelum, Kategori::count(), 'Jumlah baris berubah setelah dipulihkan.');
        $this->assertNotNull(Kategori::where('slug', 'kategori-699')->first(),
            'Baris di potongan terakhir hilang.');
    }
}
