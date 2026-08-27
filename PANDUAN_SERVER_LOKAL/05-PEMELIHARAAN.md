# 05 — PEMELIHARAAN HARIAN

Bab ini berisi kegiatan rutin agar sistem tetap sehat dan data tidak hilang.
Setiap bagian disertai langkah untuk Linux maupun Windows.

---

## Pencadangan Data (Backup)

Ini bagian **terpenting** dari seluruh panduan. Komputer bisa rusak kapan saja,
tetapi data koleksi dan riwayat peminjaman tidak boleh ikut hilang.

### Cadangan Manual

```bash
php artisan perpus:backup
```

Berkas `.sql` tersimpan di `storage/app/backups/`.

Untuk mencadangkan database **beserta seluruh gambar sampul buku**:

```bash
php artisan perpus:backup --zip
```

> Fitur ini berjalan murni dengan PHP dan **tidak membutuhkan `mysqldump`**,
> sehingga tetap berfungsi di komputer Windows yang perkakas MySQL-nya tidak
> lengkap.

Petugas juga dapat mengunduh cadangan langsung dari panel admin tanpa membuka
terminal.

### Cadangan Otomatis Setiap Hari — Linux

```bash
crontab -e
```

Tambahkan baris berikut agar cadangan dibuat setiap hari pukul 17.00:

```cron
0 17 * * * cd /opt/perpustakaan && /usr/bin/php artisan perpus:backup --zip
```

### Cadangan Otomatis Setiap Hari — Windows

Buat dahulu berkas `backup-harian.bat` di dalam folder proyek:

```bat
@echo off
cd /d "%~dp0"
php artisan perpus:backup --zip
```

Lalu daftarkan ke penjadwal bawaan Windows:

1. Tekan `Win + R`, ketik `taskschd.msc`, tekan Enter.
2. Klik **Create Basic Task** di panel kanan.
3. **Name**: `Backup Perpustakaan`, klik **Next**.
4. **Trigger**: pilih **Daily**, klik **Next**, atur pukul `17:00`.
5. **Action**: pilih **Start a program**, klik **Next**.
6. Isi kolomnya:
   - **Program/script**: `C:\laragon\www\perpustakaan\backup-harian.bat`
   - **Start in**: `C:\laragon\www\perpustakaan`
7. Klik **Finish**.

Agar tugas tetap berjalan walau tidak ada yang login, buka properti tugas
tersebut, tab **General**, lalu centang **Run whether user is logged on or not**.

Menguji tanpa menunggu pukul 17.00: klik kanan tugasnya → **Run**, lalu periksa
apakah berkas baru muncul di `storage\app\backups\`.

### Menyalin Cadangan ke Luar Komputer

Cadangan yang hanya tersimpan di komputer server **tidak melindungi apa pun**
bila komputer itu sendiri yang rusak.

Setidaknya seminggu sekali, salin isi folder `storage/app/backups/` ke
flashdisk, hard disk eksternal, atau Google Drive.

---

## Menghidupkan dan Mematikan Server dengan Benar

### Linux — bila memakai layanan systemd

```bash
sudo systemctl start perpustakaan     # menyalakan
sudo systemctl stop perpustakaan      # mematikan
sudo systemctl restart perpustakaan   # menyalakan ulang
sudo systemctl status perpustakaan    # memeriksa kondisi
```

### Windows

- **Menyalakan**: klik ganda `jalankan-server.bat`.
- **Mematikan**: tekan `Ctrl + C` pada jendela hitam server, atau tutup
  jendelanya.
- **Menyalakan ulang**: tutup jendelanya, lalu klik ganda berkas `.bat` kembali.

Bila jendela server tidak sengaja tertutup dan server terlanjur berjalan di
latar belakang:

```cmd
taskkill /F /IM php.exe
```

Perintah itu menghentikan seluruh proses PHP, lalu server dapat dinyalakan lagi
seperti biasa.

Jangan mematikan komputer secara paksa saat petugas sedang menyimpan data.

---

## Membersihkan Cache Setelah Mengubah `.env`

Berlaku sama di Linux maupun Windows:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

Tanpa ini, perubahan setelan sering kali seperti "tidak berpengaruh".

---

## Memperbarui Aplikasi

### Linux

```bash
cd /opt/perpustakaan

php artisan perpus:backup --zip          # 1. cadangkan lebih dulu
git pull origin master                   # 2. ambil versi terbaru
php artisan migrate                      # 3. terapkan perubahan database
php artisan config:clear
php artisan cache:clear
php artisan view:clear                   # 4. bersihkan cache
sudo systemctl restart perpustakaan      # 5. nyalakan ulang server
```

### Windows

```cmd
cd /d C:\laragon\www\perpustakaan

php artisan perpus:backup --zip
git pull origin master
php artisan migrate
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

Lalu tutup jendela server dan jalankan kembali `jalankan-server.bat`.

> Selalu jalankan langkah pencadangan lebih dulu. Migrasi database tidak selalu
> dapat dibatalkan.

---

## Memeriksa Kesehatan Sistem

### Melihat catatan galat

```bash
# Linux
tail -n 50 storage/logs/laravel.log
```

```cmd
REM Windows
powershell -Command "Get-Content storage\logs\laravel.log -Tail 50"
```

Atau cukup buka `storage\logs\laravel.log` dengan Notepad++ dan gulir ke bagian
paling bawah.

> **Bila berkas itu tidak ada sama sekali**, `.env` kemungkinan memakai
> `LOG_CHANNEL=stderr` — nilai untuk hosting, yang mengirim catatan galat ke
> keluaran terminal alih-alih menulisnya ke berkas. Untuk server lokal ubah
> menjadi:
>
> ```env
> LOG_CHANNEL=single
> ```
>
> lalu `php artisan config:clear`. Bila server dijalankan sebagai layanan
> systemd dan Anda ingin tetap memakai `stderr`, catatannya dibaca dengan
> `sudo journalctl -u perpustakaan -n 100`.

### Memastikan database terhubung

```bash
php artisan migrate:status
```

Seluruh baris harus bertuliskan `Ran`.

### Memeriksa sisa ruang penyimpanan

```bash
# Linux
df -h
```

```cmd
REM Windows
wmic logicaldisk get name,freespace,size
```

Atau buka **This PC** di File Explorer dan lihat sisa ruang drive C.

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
