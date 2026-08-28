<?php

namespace Tests\Feature;

use App\Models\{Role,User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Pegangan resize di pojok textarea dan tombol panah di kolom angka dimatikan
 * lewat satu berkas yang di-include semua layout. Ujian ini menjaga agar tidak
 * ada layout yang tertinggal saat kelak ada layout baru.
 */
class GayaFormulirTest extends TestCase
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

    private function pastikanRapi(string $html, string $halaman): void
    {
        $this->assertMatchesRegularExpression('/textarea\s*\{\s*resize:\s*none/', $html, "Textarea di {$halaman} masih bisa di-resize.");
        $this->assertStringContainsString('::-webkit-inner-spin-button', $html, "Panah kolom angka di {$halaman} belum dimatikan.");
        $this->assertStringContainsString('appearance: textfield', $html, "Panah kolom angka di {$halaman} belum dimatikan untuk Firefox.");
    }

    public function test_layout_publik_merapikan_kolom_isian(): void
    {
        $this->pastikanRapi($this->get(route('katalog'))->assertOk()->getContent(), 'katalog OPAC');
    }

    public function test_layout_dashboard_merapikan_kolom_isian(): void
    {
        $this->login();
        $this->pastikanRapi($this->get(route('admin.buku'))->assertOk()->getContent(), 'Koleksi Buku');
    }

    /**
     * Aturannya memakai selektor elemen, bukan kelas, supaya bobotnya tetap di
     * bawah utilitas Tailwind. Halaman yang benar-benar butuh pegangan resize
     * masih bisa memintanya kembali lewat kelas `resize-y`.
     */
    public function test_aturan_tidak_memakai_important(): void
    {
        $html = $this->get(route('katalog'))->getContent();

        $blok = strstr($html, 'textarea {');
        $blok = substr($blok, 0, strpos($blok, '</style>'));

        $this->assertStringNotContainsString('!important', $blok);
    }
}
