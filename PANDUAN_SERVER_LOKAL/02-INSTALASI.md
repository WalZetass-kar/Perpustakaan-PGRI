# 02 — INSTALASI APLIKASI

Kerjakan bab ini di **komputer server** saja. Pastikan
[01-PERSIAPAN.md](01-PERSIAPAN.md) sudah tuntas.

> **Pengguna Windows:** seluruh perintah pada bab ini dijalankan melalui
> **Terminal Laragon** (Menu → Terminal) atau **XAMPP Shell**, bukan melalui
> Command Prompt biasa. Kedua terminal itu sudah mengenali perintah `php` dan
> `mysql`.

---

## Langkah 1: Menyalin Berkas Proyek

**Bila komputer server punya internet:**

```bash
git clone https://github.com/WalZetass-kar/Perpustakaan-PGRI.git
cd Perpustakaan-PGRI
```

**Bila komputer server tanpa internet:**

Salin seluruh folder proyek dari flashdisk ke komputer server. Pastikan folder
`vendor/` **ikut tersalin** — folder itu berisi seluruh pustaka yang dibutuhkan
sehingga Composer tidak diperlukan.

Lokasi yang disarankan:

| Sistem | Lokasi |
|---|---|
| Linux | `/opt/perpustakaan` |
| Windows + Laragon | `C:\laragon\www\perpustakaan` |
| Windows + XAMPP | `C:\xampp\htdocs\perpustakaan` |

Masuk ke foldernya:

```bash
# Linux
cd /opt/perpustakaan
```

```cmd
REM Windows
cd /d C:\laragon\www\perpustakaan
```

---

## Langkah 2: Memasang Dependensi (Opsional)

Lewati langkah ini jika folder `vendor/` sudah ada.

```bash
composer install
```

---

## Langkah 3: Membuat Database Kosong

Database harus dibuat lebih dulu, karena migrasi tidak membuatnya sendiri.

### Cara A — Melalui Perintah (Linux & Windows)

```bash
mysql -u root -p -e "CREATE DATABASE perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

> Pada Laragon, kata sandi `root` bawaannya **kosong**. Cukup tekan Enter saat
> diminta kata sandi. Pada XAMPP juga umumnya kosong.

### Cara B — Melalui phpMyAdmin (Khusus Windows, Lebih Mudah)

Laragon dan XAMPP sudah menyertakan phpMyAdmin, sehingga tidak perlu mengetik
perintah sama sekali.

1. Nyalakan Laragon/XAMPP, pastikan MySQL berstatus hidup.
2. Buka `http://localhost/phpmyadmin` di browser.
3. Klik tab **Databases** di bagian atas.
4. Pada kolom **Database name**, ketik `perpustakaan`.
5. Pada kolom sebelahnya (Collation), pilih **utf8mb4_unicode_ci**.
6. Klik **Create**.

> Pemilihan collation `utf8mb4_unicode_ci` penting agar judul buku berhuruf
> khusus tidak berubah menjadi tanda tanya.

### Membuat User Database Khusus (Disarankan)

Sebaiknya aplikasi tidak memakai `root`.

```bash
mysql -u root -p
```

Lalu ketikkan:

```sql
CREATE USER 'perpus'@'localhost' IDENTIFIED BY 'GantiDenganPasswordKuat';
GRANT ALL PRIVILEGES ON perpustakaan.* TO 'perpus'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Di Windows, langkah yang sama dapat dilakukan lewat phpMyAdmin melalui tab
**User accounts → Add user account**.

### Memastikan Database Sudah Terbentuk

```bash
mysql -u root -p -e "SHOW DATABASES LIKE 'perpustakaan';"
```

Di Windows, cukup lihat daftar database pada panel kiri phpMyAdmin.

---

## Langkah 4: Menyiapkan Berkas Konfigurasi `.env`

Proyek ini menyertakan tiga berkas contoh konfigurasi. Untuk server lokal,
**hanya `env.example.lokal` yang benar**:

| Berkas | Untuk |
|---|---|
| **`env.example.lokal`** | **Server lokal LAN — pakai yang ini** |
| `env.example.hosting` | Hosting cPanel / VPS dengan domain ber-https |
| `.env.example` | Contoh umum, setelannya mengikuti hosting |

**Linux:**

```bash
cp env.example.lokal .env
php artisan key:generate
```

**Windows:**

```cmd
copy env.example.lokal .env
php artisan key:generate
```

> **Mengapa bukan `.env.example`?** Dua berkas lainnya memakai
> `APP_ENV=production`, dan pada nilai itu aplikasi memaksa seluruh alamat
> menjadi `https://`. Server lokal hanya melayani `http`, sehingga seluruh CSS
> dan JavaScript gagal dimuat: halaman terbuka tetapi tampil polos tanpa gaya,
> dan menu-menunya tidak bisa diklik — tanpa satu pun pesan galat. Keduanya
> juga memakai `LOG_CHANNEL=stderr`, yang membuat `storage/logs/laravel.log`
> tidak pernah terbentuk, padahal berkas itulah yang dibaca saat mencari
> penyebab masalah di [06-MASALAH-UMUM.md](06-MASALAH-UMUM.md).

Buka `.env` dengan editor teks, lalu sesuaikan:

```env
APP_NAME="Perpustakaan SMK PGRI"

# WAJIB `local` untuk server lokal. Nilai `production` memaksa https dan
# membuat seluruh tampilan rusak. Ini tidak membuat galat terlihat siswa —
# yang mengatur hal itu APP_DEBUG di bawahnya.
APP_ENV=local

APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://192.168.1.10:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpustakaan
DB_USERNAME=perpus
DB_PASSWORD=GantiDenganPasswordKuat

SEED_ADMIN_PASSWORD=RahasiaAdmin123

ADMIN_LOGIN_PATH=akses-perpustakaan
```

> **Peringatan untuk pengguna Windows:** jangan menyunting `.env` dengan
> **Notepad** bawaan, karena dapat menyimpan berkas dengan format akhir baris
> yang menyulitkan. Gunakan **Notepad++**, **VS Code**, atau editor bawaan
> Laragon. Pastikan pula nama berkasnya benar-benar `.env`, bukan `.env.txt` —
> aktifkan **View → File name extensions** di File Explorer untuk memastikannya.

### Penjelasan Setelan Penting

| Setelan | Penjelasan |
|---|---|
| `APP_ENV` | **Harus `local`.** Pada `production`, aplikasi memaksa seluruh alamat menjadi `https://` sementara server lokal hanya melayani `http` — akibatnya halaman tampil polos tanpa gaya dan tidak bisa dipakai. Ini terpisah dari `APP_DEBUG`, jadi galat teknis tetap tersembunyi dari siswa. |
| `APP_URL` | Isi dengan **alamat IP komputer server beserta portnya**, misalnya `http://192.168.1.10:8000`. Cara mencari IP-nya ada di [04-AKSES-DARI-KOMPUTER-LAIN.md](04-AKSES-DARI-KOMPUTER-LAIN.md). Dipakai sebagai alamat acuan sistem; halaman biasa tetap tampil walau nilainya salah, tetapi sebaiknya diisi benar agar tidak membingungkan saat menelusuri masalah. |
| `APP_DEBUG` | Isi `false` untuk pemakaian harian agar galat teknis tidak terlihat siswa. Ubah sementara ke `true` hanya saat mencari penyebab masalah. |
| `SEED_ADMIN_PASSWORD` | **Wajib diisi sebelum Langkah 5.** Jika dibiarkan kosong, sistem membuat password acak yang tidak pernah ditampilkan, sehingga akun bawaan tidak bisa dipakai. |
| `ADMIN_LOGIN_PATH` | Alamat rahasia halaman login petugas. Boleh diganti, misalnya `pintu-petugas`, agar tidak mudah ditebak siswa. |

---

## Langkah 5: Mengisi Struktur & Data Awal Database

```bash
php artisan migrate --seed
```

Perintah ini membuat seluruh tabel sekaligus mengisi data contoh: kategori,
rak, laci, beberapa buku, dan pengaturan dasar perpustakaan.

Akun bawaan yang terbentuk:

| Email | Peran | Password |
|---|---|---|
| `admin@sekolah.sch.id` | Super Administrator | sesuai `SEED_ADMIN_PASSWORD` |
| `pustakawan@sekolah.sch.id` | Admin Perpustakaan | sesuai `SEED_ADMIN_PASSWORD` |

> Jika ingin database benar-benar kosong tanpa data contoh, jalankan
> `php artisan migrate` saja tanpa `--seed`, lalu buat akun sendiri di Langkah 6.

---

## Langkah 6: Membuat Akun Administrator Sendiri

```bash
php artisan perpus:buat-admin
```

Perintah ini akan menanyakan nama, email, password, dan peran secara
interaktif. Gunakan ini untuk membuat akun kepala perpustakaan yang sebenarnya.

Jika suatu saat password terlupa:

```bash
php artisan perpus:reset-password admin@sekolah.sch.id
```

> Perlu diketahui, di dalam aplikasi hanya **Super Administrator** yang dapat
> mengganti kata sandinya sendiri. Petugas yang ingin berganti kata sandi harus
> memintanya kepada Super Admin melalui menu **Akun Pengelola**.

---

## Langkah 7: Menyambungkan Folder Penyimpanan

```bash
php artisan storage:link
```

Tanpa langkah ini, **semua gambar sampul buku tidak akan muncul**.

Memastikan berhasil:

```bash
# Linux
ls -l public/storage
```

```cmd
REM Windows
dir public\storage
```

> **Catatan khusus Windows:** pembuatan symbolic link memerlukan hak
> Administrator. Bila muncul galat, tutup terminal lalu buka ulang Laragon
> dengan klik kanan → **Run as administrator**, dan jalankan kembali perintah
> di atas. Bila tetap gagal, aktifkan **Developer Mode** melalui
> *Settings → System → For developers*.

---

## Langkah 8: Mengatur Hak Akses Folder

**Linux:**

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**Windows:** tidak diperlukan. Namun bila muncul galat "failed to open stream:
Permission denied", klik kanan folder proyek → **Properties → Security → Edit**,
lalu beri centang **Full control** untuk pengguna **Users**.

---

## Langkah 9: Uji Coba Pertama

```bash
php artisan serve
```

Buka `http://localhost:8000` di browser komputer server. Bila katalog buku
tampil, instalasi berhasil.

Tekan `Ctrl + C` untuk menghentikan, lalu lanjut ke bab berikutnya agar sistem
bisa diakses komputer lain.

---

## Selanjutnya

Lanjut ke **[03-MENJALANKAN-SERVER.md](03-MENJALANKAN-SERVER.md)**.
