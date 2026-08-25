# PANDUAN SERVER LOKAL
## Menjalankan Sistem Perpustakaan di Satu Komputer sebagai Server

Panduan ini ditujukan untuk sekolah yang **tidak menyewa hosting internet**.
Satu komputer di ruang perpustakaan dijadikan server, lalu komputer lain di
sekolah (meja petugas, komputer siswa, bahkan HP) membuka sistem melalui
jaringan lokal (LAN / WiFi sekolah).

Seluruh sistem berjalan **100% tanpa internet**.

---

## Gambaran Cara Kerjanya

```
                   JARINGAN SEKOLAH (WiFi / Kabel LAN)
                                  |
        +-------------------------+-------------------------+
        |                         |                         |
  [ KOMPUTER SERVER ]      [ Komputer Petugas ]      [ HP / Tablet Siswa ]
  Perpustakaan-PGRI              Browser                   Browser
  MySQL + PHP                       |                         |
  192.168.x.x:8000  <---------------+-------------------------+
        ^
        |
  Hanya komputer ini yang perlu dipasang aplikasi.
  Komputer lain cukup membuka browser.
```

Komputer lain **tidak perlu** memasang PHP, MySQL, maupun aplikasi ini. Mereka
hanya membuka alamat seperti `http://192.168.1.10:8000` di browser.

---

## Daftar Isi

| Berkas | Isi |
|---|---|
| **[01-PERSIAPAN.md](01-PERSIAPAN.md)** | Kebutuhan komputer server, pemasangan PHP & MySQL, pengecekan ekstensi |
| **[02-INSTALASI.md](02-INSTALASI.md)** | Menyalin proyek, membuat database, konfigurasi `.env`, migrasi, akun admin |
| **[03-MENJALANKAN-SERVER.md](03-MENJALANKAN-SERVER.md)** | Menyalakan server & membuatnya hidup otomatis saat komputer dinyalakan |
| **[04-AKSES-DARI-KOMPUTER-LAIN.md](04-AKSES-DARI-KOMPUTER-LAIN.md)** | Mencari alamat IP, membuka firewall, mengunci IP agar tidak berubah |
| **[05-PEMELIHARAAN.md](05-PEMELIHARAAN.md)** | Backup rutin, mematikan server dengan benar, memperbarui aplikasi |
| **[06-MASALAH-UMUM.md](06-MASALAH-UMUM.md)** | Kumpulan gejala galat dan cara mengatasinya |

---

## Ringkasan Cepat

Bagi yang sudah paham dan hanya butuh urutan perintahnya:

```bash
# 1. Siapkan database
mysql -u root -p -e "CREATE DATABASE perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Konfigurasi aplikasi
cd /path/ke/Perpustakaan-PGRI
cp .env.example .env
php artisan key:generate
# sunting .env: DB_USERNAME, DB_PASSWORD, APP_URL

# 3. Siapkan isi database & akun
php artisan migrate --seed
php artisan perpus:buat-admin
php artisan storage:link

# 4. Nyalakan server agar bisa diakses komputer lain
php artisan serve --host=0.0.0.0 --port=8000
```

Alamat yang dibuka dari komputer lain: `http://<IP-SERVER>:8000`

---

## Perbedaan dengan Panduan Lain

| Dokumen | Untuk Situasi |
|---|---|
| **PANDUAN_SERVER_LOKAL/** (folder ini) | Sekolah tanpa internet, satu komputer jadi server LAN |
| [PANDUAN_PEMASANGAN_TEKNISI.md](../PANDUAN_PEMASANGAN_TEKNISI.md) | Hosting cPanel, VPS, atau layanan cloud |
| [PANDUAN_PENGGUNAAN_PETUGAS.md](../PANDUAN_PENGGUNAAN_PETUGAS.md) | Cara memakai aplikasi sehari-hari oleh pustakawan |
