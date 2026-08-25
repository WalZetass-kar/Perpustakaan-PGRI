<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Halaman Koleksi Buku pernah menampilkan Grid maupun List "mengumpul di
 * tengah": isinya menyusut dan menyisakan ruang kosong lebar di kiri.
 *
 * Penyebabnya dua hal yang berdiri sendiri:
 *   1. Container Grid/List disisipkan ke dalam .dataTables_wrapper di bawah
 *      .dataTables_length yang ber-float. Saat tabel disembunyikan, clear:both
 *      bawaan table.dataTable ikut hilang, dan container Grid menyusut
 *      menghindari float itu.
 *   2. DataTables autoWidth mengukur tabel saat masih display:none (karena
 *      tampilan terakhir tersimpan sebagai 'grid'), lalu menuliskan lebar
 *      sempit itu sebagai gaya inline yang menimpa width:100%, dan
 *      margin:0 auto memusatkannya.
 *
 * Perbaikannya sempat tertinggal di branch lain sehingga bug-nya muncul lagi.
 * Test ini menahan ketiga penangkalnya supaya tidak hilang untuk kedua kalinya.
 */
class KoleksiBukuLayoutTest extends TestCase
{
    use RefreshDatabase;

    private function loginSuperAdmin(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Administrator']);
        $this->actingAs(User::create([
            'name' => 'Petugas Uji', 'email' => 'uji@uji.test',
            'password' => Hash::make('rahasia123'),
            'role_id' => $role->id, 'status' => 'active',
        ]));
    }

    public function test_halaman_koleksi_buku_render(): void
    {
        $this->loginSuperAdmin();
        $this->get(route('admin.buku'))->assertOk();
    }

    public function test_container_grid_dan_list_meng_clear_float_di_atasnya(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.buku'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/#grid-buku-container\s*,\s*#list-buku-mobile-container\s*\{\s*clear:\s*both;\s*\}/',
            $html,
            'Tanpa clear:both, container Grid menyusut menghindari float '
            .'.dataTables_length dan tampak mengumpul, tidak memenuhi lebar.'
        );
    }

    public function test_tabel_dipaksa_selebar_wadahnya(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.buku'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/#tabel-buku\s*\{\s*width:\s*100%\s*!important;\s*\}/',
            $html,
            'Tanpa ini, lebar sempit hasil pengukuran DataTables membuat tabel '
            .'tampil menyempit dan dipusatkan oleh margin:0 auto.'
        );
    }

    public function test_autowidth_datatables_dimatikan(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.buku'))->assertOk()->getContent();

        $this->assertStringContainsString(
            'autoWidth: false',
            $html,
            'autoWidth harus mati: kalau halaman dibuka dalam mode Grid, tabel '
            .'diukur saat masih display:none sehingga lebarnya tersimpan salah.'
        );
    }

    /**
     * Di HP, kartu mode Grid harus dua kolom kiri-kanan, bukan satu memanjang
     * ke bawah. Aturannya ditulis sebagai media query ber-selektor id supaya
     * menang atas grid-cols-1 bawaan Tailwind.
     */
    public function test_kartu_grid_dua_kolom_di_layar_hp(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.buku'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/#grid-buku-container\s*\{[^}]*grid-template-columns:\s*repeat\(2,\s*minmax\(0,\s*1fr\)\);/',
            $html,
            'Kartu Grid di layar HP harus dipaksa dua kolom.'
        );
    }

    public function test_kartu_grid_dirapatkan_di_layar_hp(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.buku'))->assertOk()->getContent();

        $this->assertStringContainsString(
            '.kartu-grid-buku-isi',
            $html,
            'Isi kartu perlu penanda kelas sendiri agar jaraknya bisa dirapatkan di HP.'
        );
        $this->assertStringContainsString(
            'kartu-grid-buku-isi p-3.5',
            $html,
            'Penanda kelas itu harus benar-benar dipasang oleh buildBookCard().'
        );
    }

    /**
     * Desktop tidak boleh ikut berubah: aturan dua kolom itu wajib terkurung di
     * dalam media query, bukan berlaku untuk semua lebar layar.
     */
    public function test_desktop_tidak_ikut_dipaksa_dua_kolom(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.buku'))->assertOk()->getContent();

        // Kelas Tailwind untuk layar besar harus tetap utuh di container.
        $this->assertStringContainsString('sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4', $html);

        // Dan grid-template-columns hanya boleh muncul di dalam media query.
        $posisiMedia = strpos($html, '@media (max-width: 639px)');
        $this->assertNotFalse($posisiMedia, 'Media query dua kolom tidak ditemukan sama sekali.');

        $sebelumMedia = substr($html, 0, $posisiMedia);
        $this->assertStringNotContainsString(
            'grid-template-columns',
            $sebelumMedia,
            'Aturan dua kolom bocor ke luar media query dan akan mengubah desktop.'
        );
    }

    /**
     * Di tangkapan layar 360px, badge stok pecah jadi "4/4" lalu "Eks" di baris
     * berikutnya -- bagian yang paling terlihat rusak. Badge itu harus dikunci
     * satu baris, dan kode rak yang mengalah menyusut.
     */
    public function test_badge_stok_tidak_pecah_dua_baris_di_hp(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.buku'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/\.kartu-grid-buku-kaki\s*>\s*span:last-child\s*\{[^}]*white-space:\s*nowrap;/',
            $html,
            'Badge stok harus dikunci satu baris.'
        );
        $this->assertMatchesRegularExpression(
            '/\.kartu-grid-buku-kaki\s*>\s*span:last-child\s*\{[^}]*flex-shrink:\s*0;/',
            $html,
            'Badge stok tidak boleh ikut menyusut; kode rak yang harus mengalah.'
        );
    }

    /**
     * Sampul di-resize berdasarkan lebar saja, jadi rasionya tetap tegak.
     * Kotak yang hampir persegi memangkasnya di kiri-kanan.
     */
    public function test_sampul_memakai_rasio_buku_di_hp(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.buku'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/\.kartu-grid-buku-sampul\s*\{[^}]*aspect-ratio:\s*\d+\s*\/\s*\d+;/',
            $html,
            'Sampul harus dikunci ke sebuah rasio supaya tingginya ikut lebar kartu, '
            .'bukan tinggi tetap yang memangkas sisinya di layar sempit.'
        );
    }

    /**
     * Padding bertumpuk tiga lapis memakan 22% lebar layar 360px. Inilah sebab
     * utama kartunya terasa sesak.
     */
    public function test_padding_bertumpuk_dirampingkan_di_hp(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.buku'))->assertOk()->getContent();

        $posisiMedia = strpos($html, '@media (max-width: 639px)');
        $this->assertNotFalse($posisiMedia, 'Media query mobile tidak ditemukan.');

        $didalamMedia = substr($html, $posisiMedia);
        $this->assertMatchesRegularExpression(
            '/\.dataTables_wrapper\s*\{\s*padding:\s*0\.5rem;\s*\}/',
            $didalamMedia,
            'Padding wrapper DataTables harus dirampingkan di HP.'
        );
    }

    /**
     * Banner "Menampilkan hasil untuk ..." muncul saat petugas datang dari menu
     * Temukan Buku (URL ber-?search=). Isinya satu baris mendatar: label, teks
     * petunjuk, lalu tombol reset ber-shrink-0. Di layar 360px tombol itu
     * mempertahankan lebarnya sehingga teks petunjuk terjepit dan pecah satu
     * kata per baris. Di HP ketiganya harus ditumpuk.
     */
    public function test_banner_pencarian_ditumpuk_di_hp(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.buku'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/#search-filter-banner\s*\{[^}]*flex-direction:\s*column;/',
            $html,
            'Banner harus menumpuk ke bawah di HP, bukan tetap satu baris mendatar.'
        );
        $this->assertMatchesRegularExpression(
            '/\.banner-cari-petunjuk\s*\{[^}]*flex-basis:\s*100%;/',
            $html,
            'Teks petunjuk harus dapat barisnya sendiri supaya utuh terbaca.'
        );
        $this->assertMatchesRegularExpression(
            '/#btn-reset-search-filter\s*\{\s*width:\s*100%;\s*\}/',
            $html,
            'Tombol reset harus selebar banner di HP, bukan menjepit teks di sampingnya.'
        );
    }

    /**
     * Penanda kelas yang dipakai aturan di atas harus benar-benar terpasang di
     * markup banner-nya, bukan cuma ada di CSS.
     */
    public function test_penanda_kelas_banner_terpasang_di_markup(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.buku'))->assertOk()->getContent();

        $this->assertStringContainsString('banner-cari-teks flex items-center', $html);
        $this->assertStringContainsString('banner-cari-petunjuk text-blue-500', $html);
    }

    /**
     * Seluruh aturan banner wajib terkurung di dalam media query; di desktop
     * banner itu harus tetap satu baris mendatar seperti semula.
     */
    public function test_banner_desktop_tetap_satu_baris(): void
    {
        $this->loginSuperAdmin();
        $html = $this->get(route('admin.buku'))->assertOk()->getContent();

        $posisiMedia = strpos($html, '@media (max-width: 639px)');
        $this->assertNotFalse($posisiMedia, 'Media query mobile tidak ditemukan.');

        $sebelumMedia = substr($html, 0, $posisiMedia);
        $this->assertStringNotContainsString(
            '#search-filter-banner {',
            $sebelumMedia,
            'Aturan banner bocor ke luar media query dan akan mengubah desktop.'
        );
    }
}
