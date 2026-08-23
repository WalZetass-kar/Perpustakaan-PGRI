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

#### C. Sirkulasi Peminjaman & Pengembalian (`Menu: Sirkulasi Pinjam`)

Terdapat 2 cara peminjaman:

##### 1. Menyetujui Permohonan Pinjam Online dari Siswa (OPAC Request):
- Pada halaman Sirkulasi, buka tab **Pengajuan Online (Pending)**.
- Periksa data siswa, jurusan, dan buku yang diajukan.
- Klik **Setujui Peminjaman** untuk mengonfirmasi bahwa buku fisik telah diserahkan ke siswa.
- Batas waktu pengembalian (jatuh tempo) akan otomatis dihitung berdasarkan durasi hari peminjaman yang diatur di sistem.

##### 2. Pencatatan Peminjaman Langsung di Meja Petugas (Walk-in):
- Klik tombol **Catat Peminjaman Baru**.
- Masukkan Nama Peminjam, Kelas/Jurusan, Nomor Induk/NISN, No. WhatsApp, dan pilih Buku yang dipinjam beserta jumlah eksemplar.
- Tentukan tanggal peminjaman dan tanggal jatuh tempo.
- Klik **Simpan Peminjaman**.

##### 3. Pengembalian Buku:
- Pada tabel transaksi aktif, klik tombol **Konfirmasi Kembali**.
- Status transaksi akan berubah menjadi **Selesai (Dikembalikan)**, waktu pengembalian tercatat, dan stok fisik buku otomatis bertambah kembali di rak.

##### 4. Perpanjangan Masa Pinjam:
- Jika siswa meminta perpanjangan waktu, klik tombol **Perpanjang**.
- Tanggal jatuh tempo akan diperpanjang sesuai durasi standar dan jumlah perpanjangan akan dicatat. Sistem akan membatasi perpanjangan jika telah mencapai batas maksimal.

##### 5. Pemantauan Buku Terlambat:
- Buka tab filter **Terlambat** untuk menyaring semua peminjaman yang telah melewati batas tanggal jatuh tempo.
- Hubungi siswa bersangkutan melalui kontak WhatsApp yang tercatat.

---

### 4. LAPORAN & REKAPITULASI DATA

#### A. Rekapitulasi Koleksi Buku (`Menu: Koleksi Buku > Rekapitulasi`)
- **Unduh Excel**: Menghasilkan berkas `.xlsx` rapi berisi No, Judul, ISBN, Kelas, Penulis, Penerbit, Tahun Terbit, dan Total Stok.
- **Cetak PDF (Format A4)**: Menghasilkan dokumen cetak resmi lengkap dengan Kop Surat Sekolah, tabel inventaris, dan blok tanda tangan formal 2 kolom simetris antara Petugas Administrasi dan Kepala Perpustakaan.

#### B. Rekapitulasi Sirkulasi Peminjaman (`Menu: Sirkulasi Pinjam > Laporan`)
- Menyediakan laporan rekapitulasi transaksi sirkulasi harian/bulanan dalam format PDF resmi dengan statistik total transaksi, buku dipinjam, buku kembali, dan tanda tangan pejabat berwenang.

---

### 5. PROFIL AKUN & KEAMANAN MANDIRI

Petugas dan Admin dapat memperbarui profil dan kata sandi masing-masing secara mandiri:
1. Buka menu **Profil & Keamanan** di bilah navigasi samping.
2. Di halaman ini, Anda dapat:
   - Melihat rincian nama akun, email, dan hak akses (role).
   - Mengubah kata sandi dengan memasukkan **Kata Sandi Saat Ini**, **Kata Sandi Baru** (minimal 8 karakter), dan **Konfirmasi Kata Sandi Baru**.
3. Klik **Perbarui Kata Sandi**.

---

### 6. PENGATURAN INSTANSI & SISTEM (KHUSUS SUPER ADMIN)

Bagi pengguna dengan hak akses **Super Admin**, menu **Pengaturan Sistem** dapat digunakan untuk:
1. **Identitas Sekolah & Perpustakaan**:
   - Nama Sekolah, Nama Perpustakaan, NPSN, Alamat Lengkap, Kota, Nomor Telepon, dan Email Resmi.
   - Nama Kepala Perpustakaan dan NIP (otomatis muncul pada lembar tanda tangan laporan PDF).
2. **Aturan Sirkulasi & Peminjaman**:
   - **Durasi Pinjam Standar (Hari)**: Jumlah hari peminjaman standar per transaksi.
   - **Batas Maksimal Buku Dipinjam**: Maksimum buku aktif yang boleh dipinjam oleh satu siswa.
   - **Batas Maksimal Perpanjangan**: Maksimum berapa kali masa pinjam boleh diperpanjang.
   - **Jam Operasional & Pesan Sirkulasi**: Teks informasi yang tampil di halaman katalog publik.
3. **Pencadangan Database Langsung (Backup)**:
   - Pada bagian Informasi Sistem & Database, klik tombol **Unduh Cadangan SQL (.sql)** untuk langsung mengunduh salinan basis data terbaru ke komputer Anda.
