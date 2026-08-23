<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;

class DatabaseBackupService
{
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
        $output .= "SET time_zone = \"+07:00\";\n\n";

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
                $rows = DB::table($table)->get();
                if ($rows->count() > 0) {
                    $columnNames = array_keys((array) $rows->first());
                    $escapedCols = array_map(function($c) { return "`{$c}`"; }, $columnNames);

                    $output .= "INSERT INTO `{$table}` (" . implode(', ', $escapedCols) . ") VALUES\n";
                    $valuesList = [];
                    foreach ($rows as $row) {
                        $rowArr = (array) $row;
                        $escapedVals = array_map(function($v) use ($pdo) {
                            if (is_null($v)) return 'NULL';
                            return $pdo->quote($v);
                        }, array_values($rowArr));
                        $valuesList[] = "(" . implode(', ', $escapedVals) . ")";
                    }
                    $output .= implode(",\n", $valuesList) . ";\n\n";
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
