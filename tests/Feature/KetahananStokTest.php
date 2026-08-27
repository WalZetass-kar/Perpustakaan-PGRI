<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Rak;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Sistem ini dipakai dari banyak komputer sekaligus. Yang diuji di sini adalah
 * hal-hal yang hanya rusak ketika dua orang bergerak bersamaan, atau ketika
 * satu orang menekan tombol dua kali — jenis kerusakan yang tidak pernah
 * muncul saat dicoba sendirian.
 */
class KetahananStokTest extends TestCase
{
    use RefreshDatabase;

    private function buatBuku(int $total = 3): Buku
    {
        $rak = Rak::firstOrCreate(
            ['kode_rak' => 'RAK-UJI'],
            ['nama_rak' => 'Rak Pengujian', 'lokasi' => 'Lantai 1', 'status' => 'aktif']
        );

        return Buku::create([
            'judul' => 'Buku Uji', 'isbn' => 'ISBN-' . uniqid(),
            'rak_id' => $rak->id, 'tahun_terbit' => 2024,
            'total_quantity' => $total, 'available_quantity' => $total, 'status' => 'tersedia',
        ]);
    }

    private function loginPetugas(string $email = 'petugas@uji.test'): User
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $user = User::create([
            'name' => 'Petugas Uji', 'email' => $email, 'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]);
        $this->actingAs($user);

        return $user;
    }

    private function ajukan(Buku $buku, array $ganti = [])
    {
        return $this->post(route('katalog.ajukan'), array_merge([
            'buku_id' => $buku->id, 'nama_peminjam' => 'Rani',
            'jurusan' => 'XII RPL', 'nomor_induk' => '0065', 'no_wa' => '081234567890',
            'jumlah' => 1,
        ], $ganti));
    }

    // ------------------------------------------------- balapan setujui vs tolak

    /**
     * Dua petugas, dua komputer, satu pengajuan: yang satu menekan "Setujui",
     * yang satu "Tolak". Dulu penolakan bisa menimpa persetujuan yang sudah
     * memotong stok, dan eksemplar itu hilang selamanya — pengajuan berstatus
     * `ditolak` tidak pernah muncul di halaman pengembalian.
     *
     * Yang menjaganya sekarang bukan hasil pembacaan status, melainkan syarat
     * `status = pending` yang menempel pada UPDATE-nya sendiri: satu pernyataan
     * SQL yang tidak bisa disela. Di sinilah syarat itu diperiksa, karena
     * SQLite yang dipakai test tidak mengenal penguncian baris sehingga
     * balapan sungguhan tidak bisa diperagakan di sini.
     */
    public function test_penolakan_hanya_mengubah_baris_yang_masih_pending(): void
    {
        $buku = $this->buatBuku(3);
        $this->ajukan($buku)->assertSessionHasNoErrors();
        $loan = Peminjaman::first();

        $this->loginPetugas();

        $updatePengajuan = [];
        DB::listen(function ($q) use (&$updatePengajuan) {
            if (str_starts_with(strtolower(trim($q->sql)), 'update') && str_contains($q->sql, 'peminjaman')) {
                $updatePengajuan[] = $q;
            }
        });

        $this->post(route('admin.peminjaman.request.reject', $loan->id), [
            'alasan_penolakan' => 'Buku dipakai kelas lain.',
        ])->assertSessionHas('success');

        $this->assertNotEmpty($updatePengajuan, 'Penolakan seharusnya menulis ke tabel peminjaman.');

        $sql = $updatePengajuan[0];
        $this->assertStringContainsString('"status" = ?', $sql->sql,
            'UPDATE penolakan harus dibatasi syarat status, bukan hanya id.');
        $this->assertContains('pending', $sql->bindings,
            'Syaratnya harus `status = pending`, supaya baris yang sudah disetujui petugas lain tidak ikut tertimpa.');
    }

    /**
     * Sisi perilakunya: begitu persetujuan lebih dulu masuk, penolakan harus
     * ditolak dan stok tetap sesuai status akhirnya.
     */
    public function test_pengajuan_yang_sudah_diproses_tidak_bisa_ditolak_lagi(): void
    {
        $buku = $this->buatBuku();
        $this->ajukan($buku);
        $loan = Peminjaman::first();

        $this->loginPetugas();
        $this->post(route('admin.peminjaman.request.approve', $loan->id));
        $this->post(route('admin.peminjaman.request.reject', $loan->id))
            ->assertSessionHas('error');

        $this->assertSame('dipinjam', $loan->refresh()->status);
        $this->assertSame(2, $buku->refresh()->available_quantity);
    }

    // ------------------------------------------------------------ antrean OPAC

    public function test_antrean_pending_tidak_boleh_melebihi_stok_fisik(): void
    {
        $buku = $this->buatBuku(1);

        $this->ajukan($buku, ['nama_peminjam' => 'Andi', 'nomor_induk' => '111'])
            ->assertSessionHasNoErrors();

        $this->ajukan($buku, ['nama_peminjam' => 'Budi', 'nomor_induk' => '222'])
            ->assertSessionHas('error');

        $this->assertSame(1, Peminjaman::where('status', 'pending')->count(),
            'Satu eksemplar hanya boleh diantre satu pengajuan.');
    }

    public function test_jumlah_yang_diminta_dibatasi_sisa_antrean(): void
    {
        $buku = $this->buatBuku(3);
        $this->ajukan($buku, ['nama_peminjam' => 'Andi', 'nomor_induk' => '111', 'jumlah' => 2]);

        $this->ajukan($buku, ['nama_peminjam' => 'Budi', 'nomor_induk' => '222', 'jumlah' => 2])
            ->assertSessionHas('error');

        $this->assertSame(2, (int) Peminjaman::where('status', 'pending')->sum('jumlah'));
    }

    /**
     * Stok 10. Komputer 1 mengajukan 5 (belum disetujui petugas), lalu komputer
     * 2 mengajukan 10. Yang kedua harus ditolak dan diberi tahu sisanya, bukan
     * ikut masuk hanya karena stok fisiknya memang masih 10.
     */
    public function test_pengajuan_kedua_dibatasi_sisa_setelah_pengajuan_pertama(): void
    {
        $buku = $this->buatBuku(10);

        $this->ajukan($buku, ['nama_peminjam' => 'Andi', 'nomor_induk' => '111', 'jumlah' => 5])
            ->assertSessionHasNoErrors();

        $r = $this->ajukan($buku, ['nama_peminjam' => 'Budi', 'nomor_induk' => '222', 'jumlah' => 10]);
        $r->assertSessionHas('error');
        $this->assertStringContainsString('tersisa 5 eksemplar', session('error'));

        $this->assertSame(5, (int) Peminjaman::where('status', 'pending')->sum('jumlah'));

        // Sisanya tetap boleh diambil, persis sebanyak yang diberitahukan.
        $this->ajukan($buku, ['nama_peminjam' => 'Cici', 'nomor_induk' => '333', 'jumlah' => 5])
            ->assertSessionHasNoErrors();

        $this->assertSame(10, (int) Peminjaman::where('status', 'pending')->sum('jumlah'));
    }

    /**
     * Skenario yang sama, tapi kedua siswa menekan tombol pada saat yang sama
     * persis — cukup dekat sehingga yang kedua sudah melewati pemeriksaan
     * antrean sebelum yang pertama sempat tersimpan.
     *
     * Memeriksa lalu menyimpan sebagai dua langkah terpisah tidak cukup di
     * sini: keduanya membaca "sisa 10" dan sama-sama lolos. Yang menutupnya
     * adalah kunci pada baris buku, yang membuat pengajuan atas buku yang sama
     * terlayani satu per satu.
     *
     * Balapannya sendiri tidak bisa diperagakan di sini — SQLite yang dipakai
     * test tidak mengenal penguncian baris, dan dua request tiruan berbagi satu
     * koneksi. Jadi yang diperiksa adalah rangkaiannya benar-benar menyatu:
     * pembacaan stok, penghitungan antrean, dan penyimpanannya berada di dalam
     * satu transaksi yang sama. Perilaku kuncinya sendiri baru berlaku di MySQL.
     */
    public function test_pemeriksaan_dan_penyimpanan_berada_dalam_satu_transaksi(): void
    {
        $buku = $this->buatBuku(10);

        $jejak = [];
        DB::listen(function ($q) use (&$jejak) {
            $jejak[] = ['sql' => strtolower($q->sql), 'level' => DB::transactionLevel()];
        });

        $this->ajukan($buku, ['jumlah' => 5])->assertSessionHasNoErrors();

        $didalam = array_values(array_filter($jejak, fn ($q) => $q['level'] > 0));
        $this->assertNotEmpty($didalam, 'Pengajuan harus berjalan di dalam transaksi.');

        $punya = function (string $butuh) use ($didalam) {
            foreach ($didalam as $q) {
                if (str_contains($q['sql'], $butuh)) {
                    return true;
                }
            }
            return false;
        };

        $this->assertTrue($punya('from "buku"'),
            'Stok harus dibaca ulang di dalam transaksi, bukan dipakai dari pembacaan sebelumnya.');
        $this->assertTrue($punya('sum("jumlah")'),
            'Antrean pending harus dihitung di dalam transaksi yang sama.');
        $this->assertTrue($punya('insert into "peminjaman"'),
            'Penyimpanannya harus ikut dalam transaksi itu, bukan menyusul setelahnya.');
    }

    public function test_stok_kembali_bisa_diantre_setelah_pengajuan_ditolak(): void
    {
        $buku = $this->buatBuku(1);
        $this->ajukan($buku, ['nama_peminjam' => 'Andi', 'nomor_induk' => '111']);
        $loan = Peminjaman::first();

        $this->loginPetugas();
        $this->post(route('admin.peminjaman.request.reject', $loan->id))->assertSessionHas('success');

        auth()->logout();
        $this->ajukan($buku, ['nama_peminjam' => 'Budi', 'nomor_induk' => '222'])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Peminjaman::where('status', 'pending')->count());
    }

    public function test_katalog_memberi_tahu_berapa_yang_sedang_diantre(): void
    {
        $buku = $this->buatBuku(2);
        $this->ajukan($buku, ['nama_peminjam' => 'Andi', 'nomor_induk' => '111']);

        $html = $this->get(route('buku.detail', $buku->id))->assertOk()->getContent();
        $this->assertStringContainsString('sedang diantre siswa lain', $html);
    }

    /**
     * katalog() membungkus seluruh isinya dengan catch(\Throwable) yang diam:
     * kalau query-nya rusak, halaman tetap 200 tapi kosong melompong. Jadi yang
     * diperiksa bukan status HTTP-nya, melainkan bukunya benar-benar terbit.
     */
    public function test_halaman_katalog_membawa_data_antrean_ke_tombol_pinjam(): void
    {
        $buku = $this->buatBuku(2);
        $this->ajukan($buku, ['nama_peminjam' => 'Andi', 'nomor_induk' => '111']);

        $html = $this->get(route('katalog'))->assertOk()->getContent();

        $this->assertStringContainsString($buku->judul, $html, 'Katalog tidak boleh jatuh ke penanganan error yang mengosongkan daftar.');
        $this->assertStringContainsString('sisa_antre', $html, 'Sisa antrean harus ikut ke tombol pinjam di katalog.');
        $this->assertStringContainsString('&quot;antre&quot;:1', $html, 'Jumlah antrean harus terbawa apa adanya.');
    }

    // --------------------------------------------------------- pengajuan kembar

    public function test_klik_ganda_tanpa_nomor_induk_tidak_membuat_pengajuan_kembar(): void
    {
        $buku = $this->buatBuku(5);

        for ($i = 0; $i < 4; $i++) {
            $this->ajukan($buku, ['nama_peminjam' => 'Dedi', 'nomor_induk' => null]);
        }

        $this->assertSame(1, Peminjaman::where('nama_peminjam', 'Dedi')->count(),
            'Penyaring duplikat harus tetap bekerja walau nomor induk dikosongkan.');
    }

    public function test_siswa_berbeda_tanpa_nomor_induk_tetap_boleh_mengantre(): void
    {
        $buku = $this->buatBuku(5);

        $this->ajukan($buku, ['nama_peminjam' => 'Dedi', 'nomor_induk' => null])->assertSessionHasNoErrors();
        $this->ajukan($buku, ['nama_peminjam' => 'Eka', 'nomor_induk' => null])->assertSessionHasNoErrors();

        $this->assertSame(2, Peminjaman::where('status', 'pending')->count());
    }

    // ------------------------------------------------------------- entri buku

    public function test_isbn_kembar_ditolak_dengan_pesan_bukan_error_500(): void
    {
        $this->loginPetugas();
        $rak = Rak::firstOrCreate(['kode_rak' => 'RAK-UJI'],
            ['nama_rak' => 'R', 'lokasi' => 'L', 'status' => 'aktif']);

        $data = ['judul' => 'Fisika X', 'isbn' => '978-602-1234-56-7',
                 'tahun_terbit' => 2024, 'total_quantity' => 5, 'rak_id' => $rak->id];

        $this->post(route('admin.buku.store'), $data)->assertSessionHasNoErrors();
        $this->post(route('admin.buku.store'), array_merge($data, ['judul' => 'Fisika XI']))
            ->assertSessionHasErrors('isbn');

        $this->assertSame(1, Buku::count());
    }

    public function test_buku_boleh_disimpan_ulang_dengan_isbn_miliknya_sendiri(): void
    {
        $this->loginPetugas();
        $buku = $this->buatBuku(4);

        $this->post(route('admin.buku.update', $buku->id), [
            'judul' => 'Judul Baru', 'isbn' => $buku->isbn,
            'tahun_terbit' => 2024, 'total_quantity' => 4, 'rak_id' => $buku->rak_id,
        ])->assertSessionHasNoErrors();

        $this->assertSame('Judul Baru', $buku->refresh()->judul);
    }

    /** Menaikkan/menurunkan total eksemplar harus menggeser stok tersedia. */
    public function test_perubahan_total_eksemplar_menggeser_stok_tersedia(): void
    {
        $this->loginPetugas();
        $buku = $this->buatBuku(3);
        $buku->update(['available_quantity' => 1]);

        $this->post(route('admin.buku.update', $buku->id), [
            'judul' => $buku->judul, 'isbn' => $buku->isbn,
            'tahun_terbit' => 2024, 'total_quantity' => 6, 'rak_id' => $buku->rak_id,
        ])->assertSessionHasNoErrors();

        $buku->refresh();
        $this->assertSame(6, $buku->total_quantity);
        $this->assertSame(4, $buku->available_quantity, 'Tambahan 3 eksemplar harus masuk ke stok tersedia.');
    }

    // ------------------------------------------------------------- kode unik

    public function test_kode_peminjaman_tidak_kembar(): void
    {
        $buku = $this->buatBuku(500);

        // Kode yang sudah terpakai harus dihindari, bukan sekadar diacak ulang:
        // kolomnya ber-index unique, jadi kode kembar berarti QueryException.
        for ($i = 0; $i < 300; $i++) {
            Peminjaman::create([
                'kode_peminjaman' => Peminjaman::buatKode('REQ'),
                'sumber' => 'opac', 'nama_peminjam' => "Siswa {$i}", 'jurusan' => 'XII',
                'buku_id' => $buku->id, 'jumlah' => 1,
                'tanggal_pinjam' => now()->toDateString(),
                'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
                'status' => 'pending',
            ]);
        }

        $kode = Peminjaman::pluck('kode_peminjaman')->all();
        $this->assertCount(300, $kode);
        $this->assertSame(count($kode), count(array_unique($kode)), 'Kode peminjaman harus unik.');
    }

    /** Kode yang sudah ada di database tidak boleh dipakai ulang. */
    public function test_kode_yang_sudah_terpakai_dilewati(): void
    {
        $buku = $this->buatBuku();
        $terpakai = Peminjaman::buatKode('PJ');

        Peminjaman::create([
            'kode_peminjaman' => $terpakai, 'sumber' => 'petugas',
            'nama_peminjam' => 'Ada', 'jurusan' => 'XII', 'buku_id' => $buku->id, 'jumlah' => 1,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(), 'status' => 'dipinjam',
        ]);

        for ($i = 0; $i < 50; $i++) {
            $this->assertNotSame($terpakai, Peminjaman::buatKode('PJ'));
        }
    }
}
