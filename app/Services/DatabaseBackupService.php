<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

class DatabaseBackupService
{
    /** Banyaknya baris per pernyataan INSERT saat mencadangkan. */
    private const UKURAN_POTONGAN = 500;

    public function generateSqlDump(): string
    {
        $pdo = DB::connection()->getPdo();
        $dbName = DB::connection()->getDatabaseName();
        $driver = DB::connection()->getDriverName();

        $output = "-- ========================================================\n";
        $output .= "-- Sistem Informasi Perpustakaan - Cadangan Basis Data\n";
        $output .= "-- Tanggal Ekspor : " . date('Y-m-d H:i:s') . "\n";
        $output .= "-- Basis Data     : " . $dbName . "\n";
        $output .= "-- Driver         : " . $driver . "\n";
        $output .= "-- ========================================================\n\n";

        if ($driver === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            $tableNames = array_map(function($t) { return $t->name; }, $tables);

            foreach ($tableNames as $table) {
                $create = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
                if ($create && isset($create->sql)) {
                    $output .= "DROP TABLE IF EXISTS \"{$table}\";\n";
                    $output .= $create->sql . ";\n\n";

                    $rows = DB::table($table)->get();
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $columns = array_keys($rowArray);
                        $escapedColumns = array_map(function($c) { return "\"{$c}\""; }, $columns);
                        $escapedValues = array_map(function($v) use ($pdo) {
                            if (is_null($v)) return 'NULL';
                            return $pdo->quote($v);
                        }, array_values($rowArray));

                        $output .= "INSERT INTO \"{$table}\" (" . implode(', ', $escapedColumns) . ") VALUES (" . implode(', ', $escapedValues) . ");\n";
                    }
                    $output .= "\n";
                }
            }

            return $output;
        }

        $output .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $output .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $output .= "SET time_zone = \"+07:00\";\n";
        // Menyatakan karakter set sambungan saat pemulihan. Tanpa baris ini,
        // klien yang masih memakai latin1 sebagai bawaan -- phpMyAdmin dan
        // Command Prompt di Windows masih sering begitu -- akan menafsirkan
        // ulang byte pada INSERT, sehingga judul buku bertanda baca khusus
        // berubah menjadi karakter kacau setelah dipulihkan. mysqldump selalu
        // menuliskannya justru untuk alasan ini.
        $output .= "SET NAMES utf8mb4;\n\n";

        $tables = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
        $keyName = 'Tables_in_' . $dbName;

        foreach ($tables as $t) {
            $table = $t->$keyName ?? array_values((array) $t)[0];

            $createTable = DB::selectOne("SHOW CREATE TABLE `{$table}`");
            $createSql = $createTable->{'Create Table'} ?? null;

            if ($createSql) {
                $output .= "-- Struktur tabel `{$table}`\n";
                $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $output .= $createSql . ";\n\n";

                $output .= "-- Data tabel `{$table}`\n";

                // Dibaca dan ditulis per potongan, bukan sekali angkut.
                //
                // Bentuk lamanya memuat seluruh isi tabel ke memori sekaligus,
                // lalu menuliskannya sebagai SATU pernyataan INSERT raksasa.
                // Dua-duanya menjadi masalah begitu perpustakaan berjalan
                // beberapa tahun: audit_logs yang terus bertambah bisa membuat
                // permintaan unduh kehabisan memory_limit, dan INSERT yang
                // melewati max_allowed_packet MySQL (bawaannya 64 MB) membuat
                // pemulihannya GAGAL -- justru pada saat cadangan itu paling
                // dibutuhkan.
                $columnNames = Schema::getColumnListing($table);

                if (!empty($columnNames)) {
                    $escapedCols = array_map(function ($c) { return "`{$c}`"; }, $columnNames);
                    $header = "INSERT INTO `{$table}` (" . implode(', ', $escapedCols) . ") VALUES\n";

                    $adaData = false;

                    // Kolom pertama tabel-tabel di sistem ini selalu kunci
                    // primernya, sehingga urutannya stabil dan chunk() tidak
                    // melewatkan atau menggandakan baris.
                    DB::table($table)->orderBy($columnNames[0])->chunk(
                        self::UKURAN_POTONGAN,
                        function ($rows) use (&$output, &$adaData, $header, $pdo) {
                            $valuesList = [];

                            foreach ($rows as $row) {
                                $escapedVals = array_map(function ($v) use ($pdo) {
                                    if (is_null($v)) {
                                        return 'NULL';
                                    }
                                    return $pdo->quote($v);
                                }, array_values((array) $row));

                                $valuesList[] = '(' . implode(', ', $escapedVals) . ')';
                            }

                            if ($valuesList !== []) {
                                $output .= $header . implode(",\n", $valuesList) . ";\n";
                                $adaData = true;
                            }
                        }
                    );

                    if ($adaData) {
                        $output .= "\n";
                    }
                }
            }
        }

        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $output;
    }

    public function createZipBackup(): ?string
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = date('Ymd_His');
        $zipPath = $backupDir . "/backup_perpustakaan_{$timestamp}.zip";

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return null;
        }

        $sqlContent = $this->generateSqlDump();
        $zip->addFromString("database_{$timestamp}.sql", $sqlContent);

        $coversPath = storage_path('app/public/covers');
        if (File::exists($coversPath)) {
            $files = File::allFiles($coversPath);
            foreach ($files as $file) {
                $relativePath = 'covers/' . $file->getRelativePathname();
                $zip->addFile($file->getRealPath(), $relativePath);
            }
        }

        $zip->close();
        return $zipPath;
    }
}
