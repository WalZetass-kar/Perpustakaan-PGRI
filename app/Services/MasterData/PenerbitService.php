<?php

namespace App\Services\MasterData;

use App\Exceptions\AturanBisnisException;
use App\Models\Penerbit;

class PenerbitService
{
    public function daftar(?string $cari = null)
    {
        $query = Penerbit::withCount('buku');

        if (filled($cari)) {
            $cari = trim($cari);
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                  ->orWhere('kota', 'like', "%{$cari}%");
            });
        }

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function simpan(array $data): Penerbit
    {
        return Penerbit::create($data);
    }

    public function perbarui(int $id, array $data): Penerbit
    {
        $penerbit = Penerbit::findOrFail($id);
        // $data berisi tepat kolom yang dikirim petugas; kolom yang tidak ikut
        // terkirim sengaja dibiarkan apa adanya, bukan dikosongkan.
        $penerbit->update($data);

        return $penerbit;
    }

    /**
     * @throws AturanBisnisException bila penerbitnya masih terkait buku
     */
    public function hapus(int $id): void
    {
        $penerbit = Penerbit::withCount('buku')->findOrFail($id);

        if ($penerbit->buku_count > 0) {
            throw new AturanBisnisException('Penerbit tidak dapat dihapus karena masih terkait dengan ' . $penerbit->buku_count . ' buku.');
        }

        $penerbit->delete();
    }
}
