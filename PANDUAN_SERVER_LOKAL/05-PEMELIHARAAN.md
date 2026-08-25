# 05 — PEMELIHARAAN HARIAN

Bab ini berisi kegiatan rutin agar sistem tetap sehat dan data tidak hilang.

---

## Pencadangan Data (Backup)

Ini bagian **terpenting** dari seluruh panduan. Komputer bisa rusak kapan saja,
tetapi data koleksi dan riwayat peminjaman tidak boleh ikut hilang.

### Cadangan Manual

```bash
php artisan perpus:backup
```

Berkas `.sql` akan tersimpan di `storage/app/backups/`.

Untuk mencadangkan database **beserta seluruh gambar sampul buku**:

```bash
php artisan perpus:backup --zip
```

> Fitur ini berjalan murni dengan PHP dan **tidak membutuhkan `mysqldump`**,
> sehingga tetap berfungsi di komputer yang perkakas MySQL-nya tidak lengkap.

Petugas juga dapat mengunduh cadangan langsung dari panel admin tanpa membuka
terminal.

### Cadangan Otomatis Setiap Hari (Linux)

Buka penjadwal tugas:

```bash
crontab -e
```

Tambahkan baris berikut agar cadangan dibuat setiap hari pukul 17.00:

```cron
0 17 * * * cd /opt/perpustakaan && /usr/bin/php artisan perpus:backup --zip
```

### Cadangan Otomatis Setiap Hari (Windows)

1. Buka **Task Scheduler**, pilih **Create Basic Task**.
2. Beri nama "Backup Perpustakaan", atur pemicu **Daily** pukul 17.00.
3. Pilih **Start a program**, isikan:
   - Program: `php`
   - Arguments: `artisan perpus:backup --zip`
   - Start in: `C:\perpustakaan`

### Menyalin Cadangan ke Luar Komputer

Cadangan yang hanya tersimpan di komputer server **tidak melindungi apa pun**
bila komputer itu sendiri yang rusak.

Setidaknya seminggu sekali, salin isi folder `storage/app/backups/` ke
flashdisk, hard disk eksternal, atau Google Drive.

---

## Menghidupkan dan Mematikan Server dengan Benar

**Jika memakai layanan systemd (Linux):**

```bash
sudo systemctl start perpustakaan     # menyalakan
sudo systemctl stop perpustakaan      # mematikan
sudo systemctl restart perpustakaan   # menyalakan ulang
sudo systemctl status perpustakaan    # memeriksa kondisi
```

**Jika menjalankan manual:**

Tekan `Ctrl + C` pada jendela terminal server. Jangan mematikan komputer secara
paksa saat petugas sedang menyimpan data.

---

## Membersihkan Cache Setelah Mengubah `.env`

Setiap kali berkas `.env` disunting, jalankan:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

Tanpa ini, perubahan setelan sering kali seperti "tidak berpengaruh".

---

## Memperbarui Aplikasi

Bila pengembang merilis perbaikan:

```bash
cd /opt/perpustakaan

# 1. Cadangkan lebih dulu, tanpa terkecuali
php artisan perpus:backup --zip

# 2. Ambil versi terbaru
git pull origin master

# 3. Terapkan perubahan struktur database
php artisan migrate

# 4. Bersihkan cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 5. Nyalakan ulang server
sudo systemctl restart perpustakaan
```

> Selalu jalankan langkah 1. Migrasi database tidak selalu dapat dibatalkan.

---

## Memeriksa Kesehatan Sistem

**Melihat catatan galat:**

```bash
tail -n 50 storage/logs/laravel.log
```

**Memastikan database terhubung:**

```bash
php artisan migrate:status
```

Seluruh baris harus bertuliskan `Ran`.

**Memeriksa sisa ruang penyimpanan:**

```bash
df -h
```

Folder cadangan dapat menumpuk. Hapus berkas cadangan yang berumur lebih dari
tiga bulan bila ruang mulai menipis.

---

## Merapikan Sampul Buku

Bila thumbnail sampul tidak muncul atau tampak rusak setelah pemulihan data:

```bash
php artisan covers:regenerate
```

---

## Jadwal Pemeliharaan yang Disarankan

| Waktu | Kegiatan |
|---|---|
| Setiap hari | Pastikan server hidup sebelum jam layanan dimulai |
| Setiap hari (otomatis) | Pencadangan database melalui penjadwal |
| Setiap minggu | Salin cadangan ke flashdisk atau penyimpanan awan |
| Setiap bulan | Periksa `storage/logs/laravel.log` dan sisa ruang penyimpanan |
| Setiap semester | Hapus cadangan lama, perbarui aplikasi bila ada rilis baru |

---

## Selanjutnya

Bila menemui kendala, lihat **[06-MASALAH-UMUM.md](06-MASALAH-UMUM.md)**.
