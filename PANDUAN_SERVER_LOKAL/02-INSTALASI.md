# 02 — INSTALASI APLIKASI

Kerjakan bab ini di **komputer server** saja. Pastikan
[01-PERSIAPAN.md](01-PERSIAPAN.md) sudah tuntas.

---

## Langkah 1: Menyalin Berkas Proyek

**Bila komputer server punya internet:**

```bash
git clone https://github.com/WalZetass-kar/Perpustakaan-PGRI.git
cd Perpustakaan-PGRI
```

**Bila komputer server tanpa internet:**

Salin seluruh folder proyek dari flashdisk ke komputer server, misalnya ke
`/opt/perpustakaan` (Linux) atau `C:\perpustakaan` (Windows). Pastikan folder
`vendor/` **ikut tersalin** — folder itu berisi seluruh pustaka yang dibutuhkan
sehingga Composer tidak diperlukan.

Masuk ke foldernya:

```bash
cd /opt/perpustakaan
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

```bash
mysql -u root -p -e "CREATE DATABASE perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Disarankan membuat user khusus, jangan memakai `root` untuk aplikasi:

```bash
mysql -u root -p <<'SQL'
CREATE USER 'perpus'@'localhost' IDENTIFIED BY 'GantiDenganPasswordKuat';
GRANT ALL PRIVILEGES ON perpustakaan.* TO 'perpus'@'localhost';
FLUSH PRIVILEGES;
SQL
```

Pastikan database sudah terbentuk:

```bash
mysql -u root -p -e "SHOW DATABASES LIKE 'perpustakaan';"
```

---

## Langkah 4: Menyiapkan Berkas Konfigurasi `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Buka `.env` dengan editor teks, lalu sesuaikan bagian berikut:

```env
APP_NAME="Perpustakaan SMK PGRI"
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

### Penjelasan Setelan Penting

| Setelan | Penjelasan |
|---|---|
| `APP_URL` | Isi dengan **alamat IP komputer server**, bukan `localhost`. Cara mencarinya ada di [04-AKSES-DARI-KOMPUTER-LAIN.md](04-AKSES-DARI-KOMPUTER-LAIN.md). Salah isi membuat gambar & tautan cetak PDF rusak. |
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

---

## Langkah 7: Menyambungkan Folder Penyimpanan

```bash
php artisan storage:link
```

Tanpa langkah ini, **semua gambar sampul buku tidak akan muncul**.

Pastikan berhasil:

```bash
ls -l public/storage
```

Hasilnya harus berupa tautan yang mengarah ke `storage/app/public`.

---

## Langkah 8: Mengatur Hak Akses Folder (Linux)

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

Langkah ini tidak diperlukan di Windows.

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
