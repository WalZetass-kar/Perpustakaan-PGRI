<?php

namespace App\Services\MasterData;

use App\Exceptions\AturanBisnisException;
use App\Models\Kelas;

/**
 * Kelas/jenjang yang menjadi sasaran sebuah buku, mis. "X TKJ".
 */
class KelasService
{
    public function daftar(?string $cari = null)
    {
        $query = Kelas::withCount('buku');

        if (filled($cari)) {
            $cari = trim($cari);
            $query->where(function ($q) use ($cari) {
                $q->where('nama_kelas', 'like', "%{$cari}%")
                  ->orWhere('tingkat', 'like', "%{$cari}%");
            });
        }

        // Tingkat disimpan sebagai teks karena boleh berupa angka maupun angka
        // Romawi; pengurutannya dipaksa numerik agar 10 tidak mendahului 9.
        return $query->orderByRaw('CAST(tingkat AS UNSIGNED) asc')
                     ->orderBy('nama_kelas', 'asc')
                     ->paginate(10)
                     ->withQueryString();
    }

    public function simpan(array $data): Kelas
    {
        return Kelas::create($data);
    }

    public function perbarui(int $id, array $data): Kelas
    {
        $kelas = Kelas::findOrFail($id);
        // $data berisi tepat kolom yang dikirim petugas; kolom yang tidak ikut
        // terkirim sengaja dibiarkan apa adanya, bukan dikosongkan.
        $kelas->update($data);

        return $kelas;
    }

    /**
     * @throws AturanBisnisException bila kelasnya masih terkait buku
     */
    public function hapus(int $id): void
    {
        $kelas = Kelas::withCount('buku')->findOrFail($id);

        if ($kelas->buku_count > 0) {
            throw new AturanBisnisException('Kelas tidak dapat dihapus karena masih terkait dengan ' . $kelas->buku_count . ' buku.');
        }

        $kelas->delete();
    }
}
