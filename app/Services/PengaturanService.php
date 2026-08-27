<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use App\Models\User;

/**
 * Identitas perpustakaan dan tetapan sistem, disimpan sebagai pasangan
 * kunci-nilai supaya bisa ditambah tanpa mengubah struktur tabel.
 */
class PengaturanService
{
    public function semua()
    {
        return Pengaturan::all()->pluck('value', 'key');
    }

    /**
     * Keterangan lingkungan server untuk panel bantuan di halaman Pengaturan.
     */
    public function infoSistem(): array
    {
        return [
            'laravel_version' => app()->version(),
            'php_version'     => PHP_VERSION,
            'db_driver'       => config('database.default'),
            'app_env'         => config('app.env'),
            'total_buku'      => Buku::count(),
            'total_pinjam'    => Peminjaman::count(),
            'total_pengguna'  => User::count(),
        ];
    }

    /**
     * Identitas sekolah untuk kop dan kolom tanda tangan laporan. Nilainya
     * diambil dari menu Pengaturan, dengan tanda '-' bila belum diisi petugas.
     */
    public function identitasLaporan(): array
    {
        $pengaturan = $this->semua();

        return [
            'nama_perpustakaan' => $pengaturan['nama_perpustakaan'] ?? 'Perpustakaan Sekolah',
            'nama_sekolah'      => $pengaturan['nama_sekolah'] ?? '-',
            'alamat'            => $pengaturan['alamat'] ?? '-',
            'npsn'              => $pengaturan['npsn'] ?? '-',
            'kepala'            => $pengaturan['kepala_perpustakaan'] ?? '-',
            'nip_kepala'        => $pengaturan['nip_kepala_perpustakaan'] ?? '-',
            'kota'              => $pengaturan['kota'] ?? '',
            'tanggal_cetak'     => date('d/m/Y H:i'),
            'petugas'           => auth()->user()->name ?? 'Petugas Perpustakaan',
        ];
    }

    /**
     * Simpan seluruh nilai yang dikirim dari form pengaturan.
     *
     * Tag HTML dibuang lebih dulu karena sebagian nilai ini dicetak apa adanya
     * di kop laporan dan halaman katalog.
     */
    public function simpan(array $nilai): void
    {
        foreach ($nilai as $kunci => $isi) {
            Pengaturan::updateOrCreate(
                ['key' => $kunci],
                [
                    'value' => strip_tags((string) $isi),
                    'label' => ucwords(str_replace('_', ' ', $kunci)),
                    'tipe'  => is_numeric($isi) ? 'number' : 'text',
                ]
            );
        }

        AuditLog::catat('UPDATE_PENGATURAN', 'Memperbarui konfigurasi sistem & identitas perpustakaan');
    }
}
