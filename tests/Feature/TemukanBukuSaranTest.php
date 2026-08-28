<?php

namespace Tests\Feature;

use App\Models\{Buku,Kategori,Penulis,Rak,RakLaci,Role,User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Kotak pencarian Temukan Buku memunculkan saran judul sambil petugas
 * mengetik, sepadan dengan katalog OPAC.
 */
class TemukanBukuSaranTest extends TestCase
{
    use RefreshDatabase;

    private function login(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $this->actingAs(User::create([
            'name' => 'Petugas Uji', 'email' => 'uji@uji.test',
            'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]));
    }

    private function siapkanKoleksi(): void
    {
        $rak = Rak::create(['kode_rak' => 'RK-07', 'nama_rak' => 'Rak Sastra', 'lokasi' => 'Lantai 2', 'status' => 'aktif']);
        $laci = RakLaci::create(['rak_id' => $rak->id, 'nomor_laci' => 1, 'nama_laci' => 'Laci A']);
        $kategori = Kategori::create(['nama' => 'Sastra', 'slug' => 'sastra', 'status' => 'aktif']);
        $penulis = Penulis::create(['nama' => 'Pramoedya Ananta Toer']);

        Buku::create(['judul' => 'Bumi Manusia', 'isbn' => 'ISBN-001', 'rak_id' => $rak->id, 'rak_laci_id' => $laci->id,
            'kategori_id' => $kategori->id, 'penulis_id' => $penulis->id, 'tahun_terbit' => 2024,
            'total_quantity' => 4, 'available_quantity' => 4, 'status' => 'tersedia']);
        Buku::create(['judul' => 'Fisika Dasar', 'isbn' => 'ISBN-002', 'rak_id' => $rak->id, 'rak_laci_id' => $laci->id,
            'kategori_id' => $kategori->id, 'tahun_terbit' => 2024,
            'total_quantity' => 2, 'available_quantity' => 0, 'status' => 'tersedia']);
    }

    public function test_saran_muncul_untuk_potongan_judul(): void
    {
        $this->login();
        $this->siapkanKoleksi();

        $data = $this->getJson(route('admin.temukan-buku.saran', ['q' => 'bumi']))->assertOk()->json();

        $this->assertCount(1, $data);
        $this->assertSame('Bumi Manusia', $data[0]['judul']);
        $this->assertSame('Pramoedya Ananta Toer', $data[0]['penulis']);
        $this->assertSame('RK-07', $data[0]['rak']);
        $this->assertSame('Laci A', $data[0]['laci']);
        $this->assertSame(4, $data[0]['available_quantity']);
    }

    /**
     * Kotak pencariannya juga menerima kode rak. Kalau saran memakai daftar
     * kolom yang lebih sempit dari pencarian utamanya, petugas melihat "tidak
     * ada" padahal menekan Enter menemukan bukunya.
     */
    public function test_saran_ikut_menelusuri_kode_rak_seperti_pencarian_utamanya(): void
    {
        $this->login();
        $this->siapkanKoleksi();

        $saran = $this->getJson(route('admin.temukan-buku.saran', ['q' => 'RK-07']))->assertOk()->json();
        $hasil = $this->get(route('admin.temukan-buku', ['search' => 'RK-07']))->assertOk();

        $this->assertCount(2, $saran);
        $this->assertStringContainsString('Bumi Manusia', $hasil->getContent());
    }

    public function test_kata_kunci_terlalu_pendek_tidak_menembak_database(): void
    {
        $this->login();
        $this->siapkanKoleksi();

        $this->getJson(route('admin.temukan-buku.saran', ['q' => 'b']))->assertOk()->assertExactJson([]);
    }

    public function test_saran_tertutup_untuk_tamu(): void
    {
        $this->siapkanKoleksi();

        $this->get(route('admin.temukan-buku.saran', ['q' => 'bumi']))->assertRedirect(route('login'));
    }

    /**
     * Dropdown saran menambah beberapa lapis <div> bersarang tepat di dalam
     * formulir pencarian; satu penutup yang lupa ditulis akan mengurung sisa
     * halaman di dalamnya tanpa error apa pun.
     */
    public function test_markup_div_tetap_seimbang(): void
    {
        $this->login();
        $this->siapkanKoleksi();

        $html = $this->get(route('admin.temukan-buku'))->getContent();
        $tanpaScript = preg_replace('/<script\b.*?<\/script>/s', '', $html);

        $buka  = preg_match_all('/<div\b/', $tanpaScript);
        $tutup = preg_match_all('/<\/div>/', $tanpaScript);

        $this->assertSame($buka, $tutup, "Jumlah <div> dan </div> tidak seimbang: {$buka} dibuka, {$tutup} ditutup.");
    }

    public function test_halaman_memuat_komponen_saran(): void
    {
        $this->login();
        $this->siapkanKoleksi();

        $html = $this->get(route('admin.temukan-buku'))->assertOk()->getContent();

        $this->assertStringContainsString('saranTemukanBuku(', $html);
        $this->assertStringContainsString('saran-temukan', $html);
        $this->assertStringContainsString('ambilSaran($event.target.value)', $html);
    }
}
