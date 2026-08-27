# 06 — MASALAH UMUM & CARA MENGATASINYA

Kumpulan gejala yang paling sering muncul beserta penyebab dan solusinya.
Setiap solusi disertai langkah untuk **Linux** maupun **Windows**.

---

## Komputer Lain Tidak Bisa Membuka Sistem

Ini keluhan yang paling sering terjadi. Periksa berurutan dari atas.

### 1. Server dijalankan tanpa `--host=0.0.0.0`

Penyebab nomor satu. `php artisan serve` saja hanya melayani komputer server
sendiri.

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### 2. Firewall memblokir port

**Linux (ufw):**

```bash
sudo ufw allow 8000/tcp && sudo ufw reload
```

**Linux (firewalld):**

```bash
sudo firewall-cmd --permanent --add-port=8000/tcp && sudo firewall-cmd --reload
```

**Windows** — jalankan Command Prompt **sebagai Administrator**:

```cmd
netsh advfirewall firewall add rule name="Perpustakaan Sekolah" dir=in action=allow protocol=TCP localport=8000
```

Memastikan aturannya sudah ada:

```cmd
netsh advfirewall firewall show rule name="Perpustakaan Sekolah"
```

Bila ingin lewat tampilan grafis: **Control Panel → Windows Defender Firewall →
Advanced settings → Inbound Rules → New Rule → Port → TCP 8000 → Allow**.

> Saat pertama kali menjalankan server, Windows kadang menampilkan kotak
> **Windows Security Alert**. Pastikan mencentang **Private networks** lalu
> klik **Allow access**. Bila tidak sengaja terklik **Cancel**, aturan firewall
> harus dibuat manual seperti di atas.

### 3. Alamat IP sudah berubah

```bash
# Linux
hostname -I
```

```cmd
REM Windows
ipconfig
```

Bila berbeda dari yang dicatat petugas, kunci IP-nya sesuai
[04-AKSES-DARI-KOMPUTER-LAIN.md](04-AKSES-DARI-KOMPUTER-LAIN.md) Langkah 4.

### 4. Berbeda jaringan

Pastikan komputer petugas terhubung ke WiFi/LAN yang sama. Jaringan "Guest"
pada router sekolah biasanya diisolasi dan tidak dapat menjangkau server.

### 5. Menguji dari server sendiri

```bash
# Linux
curl -I http://localhost:8000
```

```cmd
REM Windows
powershell -Command "curl http://localhost:8000 -UseBasicParsing | Select-Object StatusCode"
```

Bila dari server sendiri berhasil tetapi dari komputer lain gagal, masalahnya
pasti pada firewall atau jaringan, bukan pada aplikasi.

### 6. Menguji sambungan dari komputer petugas

```cmd
ping 192.168.100.36
```

Bila `ping` gagal, masalahnya di jaringan/kabel/WiFi. Bila `ping` berhasil
tetapi browser tetap gagal, masalahnya di firewall atau server belum menyala.

---

## Galat "SQLSTATE[HY000] [1045] Access denied for user"

Nama pengguna atau kata sandi database pada `.env` tidak cocok.

```bash
mysql -u perpus -p perpustakaan
```

Di Windows, uji lewat phpMyAdmin: `http://localhost/phpmyadmin`

Bila gagal masuk, perbaiki nilai `DB_USERNAME` dan `DB_PASSWORD`, lalu:

```bash
php artisan config:clear
```

> Pada Laragon dan XAMPP, kata sandi `root` bawaannya **kosong**. Jadi
> `DB_PASSWORD=` memang dibiarkan kosong, bukan diisi kata apa pun.

---

## Galat "SQLSTATE[HY000] [1049] Unknown database 'perpustakaan'"

Database belum dibuat.

```bash
mysql -u root -p -e "CREATE DATABASE perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate --seed
```

Di Windows lebih mudah lewat phpMyAdmin: tab **Databases** → ketik
`perpustakaan` → collation `utf8mb4_unicode_ci` → **Create**.

---

## Galat "SQLSTATE[HY000] [2002] Connection refused"

Layanan MySQL sedang mati.

**Linux:**

```bash
sudo systemctl status mysql
sudo systemctl start mysql
sudo systemctl enable mysql     # agar hidup otomatis
```

**Windows:**

Buka Laragon atau XAMPP Control Panel, pastikan **MySQL** berstatus hidup. Bila
tombol **Start** ditekan lalu langsung mati kembali, biasanya port 3306 dipakai
program lain:

```cmd
netstat -ano | findstr :3306
```

Bila ada MySQL lain yang terpasang sebagai layanan Windows, hentikan dengan:

```cmd
net stop MySQL80
```

---

## Halaman Terbuka Tapi Polos Tanpa Gaya, Menu Tidak Bisa Diklik

Gejalanya: katalog **berhasil terbuka** dan tulisannya terbaca, tetapi tampil
seperti dokumen putih polos tanpa warna maupun tata letak. Tombol dan menu tidak
memberi reaksi saat diklik. Tidak ada pesan galat sama sekali.

**Penyebabnya hampir selalu satu:** `APP_ENV` bernilai `production` pada `.env`.

Pada nilai itu aplikasi memaksa seluruh alamat menjadi `https://`, sementara
server lokal hanya melayani `http` biasa. Akibatnya berkas tampilan (CSS) dan
berkas penggerak menu (JavaScript) gagal dimuat browser.

Memastikannya:

```bash
# Linux
grep APP_ENV .env
```

```cmd
REM Windows
findstr APP_ENV .env
```

Perbaikannya, ubah menjadi:

```env
APP_ENV=local
```

```bash
php artisan config:clear
```

Lalu nyalakan ulang server.

> Ini **tidak** membuat galat teknis terlihat oleh siswa — yang mengatur hal itu
> `APP_DEBUG`, dan nilainya tetap `false`.
>
> Penyebab tersering nilai ini salah adalah menyalin berkas contoh yang keliru.
> Untuk server lokal gunakan `env.example.lokal`, bukan `.env.example` maupun
> `env.example.hosting`.

Cara memastikan dari sisi browser: klik kanan halaman → **View Page Source**,
lalu perhatikan baris `<link ... tailwind.min.css">`. Bila alamatnya diawali
`https://` padahal server berjalan pada `http://`, inilah penyebabnya.

---

## Huruf pada Halaman Login Terlihat Berbeda

Gejalanya: sistem berjalan normal, warna dan tata letak benar, hanya **bentuk
hurufnya** pada halaman login terlihat lain dari biasanya.

Ini **bukan kerusakan** dan tidak perlu diperbaiki. Halaman login mencoba
mengambil berkas huruf dari internet; pada komputer server yang memang tidak
tersambung internet, browser otomatis memakai huruf bawaan sistem.

Seluruh tombol, menu, dan proses login tetap berfungsi sepenuhnya.

> Berbeda dengan gejala "polos tanpa gaya" di atas — bila yang hilang adalah
> **warna dan tata letaknya**, penyebabnya `APP_ENV`, bukan huruf.

---

## Halaman Tampil Putih Polos Tanpa Pesan Apa Pun

Ada galat yang disembunyikan karena `APP_DEBUG=false`.

Untuk melihat penyebabnya:

```bash
# Linux
tail -n 50 storage/logs/laravel.log
```

```cmd
REM Windows
powershell -Command "Get-Content storage\logs\laravel.log -Tail 50"
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

### 1. Symlink belum dibuat

```bash
php artisan storage:link
```

**Khusus Windows:** pembuatan symbolic link memerlukan hak Administrator. Tutup
terminal, klik kanan Laragon → **Run as administrator**, lalu ulangi perintah di
atas. Bila tetap gagal, aktifkan **Developer Mode** melalui
*Settings → System → For developers*.

Sebagai jalan terakhir bila symlink tetap tidak bisa dibuat, salin manual:

```cmd
xcopy /E /I /Y storage\app\public public\storage
```

Cara ini berhasil, namun harus diulang setiap kali ada sampul buku baru
diunggah — jadi tetap usahakan symlink lebih dulu.

### 2. Berkas sampulnya memang belum ada

Sampul hanya muncul untuk buku yang gambarnya pernah diunggah petugas. Periksa
isinya:

```bash
# Linux
ls storage/app/public/covers | head
```

```cmd
REM Windows
dir storage\app\public\covers
```

Bila thumbnail tampak rusak atau kosong setelah pemulihan data, bangun ulang
variannya:

```bash
php artisan covers:regenerate
```

> **Catatan:** `APP_URL` yang salah **bukan** penyebab gambar hilang. Alamat
> gambar mengikuti alamat yang sedang dibuka di browser, bukan `APP_URL`. Jadi
> jangan menghabiskan waktu di situ — periksa symlink dan berkasnya lebih dulu.

### 3. Hak akses folder kurang

**Linux:**

```bash
sudo chmod -R 775 storage
sudo chown -R $USER:www-data storage
```

**Windows:** klik kanan folder `storage` → **Properties → Security → Edit**,
pilih **Users**, centang **Full control**, lalu **Apply**.

---

## Galat "Permission denied" atau "failed to open stream"

**Linux:**

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**Windows** — jalankan Command Prompt sebagai Administrator:

```cmd
icacls storage /grant Users:(OI)(CI)F /T
icacls bootstrap\cache /grant Users:(OI)(CI)F /T
```

---

## Galat "Address already in use" atau "Failed to listen on 0.0.0.0:8000"

Port 8000 sudah dipakai proses lain, biasanya server yang lupa dimatikan.

**Linux:**

```bash
sudo ss -tlnp | grep :8000
kill <nomor-PID>
```

**Windows:**

```cmd
netstat -ano | findstr :8000
```

Perhatikan angka pada kolom paling kanan — itu nomor PID. Hentikan dengan:

```cmd
taskkill /PID <nomor-PID> /F
```

Bila ingin menghentikan seluruh proses PHP sekaligus:

```cmd
taskkill /F /IM php.exe
```

Atau jalankan pada port berbeda:

```bash
php artisan serve --host=0.0.0.0 --port=8080
```

Ingat menyesuaikan `APP_URL` dan membuka port baru di firewall.

---

## Perintah `php` Tidak Dikenali di Windows

Muncul pesan `'php' is not recognized as an internal or external command`.

**Penyebab:** Command Prompt biasa tidak mengenal PHP karena belum terdaftar di
PATH.

**Solusi tercepat:** jangan pakai Command Prompt biasa. Gunakan
**Menu Laragon → Terminal**, atau **XAMPP Shell**.

**Solusi permanen** — mendaftarkan PHP ke PATH:

1. Tekan `Win + R`, ketik `sysdm.cpl`, tekan Enter.
2. Tab **Advanced** → **Environment Variables**.
3. Pada **System variables**, pilih **Path** → **Edit** → **New**.
4. Masukkan folder PHP, contohnya:
   - Laragon: `C:\laragon\bin\php\php-8.2.x`
   - XAMPP: `C:\xampp\php`
5. Klik **OK** pada semua jendela, lalu **tutup dan buka ulang** Command Prompt.

Menguji:

```cmd
php -v
```

---

## Berkas `.env` Tidak Terbaca di Windows

Gejalanya aplikasi terus memakai setelan bawaan walau `.env` sudah disunting.

**Penyebab tersering:** berkasnya sebenarnya bernama `.env.txt` karena Windows
menyembunyikan ekstensi.

**Solusi:**

1. Buka File Explorer → tab **View** → centang **File name extensions**.
2. Periksa nama berkasnya. Bila tertulis `.env.txt`, ganti nama menjadi `.env`.

Memastikan lewat terminal:

```cmd
dir /a .env*
```

Yang benar hanya muncul `.env`, tanpa embel-embel `.txt`.

---

## Lupa Kata Sandi Admin

```bash
php artisan perpus:reset-password admin@sekolah.sch.id
```

Bila seluruh akun admin tidak dapat diakses, buat akun baru:

```bash
php artisan perpus:buat-admin
```

> Di dalam aplikasi, hanya **Super Administrator** yang dapat mengganti kata
> sandinya sendiri. Petugas yang lupa kata sandi harus dibantu Super Admin
> melalui menu **Akun Pengelola → Reset Password**.

---

## Halaman Login Petugas Tidak Ditemukan (404)

Alamatnya bukan `/login`, melainkan mengikuti `ADMIN_LOGIN_PATH` pada `.env`.
Bawaannya:

```
http://<IP-SERVER>:8000/akses-perpustakaan
```

Periksa nilainya:

```bash
# Linux
grep ADMIN_LOGIN_PATH .env
```

```cmd
REM Windows
findstr ADMIN_LOGIN_PATH .env
```

---

## Galat "Class ... not found" Setelah Menyalin dari Flashdisk

Folder `vendor/` tidak ikut tersalin dengan lengkap.

```bash
composer install
```

Bila komputer server tanpa internet, salin ulang folder `vendor/` secara utuh
dari komputer sumber.

> Pada Windows, penyalinan folder `vendor/` yang berisi puluhan ribu berkas
> kecil sering terhenti di tengah jalan. Lebih aman memampatkannya menjadi satu
> berkas ZIP di komputer sumber, menyalin ZIP-nya, lalu mengekstrak di komputer
> server.

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

## Server Mati Sendiri Saat Jendela Terminal Ditutup

Ini bukan kerusakan. `php artisan serve` hidup selama jendelanya terbuka.

Agar server tetap hidup tanpa jendela terbuka, gunakan otomatisasi yang
dijelaskan di [03-MENJALANKAN-SERVER.md](03-MENJALANKAN-SERVER.md) — layanan
systemd untuk Linux, atau folder Startup untuk Windows.

---

## Sistem Terasa Lambat Saat Banyak Siswa Mengakses

Bawaannya `php artisan serve` melayani permintaan satu per satu. Coba dulu yang
paling mudah — tambahkan pada `.env`:

```env
PHP_CLI_SERVER_WORKERS=4
```

lalu `php artisan config:clear` dan nyalakan ulang server. Penjelasan lengkapnya
ada di [03-MENJALANKAN-SERVER.md](03-MENJALANKAN-SERVER.md).

Bila masih terasa berat, pasang Apache atau Nginx mengikuti bagian
**VPS / Dedicated Linux** pada
[PANDUAN_PEMASANGAN_TEKNISI.md](PANDUAN_PEMASANGAN_TEKNISI.md).

Pengguna Windows dapat memakai Apache bawaan Laragon/XAMPP dengan mengarahkan
*document root* ke folder `public/` milik proyek.

---

## Bila Semua Cara Sudah Dicoba

Kumpulkan informasi berikut sebelum menghubungi pengembang:

```bash
php -v
mysql --version
php artisan migrate:status
```

Lalu lampirkan 100 baris terakhir catatan galat:

```bash
# Linux
tail -n 100 storage/logs/laravel.log
```

```cmd
REM Windows
powershell -Command "Get-Content storage\logs\laravel.log -Tail 100"
```

Sertakan pula langkah persis yang dilakukan sebelum galat muncul, serta
tangkapan layar pesan galatnya.
