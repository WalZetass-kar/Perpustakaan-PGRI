<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Rak;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Petugas memutuskan menyetujui atau menolak dari halaman Request Peminjaman,
 * tetapi jumlah eksemplar yang diminta tidak pernah ditampilkan di sana —
 * sehingga permintaan 1 buku dan 5 buku tampak persis sama sampai stoknya
 * terlanjur terpotong. Angka itu sekarang menempel di kolom Buku & Lokasi,
 * berdampingan dengan sisa stoknya.
 */
class JumlahDimintaPengajuanTest extends TestCase
{
    use RefreshDatabase;

    private function loginPetugas(): User
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $user = User::create([
            'name' => 'Petugas Uji', 'email' => 'diminta@uji.test', 'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]);
        $this->actingAs($user);

        return $user;
    }

    private function buatBuku(int $total = 10): Buku
    {
        $rak = Rak::firstOrCreate(['kode_rak' => 'RAK-DMT'],
            ['nama_rak' => 'Rak Uji', 'lokasi' => 'L1', 'status' => 'aktif']);

        return Buku::create([
            'judul' => 'Buku Pengajuan', 'isbn' => 'ISBN-' . uniqid(),
            'rak_id' => $rak->id, 'tahun_terbit' => 2024,
            'total_quantity' => $total, 'available_quantity' => $total, 'status' => 'tersedia',
        ]);
    }

    private function buatPengajuan(Buku $buku, int $jumlah): Peminjaman
    {
        return Peminjaman::create([
            'kode_peminjaman'     => Peminjaman::buatKode('REQ'),
            'sumber'              => 'opac',
            'nama_peminjam'       => 'Rani',
            'jurusan'             => 'XI DKV',
            'no_wa'               => '081234567890',
            'buku_id'             => $buku->id,
            'jumlah'              => $jumlah,
            'tanggal_pinjam'      => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
            'status'              => 'pending',
        ]);
    }

    public function test_jumlah_diminta_tampil_beserta_total_eksemplarnya(): void
    {
        $this->loginPetugas();
        $this->buatPengajuan($this->buatBuku(10), 2);

        $this->get(route('admin.peminjaman.request'))
            ->assertOk()
            ->assertSee('Diminta:')
            ->assertSee('2 dari 10')
            ->assertSee('eksemplar');
    }

    public function test_angka_mengikuti_data_pengajuannya(): void
    {
        $this->loginPetugas();
        $this->buatPengajuan($this->buatBuku(7), 5);

        $halaman = $this->get(route('admin.peminjaman.request'));
        $halaman->assertSee('5 dari 7');
        $halaman->assertDontSee('2 dari 10');
    }

    public function test_muncul_di_tampilan_desktop_maupun_kartu_mobile(): void
    {
        $this->loginPetugas();
        $this->buatPengajuan($this->buatBuku(10), 3);

        // Halaman ini merender dua susunan sekaligus — tabel untuk layar lebar
        // dan kartu untuk HP — jadi angkanya harus ada di keduanya, bukan
        // hanya di salah satunya.
        $this->assertSame(
            2,
            substr_count($this->get(route('admin.peminjaman.request'))->getContent(), '3 dari 10'),
            'Jumlah diminta harus tampil di tabel desktop DAN kartu mobile.'
        );
    }

    public function test_sisa_stok_tetap_ikut_ditampilkan(): void
    {
        $this->loginPetugas();
        $buku = $this->buatBuku(10);
        $buku->update(['available_quantity' => 8]);
        $this->buatPengajuan($buku, 2);

        // Keduanya saling melengkapi: "Diminta" menjawab berapa yang keluar,
        // "Sisa Stok" menjawab apakah stoknya cukup untuk disetujui.
        $this->get(route('admin.peminjaman.request'))
            ->assertSee('Diminta:')
            ->assertSee('Sisa Stok:');
    }
}
