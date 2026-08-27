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

    /** Isi folder cadangan sebelum pengujian, agar sisa berkas bisa dibereskan. */
    private array $berkasCadanganAwal = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->berkasCadanganAwal = glob(storage_path('app/backups/*')) ?: [];
    }

    /**
     * Berkas cadangan lewat HTTP tidak terhapus sendiri di dalam pengujian:
     * deleteFileAfterSend() baru bekerja saat respons benar-benar dikirim ke
     * peramban. Tanpa pembersihan ini, setiap kali suite dijalankan folder
     * storage/app/backups bertambah sampah.
     */
    protected function tearDown(): void
    {
        foreach (glob(storage_path('app/backups/*')) ?: [] as $berkas) {
            if (!in_array($berkas, $this->berkasCadanganAwal, true)) {
                @unlink($berkas);
            }
        }

        parent::tearDown();
    }

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

    // -------------------------------------------------- cadangan lengkap (ZIP)

    public function test_super_admin_bisa_mengunduh_cadangan_lengkap(): void
    {
        $this->isiData();
        $this->login('super_admin');

        $r = $this->get(route('admin.pengaturan.backup-lengkap'));

        $r->assertOk();
        $this->assertStringContainsString('.zip', $r->headers->get('Content-Disposition'));
        $this->assertStringContainsString('attachment', $r->headers->get('Content-Disposition'));
    }

    public function test_cadangan_lengkap_tertutup_untuk_petugas_dan_tamu(): void
    {
        $this->get(route('admin.pengaturan.backup-lengkap'))->assertRedirect();

        $this->login('admin');
        $this->get(route('admin.pengaturan.backup-lengkap'))->assertForbidden();
    }

    public function test_pengunduhan_cadangan_lengkap_tercatat_di_audit_log(): void
    {
        $this->login('super_admin');
        $this->get(route('admin.pengaturan.backup-lengkap'))->assertOk();

        $this->assertSame(1, AuditLog::where('aktivitas', 'DOWNLOAD_BACKUP_ZIP')->count());
    }

    /**
     * Kolom `cover` cuma menyimpan nama berkasnya; gambarnya ada di storage.
     * Kalau ZIP hanya berisi .sql, pemulihan mengembalikan seluruh data buku
     * tetapi sampulnya hilang semua dan harus diunggah ulang satu per satu.
     */
    public function test_cadangan_lengkap_memuat_dump_sql_dan_berkas_sampul(): void
    {
        $this->isiData();

        $folderSampul = storage_path('app/public/covers');
        if (!is_dir($folderSampul)) {
            mkdir($folderSampul, 0755, true);
        }
        $sampul = $folderSampul . '/uji-sampul-backup.jpg';
        file_put_contents($sampul, 'isi-gambar-palsu');

        try {
            $zipPath = app(DatabaseBackupService::class)->createZipBackup();
            $this->assertNotNull($zipPath, 'Berkas ZIP gagal dibuat.');

            $zip = new \ZipArchive();
            $this->assertTrue($zip->open($zipPath) === true, 'Berkas ZIP tidak bisa dibuka.');

            $isi = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $isi[] = $zip->getNameIndex($i);
            }

            $berkasSql = array_values(array_filter($isi, fn ($n) => str_ends_with($n, '.sql')));
            $this->assertNotEmpty($berkasSql, 'Dump .sql tidak ada di dalam ZIP.');
            $this->assertStringContainsString('Bumi Manusia', $zip->getFromName($berkasSql[0]),
                'Data buku tidak ikut di dump yang ada di dalam ZIP.');
            $this->assertContains('covers/uji-sampul-backup.jpg', $isi,
                'Berkas sampul buku tidak ikut dicadangkan.');

            $zip->close();
            @unlink($zipPath);
        } finally {
            @unlink($sampul);
        }
    }

    /**
     * Nama berkas yang hanya presisi sampai detik membuat dua pencadangan pada
     * detik yang sama saling menimpa. Karena berkas di server dihapus setelah
     * terkirim, dua petugas yang mengklik bersamaan bisa sama-sama menerima
     * cadangan rusak -- kegagalan yang baru ketahuan saat berkas itu dipakai.
     */
    public function test_dua_cadangan_pada_detik_yang_sama_tidak_saling_menimpa(): void
    {
        $this->isiData();
        $svc = app(DatabaseBackupService::class);

        $pertama = $svc->createZipBackup();
        $kedua = $svc->createZipBackup();

        try {
            $this->assertNotSame($pertama, $kedua, 'Kedua cadangan memakai nama berkas yang sama.');
            $this->assertFileExists($pertama, 'Cadangan pertama tertimpa oleh yang kedua.');
            $this->assertFileExists($kedua);

            foreach ([$pertama, $kedua] as $berkas) {
                $zip = new \ZipArchive();
                $this->assertTrue($zip->open($berkas) === true, "Berkas {$berkas} rusak.");
                $this->assertGreaterThan(0, $zip->numFiles, 'Cadangan kosong.');
                $zip->close();
            }
        } finally {
            @unlink($pertama);
            @unlink($kedua);
        }
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
