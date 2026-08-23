# Catatan Perbaikan Sistem Perpustakaan (Katalog OPAC)

Status: **selesai dikerjakan** — 23 Agustus 2026.
Dokumen ini semula berisi daftar instruksi; sekarang ditulis ulang sebagai
catatan apa yang benar-benar dikerjakan, supaya bisa dipakai waktu review.

---

## 1. Keamanan Route `/admin`

### Hasil audit: laporan awal tidak tereproduksi

Sebelum mengubah apa pun, seluruh route diuji dengan HTTP request nyata tanpa
sesi login:

| URL | Hasil |
|---|---|
| `/admin` | 404 (tidak ada route) |
| `/admin/dashboard` | 302 → `/aksesperpuspgri` |
| `/admin/buku` | 302 → `/aksesperpuspgri` |
| `/admin/buku/export/excel` | 302 → `/aksesperpuspgri` |
| `/dashboard` | 302 → `/aksesperpuspgri` |

Seluruh route admin memang **sudah** berada di dalam grup
`Route::middleware(['auth'])` di `routes/web.php`. Tidak ada yang lolos.

Penyebab sebenarnya: **browser masih menyimpan sesi login yang aktif** (session
120 menit / cookie remember me), jadi mengetik `/admin` langsung masuk — itu
perilaku yang benar, bukan celah. Sudah dikonfirmasi.

Bug "response header aneh" di localhost juga tidak ditemukan. Konfigurasi
cookie sudah cocok untuk http: `config/session.php` → `secure` = null (false),
`same_site` = `lax`, `http_only` = true. Tidak ada masalah CORS.

### Dua celah nyata yang ditemukan dan ditutup

**a. `/admin` polos memberi 404, bukan redirect ke login**

Acceptance criteria minta diarahkan ke login. Ditambahkan route `/admin` di
dalam grup auth yang redirect ke dashboard, sehingga tamu tertahan middleware
dan dilempar ke halaman login.

**b. Middleware `CheckRole` menganggur, dan status akun hanya dicek saat login**

`app/Http/Middleware/CheckRole.php` sudah ada tapi tidak pernah dipasang di
route mana pun. Akibatnya admin yang dinonaktifkan di tengah sesi tetap bisa
memakai seluruh fitur admin sampai sesinya habis sendiri.

Perbaikan:
- `role:admin,super_admin` dipasang ke seluruh grup route admin.
- `CheckRole` sekarang mengevaluasi ulang status akun **di setiap request**,
  dan langsung logout + invalidate session kalau status bukan `active`.

Terverifikasi:

```
sebelum dinonaktifkan  → /admin/dashboard: 200
[status diubah ke inactive, sesi TIDAK disentuh]
sesudah dinonaktifkan  → /admin/dashboard: 302 → /aksesperpuspgri
```

### Test

`tests/Feature/AdminAksesTest.php` berisi 23 test yang menutup seluruh kondisi
di acceptance criteria: belum login, sudah login, sesi kedaluwarsa, setelah
logout, login gagal, dan login berhasil.

**Status: seluruh 25 test di proyek ini lulus.**

```
Tests: 25, Assertions: 64
```

Agar bisa sampai ke titik ini ada tiga hal yang perlu dibereskan:

1. `phpunit.xml` sebelumnya tidak mengarahkan test ke database terpisah — kalau
   dijalankan apa adanya, `RefreshDatabase` akan **mengosongkan database
   `perpustakaan` yang asli**. Sekarang dikunci ke sqlite in-memory; baris itu
   jangan dikomentari lagi. (Sudah diverifikasi: jumlah data MySQL sebelum dan
   sesudah test identik — 10 buku, 2 user, 2 kelas.)
2. Migration `2026_08_18_120000_add_request_fields_to_peminjaman_table.php`
   memakai `ALTER TABLE ... MODIFY COLUMN` yang hanya dikenal MySQL. Sekarang
   dibuat driver-aware: **jalur MySQL tetap memakai pernyataan aslinya persis**,
   driver lain memakai padanan Schema builder.
3. `ExampleTest` bawaan Laravel tidak menyiapkan skema padahal halaman depan
   membaca tabel buku. Ditambahkan `RefreshDatabase`.

### Catatan: `/admin/dashboard` tidak diuji dalam kondisi login

Query grafik dashboard memakai `MONTH()` dan `YEAR()` yang khas MySQL
(`AdminController.php` baris 49–82), sehingga tidak jalan di sqlite. Ini
**keterbatasan test, bukan celah akses** — penolakan tamu untuk
`/admin/dashboard` tetap diuji, dan render dashboard saat login sudah
diverifikasi manual lewat HTTP (200).

Kalau nanti dashboard mau ikut tercakup test, query itu perlu ditulis portabel
lebih dulu. Sengaja tidak dikerjakan sekarang supaya angka di dashboard yang
sudah dipakai tidak berisiko berubah.

### Peringatan deprecated saat test

Muncul `Constant PDO::MYSQL_ATTR_SSL_CA is deprecated since 8.5`. Sumbernya di
`vendor/laravel/framework/config/database.php`, **bukan** kode proyek ini, jadi
tidak diapa-apakan. Tidak memengaruhi hasil test dan akan hilang sendiri saat
Laravel diperbarui.

---

## 2. Layout Mobile — Menu Koleksi Buku

### Penyebab

Ketemu di `git log`: commit `49a33bf` mengubah tombol export dari
`hidden lg:flex` menjadi selalu tampil. Akibatnya di bawah 640px ada empat
tombol berdesakan dalam satu baris, dan teksnya dipangkas jadi ikon saja.

Lebih dari itu, ternyata ada **tiga** blok export di halaman yang sama:

1. baris toolbar (di luar panel filter),
2. baris tambahan di bawah toolbar,
3. di dasar panel filter yang di-toggle tombol panah.

Sehingga di mobile blok 2 dan 3 muncul bersamaan, di tablet blok 1 dan 3 —
duplikasi fitur.

### Perbaikan

Disisakan **satu** lokasi per ukuran layar, tidak pernah tumpang tindih:

| Lebar layar | Tombol export tampil di |
|---|---|
| ≥ 1024px (desktop) | baris toolbar (`hidden lg:flex`) |
| < 1024px (tablet & mobile) | dasar panel filter (`lg:hidden`) |

Blok tambahan di luar panel dihapus. Teks tombol tetap utuh, tidak dipangkas
jadi ikon. **Tampilan desktop ≥1024px tidak berubah sama sekali.**

Terverifikasi dari HTML yang dirender: tepat 2 blok export, dan keduanya
saling eksklusif secara breakpoint.

---

## 3. Data Kelas pada Buku + Export Excel/PDF

### Yang ternyata sudah ada

Master Kelas sudah dibuat sejak commit `a9f55c4`: model, CRUD lengkap, kolom
`kelas_id` nullable di tabel buku, dropdown di form tambah/edit buku, filter
katalog, dan badge kelas di tabel.

### Yang belum ada dan dikerjakan

**a. Field tingkat**

Tabel `kelas` semula hanya punya `nama_kelas` + `keterangan`. Lewat migration
`2026_08_23_100000_add_tingkat_to_kelas_table.php`:

- kolom `tingkat` ditambahkan (nullable),
- `keterangan` di-rename jadi `deskripsi` agar selaras dengan tabel `kategori`.

Urutan field di form sekarang: **tingkat → nama_kelas → deskripsi**.

Tingkat **diketik bebas** oleh petugas (bukan dropdown), boleh dikosongkan,
maksimal 10 karakter. Jadi "10", "11", "12", "X", "XI IPA" semuanya bisa.

Migration sudah diuji rollback bolak-balik dan bersih:

```
migrate  → [id, tingkat, nama_kelas, deskripsi, ...]
rollback → [id, nama_kelas, keterangan, ...]   ← utuh seperti semula
migrate  → oke lagi
```

**b. Kolom Kelas di export**

Export Excel sudah eager-load relasi `kelas` dan menerima filter `kelas_id`,
tapi kolomnya tidak pernah dirender. Export PDF malah tidak eager-load sama
sekali. Keduanya diperbaiki: kolom "Kelas" ditambahkan, seluruh `colspan`
(banner, KPI, baris total, blok tanda tangan) disesuaikan dari 8 ke 9 kolom.

Hasil verifikasi kedua format:

```
['No','Judul Buku','ISBN','Penulis','Penerbit','Tahun','Kategori','Kelas','Total']
['1','Administrasi Infrastru',...,'Teknik Komputer & Jari','-','4']
['2','Bahasa Indonesia',...,'Bahasa Indonesia','12 Pariwisata','10']
```

Buku tanpa kelas tampil sebagai `-`, bukan error.

### Keputusan desain

- **Relasi tetap one-to-many**, bukan many-to-many. Alasannya acceptance
  criteria menyebut "kelas bisa dipilih" (tunggal) dan one-to-many sudah jalan
  end-to-end. Kalau nanti satu buku perlu dipakai kelas 10 dan 11 sekaligus,
  perlu tabel pivot dan refactor form/filter/export.
- **Export tetap mengekspor seluruh buku**, tidak mengikuti filter yang sedang
  aktif di layar. Ini keputusan sadar sesuai permintaan, walaupun controller-nya
  sudah mendukung filter `kategori_id`, `rak_id`, dan `kelas_id`.

---

## Regression Test

Seluruh halaman publik OPAC diuji ulang setelah perubahan skema:

| URL | Status |
|---|---|
| `/` | 200 |
| `/katalog` | 200 |
| `/katalog?search=bahasa` | 200 |
| `/katalog?kelas_id=1` | 200 |
| `/api/buku/search-suggestions?q=bahasa` | 200 |
| `/buku/1` | 200 |

Seluruh data uji (user sementara, kelas uji, dan audit log-nya) sudah dihapus
bersih. Jumlah user kembali ke 2 seperti semula.

---

## Langkah Manual yang Masih Perlu Dilakukan

1. `php artisan migrate` — di environment lain yang belum menjalankan migration
   `tingkat`/`deskripsi`.
2. `sudo apt install php8.5-sqlite3` — **sudah dilakukan** 23 Agustus 2026.

## Berkas yang Berubah

```
app/Http/Controllers/AdminController.php
app/Http/Middleware/CheckRole.php
app/Models/Kelas.php
database/migrations/2026_08_23_100000_add_tingkat_to_kelas_table.php  (baru)
phpunit.xml
resources/views/admin/buku/export-pdf.blade.php
resources/views/admin/buku/index.blade.php
database/migrations/2026_08_18_120000_add_request_fields_to_peminjaman_table.php
resources/views/admin/kelas/index.blade.php
routes/web.php
tests/Feature/AdminAksesTest.php  (baru)
tests/Feature/ExampleTest.php
```
