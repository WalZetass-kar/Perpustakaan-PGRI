<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Anggota;
use App\Models\Penulis;
use App\Models\Penerbit;
use App\Models\Kategori;
use App\Models\Rak;
use App\Models\Buku;
use App\Models\Eksemplar;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Denda;
use App\Models\Reservasi;
use App\Models\Notifikasi;
use App\Models\AuditLog;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed System Roles
        $roleAdmin = Role::create(['name' => 'admin', 'display_name' => 'Administrator Utama']);
        $rolePustakawan = Role::create(['name' => 'pustakawan', 'display_name' => 'Petugas Pustakawan']);
        $roleSiswa = Role::create(['name' => 'mahasiswa', 'display_name' => 'Siswa / Anggota']);

        // 2. Seed System Permissions
        $permissions = [
            'manage_books' => 'Kelola Koleksi Buku',
            'manage_exemplars' => 'Kelola Eksemplar Buku',
            'manage_racks' => 'Kelola Rak Perpustakaan',
            'manage_categories' => 'Kelola Kategori Buku',
            'manage_loans' => 'Kelola Peminjaman & Pengembalian',
            'manage_members' => 'Kelola Data Anggota',
            'manage_fines' => 'Kelola Denda & Pembayaran',
            'manage_reservations' => 'Kelola Antrean Reservasi',
            'manage_reports' => 'Akses Laporan & Export',
            'manage_users' => 'Kelola Pengguna Sistem',
            'manage_roles' => 'Kelola Role & Hak Akses',
            'manage_settings' => 'Kelola Pengaturan Sistem',
            'view_audit_logs' => 'Lihat Audit Log',
        ];

        foreach ($permissions as $key => $disp) {
            $p = Permission::create(['name' => $key, 'display_name' => $disp]);
            $roleAdmin->permissions()->attach($p);
            if (in_array($key, ['manage_books', 'manage_exemplars', 'manage_racks', 'manage_categories', 'manage_loans', 'manage_members', 'manage_fines', 'manage_reservations', 'manage_reports'])) {
                $rolePustakawan->permissions()->attach($p);
            }
        }

        // 3. Seed Default System Users (Akun Utama Pengoperasian)
        $admin = User::create([
            'name' => 'Administrator Sekolah',
            'email' => 'admin@smkpgri.sch.id',
            'password' => Hash::make('password'),
            'role_id' => $roleAdmin->id,
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        $pustakawan = User::create([
            'name' => 'Siti Rahmawati, S.Pd',
            'email' => 'pustakawan@smkpgri.sch.id',
            'password' => Hash::make('password'),
            'role_id' => $rolePustakawan->id,
            'phone' => '081298765432',
            'status' => 'active',
        ]);

        $siswa1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'siswa@smkpgri.sch.id',
            'password' => Hash::make('password'),
            'role_id' => $roleSiswa->id,
            'phone' => '085712345678',
            'status' => 'active',
        ]);

        $siswa2 = User::create([
            'name' => 'Anita Wijaya',
            'email' => 'anita@smkpgri.sch.id',
            'password' => Hash::make('password'),
            'role_id' => $roleSiswa->id,
            'phone' => '085799988877',
            'status' => 'active',
        ]);

        // Seed Anggota details
        Anggota::create([
            'user_id' => $siswa1->id,
            'nomor_anggota' => 'LIB-2026-001',
            'nim' => '1022014001',
            'program_studi' => 'Teknik Komputer & Jaringan (TKJ)',
            'status' => 'aktif',
        ]);

        Anggota::create([
            'user_id' => $siswa2->id,
            'nomor_anggota' => 'LIB-2026-002',
            'nim' => '1022014002',
            'program_studi' => 'Rekayasa Perangkat Lunak (RPL)',
            'status' => 'aktif',
        ]);

        // 4. Seed Official Categories (Struktur Klasifikasi Resmi)
        $catTKJ = Kategori::create(['nama' => 'Teknik Komputer & Jaringan', 'slug' => 'tkj', 'deskripsi' => 'Buku jaringan komputer, router, mikrotik, server, dan keamanan siber']);
        $catRPL = Kategori::create(['nama' => 'Rekayasa Perangkat Lunak', 'slug' => 'rpl', 'deskripsi' => 'Buku pemetaan web, pemrograman dasar, basis data, dan aplikasi mobile']);
        $catAK = Kategori::create(['nama' => 'Akuntansi & Keuangan', 'slug' => 'akuntansi', 'deskripsi' => 'Perbankan, pembukuan keuangan, dan perpajakan sekolah']);
        $catUM = Kategori::create(['nama' => 'Pelajaran Umum & Sastra', 'slug' => 'umum-sastra', 'deskripsi' => 'Bahasa Indonesia, Matematika SMK, Bahasa Inggris, dan Novel']);

        // 5. Seed Racks (Fasilitas Fisik Perpustakaan)
        $rakA1 = Rak::create(['kode_rak' => 'RAK-TKJ-01', 'nama_rak' => 'Rak Komputer & Jaringan', 'lokasi' => 'Lantai 1 - Gedung Utama', 'kategori_id' => $catTKJ->id, 'status' => 'aktif']);
        $rakA2 = Rak::create(['kode_rak' => 'RAK-RPL-01', 'nama_rak' => 'Rak Pemrograman & Web', 'lokasi' => 'Lantai 1 - Gedung Utama', 'kategori_id' => $catRPL->id, 'status' => 'aktif']);
        $rakB1 = Rak::create(['kode_rak' => 'RAK-UM-01', 'nama_rak' => 'Rak Mata Pelajaran Umum', 'lokasi' => 'Lantai 2 - Ruang Baca', 'kategori_id' => $catUM->id, 'status' => 'aktif']);

        // 6. Seed Real Authors & Publishers
        $penulis1 = Penulis::create(['nama' => 'Dwi Ahmad, S.T.', 'biografi' => 'Praktisi Jaringan Komputer dan Guru Kejuruan SMK']);
        $penulis2 = Penulis::create(['nama' => 'Robert C. Martin', 'biografi' => 'Penulis Clean Code dan Arsitektur Software']);
        $penulis3 = Penulis::create(['nama' => 'Drs. Supriyanto, M.M.', 'biografi' => 'Penulis Buku Pelajaran Kejuruan Produktif SMK']);

        $penerbit1 = Penerbit::create(['nama' => 'Erlangga', 'kota' => 'Jakarta']);
        $penerbit2 = Penerbit::create(['nama' => 'Informatika Bandung', 'kota' => 'Bandung']);
        $penerbit3 = Penerbit::create(['nama' => 'Andi Publisher', 'kota' => 'Yogyakarta']);

        // 7. Seed Authentic Real Books
        $buku1 = Buku::create([
            'isbn' => '978-602-244-123-4',
            'judul' => 'Pemrograman Web dan Perangkat Bergerak Kelas XII SMK',
            'penulis_id' => $penulis3->id,
            'penerbit_id' => $penerbit1->id,
            'kategori_id' => $catRPL->id,
            'rak_id' => $rakA2->id,
            'tahun_terbit' => 2022,
            'sinopsis' => 'Buku teks pembelajaran Kurikulum Merdeka Keahlian RPL mencakup dasar HTML, CSS, JavaScript, PHP, dan framework modern.',
            'view_count' => 184,
            'status' => 'tersedia',
        ]);

        $buku2 = Buku::create([
            'isbn' => '978-602-8759-41-0',
            'judul' => 'Administrasi Infrastruktur Jaringan (AIJ) SMK TKJ',
            'penulis_id' => $penulis1->id,
            'penerbit_id' => $penerbit2->id,
            'kategori_id' => $catTKJ->id,
            'rak_id' => $rakA1->id,
            'tahun_terbit' => 2021,
            'sinopsis' => 'Panduan praktis konfigurasi VLAN, Routing MikroTik, Firewall, dan manajemen bandwidth untuk siswa kejuruan TKJ.',
            'view_count' => 142,
            'status' => 'tersedia',
        ]);

        $buku3 = Buku::create([
            'isbn' => '978-602-001-555-8',
            'judul' => 'Clean Code: Panduan Pemrograman Rapi & Efisien',
            'penulis_id' => $penulis2->id,
            'penerbit_id' => $penerbit3->id,
            'kategori_id' => $catRPL->id,
            'rak_id' => $rakA2->id,
            'tahun_terbit' => 2020,
            'sinopsis' => 'Prinsip-prinsip penulisan sintaks program yang rapi, efisien, dan mudah dirawat untuk calon programmer muda profesional.',
            'view_count' => 96,
            'status' => 'tersedia',
        ]);

        // 8. Seed Real Physical Exemplars
        $ex1 = Eksemplar::create(['buku_id' => $buku1->id, 'kode_eksemplar' => 'EX-WEB-001', 'barcode' => 'BC882001', 'kondisi' => 'baik', 'rak_id' => $rakA2->id, 'status' => 'dipinjam']);
        $ex2 = Eksemplar::create(['buku_id' => $buku1->id, 'kode_eksemplar' => 'EX-WEB-002', 'barcode' => 'BC882002', 'kondisi' => 'baik', 'rak_id' => $rakA2->id, 'status' => 'tersedia']);
        $ex3 = Eksemplar::create(['buku_id' => $buku2->id, 'kode_eksemplar' => 'EX-AIJ-001', 'barcode' => 'BC771001', 'kondisi' => 'baik', 'rak_id' => $rakA1->id, 'status' => 'tersedia']);
        $ex4 = Eksemplar::create(['buku_id' => $buku3->id, 'kode_eksemplar' => 'EX-CC-001', 'barcode' => 'BC661001', 'kondisi' => 'baik', 'rak_id' => $rakA2->id, 'status' => 'tersedia']);

        // 9. Seed Active Real Transactions
        $pinjam1 = Peminjaman::create([
            'kode_peminjaman' => 'TRX-202608-001',
            'user_id' => $siswa1->id,
            'buku_id' => $buku1->id,
            'eksemplar_id' => $ex1->id,
            'tanggal_pinjam' => '2026-08-05',
            'tanggal_jatuh_tempo' => '2026-08-12',
            'jumlah_perpanjangan' => 0,
            'status' => 'dipinjam',
            'petugas_id' => $pustakawan->id,
        ]);

        $pinjamOld = Peminjaman::create([
            'kode_peminjaman' => 'TRX-202607-099',
            'user_id' => $siswa1->id,
            'buku_id' => $buku2->id,
            'eksemplar_id' => $ex3->id,
            'tanggal_pinjam' => '2026-07-10',
            'tanggal_jatuh_tempo' => '2026-07-17',
            'jumlah_perpanjangan' => 0,
            'status' => 'dikembalikan',
            'petugas_id' => $pustakawan->id,
        ]);

        Pengembalian::create([
            'peminjaman_id' => $pinjamOld->id,
            'tanggal_kembali' => '2026-07-19',
            'hari_keterlambatan' => 2,
            'denda_keterlambatan' => 4000,
            'denda_kerusakan_kehilangan' => 0,
            'total_denda' => 4000,
            'petugas_id' => $pustakawan->id,
        ]);

        Denda::create([
            'peminjaman_id' => $pinjamOld->id,
            'user_id' => $siswa1->id,
            'jumlah_denda' => 4000,
            'alasan' => 'Keterlambatan 2 Hari',
            'status_pembayaran' => 'belum_lunas',
        ]);

        Reservasi::create([
            'kode_reservasi' => 'RES-2026-001',
            'user_id' => $siswa2->id,
            'buku_id' => $buku1->id,
            'posisi_antrean' => 1,
            'status' => 'menunggu',
            'tanggal_reservasi' => '2026-08-10',
        ]);

        // 10. Seed Notifications & Audit Logs
        Notifikasi::create([
            'user_id' => $siswa1->id,
            'judul' => 'Pengingat Jatuh Tempo Peminjaman',
            'pesan' => 'Peminjaman buku Pemrograman Web jatuh tempo pada hari ini (12 Aug 2026). Harap segera dikembalikan ke meja pustakawan.',
            'tipe' => 'jatuh_tempo',
            'dibaca' => false,
        ]);

        AuditLog::create([
            'user_id' => $admin->id,
            'user_name' => $admin->name,
            'aktivitas' => 'SYSTEM_INIT',
            'deskripsi' => 'Inisialisasi sistem perpustakaan SMK PGRI sukses.',
            'ip_address' => '127.0.0.1',
        ]);

        // 11. Seed Dynamic Configuration Rules
        $settings = [
            ['key' => 'max_buku_pinjam', 'value' => '3', 'label' => 'Maksimal Buku Dipinjam Per Siswa', 'tipe' => 'number'],
            ['key' => 'durasi_pinjam_hari', 'value' => '7', 'label' => 'Durasi Peminjaman Standar (Hari)', 'tipe' => 'number'],
            ['key' => 'max_perpanjangan', 'value' => '2', 'label' => 'Maksimal Perpanjangan Online', 'tipe' => 'number'],
            ['key' => 'denda_per_hari', 'value' => '2000', 'label' => 'Denda Keterlambatan Per Hari (Rp)', 'tipe' => 'number'],
            ['key' => 'denda_buku_rusak', 'value' => '30000', 'label' => 'Denda Buku Rusak (Rp)', 'tipe' => 'number'],
            ['key' => 'denda_buku_hilang', 'value' => '100000', 'label' => 'Denda Buku Hilang (Rp)', 'tipe' => 'number'],
            ['key' => 'nama_perpustakaan', 'value' => 'Perpustakaan SMK PGRI', 'label' => 'Nama Resmi Perpustakaan', 'tipe' => 'text'],
            ['key' => 'jam_operasional', 'value' => 'Senin - Jumat: 07.00 - 15.30 WIB | Sabtu: 07.00 - 12.00 WIB', 'label' => 'Informasi Jam Operasional Perpustakaan', 'tipe' => 'text'],
        ];

        foreach ($settings as $s) {
            Pengaturan::create($s);
        }
    }
}
