<?php

namespace App\Services\MasterData;

use App\Exceptions\AturanBisnisException;
use App\Models\Penulis;

class PenulisService
{
    public function daftar()
    {
        return Penulis::withCount('buku')->latest()->paginate(10);
    }

    public function simpan(array $data): Penulis
    {
        return Penulis::create($data);
    }

    public function perbarui(int $id, array $data): Penulis
    {
        $penulis = Penulis::findOrFail($id);
        // $data berisi tepat kolom yang dikirim petugas; kolom yang tidak ikut
        // terkirim sengaja dibiarkan apa adanya, bukan dikosongkan.
        $penulis->update($data);

        return $penulis;
    }

    /**
     * @throws AturanBisnisException bila penulisnya masih terkait buku
     */
    public function hapus(int $id): void
    {
        $penulis = Penulis::withCount('buku')->findOrFail($id);

        if ($penulis->buku_count > 0) {
            throw new AturanBisnisException('Penulis tidak dapat dihapus karena masih terkait dengan ' . $penulis->buku_count . ' buku.');
        }

        $penulis->delete();
    }
}
