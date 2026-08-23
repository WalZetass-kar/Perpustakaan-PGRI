<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Penulis;
use App\Models\Penerbit;
use App\Models\Kategori;
use App\Models\Rak;
use App\Models\RakLaci;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\AuditLog;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roleSuperAdmin = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['display_name' => 'Super Administrator']
        );

        $roleAdmin = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Admin Perpustakaan']
        );

        $seedPassword = env('SEED_ADMIN_PASSWORD') ?: \Illuminate\Support\Str::random(12);

        $admin = User::firstOrCreate(
            ['email' => 'admin@sekolah.sch.id'],
            [
                'name' => 'Administrator Sekolah',
                'password' => Hash::make($seedPassword),
                'role_id' => $roleSuperAdmin->id,
                'phone' => '081234567890',
                'status' => 'active',
            ]
        );

        $pustakawan = User::firstOrCreate(
            ['email' => 'pustakawan@sekolah.sch.id'],
            [
                'name' => 'Staf Perpustakaan',
                'password' => Hash::make($seedPassword),
                'role_id' => $roleAdmin->id,
                'phone' => '081298765432',
                'status' => 'active',
            ]
        );

        $catTKJ = Kategori::firstOrCreate(
            ['slug' => 'tkj'],
            [
                'nama' => 'Teknik Komputer & Jaringan',
                'deskripsi' => 'Buku jaringan komputer, router, mikrotik, server, dan keamanan siber',
            ]
        );

        $catRPL = Kategori::firstOrCreate(
            ['slug' => 'rpl'],
            [
                'nama' => 'Rekayasa Perangkat Lunak',
                'deskripsi' => 'Buku pemetaan web, pemrograman dasar, basis data, dan aplikasi mobile',
            ]
        );

        $catAK = Kategori::firstOrCreate(
            ['slug' => 'akuntansi'],
            [
                'nama' => 'Akuntansi & Keuangan',
                'deskripsi' => 'Perbankan, pembukuan keuangan, dan perpajakan sekolah',
            ]
        );

        $catUM = Kategori::firstOrCreate(
            ['slug' => 'umum-sastra'],
            [
                'nama' => 'Pelajaran Umum & Sastra',
                'deskripsi' => 'Bahasa Indonesia, Matematika SMK, Bahasa Inggris, dan Novel',
            ]
        );

        $rakTKJ = Rak::firstOrCreate(
            ['kode_rak' => 'RAK-TKJ-01'],
            [
                'nama_rak' => 'Rak Komputer & Jaringan',
                'lokasi' => 'Lantai 1 - Gedung Utama',
                'kategori_id' => $catTKJ->id,
                'status' => 'aktif',
            ]
        );

        $rakRPL = Rak::firstOrCreate(
            ['kode_rak' => 'RAK-RPL-01'],
            [
                'nama_rak' => 'Rak Pemrograman & Web',
                'lokasi' => 'Lantai 1 - Gedung Utama',
                'kategori_id' => $catRPL->id,
                'status' => 'aktif',
            ]
        );

        $rakUM = Rak::firstOrCreate(
            ['kode_rak' => 'RAK-UM-01'],
            [
                'nama_rak' => 'Rak Mata Pelajaran Umum',
                'lokasi' => 'Lantai 2 - Ruang Baca',
                'kategori_id' => $catUM->id,
                'status' => 'aktif',
            ]
        );

        $laciTKJ1 = RakLaci::firstOrCreate(
            ['rak_id' => $rakTKJ->id, 'nomor_laci' => 1],
            ['nama_laci' => 'Laci 1', 'keterangan' => 'Tingkat 1 pada Rak Komputer & Jaringan']
        );
        $laciTKJ2 = RakLaci::firstOrCreate(
            ['rak_id' => $rakTKJ->id, 'nomor_laci' => 2],
            ['nama_laci' => 'Laci 2', 'keterangan' => 'Tingkat 2 pada Rak Komputer & Jaringan']
        );
        $laciTKJ3 = RakLaci::firstOrCreate(
            ['rak_id' => $rakTKJ->id, 'nomor_laci' => 3],
            ['nama_laci' => 'Laci 3', 'keterangan' => 'Tingkat 3 pada Rak Komputer & Jaringan']
        );

        $laciRPL1 = RakLaci::firstOrCreate(
            ['rak_id' => $rakRPL->id, 'nomor_laci' => 1],
            ['nama_laci' => 'Laci 1', 'keterangan' => 'Tingkat 1 pada Rak Pemrograman & Web']
        );
        $laciRPL2 = RakLaci::firstOrCreate(
            ['rak_id' => $rakRPL->id, 'nomor_laci' => 2],
            ['nama_laci' => 'Laci 2', 'keterangan' => 'Tingkat 2 pada Rak Pemrograman & Web']
        );
        $laciRPL3 = RakLaci::firstOrCreate(
            ['rak_id' => $rakRPL->id, 'nomor_laci' => 3],
            ['nama_laci' => 'Laci 3', 'keterangan' => 'Tingkat 3 pada Rak Pemrograman & Web']
        );

        $laciUM1 = RakLaci::firstOrCreate(
            ['rak_id' => $rakUM->id, 'nomor_laci' => 1],
            ['nama_laci' => 'Laci 1', 'keterangan' => 'Tingkat 1 pada Rak Mata Pelajaran Umum']
        );
        $laciUM2 = RakLaci::firstOrCreate(
            ['rak_id' => $rakUM->id, 'nomor_laci' => 2],
            ['nama_laci' => 'Laci 2', 'keterangan' => 'Tingkat 2 pada Rak Mata Pelajaran Umum']
        );
        $laciUM3 = RakLaci::firstOrCreate(
            ['rak_id' => $rakUM->id, 'nomor_laci' => 3],
            ['nama_laci' => 'Laci 3', 'keterangan' => 'Tingkat 3 pada Rak Mata Pelajaran Umum']
        );

        $penulis1 = Penulis::firstOrCreate(
            ['nama' => 'Dwi Ahmad, S.T.'],
            ['biografi' => 'Praktisi Jaringan Komputer dan Guru Kejuruan SMK']
        );
        $penulis2 = Penulis::firstOrCreate(
            ['nama' => 'Robert C. Martin'],
            ['biografi' => 'Penulis Clean Code dan Arsitektur Software']
        );
        $penulis3 = Penulis::firstOrCreate(
            ['nama' => 'Drs. Supriyanto, M.M.'],
            ['biografi' => 'Penulis Buku Pelajaran Kejuruan Produktif SMK']
        );

        $penerbit1 = Penerbit::firstOrCreate(
            ['nama' => 'Erlangga'],
            ['kota' => 'Jakarta']
        );
        $penerbit2 = Penerbit::firstOrCreate(
            ['nama' => 'Informatika Bandung'],
            ['kota' => 'Bandung']
        );
        $penerbit3 = Penerbit::firstOrCreate(
            ['nama' => 'Andi Publisher'],
            ['kota' => 'Yogyakarta']
        );

        $buku1 = Buku::firstOrCreate(
            ['isbn' => '978-602-244-123-4'],
            [
                'judul' => 'Pemrograman Web dan Perangkat Bergerak Kelas XII SMK',
                'penulis_id' => $penulis3->id,
                'penerbit_id' => $penerbit1->id,
                'kategori_id' => $catRPL->id,
                'rak_id' => $rakRPL->id,
                'rak_laci_id' => $laciRPL1->id,
                'tahun_terbit' => 2022,
                'total_quantity' => 5,
                'available_quantity' => 4,
                'sinopsis' => 'Buku teks pembelajaran Kurikulum Merdeka Keahlian RPL mencakup dasar HTML, CSS, JavaScript, PHP, dan framework modern.',
                'view_count' => 184,
                'status' => 'tersedia',
            ]
        );

        $buku2 = Buku::firstOrCreate(
            ['isbn' => '978-602-8759-41-0'],
            [
                'judul' => 'Administrasi Infrastruktur Jaringan (AIJ) SMK TKJ',
                'penulis_id' => $penulis1->id,
                'penerbit_id' => $penerbit2->id,
                'kategori_id' => $catTKJ->id,
                'rak_id' => $rakTKJ->id,
                'rak_laci_id' => $laciTKJ1->id,
                'tahun_terbit' => 2021,
                'total_quantity' => 4,
                'available_quantity' => 4,
                'sinopsis' => 'Panduan praktis konfigurasi VLAN, Routing MikroTik, Firewall, dan manajemen bandwidth untuk siswa kejuruan TKJ.',
                'view_count' => 142,
                'status' => 'tersedia',
            ]
        );

        $buku3 = Buku::firstOrCreate(
            ['isbn' => '978-602-001-555-8'],
            [
                'judul' => 'Clean Code: Panduan Pemrograman Rapi & Efisien',
                'penulis_id' => $penulis2->id,
                'penerbit_id' => $penerbit3->id,
                'kategori_id' => $catRPL->id,
                'rak_id' => $rakRPL->id,
                'rak_laci_id' => $laciRPL2->id,
                'tahun_terbit' => 2020,
                'total_quantity' => 3,
                'available_quantity' => 3,
                'sinopsis' => 'Prinsip-prinsip penulisan sintaks program yang rapi, efisien, dan mudah dirawat untuk calon programmer muda profesional.',
                'view_count' => 96,
                'status' => 'tersedia',
            ]
        );

        $buku4 = Buku::firstOrCreate(
            ['isbn' => '978-602-434-889-1'],
            [
                'judul' => 'Matematika Terapan untuk SMK/MAK Kelas XI',
                'penulis_id' => $penulis3->id,
                'penerbit_id' => $penerbit1->id,
                'kategori_id' => $catUM->id,
                'rak_id' => $rakUM->id,
                'rak_laci_id' => $laciUM1->id,
                'tahun_terbit' => 2023,
                'total_quantity' => 6,
                'available_quantity' => 6,
                'sinopsis' => 'Modul pembelajaran matematika kejuruan aplikatif untuk pemecahan masalah logika teknik dan bisnis manajemen.',
                'view_count' => 112,
                'status' => 'tersedia',
            ]
        );

        Peminjaman::firstOrCreate(
            ['kode_peminjaman' => 'TRX-202608-001'],
            [
                'nama_peminjam' => 'Budi Santoso',
                'jurusan' => 'Teknik Komputer & Jaringan (TKJ)',
                'nomor_induk' => '1022014001',
                'buku_id' => $buku1->id,
                'jumlah' => 1,
                'tanggal_pinjam' => '2026-08-05',
                'tanggal_jatuh_tempo' => '2026-08-12',
                'jumlah_perpanjangan' => 0,
                'status' => 'dipinjam',
                'petugas_id' => $pustakawan->id,
            ]
        );

        Peminjaman::firstOrCreate(
            ['kode_peminjaman' => 'TRX-202607-099'],
            [
                'nama_peminjam' => 'Anita Wijaya',
                'jurusan' => 'Rekayasa Perangkat Lunak (RPL)',
                'nomor_induk' => '1022014002',
                'buku_id' => $buku2->id,
                'jumlah' => 1,
                'tanggal_pinjam' => '2026-07-10',
                'tanggal_jatuh_tempo' => '2026-07-17',
                'waktu_kembali' => '2026-07-16 10:30:00',
                'jumlah_perpanjangan' => 0,
                'status' => 'dikembalikan',
                'petugas_id' => $pustakawan->id,
            ]
        );

        AuditLog::create([
            'user_id' => $admin->id,
            'user_name' => $admin->name,
            'aktivitas' => 'SYSTEM_INIT',
            'deskripsi' => 'Inisialisasi sistem perpustakaan sukses.',
            'ip_address' => '127.0.0.1',
        ]);

        $settings = [
            ['key' => 'max_buku_pinjam', 'value' => '3', 'label' => 'Maksimal Buku Dipinjam Per Siswa', 'tipe' => 'number'],
            ['key' => 'durasi_pinjam_hari', 'value' => '7', 'label' => 'Durasi Peminjaman Standar (Hari)', 'tipe' => 'number'],
            ['key' => 'max_perpanjangan', 'value' => '2', 'label' => 'Maksimal Perpanjangan Online', 'tipe' => 'number'],
            ['key' => 'nama_perpustakaan', 'value' => 'Sistem Perpustakaan Sekolah', 'label' => 'Nama Resmi Perpustakaan', 'tipe' => 'text'],
            ['key' => 'nama_sekolah', 'value' => 'SMK Negeri / Swasta', 'label' => 'Nama Sekolah', 'tipe' => 'text'],
            ['key' => 'jam_operasional', 'value' => 'Senin - Jumat: 07.00 - 15.30 WIB', 'label' => 'Informasi Jam Operasional', 'tipe' => 'text'],
            ['key' => 'pesan_sirkulasi', 'value' => 'Untuk meminjam buku fisik, silakan langsung menuju meja layanan sirkulasi perpustakaan.', 'label' => 'Petunjuk Sirkulasi', 'tipe' => 'text'],
        ];

        foreach ($settings as $s) {
            Pengaturan::firstOrCreate(
                ['key' => $s['key']],
                $s
            );
        }
    }
}
