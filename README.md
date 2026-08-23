# Sistem Informasi Perpustakaan Digital Sekolah
### Katalog OPAC & Panel Manajemen Sirkulasi Back-Office

Sistem Informasi Manajemen Perpustakaan dan Katalog Publik (OPAC) modern yang dirancang khusus untuk mendukung operasional perpustakaan sekolah, penataan inventaris fisik, visualisasi denah lokasi rak & laci (Physical Wayfinding), serta pencatatan sirkulasi peminjaman siswa secara terintegrasi.

---

## 1. Dokumentasi Lengkap Serah Terima Proyek

Untuk mempermudah teknisi sekolah dalam melakukan pemasangan dan membantu pustakawan dalam pengoperasian sistem, telah disediakan dokumen panduan terpisah:

1. **[Panduan Pemasangan & Pemeliharaan untuk Teknisi IT](PANDUAN_PEMASANGAN_TEKNISI.md)**:
   - Persyaratan server & ekstensi PHP
   - Langkah demi langkah instalasi, migrasi, dan konfigurasi `.env`
   - Pengaturan Web Server (Apache VirtualHost, Nginx, dan cPanel)
   - Otomasi pencadangan database (Cron Job) & pemecahan masalah (Troubleshooting)
2. **[Panduan Penggunaan untuk Petugas Perpustakaan](PANDUAN_PENGGUNAAN_PETUGAS.md)**:
   - Alur pengelolaan master buku, kategori, penulis, penerbit, dan kelas
   - Manajemen rak buku & peta denah interaktif 2D
   - Alur persetujuan peminjaman online siswa & pencatatan langsung (Walk-in)
   - Pelacakan keterlambatan, perpanjangan, dan pengembalian buku
   - Rekapitulasi laporan Excel dan cetak PDF resmi format A4
   - Pengaturan instansi & fitur pencadangan database mandiri
3. **[Template Berita Acara Serah Terima (BAST)](BERITA_ACARA_SERAH_TERIMA.md)**:
   - Format berita acara serah terima pekerjaan antara pengembang dan pihak sekolah

---

## 2. Fitur Utama Sistem

### A. Katalog Publik & OPAC (Online Public Access Catalog)
- **Pencarian Cepat & Instan**: Pencarian berbasis judul, penulis, penerbit, ISBN, kelas sasaran, dan kategori modul.
- **Ketersediaan Stok Real-Time**: Status stok fisik siap pinjam vs sedang dipinjam yang selalu sinkron.
- **Penunjuk Lokasi Rak & Laci**: Informasi lokasi rak dan tingkat laci fisik pada setiap kartu buku.
- **Formulir Pengajuan Peminjaman Mandiri**: Siswa dapat mengajukan peminjaman buku langsung dari halaman detail buku dengan validasi batas pinjaman aktif.

### B. Portal Back-Office Pengelola Perpustakaan
- **Dashboard KPI Sirkulasi**: Metrik sirkulasi hari ini, buku sedang dipinjam, pengembalian, dan penghitung buku terlambat jatuh tempo.
- **Visualisasi Grafik Aktivitas**: Grafik aktivitas sirkulasi bulanan dan tahunan.
- **Manajemen Inventaris Koleksi**: Penambahan, pengubahan, dan penghapusan buku dengan proteksi transaksi aktif.
- **Denah Lokasi Rak 2D (Wayfinding)**: Tampilan visual tata letak rak dan laci perpustakaan.
- **Sirkulasi & Overdue Tracker**: Penyaringan status peminjaman (Semua, Aktif, Terlambat, Selesai, Pending) serta perpanjangan masa pinjam terukur.
- **Rekapitulasi Laporan Formal**:
  - Ekspor Excel koleksi buku dengan format kolom rapi.
  - Cetak PDF laporan inventaris buku dan sirkulasi peminjaman (A4 Portrait) lengkap dengan Kop Surat Instansi dan blok tanda tangan formal 2 kolom simetris.
- **Pencadangan Database Terintegrasi**: Fasilitas unduh salinan basis data SQL langsung dari panel admin dan melalui CLI.
- **Manajemen Akun & Keamanan Mandiri**: Pembagian hak akses (Super Admin dan Petugas) serta halaman ganti kata sandi mandiri.

---

## 3. Spesifikasi & Teknologi

- **Backend Framework**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL 5.7+ / 8.0+ atau MariaDB 10.4+
- **Frontend**: Blade Templating, Tailwind CSS, Alpine.js
- **Aset & Icon**: FontAwesome 6, Chart.js, SweetAlert2, AOS (Tersimpan lokal di `public/vendor/` untuk performa 100% luring)
- **Ekspor Dokumen**: PhpSpreadsheet & Blade HTML Print

---

## 4. Panduan Singkat Menjalankan di Lingkungan Lokal

### Langkah 1: Kloning Repositori
```bash
git clone https://github.com/WalZetass-kar/Perpustakaan-PGRI.git
cd Perpustakaan-PGRI
```

### Langkah 2: Instalasi Dependensi
```bash
composer install
```

### Langkah 3: Konfigurasi File Lingkungan (.env)
```bash
cp .env.example .env
php artisan key:generate
```
Sesuaikan konfigurasi database `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada berkas `.env`.

### Langkah 4: Migrasi & Seeder Database
```bash
php artisan migrate --seed
```

### Langkah 5: Buat Akun Administrator
```bash
php artisan perpus:buat-admin
```

### Langkah 6: Buat Symbolic Link Storage
```bash
php artisan storage:link
```

### Langkah 7: Jalankan Server Pengembangan
```bash
php artisan serve
```
- **Katalog OPAC**: `http://localhost:8000/`
- **Panel Admin**: `http://localhost:8000/akses-perpustakaan`

---

## 5. Daftar Perintah Artisan Khusus

| Perintah | Deskripsi Fungsi |
|---|---|
| `php artisan perpus:buat-admin` | Membuat akun Super Admin atau Petugas baru secara interaktif melalui CLI |
| `php artisan perpus:reset-password` | Mereset kata sandi akun admin dalam kondisi darurat |
| `php artisan perpus:backup` | Mencadangkan basis data ke berkas SQL di `storage/app/backups/` |
| `php artisan perpus:backup --zip` | Mencadangkan basis data beserta seluruh berkas cover buku ke arsip ZIP |
| `php artisan perpus:regenerate-covers` | Menghasilkan ulang varian thumbnail cover buku |

---

## 6. Hak Cipta & Lisensi

Sistem Informasi Perpustakaan Digital Sekolah. Seluruh hak kepemilikan dan pemeliharaan diserahkan kepada pihak sekolah sesuai Berita Acara Serah Terima (BAST).
