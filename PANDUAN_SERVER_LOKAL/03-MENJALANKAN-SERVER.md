# 03 — MENJALANKAN SERVER

Bab ini menjelaskan cara menyalakan sistem agar dapat diakses komputer lain,
sekaligus membuatnya hidup otomatis setiap komputer server dinyalakan.

---

## Perbedaan Penting: `serve` Biasa vs Server Jaringan

```bash
php artisan serve
```

Perintah di atas **hanya bisa dibuka di komputer server itu sendiri**. Komputer
lain akan gagal terhubung. Ini penyebab kebingungan yang paling sering terjadi.

Agar dapat diakses seluruh jaringan, tambahkan `--host=0.0.0.0`:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Arti `0.0.0.0` adalah "terima sambungan dari semua kartu jaringan", bukan hanya
dari komputer sendiri.

Setelah dijalankan akan muncul:

```
INFO  Server running on [http://0.0.0.0:8000].
Press Ctrl+C to stop the server
```

Biarkan jendela terminal ini **tetap terbuka** selama perpustakaan beroperasi.
Menutup terminal berarti mematikan server.

---

## Pilihan A — Menjalankan Manual Setiap Hari

Cocok untuk sekolah yang mematikan komputer setiap sore.

**Linux**, buat berkas `jalankan-server.sh` di dalam folder proyek:

```bash
cat > jalankan-server.sh <<'SH'
#!/bin/bash
cd "$(dirname "$0")"
php artisan serve --host=0.0.0.0 --port=8000
SH
chmod +x jalankan-server.sh
```

Menjalankannya cukup dengan klik ganda, atau:

```bash
./jalankan-server.sh
```

**Windows**, buat berkas `jalankan-server.bat`:

```bat
@echo off
cd /d "%~dp0"
php artisan serve --host=0.0.0.0 --port=8000
pause
```

Klik ganda berkas tersebut setiap pagi. Jangan menutup jendela hitam yang
muncul sampai perpustakaan tutup.

---

## Pilihan B — Otomatis Menyala Saat Komputer Dinyalakan (Linux)

Cara ini paling disarankan, karena petugas tidak perlu mengingat apa pun.

Buat berkas layanan systemd:

```bash
sudo nano /etc/systemd/system/perpustakaan.service
```

Isikan (sesuaikan `User` dan `WorkingDirectory` dengan kondisi komputer Anda):

```ini
[Unit]
Description=Server Sistem Perpustakaan Sekolah
After=network.target mysql.service

[Service]
Type=simple
User=namauser
WorkingDirectory=/opt/perpustakaan
ExecStart=/usr/bin/php artisan serve --host=0.0.0.0 --port=8000
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

Aktifkan:

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now perpustakaan
```

Periksa kondisinya:

```bash
sudo systemctl status perpustakaan
```

Baris `active (running)` berwarna hijau menandakan server sudah hidup dan akan
menyala sendiri setiap komputer dinyalakan. Berkat `Restart=always`, server juga
akan bangkit sendiri seandainya tiba-tiba berhenti.

Perintah pengelolaan sehari-hari:

```bash
sudo systemctl stop perpustakaan       # mematikan
sudo systemctl start perpustakaan      # menyalakan
sudo systemctl restart perpustakaan    # menyalakan ulang
sudo journalctl -u perpustakaan -f     # melihat catatan langsung
```

---

## Pilihan C — Otomatis Menyala Saat Komputer Dinyalakan (Windows)

1. Buat `jalankan-server.bat` seperti pada Pilihan A.
2. Tekan `Win + R`, ketik `shell:startup`, lalu tekan Enter.
3. Salin pintasan (*shortcut*) berkas `.bat` tadi ke folder yang terbuka.

Server akan menyala otomatis setiap Windows masuk ke desktop.

> Agar jendela hitamnya tidak mengganggu, buka properti pintasan lalu ubah
> **Run** menjadi **Minimized**.

---

## Catatan Kejujuran Teknis

`php artisan serve` adalah server bawaan PHP untuk pengembangan. Untuk
perpustakaan sekolah dengan pemakaian wajar — belasan hingga puluhan pengguna
bersamaan — kemampuannya sudah memadai dan jauh lebih sederhana dipelihara.

Namun perlu diketahui keterbatasannya:

- Melayani permintaan satu per satu, sehingga terasa melambat bila puluhan siswa
  membuka katalog secara bersamaan.
- Tidak memiliki HTTPS.
- Bukan rancangan untuk beban berat jangka panjang.

Bila di kemudian hari sekolah membutuhkan kapasitas lebih besar, pasang Apache
atau Nginx dengan mengikuti bagian **VPS / Dedicated Linux** pada
[PANDUAN_PEMASANGAN_TEKNISI.md](../PANDUAN_PEMASANGAN_TEKNISI.md). Aplikasinya
sama persis, hanya cara penyajiannya yang berbeda.

---

## Selanjutnya

Lanjut ke **[04-AKSES-DARI-KOMPUTER-LAIN.md](04-AKSES-DARI-KOMPUTER-LAIN.md)**.
