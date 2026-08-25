# 04 — AKSES DARI KOMPUTER LAIN

Bab ini menjelaskan cara agar komputer petugas, komputer siswa, dan HP dapat
membuka sistem perpustakaan melalui jaringan sekolah.

---

## Langkah 1: Mencari Alamat IP Komputer Server

Jalankan **di komputer server**.

**Linux:**

```bash
hostname -I
```

Contoh hasil:

```
192.168.100.36 2001:448a:1090:6ee7:651d:fc2b:158f:7c70
```

Ambil angka yang pertama dan berformat empat kelompok, yaitu `192.168.100.36`.
Abaikan deretan panjang berhuruf (itu alamat IPv6).

**Windows:**

```cmd
ipconfig
```

Cari baris **IPv4 Address** pada bagian adaptor yang aktif:

```
IPv4 Address. . . . . . . . . . . : 192.168.100.36
```

Alamat IP lokal umumnya diawali `192.168.`, `10.`, atau `172.16.`–`172.31.`

---

## Langkah 2: Membuka Firewall

Firewall komputer server biasanya memblokir sambungan masuk. Port `8000` harus
diizinkan.

**Linux (ufw):**

```bash
sudo ufw allow 8000/tcp
sudo ufw reload
sudo ufw status
```

**Linux (firewalld):**

```bash
sudo firewall-cmd --permanent --add-port=8000/tcp
sudo firewall-cmd --reload
```

**Windows:**

```cmd
netsh advfirewall firewall add rule name="Perpustakaan Sekolah" dir=in action=allow protocol=TCP localport=8000
```

Perintah tersebut harus dijalankan melalui **Command Prompt sebagai
Administrator**.

---

## Langkah 3: Menguji dari Komputer Lain

Di komputer petugas atau HP yang terhubung jaringan sekolah yang sama, buka
browser lalu ketik:

```
http://192.168.100.36:8000
```

Ganti angkanya dengan IP hasil Langkah 1. Katalog buku seharusnya langsung
tampil.

Alamat penting yang perlu dicatat:

| Halaman | Alamat |
|---|---|
| Katalog OPAC untuk siswa | `http://192.168.100.36:8000/` |
| Pencarian katalog | `http://192.168.100.36:8000/katalog` |
| Panel petugas | `http://192.168.100.36:8000/akses-perpustakaan` |

> Alamat panel petugas mengikuti `ADMIN_LOGIN_PATH` pada `.env`.

---

## Langkah 4: Mengunci Alamat IP Agar Tidak Berubah

Ini langkah yang **sering terlewat**, padahal penting.

Secara bawaan, router sekolah memberi alamat IP secara otomatis dan dapat
berubah setiap komputer dinyalakan ulang. Bila IP berubah, seluruh komputer
petugas mendadak tidak bisa membuka sistem.

### Cara A — Mengunci dari Komputer Server (Linux)

Buka **Settings → Network**, klik ikon roda gigi pada sambungan yang aktif,
pilih tab **IPv4**, lalu ubah **Method** menjadi **Manual** dan isikan:

| Kolom | Contoh Isian |
|---|---|
| Address | `192.168.100.36` |
| Netmask | `255.255.255.0` |
| Gateway | `192.168.100.1` |
| DNS | `192.168.100.1` |

Sesuaikan Gateway dengan alamat router sekolah. Cari dengan:

```bash
ip route | grep default
```

### Cara A — Mengunci dari Komputer Server (Windows)

Cari dahulu alamat Gateway router sekolah:

```cmd
ipconfig
```

Perhatikan baris **Default Gateway**, misalnya `192.168.100.1`.

Kemudian melalui tampilan grafis:

1. Tekan `Win + R`, ketik `ncpa.cpl`, tekan Enter.
2. Klik kanan adaptor yang sedang aktif (Ethernet atau Wi-Fi) → **Properties**.
3. Pilih **Internet Protocol Version 4 (TCP/IPv4)** → klik **Properties**.
4. Pilih **Use the following IP address**, lalu isikan:

   | Kolom | Contoh Isian |
   |---|---|
   | IP address | `192.168.100.36` |
   | Subnet mask | `255.255.255.0` |
   | Default gateway | `192.168.100.1` |
   | Preferred DNS server | `192.168.100.1` |

5. Centang **Validate settings upon exit**, lalu klik **OK**.

Atau melalui Command Prompt **sebagai Administrator** (ganti `"Ethernet"`
dengan nama adaptor Anda):

```cmd
netsh interface ip set address name="Ethernet" static 192.168.100.36 255.255.255.0 192.168.100.1
netsh interface ip set dns name="Ethernet" static 192.168.100.1
```

Melihat daftar nama adaptor:

```cmd
netsh interface show interface
```

Mengembalikan ke otomatis bila diperlukan:

```cmd
netsh interface ip set address name="Ethernet" dhcp
```

> **Peringatan:** pilih alamat IP di luar jangkauan DHCP router, misalnya angka
> akhir di atas 200, agar tidak diberikan router ke komputer lain dan
> menyebabkan bentrok.

### Cara B — Mengunci dari Router (Disarankan)

Masuk ke halaman admin router sekolah, cari menu bernama **DHCP Reservation**
atau **Static Lease**, lalu ikat alamat MAC komputer server ke satu IP tetap.

Alamat MAC dapat dilihat dengan:

```bash
# Linux
ip link | grep ether
```

```cmd
REM Windows
ipconfig /all
```

Pada Windows, perhatikan baris **Physical Address**, misalnya
`A4-B1-C2-D3-E4-F5`.

Cara B lebih baik karena pengaturannya terpusat di router dan tidak berisiko
bentrok dengan komputer lain.

---

## Langkah 5: Menyesuaikan `APP_URL`

Setelah IP terkunci, buka kembali `.env` di komputer server dan pastikan:

```env
APP_URL=http://192.168.100.36:8000
```

Lalu bersihkan cache konfigurasi:

```bash
php artisan config:clear
```

Bila langkah ini dilewati, tautan cetak PDF dan sebagian gambar akan menunjuk ke
`localhost` sehingga rusak saat dibuka dari komputer lain.

---

## Membuat Akses Lebih Mudah bagi Petugas

### Pintasan di Desktop

Buat pintasan browser pada setiap komputer petugas yang langsung menuju
`http://192.168.100.36:8000/akses-perpustakaan`.

### Nama yang Mudah Diingat (Opsional)

Agar petugas cukup mengetik `perpustakaan` tanpa menghafal angka IP, tambahkan
baris berikut pada berkas hosts **di setiap komputer petugas**:

- Windows: `C:\Windows\System32\drivers\etc\hosts`
- Linux: `/etc/hosts`

```
192.168.100.36  perpustakaan
```

Setelah itu alamatnya menjadi `http://perpustakaan:8000`.

---

## Menghilangkan `:8000` dari Alamat (Opsional)

Agar cukup mengetik `http://192.168.100.36`, jalankan server pada port 80:

**Linux** — port di bawah 1024 memerlukan hak akses khusus:

```bash
sudo php artisan serve --host=0.0.0.0 --port=80
```

Bila memakai layanan systemd, ubah baris `ExecStart` menjadi `--port=80`, lalu:

```bash
sudo systemctl daemon-reload
sudo systemctl restart perpustakaan
```

**Windows** — jalankan melalui terminal yang dibuka **sebagai Administrator**:

```cmd
php artisan serve --host=0.0.0.0 --port=80
```

Sesuaikan pula isi berkas `jalankan-server.bat` menjadi `--port=80`, dan atur
pintasannya agar berjalan sebagai Administrator melalui
**klik kanan pintasan → Properties → Advanced → Run as administrator**.

Jangan lupa buka port 80 di firewall dan perbarui `APP_URL` menjadi
`http://192.168.100.36` (tanpa port).

> Pastikan tidak ada Apache, Nginx, atau IIS yang sudah memakai port 80, karena
> akan bentrok. Periksa dengan:
>
> ```bash
> # Linux
> sudo ss -tlnp | grep :80
> ```
>
> ```cmd
> REM Windows
> netstat -ano | findstr :80
> ```
>
> Pada Windows, Laragon dan XAMPP biasanya sudah memakai port 80 untuk Apache.
> Matikan Apache-nya lebih dulu bila ingin memakai port tersebut.

---

## Selanjutnya

Lanjut ke **[05-PEMELIHARAAN.md](05-PEMELIHARAAN.md)**.
