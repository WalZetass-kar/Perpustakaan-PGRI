# Sistem Informasi Perpustakaan Digital Sekolah
### Katalog OPAC & Panel Manajemen Sirkulasi Back-Office

Sistem Informasi Manajemen Perpustakaan dan Katalog Publik (OPAC) modern yang dirancang khusus untuk mendukung operasional perpustakaan sekolah, penataan inventaris fisik, visualisasi denah lokasi rak & laci (Physical Wayfinding), serta pencatatan sirkulasi peminjaman siswa secara terintegrasi.

---

## 1. Dokumentasi Lengkap Serah Terima Proyek

Untuk mempermudah teknisi sekolah dalam melakukan pemasangan dan membantu pustakawan dalam pengoperasian sistem, telah disediakan dokumen panduan terpisah:

1. **[Panduan Pemasangan & Pemeliharaan untuk Teknisi IT](PANDUAN_SERVER_LOKAL/PANDUAN_PEMASANGAN_TEKNISI.md)**:
   - Persyaratan server & ekstensi PHP
   - Langkah demi langkah instalasi, migrasi, dan konfigurasi `.env`
   - Pengaturan Web Server (Apache VirtualHost, Nginx, dan cPanel)
   - Otomasi pencadangan database (Cron Job) & pemecahan masalah (Troubleshooting)
2. **[Panduan Penggunaan untuk Petugas Perpustakaan](PANDUAN_SERVER_LOKAL/PANDUAN_PENGGUNAAN_PETUGAS.md)**:
   - Alur pengelolaan master buku, kategori, penulis, penerbit, dan kelas
   - Manajemen rak buku & peta denah interaktif 2D
   - Alur persetujuan peminjaman online siswa & pencatatan langsung (Walk-in)
   - Pelacakan keterlambatan, perpanjangan, dan pengembalian buku
   - Rekapitulasi laporan Excel dan cetak PDF resmi format A4
   - Pengaturan instansi & fitur pencadangan database mandiri
3. **[Panduan Server Lokal (Satu Komputer sebagai Server)](PANDUAN_SERVER_LOKAL/)**:
   - Menjadikan satu komputer sekolah sebagai server tanpa hosting internet
   - Akses dari komputer petugas & HP siswa melalui jaringan LAN / WiFi
   - Otomatisasi server menyala sendiri, pencadangan berkala, dan pemecahan masalah

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
- **Manajemen Akun & Kendali Kata Sandi Terpusat**: Pembagian hak akses Super Admin dan Petugas. Penggantian kata sandi dipusatkan pada Super Admin — Super Admin dapat mengubah kata sandinya sendiri melalui halaman Profil & Keamanan sekaligus mereset kata sandi akun Petugas, sedangkan Petugas menghubungi Super Admin bila perlu penggantian.

---

## 3. Spesifikasi & Teknologi

- **Backend Framework**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL 5.7+ / 8.0+ atau MariaDB 10.4+
- **Ekstensi PHP Wajib Aktif**: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `json`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `zip`
  > Periksa dengan `php -m`. Tanpa `gd` thumbnail cover gagal dibuat, tanpa `zip` fitur backup ZIP tidak jalan.
- **Frontend**: Blade Templating, Tailwind CSS, Alpine.js
- **Aset & Icon**: FontAwesome 6, Chart.js, SweetAlert2, AOS (Tersimpan lokal di `public/vendor/` untuk performa 100% luring)
- **Ekspor Dokumen**: PhpSpreadsheet & Blade HTML Print

---

## 4. Panduan Singkat Menjalankan di Lingkungan Lokal

> Butuh panduan yang jauh lebih rinci, termasuk cara menjadikan satu komputer
> sebagai server yang diakses komputer lain lewat jaringan sekolah? Lihat folder
> **[PANDUAN_SERVER_LOKAL/](PANDUAN_SERVER_LOKAL/)**.

### Langkah 1: Kloning Repositori
```bash
git clone https://github.com/WalZetass-kar/Perpustakaan-PGRI.git
cd Perpustakaan-PGRI
```

### Langkah 2: Instalasi Dependensi
```bash
composer install
```
> Folder `vendor/` sudah disertakan di dalam repositori, sehingga langkah ini
> dapat dilewati jika Composer belum terpasang di komputer Anda.

### Langkah 3: Membuat Database Kosong
Basis data harus sudah ada sebelum migrasi dijalankan.
```bash
mysql -u root -p -e "CREATE DATABASE perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Langkah 4: Konfigurasi File Lingkungan (.env)
```bash
cp .env.example .env
php artisan key:generate
```
Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada berkas `.env`.

> **Penting untuk komputer pengembangan:** `.env.example` disetel untuk server
> produksi (`APP_ENV=production`, `APP_DEBUG=false`) sehingga pesan galat
> disembunyikan. Untuk pengembangan lokal, ubah menjadi:
> ```env
> APP_ENV=local
> APP_DEBUG=true
> ```

### Langkah 5: Migrasi & Data Awal
```bash
php artisan migrate --seed
```
> **Perhatian:** seeder membuat akun `admin@sekolah.sch.id` memakai nilai
> `SEED_ADMIN_PASSWORD` dari `.env`. Jika dibiarkan kosong, sistem membuat
> password acak yang **tidak pernah ditampilkan** dan akun tersebut menjadi
> tidak dapat diakses. Isi `SEED_ADMIN_PASSWORD` terlebih dahulu, atau lewati
> peringatan ini dan buat akun sendiri pada Langkah 6.

### Langkah 6: Buat Akun Administrator
```bash
php artisan perpus:buat-admin
```

### Langkah 7: Buat Symbolic Link Storage
```bash
php artisan storage:link
```
Tanpa langkah ini, gambar sampul buku tidak akan tampil.

### Langkah 8: Jalankan Server Pengembangan
```bash
php artisan serve
```
- **Katalog OPAC**: `http://localhost:8000/`
- **Panel Admin**: `http://localhost:8000/akses-perpustakaan`

> Alamat panel admin mengikuti `ADMIN_LOGIN_PATH` pada `.env`
> (bawaan: `akses-perpustakaan`).

---

## 5. Daftar Perintah Artisan Khusus

| Perintah | Deskripsi Fungsi |
|---|---|
| `php artisan perpus:buat-admin` | Membuat akun Super Admin atau Petugas baru secara interaktif melalui CLI |
| `php artisan perpus:reset-password` | Mereset kata sandi akun admin dalam kondisi darurat |
| `php artisan perpus:backup` | Mencadangkan basis data ke berkas SQL di `storage/app/backups/` |
| `php artisan perpus:backup --zip` | Mencadangkan basis data beserta seluruh berkas cover buku ke arsip ZIP |
| `php artisan covers:regenerate` | Menghasilkan ulang varian thumbnail cover buku |

---

## 6. Hak Cipta & Lisensi

Sistem Informasi Perpustakaan Digital Sekolah. Seluruh hak kepemilikan dan pemeliharaan diserahkan kepada pihak sekolah sesuai Berita Acara Serah Terima (BAST).
