# 06 — MASALAH UMUM & CARA MENGATASINYA

Kumpulan gejala yang paling sering muncul beserta penyebab dan solusinya.

---

## Komputer Lain Tidak Bisa Membuka Sistem

Ini keluhan yang paling sering terjadi. Periksa berurutan dari atas.

**1. Server dijalankan tanpa `--host=0.0.0.0`**

Penyebab nomor satu. `php artisan serve` saja hanya melayani komputer server
sendiri.

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

**2. Firewall memblokir port**

```bash
sudo ufw allow 8000/tcp && sudo ufw reload
```

**3. Alamat IP sudah berubah**

```bash
hostname -I
```

Bila berbeda dari yang dicatat petugas, kunci IP-nya sesuai
[04-AKSES-DARI-KOMPUTER-LAIN.md](04-AKSES-DARI-KOMPUTER-LAIN.md) Langkah 4.

**4. Berbeda jaringan**

Pastikan komputer petugas terhubung ke WiFi/LAN yang sama. Jaringan "Guest"
pada router sekolah biasanya diisolasi dan tidak dapat menjangkau server.

**5. Menguji dari server sendiri**

```bash
curl -I http://localhost:8000
```

Bila dari server sendiri berhasil tetapi dari komputer lain gagal, masalahnya
pasti pada firewall atau jaringan, bukan pada aplikasi.

---

## Galat "SQLSTATE[HY000] [1045] Access denied for user"

Nama pengguna atau kata sandi database pada `.env` tidak cocok.

```bash
mysql -u perpus -p perpustakaan
```

Bila gagal masuk, perbaiki nilai `DB_USERNAME` dan `DB_PASSWORD`, lalu:

```bash
php artisan config:clear
```

---

## Galat "SQLSTATE[HY000] [1049] Unknown database 'perpustakaan'"

Database belum dibuat.

```bash
mysql -u root -p -e "CREATE DATABASE perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate --seed
```

---

## Galat "SQLSTATE[HY000] [2002] Connection refused"

Layanan MySQL sedang mati.

```bash
sudo systemctl status mysql
sudo systemctl start mysql
sudo systemctl enable mysql     # agar hidup otomatis
```

---

## Halaman Tampil Putih Polos Tanpa Pesan Apa Pun

Ada galat yang disembunyikan karena `APP_DEBUG=false`.

Untuk melihat penyebabnya:

```bash
tail -n 50 storage/logs/laravel.log
```

Atau ubah sementara di `.env`:

```env
APP_DEBUG=true
```

```bash
php artisan config:clear
```

> Kembalikan ke `false` setelah masalah teratasi, agar siswa tidak melihat
> detail teknis sistem.

---

## Gambar Sampul Buku Tidak Muncul

**1. Symlink belum dibuat**

```bash
php artisan storage:link
```

**2. `APP_URL` masih `localhost`**

Bila diakses dari komputer lain, `localhost` menunjuk ke komputer petugas
sendiri, bukan ke server. Perbaiki di `.env`:

```env
APP_URL=http://192.168.100.36:8000
```

```bash
php artisan config:clear
```

**3. Hak akses folder kurang**

```bash
sudo chmod -R 775 storage
sudo chown -R $USER:www-data storage
```

---

## Galat "Permission denied" pada `storage` atau `bootstrap/cache`

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## Galat "Address already in use"

Port 8000 sudah dipakai proses lain, biasanya server yang lupa dimatikan.

```bash
sudo ss -tlnp | grep :8000
```

Hentikan prosesnya, atau jalankan pada port berbeda:

```bash
php artisan serve --host=0.0.0.0 --port=8080
```

Ingat menyesuaikan `APP_URL` dan membuka port baru di firewall.

---

## Lupa Kata Sandi Admin

```bash
php artisan perpus:reset-password admin@sekolah.sch.id
```

Bila seluruh akun admin tidak dapat diakses, buat akun baru:

```bash
php artisan perpus:buat-admin
```

---

## Halaman Login Petugas Tidak Ditemukan (404)

Alamatnya bukan `/login`, melainkan mengikuti `ADMIN_LOGIN_PATH` pada `.env`.
Bawaannya:

```
http://<IP-SERVER>:8000/akses-perpustakaan
```

Periksa nilainya:

```bash
grep ADMIN_LOGIN_PATH .env
```

---

## Galat "Class ... not found" Setelah Menyalin dari Flashdisk

Folder `vendor/` tidak ikut tersalin dengan lengkap.

```bash
composer install
```

Bila komputer server tanpa internet, salin ulang folder `vendor/` secara utuh
dari komputer sumber.

---

## Perubahan pada `.env` Tidak Berpengaruh

Konfigurasi masih tersimpan di cache.

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

Lalu nyalakan ulang server.

---

## Sistem Terasa Lambat Saat Banyak Siswa Mengakses

Ini keterbatasan wajar `php artisan serve` yang melayani permintaan satu per
satu. Bila mengganggu, pasang Apache atau Nginx mengikuti bagian
**VPS / Dedicated Linux** pada
[PANDUAN_PEMASANGAN_TEKNISI.md](../PANDUAN_PEMASANGAN_TEKNISI.md).

---

## Bila Semua Cara Sudah Dicoba

Kumpulkan informasi berikut sebelum menghubungi pengembang:

```bash
php -v
mysql --version
php artisan migrate:status
tail -n 100 storage/logs/laravel.log
```

Sertakan pula langkah persis yang dilakukan sebelum galat muncul, serta tangkapan
layar pesan galatnya.
