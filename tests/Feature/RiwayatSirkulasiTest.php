<?php
namespace Tests\Feature;
use App\Models\{Buku,Peminjaman,Rak,Role,User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RiwayatSirkulasiTest extends TestCase
{
    use RefreshDatabase;

    private function siapkan(): array
    {
        $rak = Rak::create(['kode_rak'=>'R','nama_rak'=>'R','lokasi'=>'L','status'=>'aktif']);
        $buku = Buku::create(['judul'=>'Bumi Manusia','isbn'=>'X','rak_id'=>$rak->id,'tahun_terbit'=>2024,
            'total_quantity'=>5,'available_quantity'=>5,'status'=>'tersedia']);
        $sa = Role::firstOrCreate(['name'=>'super_admin'],['display_name'=>'S']);
        $bos = User::create(['name'=>'Kepala','email'=>'bos@u.test','password'=>Hash::make('x'),'role_id'=>$sa->id,'status'=>'active']);
        $petugas = User::create(['name'=>'Petugas Lama','email'=>'lama@u.test','password'=>Hash::make('x'),'role_id'=>$sa->id,'status'=>'active']);
        return [$buku,$bos,$petugas];
    }

    public function test_riwayat_bertahan_saat_akun_petugas_dihapus(): void
    {
        [$buku,$bos,$petugas] = $this->siapkan();

        // Petugas mencatat 3 peminjaman, semuanya SUDAH dikembalikan.
        $this->actingAs($petugas);
        for ($i=0;$i<3;$i++) {
            $this->post(route('admin.peminjaman.store'), ['nama_peminjam'=>"Siswa $i",'jurusan'=>'XII','buku_id'=>$buku->id,'jumlah'=>1]);
        }
        foreach (Peminjaman::all() as $p) $this->post(route('admin.peminjaman.kembali', $p->id));

        $sebelum = Peminjaman::count();

        $this->actingAs($bos);
        $r = $this->post(route('admin.anggota.delete', $petugas->id));

        $sesudah = Peminjaman::count();
        $this->assertSame($sebelum, $sesudah, 'Riwayat peminjaman ikut terhapus bersama akun petugas.');
    }

    public function test_buku_yang_punya_riwayat_tidak_bisa_dihapus(): void
    {
        [$buku,$bos,$petugas] = $this->siapkan();

        $this->actingAs($petugas);
        for ($i=0;$i<3;$i++) {
            $this->post(route('admin.peminjaman.store'), ['nama_peminjam'=>"Siswa $i",'jurusan'=>'XII','buku_id'=>$buku->id,'jumlah'=>1]);
        }
        foreach (Peminjaman::all() as $p) $this->post(route('admin.peminjaman.kembali', $p->id));

        $sebelum = Peminjaman::count();

        $this->actingAs($bos);
        $r = $this->post(route('admin.buku.delete', $buku->id));

        $sesudah = Peminjaman::count();
        $this->assertSame($sebelum, $sesudah, 'Riwayat peminjaman ikut terhapus bersama data buku.');
    }
}
