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

        // Diurutkan lewat `tingkat_angka`, bukan `tingkat` mentahnya. SQL tidak
        // mengenal angka Romawi — CAST('XI' AS UNSIGNED) bernilai 0 sehingga
        // kelas XI mendahului kelas 9 — jadi terjemahannya disimpan lebih dulu
        // oleh model saat baris disimpan. Kelas tanpa tingkat bernilai null dan
        // muncul lebih dulu, sebagaimana perilaku sebelumnya.
        return $query->orderBy('tingkat_angka', 'asc')
                     ->orderBy('nama_kelas', 'asc')
                     ->paginate(10)
                     ->withQueryString();
    }

    /**
     * @throws AturanBisnisException bila tingkatnya bertentangan dengan nama,
     *                               atau pasangan tingkat & nama itu sudah dipakai kelas lain
     */
    public function simpan(array $data): Kelas
    {
        $this->pastikanTingkatSelaras($data['tingkat'] ?? null, $data['nama_kelas'] ?? null);
        $this->pastikanBelumAda($data['tingkat'] ?? null, $data['nama_kelas'] ?? null);

        return Kelas::create($data);
    }

    /**
     * @throws AturanBisnisException bila tingkatnya bertentangan dengan nama,
     *                               atau pasangan tingkat & nama itu sudah dipakai kelas lain
     */
    public function perbarui(int $id, array $data): Kelas
    {
        $kelas = Kelas::findOrFail($id);

        // Yang diperiksa adalah rupa kelas ini SETELAH perubahan diterapkan.
        // Kolom yang tidak ikut terkirim diambil dari nilai yang tersimpan,
        // supaya mengubah deskripsi saja tidak ikut membandingkan nama kosong.
        $tingkat = array_key_exists('tingkat', $data) ? $data['tingkat'] : $kelas->tingkat;
        $namaKelas = array_key_exists('nama_kelas', $data) ? $data['nama_kelas'] : $kelas->nama_kelas;

        $this->pastikanTingkatSelaras($tingkat, $namaKelas);
        $this->pastikanBelumAda($tingkat, $namaKelas, $kelas->id);

        // $data berisi tepat kolom yang dikirim petugas; kolom yang tidak ikut
        // terkirim sengaja dibiarkan apa adanya, bukan dikosongkan.
        $kelas->update($data);

        return $kelas;
    }

    /**
     * Tingkat yang dipilih tidak boleh bertentangan dengan tingkat yang ikut
     * tertulis di awal nama kelas — mis. tingkat 11 dengan nama "XII DKV".
     * Ketika keduanya berbeda tidak ada cara menebak mana yang benar, dan
     * bukunya berakhir salah sasaran.
     *
     * @throws AturanBisnisException
     */
    private function pastikanTingkatSelaras(?string $tingkat, ?string $namaKelas): void
    {
        $tingkatDipilih = Kelas::angkaTingkat($tingkat);
        $tingkatDiNama = Kelas::tingkatDiAwalNama($namaKelas);

        if ($tingkatDipilih === null || $tingkatDiNama === null || $tingkatDipilih === $tingkatDiNama) {
            return;
        }

        throw new AturanBisnisException(
            'Tingkat yang dipilih (' . $tingkatDipilih . ') tidak cocok dengan nama kelas "'
            . $namaKelas . '" yang menunjuk tingkat ' . $tingkatDiNama . '. Samakan keduanya '
            . 'lebih dulu; angka Romawi dihitung sama dengan angka biasa.'
        );
    }

    /**
     * Penjagaan yang sama seperti aturan validasi KelasBelumTerdaftar, diulang
     * di lapisan ini supaya jalur yang tidak lewat formulir — seeder, tinker,
     * atau impor data di kemudian hari — tidak bisa menyelipkan kelas kembar.
     *
     * @throws AturanBisnisException
     */
    private function pastikanBelumAda(?string $tingkat, ?string $namaKelas, ?int $kecualiId = null): void
    {
        $kembar = Kelas::kembarDengan($tingkat, $namaKelas, $kecualiId);

        if ($kembar) {
            throw new AturanBisnisException(
                'Kelas "' . $kembar->label_lengkap . '" sudah terdaftar. Nama kelas tidak boleh sama '
                . 'pada tingkat yang sama — termasuk bila hanya berbeda spasi, huruf besar/kecil, '
                . 'atau penulisan tingkatnya (X sama dengan 10).'
            );
        }
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
