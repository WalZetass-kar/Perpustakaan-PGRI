<?php
namespace Tests\Feature;
use App\Models\{Buku,Peminjaman,Pengaturan,Rak,Role,User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuditKritisTest extends TestCase
{
    use RefreshDatabase;

    private function roles(): array
    {
        return [
            Role::firstOrCreate(['name'=>'super_admin'],['display_name'=>'Super Administrator']),
            Role::firstOrCreate(['name'=>'admin'],['display_name'=>'Admin Perpustakaan']),
        ];
    }

    /** Super admin menurunkan pangkat dirinya sendiri = terkunci dari sistem. */
    public function test_super_admin_tidak_bisa_menurunkan_pangkat_dirinya_sendiri(): void
    {
        [$sa,$adm] = $this->roles();
        User::create(['name'=>'Utama','email'=>'satu@u.test','password'=>Hash::make('x'),'role_id'=>$sa->id,'status'=>'active']);
        $saya = User::create(['name'=>'Saya','email'=>'saya@u.test','password'=>Hash::make('x'),'role_id'=>$sa->id,'status'=>'active']);
        $this->actingAs($saya);

        $this->post(route('admin.anggota.update', $saya->id), [
            'name'=>'Saya','email'=>'saya@u.test','role_id'=>$adm->id,'status'=>'active',
        ]);

        $saya->refresh();
        $this->assertSame('super_admin', $saya->role->name, 'Super admin mengunci dirinya sendiri keluar dari sistem.');
    }

    /** Super admin menonaktifkan dirinya sendiri lewat anggotaUpdate. */
    public function test_super_admin_tidak_bisa_menonaktifkan_dirinya_sendiri(): void
    {
        [$sa,$adm] = $this->roles();
        User::create(['name'=>'Utama','email'=>'satu@u.test','password'=>Hash::make('x'),'role_id'=>$sa->id,'status'=>'active']);
        $saya = User::create(['name'=>'Saya','email'=>'saya@u.test','password'=>Hash::make('x'),'role_id'=>$sa->id,'status'=>'active']);
        $this->actingAs($saya);

        $this->post(route('admin.anggota.update', $saya->id), [
            'name'=>'Saya','email'=>'saya@u.test','role_id'=>$sa->id,'status'=>'inactive',
        ]);

        $saya->refresh();
        $this->assertSame('active', $saya->status, 'Super admin menonaktifkan akunnya sendiri.');
    }

    /** Setelan yang bisa diubah tapi tidak berpengaruh apa pun = fitur bohong. */
    public function test_setelan_buku_per_halaman_benar_benar_dipakai(): void
    {
        $rak = Rak::create(['kode_rak'=>'R','nama_rak'=>'R','lokasi'=>'L','status'=>'aktif']);
        for ($i=0;$i<9;$i++) {
            Buku::create(['judul'=>"Buku $i",'isbn'=>"I$i",'rak_id'=>$rak->id,'tahun_terbit'=>2024,
                'total_quantity'=>1,'available_quantity'=>1,'status'=>'tersedia']);
        }
        Pengaturan::updateOrCreate(['key'=>'buku_per_halaman'],['value'=>'4','label'=>'Buku per halaman']);

        $jumlah = $this->get(route('katalog'))->assertOk()->viewData('buku')->count();
        $this->assertSame(4, $jumlah, 'Setelan buku per halaman diabaikan sistem.');
    }

    private function petugasBiasa(): User
    {
        [$sa,$adm] = $this->roles();
        $u = User::create(['name'=>'Petugas','email'=>'p@u.test','password'=>Hash::make('x'),'role_id'=>$adm->id,'status'=>'active']);
        $this->actingAs($u);
        return $u;
    }

    private function superAdmin(): User
    {
        [$sa,$adm] = $this->roles();
        $u = User::create(['name'=>'Bos','email'=>'bos@u.test','password'=>Hash::make('x'),'role_id'=>$sa->id,'status'=>'active']);
        $this->actingAs($u);
        return $u;
    }

    /** Buku ditaruh di Rak A tapi lacinya milik Rak B — lokasi jadi omong kosong. */
    public function test_laci_harus_milik_rak_yang_dipilih(): void
    {
        $this->superAdmin();
        $rakA = Rak::create(['kode_rak'=>'RA','nama_rak'=>'Rak A','lokasi'=>'L1','status'=>'aktif']);
        $rakB = Rak::create(['kode_rak'=>'RB','nama_rak'=>'Rak B','lokasi'=>'L2','status'=>'aktif']);
        $laciB = \App\Models\RakLaci::create(['rak_id'=>$rakB->id,'nama_laci'=>'Laci B1']);

        $this->post(route('admin.buku.store'), [
            'judul'=>'Buku Nyasar','tahun_terbit'=>2024,'total_quantity'=>1,
            'rak_id'=>$rakA->id, 'rak_laci_id'=>$laciB->id,
        ]);

        $buku = Buku::first();
        if ($buku) {
        }
        $this->assertNull($buku, 'Laci dari rak lain seharusnya ditolak, bukan tersimpan sebagai lokasi palsu.');
    }

    /**
     * Menu "Akun Pengelola" sudah disembunyikan dari petugas biasa di sidebar,
     * tetapi rutenya semula masih terbuka lewat URL yang diketik langsung —
     * sehingga nama dan email seluruh pengelola tetap terbaca.
     */
    public function test_petugas_biasa_tidak_bisa_membuka_daftar_akun_pengelola(): void
    {
        [$sa, $adm] = $this->roles();
        User::create(['name'=>'Bos','email'=>'rahasia@sekolah.test','password'=>Hash::make('x'),'role_id'=>$sa->id,'status'=>'active']);
        $this->petugasBiasa();

        $r = $this->get(route('admin.anggota'));
        $r->assertForbidden();
        $this->assertStringNotContainsString('rahasia@sekolah.test', $r->getContent());
    }

    public function test_super_admin_tetap_bisa_membuka_daftar_akun_pengelola(): void
    {
        $this->superAdmin();
        $this->get(route('admin.anggota'))->assertOk();
    }

    /** Nama dan email milik sendiri tetap boleh disunting. */
    public function test_menyunting_nama_sendiri_tetap_diizinkan(): void
    {
        [$sa, $adm] = $this->roles();
        User::create(['name'=>'Utama','email'=>'satu@u.test','password'=>Hash::make('x'),'role_id'=>$sa->id,'status'=>'active']);
        $saya = User::create(['name'=>'Saya','email'=>'saya@u.test','password'=>Hash::make('x'),'role_id'=>$sa->id,'status'=>'active']);
        $this->actingAs($saya);

        $this->post(route('admin.anggota.update', $saya->id), [
            'name'=>'Nama Baru','email'=>'saya@u.test','role_id'=>$sa->id,'status'=>'active',
        ])->assertSessionHas('success');

        $this->assertSame('Nama Baru', $saya->refresh()->name);
    }

    /** Laci dari rak yang sama harus tetap diterima. */
    public function test_laci_dari_rak_yang_sama_tetap_diterima(): void
    {
        $this->superAdmin();
        $rak = Rak::create(['kode_rak'=>'RA','nama_rak'=>'Rak A','lokasi'=>'L1','status'=>'aktif']);
        $laci = \App\Models\RakLaci::create(['rak_id'=>$rak->id,'nama_laci'=>'Laci A1']);

        $this->post(route('admin.buku.store'), [
            'judul'=>'Buku Benar','tahun_terbit'=>2024,'total_quantity'=>1,
            'rak_id'=>$rak->id,'rak_laci_id'=>$laci->id,
        ])->assertSessionHasNoErrors();

        $this->assertSame($laci->id, Buku::first()->rak_laci_id);
    }

    /**
     * Formulirnya sudah menuliskan "Minimal 8 karakter" dan memasang
     * minlength="8", tetapi servernya semula masih menerima 6 — sehingga
     * pengiriman langsung (atau browser tanpa validasi HTML) bisa menembusnya.
     * Justru dua jalur inilah yang dipakai Super Admin menetapkan password
     * orang lain.
     */
    public function test_password_akun_baru_minimal_delapan_karakter(): void
    {
        [$sa, $adm] = $this->roles();
        $this->superAdmin();

        $this->post(route('admin.anggota.store'), [
            'name'=>'Petugas Baru','email'=>'baru@u.test','password'=>'rahasia',
            'role_id'=>$adm->id,'status'=>'active',
        ])->assertSessionHasErrors('password');

        $this->assertNull(User::where('email','baru@u.test')->first());

        $this->post(route('admin.anggota.store'), [
            'name'=>'Petugas Baru','email'=>'baru@u.test','password'=>'rahasia123',
            'role_id'=>$adm->id,'status'=>'active',
        ])->assertSessionHasNoErrors();

        $this->assertNotNull(User::where('email','baru@u.test')->first());
    }

    public function test_reset_password_juga_minimal_delapan_karakter(): void
    {
        [$sa, $adm] = $this->roles();
        $this->superAdmin();
        $target = User::create(['name'=>'Target','email'=>'t@u.test','password'=>Hash::make('lamalama'),'role_id'=>$adm->id,'status'=>'active']);

        $this->post(route('admin.anggota.reset-password', $target->id), [
            'password'=>'pendek','password_confirmation'=>'pendek',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('lamalama', $target->refresh()->password),
            'Password lama tidak boleh berubah saat validasi gagal.');
    }
}
