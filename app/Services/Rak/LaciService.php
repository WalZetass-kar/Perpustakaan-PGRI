<?php

namespace App\Services\Rak;

use App\Models\AuditLog;
use App\Models\Buku;
use App\Models\Rak;
use App\Models\RakLaci;

/**
 * Laci: tingkatan di dalam sebuah rak, tempat buku benar-benar berada.
 */
class LaciService
{
    /**
     * Ambil rak, atau hentikan permintaan dengan 404 bila tidak ada.
     */
    public function rak(int $rakId): Rak
    {
        return Rak::findOrFail($rakId);
    }

    public function simpan(int $rakId, array $data): RakLaci
    {
        $rak = $this->rak($rakId);

        // Tanpa nomor yang ditentukan petugas, laci baru diletakkan sesudah
        // laci terakhir yang sudah ada di rak itu.
        $nomor = $data['nomor_laci'] ?? (RakLaci::where('rak_id', $rakId)->max('nomor_laci') + 1);

        $laci = RakLaci::create([
            'rak_id'     => $rak->id,
            'nomor_laci' => $nomor,
            'nama_laci'  => trim($data['nama_laci']),
            'keterangan' => filled($data['keterangan'] ?? null) ? trim($data['keterangan']) : null,
        ]);

        AuditLog::catat('TAMBAH_LACI', "Menambahkan laci '{$laci->nama_laci}' pada rak '{$rak->nama_rak}'");

        return $laci;
    }

    public function perbarui(int $id, array $data): RakLaci
    {
        $laci = RakLaci::with('rak')->findOrFail($id);

        $laci->update([
            'nama_laci'  => trim($data['nama_laci']),
            'nomor_laci' => (int) $data['nomor_laci'],
            'keterangan' => filled($data['keterangan'] ?? null) ? trim($data['keterangan']) : null,
        ]);

        AuditLog::catat('UPDATE_LACI', "Memperbarui laci '{$laci->nama_laci}' pada rak '{$laci->rak->nama_rak}'");

        return $laci;
    }

    /**
     * Hapus laci. Buku yang ada di dalamnya tidak ikut terhapus, hanya
     * kehilangan penanda lacinya — jadi tetap terdaftar di raknya dan bisa
     * ditemukan lewat jalur "tanpa laci" pada halaman Data Buku.
     *
     * @return array{laci: string, rak: string}
     */
    public function hapus(int $id): array
    {
        $laci = RakLaci::with('rak')->withCount('buku')->findOrFail($id);

        if ($laci->buku_count > 0) {
            Buku::where('rak_laci_id', $laci->id)->update(['rak_laci_id' => null]);
        }

        $identitas = ['laci' => $laci->nama_laci, 'rak' => $laci->rak->nama_rak ?? 'Rak'];
        $laci->delete();

        AuditLog::catat('HAPUS_LACI', "Menghapus laci '{$identitas['laci']}' dari '{$identitas['rak']}'");

        return $identitas;
    }

    /**
     * Ringkasan laci sebuah rak untuk form buku: begitu rak dipilih, daftar
     * lacinya diambil tanpa memuat ulang halaman.
     */
    public function ringkasanUntukRak(int $rakId)
    {
        return RakLaci::with('buku.kategori')
            ->where('rak_id', $rakId)
            ->orderBy('nomor_laci')
            ->get()
            ->map(function (RakLaci $laci) {
                return [
                    'id'          => $laci->id,
                    'nomor_laci'  => $laci->nomor_laci,
                    'nama_laci'   => $laci->nama_laci,
                    'keterangan'  => $laci->keterangan,
                    'buku_count'  => $laci->buku->count(),
                    'categories'  => $laci->buku->pluck('kategori.nama')->filter()->unique()->values()->all(),
                ];
            });
    }
}
