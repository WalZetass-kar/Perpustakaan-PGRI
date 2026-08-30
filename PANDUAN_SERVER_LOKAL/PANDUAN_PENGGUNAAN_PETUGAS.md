# PANDUAN PENGGUNAAN APLIKASI UNTUK PETUGAS PERPUSTAKAAN
## Sistem Informasi Perpustakaan Digital Sekolah (Katalog OPAC & Panel Admin)

Dokumen ini disusun sebagai buku panduan operasional harian bagi Kepala Perpustakaan, Petugas Sirkulasi, dan Administrator Sekolah.

---

### 1. AKSES & MASUK KE SISTEM

#### A. Halaman Publik & Katalog OPAC
- **Alamat Akses**: Buka browser dan akses alamat utama web (contoh: `http://localhost:8000` atau `http://perpustakaan.sekolah.sch.id`).
- **Fungsi**: Siswa, guru, dan staf dapat mencari judul buku, melihat informasi ketersediaan stok fisik, mengetahui posisi nomor rak & laci, serta mengajukan permohonan peminjaman buku secara mandiri.

#### B. Panel Admin (Back-Office)
- **Alamat Login**: Akses menu **Masuk Admin** pada pojok kanan atas beranda atau buka URL `/akses-perpustakaan` (atau URL kustom yang dikonfigurasi).
- Masukkan **Email Pengelola** dan **Kata Sandi Akses** Anda.
- Klik **Masuk ke Dashboard**.

---

### 2. STRUKTUR MENU & TAMPILAN DASHBOARD

Setelah berhasil masuk, Anda akan diarahkan ke halaman **Dashboard Utama**:
1. **Ringkasan Sirkulasi Hari Ini**:
   - **Peminjaman Hari Ini**: Jumlah transaksi peminjaman baru hari ini.
   - **Buku Sedang Dipinjam**: Total fisik buku yang saat ini masih berada di tangan peminjam.
   - **Terlambat Kembali**: Transaksi yang telah melampaui batas tanggal jatuh tempo.
   - **Pengembalian Hari Ini**: Jumlah buku yang telah diserahkan kembali hari ini.
2. **Master Koleksi & Inventaris Fisik**: Ringkasan total judul, total eksemplar fisik, kategori, kelas modul, penulis, dan penerbit.
3. **Grafik Sirkulasi**: Visualisasi aktivitas peminjaman dan pengembalian (tahunan / bulanan).
4. **Daftar Peminjaman Aktif Terbaru**: Tabel transaksi aktif terbaru dengan tombol aksi cepat.

---

### 3. PANDUAN OPERASIONAL FITUR UTAMA

#### A. Manajemen Koleksi Buku (`Menu: Koleksi Buku`)
1. **Menambah Buku Baru**:
   - Klik tombol **Tambah Buku Baru**.
   - Masukkan informasi buku: Judul, Penulis (pilih/buat baru), Penerbit, Kategori Kejuruan, Kelas Sasaran, ISBN, Tahun Terbit, dan Jumlah Eksemplar Fisik.
   - Pilih penempatan lokasi: **Rak Buku** dan tingkat **Laci Rak**.
   - Unggah gambar cover buku (opsional, jika tidak diunggah sistem akan otomatis menghasilkan cover digital elegan).
   - Klik **Simpan Koleksi Buku**.
2. **Mengubah / Memperbarui Data Buku**:
   - Cari buku pada tabel, klik tombol **Aksi** lalu pilih **Ubah / Edit**.
   - Perbarui data yang diperlukan lalu klik **Simpan Perubahan**.
3. **Menghapus Buku**:
   - *Catatan Keamanan*: Sistem secara otomatis melarang penghapusan buku yang sedang memiliki transaksi peminjaman aktif (`dipinjam`) atau pengajuan tertunda (`pending`).

#### B. Manajemen Lokasi Rak & Laci (`Menu: Lokasi Rak`)
1. Penataan rak memudahkan siswa dan petugas menemukan fisik buku di ruang perpustakaan.
2. Anda dapat menambahkan kode rak (misalnya `RAK-01`, `RAK-02`), nama rak, lokasi lantai/ruangan, serta menambahkan sub-tingkatan laci (misalnya `Laci 1`, `Laci 2`, `Laci 3`).
3. Terdapat fitur **Peta Denah Rak 2D** yang menampilkan visualisasi tata letak rak secara interaktif.

#### C. Menemukan Buku di Rak (`Menu: Temukan Buku`)

Menu ini untuk saat siswa datang menanyakan sebuah judul dan Anda perlu tahu
buku itu ada di rak mana.

1. Ketik judul, penulis, atau ISBN pada kotak pencarian. Saran judul muncul
   sementara Anda mengetik — pilih salah satu untuk langsung membukanya.
2. Sistem menampilkan **kode rak**, **nama laci**, dan keterangan posisi
   tambahan bila diisi, beserta sisa stoknya.
3. Dari halaman ini Anda bisa langsung menekan **Pinjamkan** untuk mencatat
   peminjaman tanpa berpindah menu.

#### D. Denah Koleksi per Rak (`Menu: Data Koleksi Buku`)

Tampilan menelusuri koleksi lewat susunan fisiknya: pilih rak, lalu pilih laci,
lalu lihat buku apa saja yang ada di dalamnya. Berguna saat menata ulang rak
atau melakukan stok opname.

#### E. Data Pendukung: Kategori, Penulis, Penerbit, Kelas

Empat menu ini mengisi pilihan pada formulir buku. Semuanya punya penjagaan
yang sama: **tidak bisa dihapus selama masih dipakai oleh buku**. Bila muncul
pesan penolakan, pindahkan dulu buku-bukunya ke data lain, baru hapus.

##### Menu Kelas — aturan penamaan yang perlu dipahami

Kelas dipakai menandai buku ini diperuntukkan bagi angkatan yang mana. Isinya
dua kolom: **Tingkat** (10, 11, 12 — boleh juga ditulis X, XI, XII) dan **Nama
Kelas** (misalnya `RPL`, `DKV`, `XI TKJ 1`).

Sistem menolak kelas kembar, dan yang dibandingkan bukan tulisannya melainkan
maknanya. Empat penulisan berikut dianggap **kelas yang sama persis**:

| Tingkat | Nama Kelas |
|---------|------------|
| 11      | `RPL`      |
| 11      | `11 RPL`   |
| 11      | `XI RPL`   |
| *(kosong)* | `11 RPL` |

Yang disamakan: huruf besar/kecil (`DKV` = `dkv`), spasi (`11 dkv` = `11dkv`),
angka Romawi (`XI` = `11`), dan pengulangan tingkat di dalam nama.

Yang **tetap boleh** — dan memang seharusnya boleh — adalah jurusan yang sama
di angkatan berbeda: `10 + RPL`, `11 + RPL`, dan `12 + RPL` adalah tiga kelas
yang berbeda.

Satu lagi: **tingkat tidak boleh bertentangan dengan namanya sendiri**. Memilih
Tingkat 11 tetapi menulis nama `XII DKV` akan ditolak, karena XII berarti 12.
Samakan dulu keduanya — perbaiki tingkatnya, atau hapus angka dari nama kelas.

> Bila muncul pesan "sudah terdaftar" padahal menurut Anda tulisannya berbeda,
> pesan itu menyebutkan nama kelas yang sudah ada. Cari kelas tersebut di
> daftar; kemungkinan besar itu kelas yang Anda maksud, hanya ditulis dengan
> gaya lain oleh petugas sebelumnya.

Daftar kelas selalu terurut menurut jenjangnya, bukan menurut tulisannya —
kelas 9 tetap di atas kelas 10 walau ditulis `IX`, dan `XI` tidak melompat ke
urutan teratas.

#### F. Menyetujui Pengajuan Siswa (`Menu: Request Peminjaman`)

Menu ini berdiri sendiri di bilah samping, terpisah dari Sirkulasi Pinjam.
Angka di sebelah namanya menunjukkan berapa pengajuan yang masih menunggu.

1. Buka tab **Menunggu**. Setiap baris menampilkan identitas siswa, nomor
   WhatsApp, buku yang diminta, letak rak & lacinya, serta dua angka penting:
   - **Diminta**: berapa eksemplar yang siswa minta, dari total yang dimiliki
     perpustakaan — misalnya `Diminta: 2 dari 10 eksemplar`.
   - **Sisa Stok**: berapa yang masih tersedia saat ini.

   Keduanya saling melengkapi: yang pertama menjawab *berapa yang akan keluar*,
   yang kedua menjawab *apakah stoknya cukup*.
2. **Menyetujui**: klik **Setujui**. Stok fisik langsung dipotong, dan tanggal
   jatuh tempo dihitung otomatis dari Durasi Pinjam yang diatur di Pengaturan.
3. **Menolak**: klik **Tolak**, lalu tulis alasannya. Alasan ini akan dibaca
   siswa, jadi tulislah kalimat yang bisa dimengerti — misalnya "Buku sedang
   dalam perbaikan sampul". Penolakan **tidak** memotong stok.

> **Yang dilihat siswa.** Setelah mengajukan dari katalog, layar siswa
> menampilkan popup "Menunggu Verifikasi Petugas" selama halamannya masih
> terbuka. Begitu Anda menekan Setujui atau Tolak, popup itu berubah sendiri
> menjadi tanda centang (beserta kode dan jatuh temponya) atau tanda silang
> beserta alasan yang Anda tulis. Jadi usahakan memproses pengajuan selagi
> siswa masih menunggu di tempat. Bila Anda belum sempat memutuskan dalam
> beberapa menit, popupnya berhenti sendiri dan meminta siswa menanyakan ke
> meja sirkulasi dengan kode referensinya — pengajuannya tetap tercatat.

#### G. Sirkulasi & Pengembalian (`Menu: Sirkulasi Pinjam`)

##### 1. Pencatatan Peminjaman Langsung di Meja Petugas (Walk-in):
- Klik tombol **Catat Peminjaman Baru**.
- Masukkan Nama Peminjam, Kelas/Jurusan, Nomor Induk/NISN, No. WhatsApp, dan pilih Buku yang dipinjam beserta jumlah eksemplar.
- Klik **Simpan Peminjaman**. Tanggal jatuh tempo dihitung otomatis dari Durasi
  Pinjam yang diatur di Pengaturan.

##### 2. Pengembalian Buku:
- Pada tabel transaksi aktif, klik tombol **Konfirmasi Kembali**.
- Status transaksi akan berubah menjadi **Selesai (Dikembalikan)**, waktu pengembalian tercatat, dan stok fisik buku otomatis bertambah kembali di rak.
- Hanya peminjaman yang benar-benar berjalan yang bisa dikembalikan. Pengajuan
  yang belum disetujui atau sudah ditolak tidak pernah memotong stok, jadi
  tidak ada yang perlu dikembalikan — sistem akan menolaknya.

##### 3. Pemantauan Buku Terlambat:
- Buka tab filter **Terlambat** untuk menyaring semua peminjaman yang telah melewati batas tanggal jatuh tempo.
- Hubungi siswa bersangkutan melalui kontak WhatsApp yang tercatat. Nomornya
  bisa langsung diklik untuk membuka WhatsApp.

##### 4. Riwayat Lengkap (`Menu: Riwayat`):
- Seluruh transaksi yang pernah terjadi, dengan penyaring status dan tanggal.

---

### 4. LAPORAN & REKAPITULASI DATA

#### A. Rekapitulasi Koleksi Buku (`Menu: Koleksi Buku > Rekapitulasi`)
- **Unduh Excel**: Menghasilkan berkas `.xls` berisi No, Judul, ISBN, Kelas,
  Penulis, Penerbit, Tahun Terbit, dan Total Stok, lengkap dengan kop instansi.
  Berkasnya dibuka normal oleh Microsoft Excel, LibreOffice Calc, maupun Google
  Sheets. Bila Excel menampilkan peringatan "format berbeda dari ekstensinya"
  saat dibuka, pilih **Yes / Ya** — isinya tetap utuh. Sistem sengaja tidak
  memakai pustaka spreadsheet tambahan agar tetap ringan di hosting sekolah.
- **Cetak PDF (Format A4)**: Menghasilkan dokumen cetak resmi lengkap dengan Kop Surat Sekolah, tabel inventaris, dan blok tanda tangan formal 2 kolom simetris antara Petugas Administrasi dan Kepala Perpustakaan.

#### B. Rekapitulasi Sirkulasi Peminjaman (`Menu: Sirkulasi Pinjam > Laporan`)
- Menyediakan laporan rekapitulasi transaksi sirkulasi harian/bulanan dalam format PDF resmi dengan statistik total transaksi, buku dipinjam, buku kembali, dan tanda tangan pejabat berwenang.

---

### 5. PROFIL AKUN & KEAMANAN

Seluruh pengguna dapat meninjau data akunnya sendiri, namun **penggantian kata
sandi hanya dapat dilakukan oleh Super Administrator**. Ketentuan ini berlaku
agar kendali keamanan akun terpusat pada satu penanggung jawab.

#### Untuk Semua Pengguna (Petugas & Super Admin)
1. Buka menu **Profil & Keamanan** di bilah navigasi samping.
2. Di halaman ini Anda dapat melihat rincian nama akun, email, dan hak akses (role).

#### Mengubah Kata Sandi Sendiri (Khusus Super Administrator)
1. Buka menu **Profil & Keamanan**.
2. Isi **Kata Sandi Saat Ini**, **Kata Sandi Baru** (minimal 8 karakter), dan
   **Konfirmasi Kata Sandi Baru**.
3. Klik **Perbarui Kata Sandi**.

> Formulir penggantian kata sandi tidak ditampilkan pada akun Petugas. Ini
> perilaku yang memang dirancang demikian, bukan kerusakan sistem.

#### Bila Petugas Perlu Mengganti Kata Sandi
Petugas menghubungi Super Administrator, lalu Super Administrator mereset kata
sandi tersebut melalui menu **Akun Pengelola**:
1. Buka menu **Akun Pengelola**.
2. Cari nama petugas yang bersangkutan.
3. Pilih tindakan **Reset Password**, lalu masukkan kata sandi baru beserta
   konfirmasinya.
4. Sampaikan kata sandi baru itu kepada petugas yang bersangkutan.

> Dalam keadaan darurat, misalnya seluruh akun Super Admin tidak dapat diakses,
> teknisi sekolah dapat mereset kata sandi melalui terminal server dengan
> perintah `php artisan perpus:reset-password <email>`.

---

### 6. CATATAN AKTIVITAS (`Menu: Audit Log` — Khusus Super Admin)

Setiap tindakan penting tercatat otomatis beserta nama pelaku, waktu, dan
alamat IP-nya: login berhasil maupun gagal, penambahan dan perubahan buku,
persetujuan dan penolakan pengajuan, pengembalian, pencetakan laporan,
penggantian kata sandi, dan pengunduhan cadangan.

Catatan ini tidak bisa dihapus dari dalam aplikasi. Gunakan saat perlu
menelusuri "siapa mengubah apa, kapan" — misalnya ketika data terasa berubah
tanpa diketahui.

---

### 7. PENGATURAN INSTANSI & SISTEM (KHUSUS SUPER ADMIN)

Bagi pengguna dengan hak akses **Super Admin**, menu **Pengaturan Sistem** dapat digunakan untuk:
1. **Identitas Sekolah & Perpustakaan**:
   - Nama Sekolah, Nama Perpustakaan, NPSN, Alamat Lengkap, Website, Nomor
     Telepon, dan Email Resmi.
   - **Kota / Kabupaten** — dicetak mendahului tanggal pada blok tanda tangan
     setiap laporan, misalnya "Sukabumi, 30 Agustus 2026". **Isi ini lebih
     dulu sebelum mencetak laporan resmi**; bila dikosongkan, yang tercetak
     hanya tanggalnya saja.
   - Nama Kepala Perpustakaan dan NIP (otomatis muncul pada lembar tanda tangan laporan PDF).
2. **Aturan Sirkulasi & Tampilan Katalog**:
   - **Durasi Pinjam Standar (Hari)**: Jumlah hari peminjaman standar per
     transaksi. Angka inilah yang dipakai menghitung tanggal jatuh tempo, baik
     untuk pengajuan yang disetujui maupun pencatatan langsung.
   - **Jam Operasional** (dan jam khusus Jumat), **Pesan Sirkulasi**, serta
     **Syarat Peminjaman**: teks yang tampil di halaman katalog publik.
   - **Judul & Subjudul Hero**: tulisan besar di beranda katalog.
   - **Buku per Halaman**: banyaknya buku yang ditampilkan per halaman katalog.
3. **Pencadangan Database Langsung (Backup)**:
   - Pada bagian Informasi Sistem & Database, klik tombol **Unduh Cadangan SQL (.sql)** untuk langsung mengunduh salinan basis data terbaru ke komputer Anda.
