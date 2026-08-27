<?php

namespace App\Services;

use App\Exceptions\AturanBisnisException;
use App\Models\AuditLog;

/**
 * Pencadangan data perpustakaan untuk dibawa teknisi sekolah.
 *
 * Membungkus DatabaseBackupService — yang mengurus teknis dump dan zip —
 * dengan hal-hal yang jadi urusan perpustakaan: penamaan berkas, pencatatan
 * di jejak audit, dan pesan yang bisa ditindaklanjuti bila servernya belum
 * siap.
 */
class CadanganService
{
    public function __construct(private DatabaseBackupService $backup)
    {
    }

    /**
     * Dump SQL saja.
     *
     * @return array{isi: string, nama: string}
     */
    public function dumpSql(): array
    {
        $nama = 'backup_perpustakaan_' . date('Ymd_His') . '.sql';
        $isi = $this->backup->generateSqlDump();

        AuditLog::catat('DOWNLOAD_BACKUP_SQL', "Super Admin mengunduh berkas cadangan database ({$nama})");

        return ['isi' => $isi, 'nama' => $nama];
    }

    /**
     * Cadangan lengkap: dump SQL sekaligus berkas sampul buku.
     *
     * Berkas .SQL saja tidak cukup untuk memulihkan perpustakaan seperti
     * semula. Kolom `cover` pada tabel buku hanya menyimpan NAMA berkasnya,
     * gambarnya sendiri ada di storage. Kalau server bermasalah dan yang
     * dipegang teknisi cuma .SQL, seluruh data buku memang kembali, tetapi
     * sampulnya hilang semua dan harus diunggah ulang satu per satu.
     *
     * @return string lokasi berkas ZIP sementara
     * @throws AturanBisnisException bila server tidak mendukung ZIP atau gagal menulis
     */
    public function zipLengkap(): string
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new AturanBisnisException('Ekstensi ZIP tidak aktif di server ini. Silakan gunakan tombol "Unduh .SQL Saja", lalu salin folder storage/app/public/covers secara manual.');
        }

        $lokasi = $this->backup->createZipBackup();

        if (!$lokasi || !file_exists($lokasi)) {
            throw new AturanBisnisException('Gagal membuat berkas cadangan ZIP. Periksa izin tulis pada folder storage/app/backups.');
        }

        AuditLog::catat('DOWNLOAD_BACKUP_ZIP', 'Super Admin mengunduh cadangan lengkap database + sampul buku (' . basename($lokasi) . ')');

        return $lokasi;
    }
}
