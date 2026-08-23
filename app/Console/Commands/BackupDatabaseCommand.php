<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DatabaseBackupService;
use App\Models\AuditLog;
use Illuminate\Support\Facades\File;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'perpus:backup {--zip : Sertakan folder sampul buku ke dalam format arsip ZIP}';
    protected $description = 'Cadangkan basis data perpustakaan ke format SQL atau ZIP';

    public function handle(DatabaseBackupService $backupService): int
    {
        $this->info('Memulai proses pencadangan basis data...');

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = date('Ymd_His');

        if ($this->option('zip')) {
            $zipPath = $backupService->createZipBackup();
            if (!$zipPath) {
                $this->error('Gagal membuat berkas arsip ZIP backup.');
                return Command::FAILURE;
            }

            $size = round(filesize($zipPath) / 1024, 2);
            $this->info("Pencadangan berhasil (Database + Sampul): {$zipPath} ({$size} KB)");
        } else {
            $sqlContent = $backupService->generateSqlDump();
            $sqlPath = $backupDir . "/backup_database_{$timestamp}.sql";
            File::put($sqlPath, $sqlContent);

            $size = round(filesize($sqlPath) / 1024, 2);
            $this->info("Pencadangan SQL berhasil: {$sqlPath} ({$size} KB)");
        }

        AuditLog::create([
            'user_id' => null,
            'user_name' => 'System CLI',
            'aktivitas' => 'BACKUP_DATABASE_CLI',
            'deskripsi' => "Pencadangan basis data berhasil dijalankan via CLI ({$timestamp})",
            'ip_address' => '127.0.0.1',
        ]);

        return Command::SUCCESS;
    }
}
