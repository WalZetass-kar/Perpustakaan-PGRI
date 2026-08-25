# 01 — PERSIAPAN KOMPUTER SERVER

Sebelum memasang aplikasi, komputer yang akan dijadikan server harus disiapkan
lebih dulu. Kerjakan bab ini sampai selesai sebelum lanjut ke
[02-INSTALASI.md](02-INSTALASI.md).

---

## 1. Spesifikasi Komputer yang Disarankan

Aplikasi ini ringan. Komputer kantor biasa sudah lebih dari cukup.

| Bagian | Minimum | Disarankan |
|---|---|---|
| Prosesor | Dual Core | Quad Core |
| RAM | 2 GB | 4 GB atau lebih |
| Penyimpanan kosong | 2 GB | 10 GB (untuk cover buku & backup) |
| Sistem Operasi | Windows 10 / Ubuntu 20.04 | Ubuntu 22.04+ atau Windows 11 |
| Jaringan | Terhubung LAN/WiFi sekolah | Kabel LAN (lebih stabil daripada WiFi) |

> **Saran penting:** gunakan kabel LAN, bukan WiFi. Komputer server sebaiknya
> juga tidak dimatikan selama jam operasional perpustakaan.

---

## 2. Perangkat Lunak yang Harus Terpasang

Hanya dua yang wajib:

1. **PHP 8.2 atau lebih baru**
2. **MySQL 8.0** atau **MariaDB 10.4+**

Composer bersifat opsional, karena folder `vendor/` sudah disertakan di dalam
proyek ini.

---

## 3. Pemasangan di Linux (Ubuntu / Debian)

```bash
sudo apt update
sudo apt install -y php php-cli php-mysql php-mbstring php-xml php-curl \
                    php-gd php-zip php-bcmath mysql-server
```

Nyalakan MySQL dan pastikan hidup otomatis:

```bash
sudo systemctl enable --now mysql
sudo systemctl status mysql
```

Amankan MySQL (buat password root):

```bash
sudo mysql_secure_installation
```

---

## 4. Pemasangan di Windows

Cara termudah adalah memakai **Laragon** atau **XAMPP**, karena keduanya sudah
memuat PHP dan MySQL sekaligus.

1. Unduh **Laragon Full** dari situs resminya, lalu pasang seperti biasa.
2. Buka Laragon, klik **Start All**. PHP dan MySQL akan menyala bersamaan.
3. Pastikan versi PHP-nya 8.2 ke atas melalui menu **Menu → PHP → Version**.
4. Buka **Menu → Terminal** dari Laragon setiap kali ingin menjalankan
   perintah `php artisan`, agar PHP dikenali.

> Jika memakai XAMPP, aktifkan modul **Apache** dan **MySQL** dari XAMPP
> Control Panel, lalu jalankan perintah melalui **Shell**.

---

## 5. Memastikan Versi PHP Sudah Benar

```bash
php -v
```

Keluaran harus menunjukkan `PHP 8.2` atau lebih tinggi. Contoh hasil yang benar:

```
PHP 8.5.4 (cli) (built: Jul 16 2026 18:56:38) (NTS)
```

Jika yang muncul PHP 7.x atau 8.0/8.1, aplikasi **tidak akan berjalan** dan
harus diperbarui dulu.

---

## 6. Memastikan Ekstensi PHP Lengkap

Aplikasi membutuhkan lima belas ekstensi. Periksa semuanya sekaligus:

```bash
for e in bcmath ctype curl dom fileinfo gd json mbstring openssl pcre pdo pdo_mysql tokenizer xml zip; do
  php -m | grep -qix "$e" && echo "OK    : $e" || echo "KURANG: $e"
done
```

Semua baris harus bertuliskan `OK`. Jika ada yang `KURANG`, pasang di Linux
dengan pola `sudo apt install php-<nama>`, misalnya:

```bash
sudo apt install php-gd php-zip
```

Di Windows (Laragon/XAMPP), buka `php.ini`, hapus tanda titik koma `;` di depan
baris ekstensi yang kurang, lalu nyalakan ulang layanannya.

**Akibat jika ekstensi kurang:**

| Ekstensi | Jika tidak ada |
|---|---|
| `pdo_mysql` | Aplikasi sama sekali tidak bisa terhubung ke database |
| `gd` | Thumbnail sampul buku gagal dibuat |
| `zip` | Fitur backup format ZIP tidak berfungsi |
| `mbstring` | Judul buku berhuruf non-latin menjadi rusak |

---

## 7. Memastikan MySQL Berjalan

```bash
mysql --version
```

Contoh hasil yang benar:

```
mysql  Ver 8.4.10-0ubuntu0.26.04.1 for Linux on x86_64
```

Lalu uji apakah bisa masuk:

```bash
mysql -u root -p
```

Jika berhasil masuk ke prompt `mysql>`, ketik `exit` untuk keluar. Persiapan
selesai.

---

## Selanjutnya

Lanjut ke **[02-INSTALASI.md](02-INSTALASI.md)** untuk memasang aplikasinya.
