# Sistem Perpustakaan SMK PGRI Pekanbaru

Sistem Informasi Manajemen Perpustakaan dan Katalog Publik (OPAC) modern yang dirancang khusus untuk mendukung operasional perpustakaan, pencatatan sirkulasi buku fisik, serta penunjuk lokasi rak & laci (Physical Wayfinding) di SMK PGRI Pekanbaru.

---

## Fitur Utama Sistem

### 1. Katalog Publik & OPAC (Online Public Access Catalog)
* **Pencarian Koleksi Cepat**: Pencarian instan berbasis judul, pengarang, penerbit, ISBN, subjek kategori, dan tahun terbit.
* **Status Ketersediaan Real-Time**: Informasi stok buku fisik yang siap dipinjam vs sedang dipinjam oleh siswa.
* **Autosuggestion & Filter Dinamis**: Saran pencarian otomatis dan penyaringan berdasarkan kategori dan lokasi rak.

### 2. Fitur Wayfinding Lokasi Fisik Buku
* **Navigasi Cepat (2–3 Detik)**: Alur lokasi berurutan dari `Lantai & Ruangan` &rarr; `Lemari Rak` &rarr; `Laci Tujuan (Endpoint)`.
* **Kode Lokasi Terstandarisasi**: Format lokasi ringkas (contoh: `L1 · RAK-TKJ-01 · L01`).
* **Modal Peta Rak Interaktif**: Fitur visual denah tata letak lemari rak di ruangan perpustakaan yang menandai secara dinamis posisi rak dan laci target buku.

### 3. Portal Back-Office & Manajemen Sirkulasi
* **Fitur Mandiri "Temukan Buku"**: Menu lokator buku mandiri pada sidebar admin untuk membantu pustakawan menemukan letak buku secara instan.
* **Sirkulasi Peminjaman Fisik**:
  * Pencatatan peminjaman cepat di meja sirkulasi menggunakan data nama peminjam, kelas/jurusan, dan NIS.
  * Pelacakan jatuh tempo pinjaman, perpanjangan, dan pengembalian buku dengan validasi otomatis stok.
* **Manajemen Master Data Lengkap**:
  * Pengelolaan Koleksi Buku beserta cover dan spesifikasi bibliografi.
  * Pengelolaan Kategori Subjek, Penulis, dan Penerbit.
  * Pengelolaan Lemari Rak dan Tingkat Laci (*Multi-tier Drawers*).
* **Sidebar Fleksibel & Mini Sidebar Mode**:
  * Dukungan buka/tutup (*collapsible*) pada desktop dan mobile.
  * Mode mini sidebar (`w-20`) dengan logo tengah dan ikon menu ber-tooltip.
* **Keamanan & Audit Log**:
  * Role-based access control (`super_admin` dan `admin`).
  * Pencatatan riwayat aktivitas penting pengguna (*Audit Trail*).
  * Perlindungan headers keamanan standar web.

---

## Teknologi yang Digunakan

* **Backend**: PHP 8.2+, Laravel 11.x
* **Database**: MySQL / MariaDB (XAMPP / LAMPP)
* **Frontend**: Blade Templating, TailwindCSS, Alpine.js
* **Komponen & Dialog**: SweetAlert2, Plus Jakarta Sans & JetBrains Mono Fonts

---

## Struktur Database Inti

Sistem menggunakan 19 tabel aktif yang terorganisir dan bersih:
* **Master Buku & Lokasi**: `buku`, `kategori`, `penulis`, `penerbit`, `rak`, `rak_laci`
* **Transaksi Sirkulasi**: `peminjaman`
* **Akun & Konfigurasi**: `users`, `roles`, `audit_logs`, `pengaturan`
* **Sistem Laravel**: `migrations`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_reset_tokens`

---

## Panduan Instalasi & Menjalankan Proyek

### 1. Prasyarat Sistem
* PHP >= 8.2 dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`
* Composer
* Web Server & Database (MySQL / MariaDB via XAMPP/LAMPP)
* Node.js & NPM (opsional jika memerlukan build asset tambahan)

### 2. Kloning Repositori
```bash
git clone https://github.com/WalZetass-kar/Perpustakaan-PGRI.git
cd Perpustakaan-PGRI
```

### 3. Instalasi Dependensi
```bash
composer install
```

### 4. Konfigurasi Environment (`.env`)
Salin file konfigurasi environment dan sesuaikan pengaturan database:
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan baris konfigurasi database di file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpustakaan_pgri
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan Migrasi & Database Seeder
Pastikan MySQL service di LAMPP/XAMPP telah berjalan, kemudian jalankan:
```bash
php artisan migrate --seed
```

### 6. Buat Symlink Storage Cover Buku
```bash
php artisan storage:link
```

### 7. Jalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser:
* **Katalog Publik (OPAC)**: `http://localhost:8000/`
* **Portal Login Admin**: `http://localhost:8000/aksesperpuspgri`

---

## Akun Default Masuk Admin

* **URL Login**: `/aksesperpuspgri`
* **Email**: `admin@smkpgri.sch.id`
* **Password**: `password123` (atau sesuai konfigurasi seeder awal)

---

## Lisensi & Hak Cipta

Dikembangkan untuk SMK PGRI Pekanbaru. Hak cipta dilindungi undang-undang.
