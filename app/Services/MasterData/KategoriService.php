<?php

namespace App\Services\MasterData;

use App\Exceptions\AturanBisnisException;
use App\Models\Kategori;
use Illuminate\Support\Str;

/**
 * Kategori buku. Slug-nya diturunkan dari nama supaya alamat katalog di sisi
 * pengunjung tetap terbaca manusia.
 */
class KategoriService
{
    public function daftar(?string $cari = null)
    {
        $query = Kategori::withCount('buku');

        if (filled($cari)) {
            $query->where('nama', 'like', '%' . trim($cari) . '%');
        }

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function simpan(array $data): Kategori
    {
        return Kategori::create($this->kolom($data));
    }

    public function perbarui(int $id, array $data): Kategori
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->update($this->kolom($data));

        return $kategori;
    }

    /**
     * @throws AturanBisnisException bila kategorinya masih dipakai buku
     */
    public function hapus(int $id): void
    {
        $kategori = Kategori::withCount('buku')->findOrFail($id);

        if ($kategori->buku_count > 0) {
            throw new AturanBisnisException('Kategori tidak dapat dihapus karena masih digunakan oleh ' . $kategori->buku_count . ' buku.');
        }

        $kategori->delete();
    }

    private function kolom(array $data): array
    {
        return [
            'nama'      => $data['nama'],
            'slug'      => Str::slug($data['nama']),
            'deskripsi' => $data['deskripsi'] ?? null,
        ];
    }
}
