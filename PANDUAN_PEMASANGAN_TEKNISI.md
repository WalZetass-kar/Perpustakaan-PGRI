# PANDUAN PEMASANGAN & PEMELIHARAAN SISTEM
## Sistem Informasi Perpustakaan Digital Sekolah (Katalog OPAC & Panel Admin)

Dokumen ini disusun sebagai panduan teknis bagi teknisi IT atau administrator sistem di pihak sekolah untuk memasang, mengonfigurasi, dan memelihara aplikasi Sistem Informasi Perpustakaan.

---

### 1. SPESIFIKASI & KEBUTUHAN SISTEM

#### A. Kebutuhan Server / Hosting
- **Sistem Operasi**: Linux (Ubuntu 22.04 LTS / Debian 12 / AlmaLinux / CloudLinux) atau Windows Server (XAMPP / LAMPP).
- **Web Server**: Apache 2.4+ (dengan modul `mod_rewrite` aktif) atau Nginx 1.20+.
- **PHP**: Versi 8.2 atau 8.3 (PHP 8.2+ kompatibel penuh).
- **Database**: MySQL 5.7+ / MySQL 8.0+ atau MariaDB 10.4+.
- **Composer**: Composer 2.x.
- **Node.js & NPM**: Versi 18+ (hanya jika ingin melakukan kompilasi ulang aset mandiri).

#### B. Ekstensi PHP yang Wajib Aktif
- `bcmath`
- `ctype`
- `curl`
- `dom`
- `fileinfo`
- `gd` (diperlukan untuk manipulasi cover buku)
- `json`
- `mbstring`
- `openssl`
- `pcre`
- `pdo` & `pdo_mysql`
- `tokenizer`
- `xml`
- `zip`

---

### 2. LANGKAH-LANGKAH INSTALASI

#### Langkah 1: Persiapan Berkas Sumber (Source Code)
1. Salin seluruh direktori proyek ke direktori root web server Anda:
   - Untuk Apache di Linux: `/var/www/perpustakaan` atau `/opt/lampp/htdocs/perpustakaan`
   - Untuk cPanel: `/home/username/public_html` atau direktori di luar public_html dengan symlink/document root ke `public/`
   - Untuk XAMPP di Windows: `C:\xampp\htdocs\perpustakaan`

2. Masuk ke direktori proyek melalui terminal:
   ```bash
   cd /var/www/perpustakaan
   ```

#### Langkah 2: Instalasi Dependensi (Jika Berkas Vendor Belum Ada)
Jika folder `vendor` belum terpasang, jalankan perintah berikut:
```bash
composer install --no-dev --optimize-autoloader
```

#### Langkah 3: Konfigurasi Berkas Lingkungan (.env)
1. Salin berkas `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```

2. Buka berkas `.env` dengan editor teks (nano, vim, atau File Manager cPanel) dan sesuaikan konfigurasi:
   ```env
   APP_NAME="Sistem Informasi Perpustakaan"
   APP_ENV=production
   APP_KEY=
   APP_DEBUG=false
   APP_URL=http://localhost:8000
   APP_TIMEZONE=Asia/Jakarta

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=user_database_anda
   DB_PASSWORD=password_database_anda

   # Kustomisasi jalur URL login admin (default: akses-perpustakaan)
   ADMIN_LOGIN_PATH=akses-perpustakaan
   ```

3. Buat application key Laravel:
   ```bash
   php artisan key:generate
   ```

#### Langkah 4: Migrasi & Inisialisasi Database
1. Pastikan database kosong telah dibuat di MySQL/MariaDB.
2. Jalankan perintah migrasi dan seeder awal:
   ```bash
   php artisan migrate --seed
   ```
   *Catatan: Perintah ini akan membuat struktur tabel lengkap dan mengisikan pengaturan dasar sistem.*

#### Langkah 5: Pembuatan Akun Administrator Utama (Super Admin)
Gunakan perintah interaktif Artisan yang telah disediakan:
```bash
php artisan perpus:buat-admin
```
Sistem akan meminta Anda memasukkan:
- Nama Lengkap Admin
- Alamat Email (digunakan untuk login)
- Role Pengguna (Pilih `1` untuk Super Admin atau `2` untuk Petugas Perpustakaan)
- Kata Sandi Akun (minimal 8 karakter)

#### Langkah 6: Pembuatan Symlink Direktori Storage
Agar cover buku yang diunggah dapat diakses oleh publik, buat symbolic link storage:
```bash
php artisan storage:link
```

#### Langkah 7: Pengaturan Hak Akses Folder (Permissions)
Pastikan web server memiliki izin menulis ke direktori `storage` dan `bootstrap/cache`:
- Pada Linux / Nginx / Apache:
  ```bash
  chown -R www-data:www-data storage bootstrap/cache
  chmod -R 775 storage bootstrap/cache
  ```
- Pada XAMPP Linux (`/opt/lampp`):
  ```bash
  chmod -R 777 storage bootstrap/cache
  ```

---

### 3. KONFIGURASI WEB SERVER PRODUCTION

#### A. Konfigurasi Apache (Virtual Host)
Pastikan `DocumentRoot` mengarah langsung ke subdirektori `/public`:
```apache
<VirtualHost *:80>
    ServerName perpustakaan.sekolah.sch.id
    DocumentRoot /var/www/perpustakaan/public

    <Directory /var/www/perpustakaan/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/perpus_error.log
    CustomLog ${APACHE_LOG_DIR}/perpus_access.log combined
</VirtualHost>
```

#### B. Konfigurasi Nginx
```nginx
server {
    listen 80;
    server_name perpustakaan.sekolah.sch.id;
    root /var/www/perpustakaan/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

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

#### C. Konfigurasi cPanel / Shared Hosting
Jika menggunakan hosting cPanel tanpa akses konfigurasi VirtualHost:
1. Letakkan isi folder proyek (di luar folder `public`) pada direktori satu tingkat di atas `public_html` (misalnya `/home/username/perpustakaan_core`).
2. Pindahkan seluruh isi dari folder `public` ke dalam `public_html`.
3. Buka berkas `public_html/index.php` dan sesuaikan jalur `autoload.php` serta `bootstrap/app.php`:
   ```php
   require __DIR__.'/../perpustakaan_core/vendor/autoload.php';
   $app = require_once __DIR__.'/../perpustakaan_core/bootstrap/app.php';
   ```
4. Buat symlink dari `storage/app/public` ke `public_html/storage`.

---

### 4. MANAJEMEN & PEMELIHARAAN (MAINTENANCE)

#### A. Perintah Artisan Khusus Aplikasi
Aplikasi ini dilengkapi serangkaian perintah CLI bawaan untuk memudahkan pemeliharaan:

1. **Pembuatan Akun Admin Baru**:
   ```bash
   php artisan perpus:buat-admin
   ```
2. **Reset Kata Sandi Admin (Darurat)**:
   Jika petugas lupa kata sandi dan tidak bisa masuk:
   ```bash
   php artisan perpus:reset-password
   # Atau langsung dengan email:
   php artisan perpus:reset-password admin@sekolah.sch.id
   ```
3. **Pencadangan Database (Backup CLI)**:
   - Mencadangkan database ke berkas SQL:
     ```bash
     php artisan perpus:backup
     ```
   - Mencadangkan database sekaligus seluruh berkas cover buku ke ZIP:
     ```bash
     php artisan perpus:backup --zip
     ```
   *Berkas cadangan tersimpan di `storage/app/backups/`.*
4. **Optimasi Varian Cover Buku**:
   ```bash
   php artisan perpus:regenerate-covers
   ```

#### B. Otomasi Pencadangan Database (Cron Job)
Untuk menjadwalkan pencadangan otomatis setiap tengah malam (pukul 01.00):
Tambahkan entri berikut pada crontab server (`crontab -e`):
```cron
0 1 * * * cd /var/www/perpustakaan && php artisan perpus:backup --zip >> /var/log/perpus_backup.log 2>&1
```

#### C. Optimasi Cache Production
Setelah melakukan konfigurasi atau pembaruan berkas, jalankan perintah optimasi:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
Untuk membersihkan cache jika ada perubahan setting:
```bash
php artisan optimize:clear
```

---

### 5. PANDUAN PEMECAHAN MASALAH (TROUBLESHOOTING)

| Gejala Masalah | Penyebab Umum | Solusi |
|---|---|---|
| Halaman 500 Server Error | `APP_KEY` belum dibuat atau izin direktori `storage` salah | Jalankan `php artisan key:generate` dan `chmod -R 775 storage bootstrap/cache` |
| Cover buku tidak muncul | Symbolic link storage belum dibuat | Jalankan `php artisan storage:link` |
| Halaman 419 Page Expired saat login / submit form | Sesi kedaluwarsa atau domain pada `APP_URL` berbeda | Pastikan `APP_URL` di `.env` sesuai dengan domain/IP yang dibuka di browser |
| Gagal mengunggah file cover besar | Limit upload PHP terlalu kecil | Tingkatkan `upload_max_filesize = 10M` dan `post_max_size = 12M` pada berkas `php.ini` |
| Error `PDOException` saat migrasi | Kredensial database di `.env` salah atau server MySQL belum jalan | Periksa parameter `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di `.env` |
