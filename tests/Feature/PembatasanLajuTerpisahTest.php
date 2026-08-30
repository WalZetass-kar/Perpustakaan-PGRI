<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Rak;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tiap rute yang dibatasi lajunya harus punya penghitung sendiri.
 *
 * Laravel menyusun kunci pembatas laju dari `domain|ip` saja — nama rutenya
 * tidak ikut. Akibatnya, tanpa argumen ketiga pada `throttle`, seluruh rute
 * pengunjung berbagi satu penghitung sementara masing-masing memeriksa batasnya
 * sendiri: rute yang longgar menghabiskan jatah rute yang ketat. Enam
 * permintaan saran pencarian sudah cukup membuat halaman login petugas menolak
 * dengan 429, dan satu menit memantau pengajuan membuat siswa tidak bisa
 * mengajukan lagi.
 *
 * Kejadian itu tidak terlihat saat dicoba sendirian di komputer sendiri, tapi
 * pasti muncul di sekolah yang seluruh perangkatnya keluar lewat satu IP.
 */
class PembatasanLajuTerpisahTest extends TestCase
{
    use RefreshDatabase;

    private function buatBuku(int $total = 60): Buku
    {
        $rak = Rak::firstOrCreate(['kode_rak' => 'RAK-LAJU'],
            ['nama_rak' => 'Rak Uji', 'lokasi' => 'L1', 'status' => 'aktif']);

        return Buku::create([
            'judul' => 'Buku Laju', 'isbn' => 'ISBN-' . uniqid(), 'rak_id' => $rak->id,
            'tahun_terbit' => 2024, 'total_quantity' => $total,
            'available_quantity' => $total, 'status' => 'tersedia',
        ]);
    }

    private function ajukan(Buku $buku, string $nama)
    {
        return $this->postJson(route('katalog.ajukan'), [
            'buku_id' => $buku->id, 'nama_peminjam' => $nama,
            'jurusan' => 'XI DKV', 'no_wa' => '081234567890', 'jumlah' => 1,
        ]);
    }

    // ------------------------------------------- jatah tidak saling menggerogoti

    public function test_saran_pencarian_tidak_menghabiskan_jatah_login_petugas(): void
    {
        // Batas saran 60/menit, batas login hanya 5/menit. Siswa yang mengetik
        // di kotak pencarian tidak boleh mengunci petugas dari halaman login.
        for ($i = 0; $i < 10; $i++) {
            $this->get('/api/buku/search-suggestions?q=buku')->assertOk();
        }

        $this->post(route('login.post'), ['email' => 'petugas@uji.test', 'password' => 'salah'])
            ->assertStatus(302);
    }

    public function test_memantau_pengajuan_tidak_menghabiskan_jatah_mengajukan(): void
    {
        $buku = $this->buatBuku();
        $id = $this->ajukan($buku, 'Rani')->json('id');

        // Satu menit menunggu keputusan petugas = 12 pemeriksaan status.
        for ($i = 0; $i < 12; $i++) {
            $this->getJson(route('katalog.pengajuan.status', $id))->assertOk();
        }

        $this->ajukan($buku, 'Budi')->assertOk();
    }

    public function test_saran_petugas_tidak_menghabiskan_jatah_siswa(): void
    {
        $buku = $this->buatBuku();

        for ($i = 0; $i < 10; $i++) {
            $this->get('/api/buku/search-suggestions?q=laju')->assertOk();
        }

        $this->ajukan($buku, 'Sari')->assertOk();
    }

    // ------------------------------------ tapi batas masing-masing tetap berlaku

    public function test_pengajuan_berlebihan_tetap_dibendung(): void
    {
        $buku = $this->buatBuku();

        for ($i = 0; $i < 10; $i++) {
            $this->ajukan($buku, 'Siswa ' . $i)->assertOk();
        }

        $this->ajukan($buku, 'Siswa Kesebelas')->assertStatus(429);
    }

    public function test_percobaan_login_berlebihan_tetap_dibendung(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.post'), ['email' => 'a@uji.test', 'password' => 'salah']);
        }

        $this->post(route('login.post'), ['email' => 'a@uji.test', 'password' => 'salah'])
            ->assertStatus(429);
    }
}
