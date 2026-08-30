# Sistem Informasi Perpustakaan Digital Sekolah
### Katalog OPAC & Panel Manajemen Sirkulasi Back-Office

Sistem Informasi Manajemen Perpustakaan dan Katalog Publik (OPAC) modern yang dirancang khusus untuk mendukung operasional perpustakaan sekolah, penataan inventaris fisik, visualisasi denah lokasi rak & laci (Physical Wayfinding), serta pencatatan sirkulasi peminjaman siswa secara terintegrasi.

---

## 1. Dokumentasi Lengkap Serah Terima Proyek

Untuk mempermudah teknisi sekolah dalam melakukan pemasangan dan membantu pustakawan dalam pengoperasian sistem, telah disediakan dokumen panduan terpisah:

1. **[Panduan Pemasangan & Pemeliharaan untuk Teknisi IT](PANDUAN_SERVER_LOKAL/PANDUAN_PEMASANGAN_TEKNISI.md)**:
   - Persyaratan server & ekstensi PHP
   - Langkah demi langkah instalasi, migrasi, dan konfigurasi `.env`
   - Pengaturan Web Server (Apache VirtualHost, Nginx, dan cPanel)
   - Otomasi pencadangan database (Cron Job) & pemecahan masalah (Troubleshooting)
2. **[Panduan Penggunaan untuk Petugas Perpustakaan](PANDUAN_SERVER_LOKAL/PANDUAN_PENGGUNAAN_PETUGAS.md)**:
   - Alur pengelolaan master buku, kategori, penulis, penerbit, dan kelas
   - Manajemen rak buku & peta denah interaktif 2D
   - Alur persetujuan peminjaman online siswa & pencatatan langsung (Walk-in)
   - Pelacakan keterlambatan dan pengembalian buku
   - Rekapitulasi laporan Excel dan cetak PDF resmi format A4
   - Pengaturan instansi & fitur pencadangan database mandiri
3. **[Panduan Server Lokal (Satu Komputer sebagai Server)](PANDUAN_SERVER_LOKAL/)**:
   - Menjadikan satu komputer sekolah sebagai server tanpa hosting internet
   - Akses dari komputer petugas & HP siswa melalui jaringan LAN / WiFi
   - Otomatisasi server menyala sendiri, pencadangan berkala, dan pemecahan masalah

---

## 2. Fitur Utama Sistem

### A. Katalog Publik & OPAC (Online Public Access Catalog)
- **Pencarian Cepat & Instan**: Pencarian berbasis judul, penulis, penerbit, ISBN, kelas sasaran, dan kategori modul.
- **Ketersediaan Stok Real-Time**: Status stok fisik siap pinjam vs sedang dipinjam yang selalu sinkron.
- **Penunjuk Lokasi Rak & Laci**: Informasi lokasi rak dan tingkat laci fisik pada setiap kartu buku.
- **Formulir Pengajuan Peminjaman Mandiri**: Siswa dapat mengajukan peminjaman buku langsung dari katalog maupun halaman detail buku. Pengajuan kembar dan permintaan yang melebihi eksemplar tersisa ditolak di dalam satu transaksi terkunci, sehingga dua siswa yang menekan tombol bersamaan tidak bisa sama-sama lolos.
- **Pemantauan Keputusan Petugas secara Langsung**: Setelah mengajukan, siswa melihat popup "Menunggu Verifikasi Petugas" yang berubah sendiri menjadi tanda centang (beserta kode dan jatuh tempo) atau tanda silang beserta alasan penolakan, tanpa perlu memuat ulang halaman. Pemantauannya terikat pada sesi peramban pengaju, sehingga tidak ada alamat yang bisa ditebak untuk mengintip pengajuan siswa lain.

### B. Portal Back-Office Pengelola Perpustakaan
- **Dashboard KPI Sirkulasi**: Metrik sirkulasi hari ini, buku sedang dipinjam, pengembalian, dan penghitung buku terlambat jatuh tempo.
- **Visualisasi Grafik Aktivitas**: Grafik aktivitas sirkulasi bulanan dan tahunan.
- **Manajemen Inventaris Koleksi**: Penambahan, pengubahan, dan penghapusan buku dengan proteksi transaksi aktif.
- **Denah Lokasi Rak 2D (Wayfinding)**: Tampilan visual tata letak rak dan laci perpustakaan.
- **Sirkulasi & Overdue Tracker**: Penyaringan status peminjaman (Semua, Aktif, Terlambat, Selesai, Pending), dengan nomor WhatsApp peminjam yang bisa langsung dihubungi.
- **Panel Persetujuan Pengajuan**: Halaman tersendiri berisi pengajuan yang menunggu, menampilkan jumlah eksemplar yang diminta dari total yang dimiliki berdampingan dengan sisa stoknya, serta penolakan beserta alasan yang dapat dibaca kembali oleh pengaju.
- **Master Kelas dengan Penjagaan Duplikat**: Kelas dibandingkan menurut maknanya, bukan tulisannya — huruf besar/kecil, spasi, angka Romawi (`XI` = `11`), dan pengulangan tingkat di dalam nama semuanya disetarakan, sehingga satu kelas tidak tercatat berkali-kali. Daftarnya pun terurut menurut jenjang sebenarnya.
- **Rekapitulasi Laporan Formal**:
  - Ekspor Excel koleksi buku dengan format kolom rapi.
  - Cetak PDF laporan inventaris buku dan sirkulasi peminjaman (A4 Portrait) lengkap dengan Kop Surat Instansi dan blok tanda tangan formal 2 kolom simetris.
- **Pencadangan Database Terintegrasi**: Fasilitas unduh salinan basis data SQL langsung dari panel admin dan melalui CLI.
- **Manajemen Akun & Kendali Kata Sandi Terpusat**: Pembagian hak akses Super Admin dan Petugas. Penggantian kata sandi dipusatkan pada Super Admin — Super Admin dapat mengubah kata sandinya sendiri melalui halaman Profil & Keamanan sekaligus mereset kata sandi akun Petugas, sedangkan Petugas menghubungi Super Admin bila perlu penggantian.

---

## 3. Spesifikasi & Teknologi

- **Backend Framework**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL 5.7+ / 8.0+ atau MariaDB 10.4+
- **Ekstensi PHP Wajib Aktif**: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `json`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `zip`
  > Periksa dengan `php -m`. Tanpa `gd` thumbnail cover gagal dibuat, tanpa `zip` fitur backup ZIP tidak jalan.
- **Frontend**: Blade Templating, Tailwind CSS, Alpine.js
- **Aset & Icon**: FontAwesome 6, jQuery, DataTables, Chart.js, SweetAlert2, AOS
  > Seluruhnya dilayani dari `public/vendor/`, bukan dari CDN, agar sistem tetap
  > utuh saat jaringan sekolah putus. Satu-satunya berkas yang masih diambil dari
  > internet adalah **font** pada halaman login; bila gagal dimuat, tulisan jatuh
  > ke huruf bawaan sistem dan seluruh fungsi tetap berjalan normal.
- **Performa**: tanpa Redis, Memcached, atau layanan tambahan apa pun.
  - Seluruh aset statis dikirim dengan masa simpan cache dan penanda versi
    (`ETag`), sehingga kunjungan berikutnya dijawab `304 Not Modified` tanpa
    mengunduh ulang — gambar & huruf 1 tahun, CSS & JS 1 minggu. Diatur di
    `public/.htaccess` (Apache/cPanel) dan `server.php` (`php artisan serve`).
  - Halaman katalog sengaja **tidak** disimpan di cache peramban, karena sisa
    stoknya harus selalu akurat.
  - Untuk server yang sudah berjalan tetap, jalankan `php artisan config:cache`,
    `route:cache`, dan `view:cache` — lihat panduan teknisi bagian 2B.
- **Ekspor Dokumen**: tanpa pustaka tambahan.
  - **Excel** — laporan disusun sebagai tabel HTML ber-CSS yang dikenali Excel
    sebagai lembar kerja (`.xls`). Berkasnya dirakit di
    `resources/views/admin/laporan/excel/`.
  - **PDF** — halaman cetak A4 dari Blade, dicetak lewat dialog cetak browser.

---

## 4. Struktur Kode & Alur Pengembangan

Bagian ini untuk siapa pun yang akan melanjutkan pengembangan sistem.

### A. Tiga Lapisan

Kode dipisah berdasarkan **tanggung jawab**, bukan berdasarkan jenis berkas:

| Lapisan | Letak | Tanggung jawab | Tidak boleh |
|---|---|---|---|
| **HTTP** | `app/Http/Controllers/Admin/` | Memvalidasi masukan, memeriksa hak akses, memilih tampilan/pengalihan | Menyentuh model atau menulis aturan bisnis |
| **Bisnis** | `app/Services/` | Aturan perpustakaan: stok, transaksi, syarat boleh/tidaknya menghapus, pencatatan audit | Mengenal `Request`, `response()`, atau `view()` |
| **Data** | `app/Models/` | Tabel dan relasinya | — |

```
app/
├── Http/
│   ├── Controllers/Admin/    17 controller, satu bagian layar per berkas
│   ├── DataTables/           penerjemah parameter tabel server-side
│   └── Laporan/              perakit respons unduhan Excel
├── Services/                 17 service — seluruh aturan bisnis ada di sini
│   ├── Buku/                 katalog & penelusuran koleksi
│   ├── Sirkulasi/            peminjaman, pengembalian, pengajuan OPAC
│   ├── Rak/                  rak & laci
│   ├── Pengguna/             akun pengelola
│   ├── MasterData/           kategori, penulis, penerbit, kelas
│   └── Statistik/            angka & grafik dashboard
├── Rules/                    aturan validasi khusus (mis. laci harus milik raknya)
└── Exceptions/               AturanBisnisException

routes/
├── web.php                   kerangka: siapa boleh masuk ke mana
├── publik.php                halaman pengunjung (OPAC)
├── auth.php                  masuk & keluar petugas
└── admin/                    7 berkas, satu per bagian
```

### B. Cara Kedua Lapisan Berbicara

Ketika sebuah operasi melanggar aturan perpustakaan — stok kurang, pengajuan
sudah diproses petugas lain, buku masih tercatat di riwayat — service
**melempar `AturanBisnisException`** berisi kalimat yang siap dibaca petugas.
Controller menangkapnya dan memutuskan tampilannya:

```php
try {
    $this->peminjaman->catat($data);
} catch (AturanBisnisException $e) {
    return back()->with('error', $e->getMessage());
}
```

Dengan begitu aturan bisnisnya bisa diuji tanpa menjalankan HTTP sama sekali.

### C. Menambah Sesuatu yang Baru

| Yang ingin ditambah | Berkas yang disentuh |
|---|---|
| Halaman admin baru | berkas di `routes/admin/` → controller baru di `app/Http/Controllers/Admin/` → view |
| Aturan baru (mis. batas maksimal pinjam) | service terkait di `app/Services/`, **bukan** controller |
| Kolom baru pada tabel | migrasi → `$fillable` model → aturan validasi di controller → service |
| Laporan Excel baru | service penyedia datanya → view di `resources/views/admin/laporan/excel/` |

> **Aturan praktis:** controller yang baik hampir tidak pernah memanggil model
> secara langsung. Bila di controller muncul `DB::transaction`, `Model::create`,
> atau perhitungan stok, tempatnya sebenarnya di service.

---

## 5. Panduan Singkat Menjalankan di Lingkungan Lokal

> Butuh panduan yang jauh lebih rinci, termasuk cara menjadikan satu komputer
> sebagai server yang diakses komputer lain lewat jaringan sekolah? Lihat folder
> **[PANDUAN_SERVER_LOKAL/](PANDUAN_SERVER_LOKAL/)**.

### Langkah 1: Kloning Repositori
```bash
git clone https://github.com/WalZetass-kar/Perpustakaan-PGRI.git
cd Perpustakaan-PGRI
```

### Langkah 2: Instalasi Dependensi
```bash
composer install
```
> Folder `vendor/` sudah disertakan di dalam repositori, sehingga langkah ini
> dapat dilewati jika Composer belum terpasang di komputer Anda.

### Langkah 3: Membuat Database Kosong
Basis data harus sudah ada sebelum migrasi dijalankan.
```bash
mysql -u root -p -e "CREATE DATABASE perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Langkah 4: Konfigurasi File Lingkungan (.env)
```bash
cp .env.example .env
php artisan key:generate
```
Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada berkas `.env`.

> **Penting untuk komputer pengembangan:** `.env.example` disetel untuk server
> produksi (`APP_ENV=production`, `APP_DEBUG=false`) sehingga pesan galat
> disembunyikan. Untuk pengembangan lokal, ubah menjadi:
> ```env
> APP_ENV=local
> APP_DEBUG=true
> ```

### Langkah 5: Migrasi & Data Awal
```bash
php artisan migrate --seed
```
> **Perhatian:** seeder membuat akun `admin@sekolah.sch.id` memakai nilai
> `SEED_ADMIN_PASSWORD` dari `.env`. Jika dibiarkan kosong, sistem membuat
> password acak yang **tidak pernah ditampilkan** dan akun tersebut menjadi
> tidak dapat diakses. Isi `SEED_ADMIN_PASSWORD` terlebih dahulu, atau lewati
> peringatan ini dan buat akun sendiri pada Langkah 6.

### Langkah 6: Buat Akun Administrator
```bash
php artisan perpus:buat-admin
```

### Langkah 7: Buat Symbolic Link Storage
```bash
php artisan storage:link
```
Tanpa langkah ini, gambar sampul buku tidak akan tampil.

### Langkah 8: Jalankan Server Pengembangan
```bash
php artisan serve
```
- **Katalog OPAC**: `http://localhost:8000/`
- **Panel Admin**: `http://localhost:8000/akses-perpustakaan`

> Alamat panel admin mengikuti `ADMIN_LOGIN_PATH` pada `.env`
> (bawaan: `akses-perpustakaan`).

---

## 6. Menjalankan Pengujian

Sistem disertai **36 berkas uji otomatis** (250 pengujian) di folder `tests/`.
Isinya bukan sekadar formalitas: uji-uji itu mengunci perilaku yang pernah
bermasalah, di antaranya

- ketahanan stok saat dua petugas menyetujui pengajuan yang sama bersamaan;
- pembatasan hak akses Super Administrator dan keutuhan berkas cadangan;
- penjagaan kelas kembar beserta penyetaraan angka Romawi dan urutan jenjangnya;
- pemisahan jatah pembatasan laju antar rute, agar penelusuran katalog oleh
  siswa tidak sampai mengunci halaman login petugas;
- identitas kota pada blok tanda tangan laporan, agar tidak ada nama kota yang
  tertanam di dalam kode;
- setiap halaman terbuka tanpa galat server, disusuri langsung dari daftar rute
  sehingga halaman baru ikut terperiksa tanpa perlu diingat.

```bash
php artisan test
```

Jalankan setiap kali selesai mengubah kode; bila ada yang gagal, perubahan
tersebut mengubah perilaku yang seharusnya tetap.

> Pengujian memakai SQLite di memori (lihat `phpunit.xml`), sehingga **tidak
> menyentuh database MySQL** perpustakaan dan tidak meninggalkan berkas apa pun.
> Karena itu ekstensi PHP `sqlite3` perlu aktif di komputer pengembang —
> di Ubuntu/Debian: `sudo apt install php8.5-sqlite3` (sesuaikan dengan
> versi PHP yang terpasang, cek dengan `php -v`).

Menjalankan sebagian saja:

```bash
php artisan test --filter=KetahananStokTest
```

---

## 7. Daftar Perintah Artisan Khusus

| Perintah | Deskripsi Fungsi |
|---|---|
| `php artisan perpus:buat-admin` | Membuat akun Super Admin atau Petugas baru secara interaktif melalui CLI |
| `php artisan perpus:reset-password` | Mereset kata sandi akun admin dalam kondisi darurat |
| `php artisan perpus:backup` | Mencadangkan basis data ke berkas SQL di `storage/app/backups/` |
| `php artisan perpus:backup --zip` | Mencadangkan basis data beserta seluruh berkas cover buku ke arsip ZIP |
| `php artisan covers:regenerate` | Menghasilkan ulang varian thumbnail cover buku |

---

## 8. Hak Cipta & Lisensi

Sistem Informasi Perpustakaan Digital Sekolah. Seluruh hak kepemilikan dan pemeliharaan diserahkan kepada pihak sekolah sesuai Berita Acara Serah Terima (BAST).
