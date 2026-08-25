<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Rak;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Menu titik-tiga pada baris/kartu buku di halaman Koleksi Buku.
 *
 * Menu ini di-teleport ke <body> dengan z-[100] -- sama dengan z-index modal
 * edit. Karena teleport menaruhnya di akhir body, ia menang dalam adu tumpuk
 * dan mengambang DI ATAS modal. Jadi kalau menunya tidak menutup diri saat
 * aksinya dipilih, ia akan menghalangi isi modal yang baru saja dibuka.
 *
 * Lima halaman lain dengan pola dropdown yang sama (Kategori, Penulis,
 * Penerbit, Kelas, Akun Pengelola) sudah melakukannya sejak awal; hanya
 * Koleksi Buku yang tertinggal, karena markup-nya dibangun sebagai string di
 * controller dan modalnya dibuka lewat handler jQuery terpisah.
 */
class MenuAksiBukuTest extends TestCase
{
    use RefreshDatabase;

    private function ambilKolomAksi(): string
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $this->actingAs(User::create([
            'name' => 'Petugas Uji', 'email' => 'uji@uji.test',
            'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]));

        $rak = Rak::create([
            'kode_rak' => 'RAK-UJI-01', 'nama_rak' => 'Rak Pengujian',
            'lokasi' => 'Lantai 1', 'status' => 'aktif',
        ]);
        Buku::create([
            'judul' => 'Teknik Mesin', 'isbn' => 'ISBN-'.uniqid(),
            'rak_id' => $rak->id, 'tahun_terbit' => 2009,
            'total_quantity' => 15, 'available_quantity' => 15, 'status' => 'tersedia',
        ]);

        $json = $this->getJson(route('admin.buku').'?draw=1')->assertOk()->json();

        $this->assertNotEmpty($json['data'] ?? [], 'Endpoint DataTables tidak mengembalikan baris apa pun.');

        return $json['data'][0]['aksi'];
    }

    public function test_tombol_edit_menutup_menu_titik_tiga(): void
    {
        $aksi = $this->ambilKolomAksi();

        $this->assertStringContainsString('btn-edit-buku', $aksi, 'Tombol Edit Buku tidak ditemukan.');

        preg_match('/<button[^>]*btn-edit-buku[^>]*>/', $aksi, $m);
        $this->assertNotEmpty($m, 'Tag tombol Edit Buku tidak terbaca.');
        $this->assertStringContainsString(
            'open = false',
            $m[0],
            'Tombol "Edit Buku" harus menutup menu titik-tiga; kalau tidak, menunya '
            .'mengambang di atas modal edit dan menutupi isinya.'
        );
    }

    public function test_tombol_hapus_menutup_menu_titik_tiga(): void
    {
        $aksi = $this->ambilKolomAksi();

        preg_match('/<button type="submit"[^>]*>/', $aksi, $m);
        $this->assertNotEmpty($m, 'Tombol Hapus Buku tidak terbaca.');
        $this->assertStringContainsString(
            'open = false',
            $m[0],
            'Tombol "Hapus Buku" harus menutup menu titik-tiga sebelum konfirmasi muncul.'
        );
    }

    /**
     * Menu tetap harus bisa dibuka dan ditutup seperti biasa -- perbaikan di
     * atas tidak boleh merusak perilaku dasarnya.
     */
    public function test_menu_tetap_bisa_dibuka_dan_ditutup_seperti_biasa(): void
    {
        $aksi = $this->ambilKolomAksi();

        $this->assertStringContainsString('open: false', $aksi, 'State dropdown hilang.');
        $this->assertStringContainsString('open = !open', $aksi, 'Tombol titik-tiga tidak lagi men-toggle menu.');
        $this->assertStringContainsString('@click.outside="open = false"', $aksi, 'Klik di luar harus tetap menutup menu.');
    }
}
