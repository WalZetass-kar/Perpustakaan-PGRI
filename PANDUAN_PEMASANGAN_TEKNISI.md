# PANDUAN LENGKAP PEMASANGAN & KONEKSI DATABASE DI HOSTING & SERVER
## Sistem Informasi Perpustakaan Digital Sekolah (Katalog OPAC & Panel Admin)

Dokumen ini disusun sebagai panduan langkah-demi-langkah bagi teknisi IT sekolah atau pengelola web untuk memasang aplikasi di berbagai jenis lingkungan — jaringan lokal sekolah (LAN), hosting berbasis cPanel, VPS, maupun Cloud PaaS seperti Railway — mulai dari pembuatan basis data (database), menghubungkan koneksi, hingga aplikasi siap digunakan.

---

## PILIH SKENARIO PEMASANGAN

Aplikasi ini mendukung dua skenario utama. Pilih sesuai kebutuhan sekolah:

| Skenario | Keterangan | File Konfigurasi |
|---|---|---|
| **Lokal (LAN)** | Satu komputer di sekolah jadi server, komputer lain akses lewat jaringan WiFi/kabel lokal | `env.example.lokal` |
| **Hosting / Cloud** | Aplikasi diakses lewat internet via domain sekolah (cPanel, VPS, Railway, dll.) | `env.example.hosting` |

Kedua file contoh tersebut sudah tersedia di folder proyek. Salin salah satu sesuai pilihan dan ubah namanya menjadi `.env`.

---

## DAFTAR ISI
0. [Panduan Pemasangan di Jaringan Lokal Sekolah (LAN/XAMPP)](#0-panduan-pemasangan-di-jaringan-lokal-sekolah-lanxampp)
1. [Spesifikasi & Kebutuhan Server](#1-spesifikasi--kebutuhan-server)
2. [Panduan Pemasangan di cPanel / Shared Hosting](#2-panduan-pemasangan-di-cpanel--shared-hosting)
   - [Langkah 1: Membuat Database & User di cPanel](#langkah-1-membuat-database--user-di-cpanel)
   - [Langkah 2: Mengunggah Berkas Proyek](#langkah-2-mengunggah-berkas-proyek)
   - [Langkah 3: Konfigurasi File .env & Koneksi Database](#langkah-3-konfigurasi-file-env--koneksi-database)
   - [Langkah 4: Menjalankan Migrasi & Data Awal](#langkah-4-menjalankan-migrasi--data-awal)
   - [Langkah 5: Membuat Symlink Storage (Cover Buku)](#langkah-5-membuat-symlink-storage-cover-buku)
   - [Langkah 6: Membuat Akun Administrator](#langkah-6-membuat-akun-administrator)
3. [Panduan Pemasangan di VPS / Dedicated Linux (Ubuntu/Debian)](#3-panduan-pemasangan-di-vps--dedicated-linux-ubuntudebian)
4. [Panduan Pemasangan di Cloud PaaS (Railway / Docker Container)](#4-panduan-pemasangan-di-cloud-paas-railway--docker-container)
5. [Daftar Perintah Artisan untuk Pemeliharaan](#5-daftar-perintah-artisan-untuk-pemeliharaan)
6. [Panduan Pemecahan Masalah (Troubleshooting)](#6-panduan-pemecahan-masalah-troubleshooting)

---

## 0. PANDUAN PEMASANGAN DI JARINGAN LOKAL SEKOLAH (LAN/XAMPP)

Skenario ini cocok jika sekolah memiliki satu komputer yang dijadikan server dan komputer lain (di ruang perpustakaan, ruang guru, dll.) mengakses sistem via jaringan WiFi atau kabel lokal.

### Prasyarat
- Komputer server menggunakan **XAMPP** (Windows) atau **LAMPP** (Linux/Ubuntu)
- PHP 8.2+ dan MySQL sudah aktif di XAMPP/LAMPP
- Semua komputer klien terhubung ke jaringan yang sama (WiFi sekolah atau switch/hub)

---

### Langkah 1: Letakkan Folder Proyek di XAMPP

**Windows (XAMPP):**
1. Salin seluruh folder proyek ke: `C:\xampp\htdocs\perpustakaan`
2. Buka browser, akses `http://localhost/perpustakaan/public` — pastikan halaman muncul.

**Linux (LAMPP):**
1. Salin seluruh folder proyek ke: `/opt/lampp/htdocs/perpustakaan`
2. Buka browser, akses `http://localhost/perpustakaan/public` — pastikan halaman muncul.

> **Tips:** Agar URL lebih rapi tanpa `/public`, konfigurasi Virtual Host Apache di XAMPP/LAMPP sehingga document root mengarah langsung ke folder `public/` proyek.

---

### Langkah 2: Buat Database di phpMyAdmin

1. Buka `http://localhost/phpmyadmin` di browser komputer server.
2. Klik tab **Databases** (Basis Data).
3. Di kolom "Create database", ketik `perpustakaan` lalu pilih collation `utf8mb4_unicode_ci`, klik **Create**.

---

### Langkah 3: Konfigurasi File .env

1. Masuk ke folder proyek, salin file `env.example.lokal` dan ubah namanya menjadi `.env`:
   ```bash
   cp env.example.lokal .env
   ```
2. Buka file `.env`, cari dan ganti nilai `APP_URL`:
   ```env
   # Ganti 192.168.1.10 dengan IP komputer SERVER (bukan komputer yang sedang dipakai)
   APP_URL=http://192.168.1.10
   ```
3. Cara mengetahui IP komputer server:
   - **Windows**: Buka CMD → ketik `ipconfig` → lihat **IPv4 Address** (biasanya 192.168.x.x)
   - **Linux**: Buka Terminal → ketik `ip addr` → lihat nilai `inet` pada antarmuka `eth0` atau `wlan0`
4. Pastikan konfigurasi database sesuai (default XAMPP/LAMPP tidak pakai password):
   ```env
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=perpustakaan
   DB_USERNAME=root
   DB_PASSWORD=
   ```

---

### Langkah 4: Inisialisasi Aplikasi

Buka Terminal (Linux) atau Command Prompt (Windows), masuk ke folder proyek:

```bash
# Windows (jalankan di CMD, bukan PowerShell)
cd C:\xampp\htdocs\perpustakaan

# Linux
cd /opt/lampp/htdocs/perpustakaan
```

Jalankan perintah berikut satu per satu:

```bash
php artisan key:generate
php artisan migrate --seed --force
php artisan perpus:buat-admin
php artisan storage:link
```

---

### Langkah 5: Uji Akses dari Komputer Lain

1. Di komputer server, pastikan Apache dan MySQL di XAMPP/LAMPP **sudah berjalan**.
2. Di komputer klien (komputer lain di jaringan yang sama), buka browser.
3. Akses: `http://192.168.1.10/perpustakaan/public` (ganti IP sesuai IP server)
4. Halaman katalog seharusnya muncul.
5. Akses halaman admin: `http://192.168.1.10/perpustakaan/public/akses-perpustakaan`

> **Catatan Firewall:** Jika komputer klien tidak bisa mengakses server, nonaktifkan sementara Windows Firewall di komputer server, atau tambahkan rule exception untuk port 80 (Apache).

---

### Pindah ke Hosting di Kemudian Hari

Jika sekolah nantinya ingin memindahkan sistem ke hosting online:
1. Gunakan file `env.example.hosting` sebagai template `.env` baru.
2. Ikuti panduan di seksi 2 (cPanel) atau seksi 3 (VPS) di bawah.
3. Export database dari phpMyAdmin lokal, lalu import ke database hosting.

Tidak ada perubahan kode yang diperlukan — cukup ganti file `.env`.

---

## 1. SPESIFIKASI & KEBUTUHAN SERVER

- **Versi PHP**: PHP 8.2 atau PHP 8.3 (PHP 8.2+ kompatibel penuh).
- **Web Server**: Apache 2.4+ (dengan `mod_rewrite` aktif) atau Nginx 1.20+ atau Caddy/FrankenPHP.
- **Database Engine**: MySQL 5.7+ / MySQL 8.0+ atau MariaDB 10.4+.
- **Ekstensi PHP Wajib Aktif**:
  `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `json`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `zip`.

---

## 2. PANDUAN PEMASANGAN DI CPANEL / SHARED HOSTING

Panduan ini ditujukan bagi sekolah yang menggunakan web hosting berbasis cPanel / DirectAdmin.

### Langkah 1: Membuat Database & User di cPanel
1. Masuk ke dashboard **cPanel** sekolah Anda.
2. Cari dan klik menu **MySQL Database Wizard** (atau **MySQL Databases**).
3. **Buat Database Baru**:
   - Masukkan nama database, misalnya `perpus` (nama lengkap menjadi `usercpanel_perpus`).
   - Klik **Next Step**.
4. **Buat User Database**:
   - Masukkan username, misalnya `dbadmin` (nama lengkap menjadi `usercpanel_dbadmin`).
   - Masukkan kata sandi yang kuat dan catat kata sandi tersebut.
   - Klik **Create User**.
5. **Berikan Hak Akses (Privileges)**:
   - Centang opsi **ALL PRIVILEGES** (Semua Hak Akses).
   - Klik **Make Changes** / **Next Step**.
6. Simpan 3 data penting berikut untuk file konfigurasi:
   - **Nama Database** : `usercpanel_perpus`
   - **User Database** : `usercpanel_dbadmin`
   - **Password User** : `(password yang dibuat tadi)`
   - **Host Database** : `127.0.0.1` atau `localhost`

---

### Langkah 2: Mengunggah Berkas Proyek
Struktur direktori Laravel menempatkan file publik di folder `public/` demi keamanan file inti aplikasi.

1. Buka **File Manager** di cPanel.
2. Buat folder baru di luar folder `public_html` (pada root user `/home/usercpanel/`), beri nama misalnya `perpustakaan_core`.
3. Kompres seluruh file proyek sistem perpustakaan dari komputer Anda menjadi berkas `.zip`.
4. Unggah berkas `.zip` tersebut ke dalam folder `/home/usercpanel/perpustakaan_core` lalu klik **Extract**.
5. Pindahkan seluruh isi yang ada di dalam subfolder `perpustakaan_core/public/` ke dalam folder `public_html/` (atau ke folder subdomain sekolah jika menggunakan subdomain).
   *(Termasuk file `.htaccess`, `index.php`, folder `vendor/`, `images/`, dll.)*
6. Buka dan edit file `public_html/index.php`, sesuaikan dua baris berikut agar mengarah ke folder inti `perpustakaan_core`:
   ```php
   // Ubah dari:
   // require __DIR__.'/../vendor/autoload.php';
   // $app = require_once __DIR__.'/../bootstrap/app.php';

   // Menjadi:
   require __DIR__.'/../perpustakaan_core/vendor/autoload.php';
   $app = require_once __DIR__.'/../perpustakaan_core/bootstrap/app.php';
   ```
7. Simpan perubahan file `index.php`.

---

### Langkah 3: Konfigurasi File .env & Koneksi Database
1. Pada **File Manager**, klik icon **Settings** (pojok kanan atas) dan pastikan opsi **Show Hidden Files (dotfiles)** tercentang, lalu klik **Save**.
2. Masuk ke folder `/home/usercpanel/perpustakaan_core/`.
3. Jika belum ada file `.env`, salin file `.env.example` dan ubah namanya menjadi `.env`.
4. Klik kanan file `.env` lalu pilih **Edit**.
5. Sesuaikan konfigurasi berikut dengan data database dari Langkah 1:
   ```env
   APP_NAME="Sistem Informasi Perpustakaan"
   APP_ENV=production
   APP_KEY=base64:XyZ... (jika kosong, lihat langkah 4)
   APP_DEBUG=false
   APP_URL=https://perpustakaan.sekolah.sch.id
   APP_TIMEZONE=Asia/Jakarta

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=usercpanel_perpus
   DB_USERNAME=usercpanel_dbadmin
   DB_PASSWORD=password_database_anda

   ADMIN_LOGIN_PATH=akses-perpustakaan
   ```
6. Klik **Save Changes**.

---

### Langkah 4: Menjalankan Migrasi & Data Awal

#### Pilihan A: Menggunakan Fitur Terminal di cPanel (Direkomendasikan)
1. Buka menu **Terminal** pada cPanel.
2. Masuk ke direktori aplikasi:
   ```bash
   cd ~/perpustakaan_core
   ```
3. Jika `APP_KEY` di `.env` masih kosong, buat key aplikasi:
   ```bash
   php artisan key:generate
   ```
4. Jalankan migrasi tabel dan data awal sistem:
   ```bash
   php artisan migrate --seed --force
   ```

#### Pilihan B: Menggunakan phpMyAdmin (Jika cPanel Tidak Memiliki Menu Terminal)
1. Buka menu **phpMyAdmin** di cPanel.
2. Pilih nama database Anda di panel sebelah kiri (`usercpanel_perpus`).
3. Pada komputer lokal/pengembang, lakukan export SQL dari sistem perpustakaan atau jalankan perintah `php artisan perpus:backup` untuk menghasilkan berkas database `.sql`.
4. Di phpMyAdmin, klik tab **Import** (Impor) pada bagian atas.
5. Klik **Choose File**, pilih file `.sql` tersebut, lalu klik tombol **Import** di bagian bawah.

---

### Langkah 5: Membuat Symlink Storage (Cover Buku)
Agar gambar sampul buku yang diunggah admin dapat tampil di katalog publik, direktori `storage/app/public` harus terhubung dengan `public_html/storage`.

#### Pilihan A: Melalui Terminal cPanel
```bash
ln -s /home/usercpanel/perpustakaan_core/storage/app/public /home/usercpanel/public_html/storage
```

#### Pilihan B: Menggunakan Skrip PHP Satu Kali
1. Di dalam folder `public_html/`, buat file baru bernama `buat_symlink.php`.
2. Isi file tersebut dengan kode berikut:
   ```php
   <?php
   $target = '/home/usercpanel/perpustakaan_core/storage/app/public';
   $shortcut = __DIR__ . '/storage';
   if (symlink($target, $shortcut)) {
       echo 'Symlink storage berhasil dibuat!';
   } else {
       echo 'Gagal membuat symlink. Periksa path folder.';
   }
   ```
   *(Ganti `usercpanel` dengan username cPanel asli Anda).*
3. Buka URL `https://perpustakaan.sekolah.sch.id/buat_symlink.php` di browser.
4. Setelah muncul pesan berhasil, **segera hapus** file `buat_symlink.php` dari File Manager demi keamanan.

---

### Langkah 6: Membuat Akun Administrator
1. Melalui **Terminal cPanel**:
   ```bash
   cd ~/perpustakaan_core
   php artisan perpus:buat-admin
   ```
2. Ikuti instruksi interaktif di layar (masukkan Nama, Email, pilih Role Super Admin, dan tentukan Password).
3. Buka URL login admin di browser:
   `https://perpustakaan.sekolah.sch.id/akses-perpustakaan`
4. Masuk menggunakan email dan password yang baru saja Anda buat.

---

## 3. PANDUAN PEMASANGAN DI VPS / DEDICATED LINUX (UBUNTU/DEBIAN)

### 1. Persiapan Direktori & Dependensi
```bash
cd /var/www
git clone https://github.com/WalZetass-kar/Perpustakaan-PGRI.git perpustakaan
cd perpustakaan
composer install --no-dev --optimize-autoloader
```

### 2. Membuat Database di MySQL VPS
```bash
mysql -u root -p
```
Di dalam MySQL console, ketik:
```sql
CREATE DATABASE perpustakaan_sekolah CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'perpus_user'@'localhost' IDENTIFIED BY 'PasswordKuat123!';
GRANT ALL PRIVILEGES ON perpustakaan_sekolah.* TO 'perpus_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Konfigurasi Environment & Migrasi
```bash
cp .env.example .env
nano .env
```
Isi konfigurasi database:
```env
APP_NAME="Sistem Informasi Perpustakaan"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://perpustakaan.sekolah.sch.id
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpustakaan_sekolah
DB_USERNAME=perpus_user
DB_PASSWORD=PasswordKuat123!
```
Jalankan inisialisasi:
```bash
php artisan key:generate
php artisan migrate --seed --force
php artisan perpus:buat-admin
php artisan storage:link
```

### 4. Hak Akses Folder & Web Server VirtualHost
```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

Konfigurasi Nginx (`/etc/nginx/sites-available/perpustakaan`):
```nginx
server {
    listen 80;
    server_name perpustakaan.sekolah.sch.id;
    root /var/www/perpustakaan/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 4. PANDUAN PEMASANGAN DI CLOUD PAAS (RAILWAY / DOCKER CONTAINER)

Jika menggunakan platform PaaS modern seperti Railway:
1. Hubungkan repositori GitHub ke project Railway baru.
2. Tambahkan plugin database **MySQL** di canvas project Railway Anda.
3. Di tab **Variables** service aplikasi web, tambahkan environment variables berikut:
   - `APP_NAME` : `Sistem Informasi Perpustakaan`
   - `APP_ENV` : `production`
   - `APP_KEY` : `(hasil dari php artisan key:generate)`
   - `APP_DEBUG` : `false`
   - `APP_URL` : `https://${{RAILWAY_PUBLIC_DOMAIN}}`
   - `DB_CONNECTION` : `mysql`
   - `DB_HOST` : `${{MySQL.MYSQLHOST}}`
   - `DB_PORT` : `${{MySQL.MYSQLPORT}}`
   - `DB_DATABASE` : `${{MySQL.MYSQLDATABASE}}`
   - `DB_USERNAME` : `${{MySQL.MYSQLUSER}}`
   - `DB_PASSWORD` : `${{MySQL.MYSQLPASSWORD}}`
   - `ADMIN_LOGIN_PATH` : `akses-perpustakaan`
4. Set **Custom Start Command** pada service web:
   ```bash
   php artisan migrate --seed --force && php artisan storage:link && /start-container.sh
   ```

---

## 5. DAFTAR PERINTAH ARTISAN UNTUK PEMELIHARAAN

| Perintah | Fungsi |
|---|---|
| `php artisan perpus:buat-admin` | Membuat akun Super Admin atau Petugas baru secara interaktif |
| `php artisan perpus:reset-password` | Mereset kata sandi akun admin jika petugas lupa password |
| `php artisan perpus:backup` | Mencadangkan basis data ke berkas SQL di `storage/app/backups/` |
| `php artisan perpus:backup --zip` | Mencadangkan basis data beserta seluruh berkas cover buku ke format ZIP |
| `php artisan covers:regenerate` | Menghasilkan ulang varian thumbnail cover buku |

---

## 6. PANDUAN PEMECAHAN MASALAH (TROUBLESHOOTING)

| Gejala Masalah | Penyebab Umum | Solusi |
|---|---|---|
| **Error `SQLSTATE[HY000] [2002] Connection refused`** | `DB_HOST` atau `DB_PORT` salah, atau MySQL belum aktif | Pastikan `DB_HOST=127.0.0.1` dan MySQL service berjalan di server |
| **Error `Access denied for user`** | Username atau password database di `.env` salah | Samakan `DB_USERNAME` dan `DB_PASSWORD` dengan data user MySQL di cPanel/server |
| **Error `Table 'xxx' doesn't exist`** | Migrasi database belum dijalankan | Jalankan `php artisan migrate --seed --force` atau import file `.sql` awal |
| **Gambar cover buku tidak tampil (404)** | Symbolic link storage belum terhubung | Buat symlink storage mengikuti Langkah 5 |
| **Halaman 419 Page Expired saat form dikirim** | Nilai `APP_URL` di `.env` berbeda protokol (http vs https) dengan browser | Pastikan `APP_URL` di `.env` sama persis dengan URL yang diakses di address bar |
| **Halaman 500 Internal Server Error** | Izin folder `storage` tidak dapat ditulis oleh web server | Jalankan `chmod -R 775 storage bootstrap/cache` di terminal cPanel/VPS |
