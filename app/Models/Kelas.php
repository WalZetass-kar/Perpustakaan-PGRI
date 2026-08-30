<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    protected $table = 'kelas';
    protected $fillable = ['tingkat', 'nama_kelas', 'deskripsi'];

    /**
     * Keduanya hanya alat bantu internal — satu untuk membandingkan, satu untuk
     * mengurutkan — dan tidak berarti apa-apa bagi pembacanya. Disembunyikan
     * dari serialisasi supaya tidak ikut terkirim kalau suatu saat ada halaman
     * atau endpoint baru yang mengembalikan model ini sebagai JSON.
     */
    protected $hidden = ['kunci_unik', 'tingkat_angka'];

    /**
     * `kunci_unik` dan `tingkat_angka` sengaja tidak ikut $fillable: keduanya
     * bukan isian petugas, melainkan selalu dihitung ulang dari tingkat +
     * nama_kelas tepat sebelum baris disimpan. Ditaruh di event `saving`, bukan
     * di service, supaya tidak ada jalur penyimpanan yang bisa melewatkannya —
     * termasuk seeder, tinker, dan impor data di kemudian hari.
     */
    protected static function booted(): void
    {
        static::saving(function (self $kelas) {
            $kelas->kunci_unik = static::kunciUnik($kelas->tingkat, $kelas->nama_kelas);
            $kelas->tingkat_angka = static::angkaTingkat($kelas->tingkat);
        });
    }

    public function buku()
    {
        return $this->hasMany(Buku::class);
    }

    /**
     * Bentuk baku sebuah kelas untuk dibandingkan dengan kelas lain.
     *
     * Tingkat ikut menjadi bagian kunci karena satu jurusan memang hidup di
     * beberapa angkatan sekaligus: "10 DKV" dan "11 DKV" adalah dua kelas yang
     * berbeda dan keduanya sah. Yang dilarang hanyalah tingkat DAN nama yang
     * sama-sama kembar.
     *
     * Tingkat yang kosong tetap menghasilkan potongan kunci berupa string
     * kosong, bukan null. Ini penting: indeks unik di MySQL maupun SQLite
     * menganggap dua NULL sebagai nilai yang berbeda, sehingga kelas tanpa
     * tingkat justru akan lolos berkali-kali kalau nilainya dibiarkan null.
     */
    public static function kunciUnik(?string $tingkat, ?string $namaKelas): string
    {
        $bagianTingkat = static::normalkan($tingkat);
        $bagianNama    = static::normalkan($namaKelas);

        $tingkatDipilih = static::angkaTingkat($tingkat);
        $tingkatDiNama  = static::tingkatDiAwalNama($namaKelas);

        // Petugas menuliskan jenjang dengan dua kebiasaan yang sama-sama benar:
        // "tingkat 11 + nama 11 RPL" dan "tingkat 11 + nama RPL" memaksudkan
        // kelas yang sama persis. Kalau jenjang di awal nama cuma mengulang
        // tingkatnya, ia dibuang dari pembanding sehingga keduanya bertemu di
        // satu kunci. Tanpa ini, satu kelas bisa tercatat dua kali hanya karena
        // yang kedua tidak ikut menuliskan angkanya.
        //
        // Ikut berlaku saat kolom tingkat dikosongkan dan jenjangnya hanya ada
        // di dalam nama: "11 RPL" tanpa tingkat pun bertemu dengan
        // "tingkat 11 + RPL".
        //
        // Yang TIDAK dibuang adalah jenjang yang bertentangan dengan tingkatnya
        // — mis. tingkat 10 dengan nama "12 Pariwisata". Membuangnya berarti
        // menebak mana yang benar, padahal justru pertentangan itu yang harus
        // tetap terlihat (lihat App\Rules\TingkatSelarasDenganNama).
        if ($tingkatDiNama !== null && ($tingkatDipilih === null || $tingkatDipilih === $tingkatDiNama)) {
            $bagianTingkat = (string) $tingkatDiNama;
            $bagianNama    = substr($bagianNama, strlen((string) $tingkatDiNama));
        }

        return $bagianTingkat . '|' . $bagianNama;
    }

    /**
     * Angka Romawi tingkat sekolah beserta padanan angka biasanya.
     *
     * Sengaja dibatasi I sampai XII — persis rentang jenjang SD sampai SMA/SMK
     * — bukan angka Romawi pada umumnya. Pembatasan ini yang menjaga nama
     * jurusan tidak ikut tertukar jadi angka: "MI" memang angka Romawi yang
     * sah (1001), tetapi tidak ada kelas ke-1001, sedangkan "MI" sebagai nama
     * memang dipakai sekolah.
     *
     * Urutannya sengaja dari yang terpanjang supaya "xii" tidak keburu
     * tercocokkan sebagian sebagai "xi".
     */
    private const ROMAWI = [
        'viii' => '8',
        'xii'  => '12',
        'vii'  => '7',
        'iii'  => '3',
        'xi'   => '11',
        'ix'   => '9',
        'vi'   => '6',
        'iv'   => '4',
        'ii'   => '2',
        'x'    => '10',
        'v'    => '5',
        'i'    => '1',
    ];

    /**
     * Menyamakan tulisan yang secara praktis berarti sama.
     *
     * Tiga hal disamakan di sini:
     *
     * 1. Huruf besar/kecil, supaya "DKV", "Dkv", dan "dkv" tidak bisa berdiri
     *    sendiri-sendiri.
     * 2. Angka Romawi jenjang, supaya "X DKV" dan "10 DKV" dikenali sebagai
     *    kelas yang sama. Sekolah menulis tingkat dengan dua kebiasaan yang
     *    sama-sama benar, dan tanpa penyetaraan ini seluruh daftar kelas bisa
     *    tercatat dobel hanya karena dua petugas punya kebiasaan berbeda.
     * 3. Spasi, yang dibuang seluruhnya dan bukan sekadar dirapatkan. Di data
     *    lapangan "11 dkv" dan "11dkv" sudah pernah masuk berdampingan sebagai
     *    dua baris berbeda, padahal maksudnya satu kelas — dan merapatkan
     *    spasi ganda saja tidak menyatukan keduanya.
     *
     * Urutannya penting: Romawi harus diterjemahkan SELAGI spasinya masih ada,
     * karena yang menentukan sebuah huruf itu angka atau bagian nama jurusan
     * adalah batas katanya. Setelah spasi hilang, "x dkv" dan "xdkv" tidak lagi
     * bisa dibedakan dari "vokasi" yang kebetulan berawalan huruf Romawi.
     *
     * Spasi tak-putus (U+00A0) yang sering terbawa saat menyalin dari Word atau
     * Excel dibersihkan lebih dulu, karena \s pada regex tidak mengenalinya
     * sebagai spasi.
     */
    private static function normalkan(?string $nilai): string
    {
        $nilai = str_replace("\u{00A0}", ' ', (string) $nilai);
        $nilai = mb_strtolower($nilai, 'UTF-8');

        // Hanya angka Romawi yang berdiri sebagai kata utuh yang diterjemahkan.
        // Tanpa syarat batas kata ini, "vokasi" berubah jadi "5okasi" dan
        // "iklan" jadi "1klan".
        $nilai = preg_replace_callback(
            '/(?<![a-z0-9])(' . implode('|', array_keys(self::ROMAWI)) . ')(?![a-z0-9])/u',
            fn (array $cocok) => self::ROMAWI[$cocok[1]],
            $nilai
        );

        return preg_replace('/\s+/u', '', $nilai);
    }

    /**
     * Angka tingkat dari sebuah isian `tingkat`, atau null bila isian itu
     * bukan penunjuk jenjang. "11" dan "XI" sama-sama menghasilkan 11, karena
     * normalkan() sudah lebih dulu menyetarakan angka Romawi.
     */
    public static function angkaTingkat(?string $tingkat): ?int
    {
        $baku = static::normalkan($tingkat);

        return preg_match('/^\d{1,2}$/', $baku) === 1 ? (int) $baku : null;
    }

    /**
     * Angka tingkat yang tertulis di AWAL nama kelas, atau null bila namanya
     * tidak diawali penunjuk jenjang.
     *
     * Petugas terbiasa mengulang tingkat di dalam nama ("11 DKV", "xii Mesin"),
     * dan pengulangan itu bisa bertentangan dengan kolom tingkatnya sendiri.
     * Nilai inilah yang dibandingkan oleh aturan TingkatSelarasDenganNama.
     *
     * Bekerja di atas hasil normalkan(), jadi Romawi sudah menjadi angka dan
     * spasi sudah hilang: "xii Mesin" menjadi "12mesin", "X RPL" menjadi
     * "10rpl", sedangkan "Vokasi" tetap "vokasi" dan menghasilkan null.
     *
     * Deretan angka yang lebih panjang dari dua digit sengaja diabaikan supaya
     * nama seperti "2024 Angkatan" tidak dikira tingkat 20.
     */
    public static function tingkatDiAwalNama(?string $namaKelas): ?int
    {
        $baku = static::normalkan($namaKelas);

        return preg_match('/^(\d{1,2})(?!\d)/', $baku, $cocok) === 1 ? (int) $cocok[1] : null;
    }

    /**
     * Kelas lain yang tingkat dan namanya sama dengan yang hendak disimpan,
     * atau null bila belum ada. `$kecualiId` diisi saat mengubah data supaya
     * sebuah kelas tidak dianggap kembar dengan dirinya sendiri.
     */
    public static function kembarDengan(?string $tingkat, ?string $namaKelas, ?int $kecualiId = null): ?self
    {
        return static::where('kunci_unik', static::kunciUnik($tingkat, $namaKelas))
            ->when($kecualiId, fn ($q) => $q->whereKeyNot($kecualiId))
            ->first();
    }

    /**
     * Label siap pakai untuk laporan & dropdown: "10 - X RPL 1". Kalau tingkat
     * belum diisi (kelas lama sebelum migrasi, atau kelas non-tingkat), cukup
     * tampilkan nama kelasnya saja.
     */
    public function getLabelLengkapAttribute(): string
    {
        return $this->tingkat
            ? $this->tingkat . ' - ' . $this->nama_kelas
            : $this->nama_kelas;
    }
}
