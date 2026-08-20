<?php

namespace App\Console\Commands;

use App\Models\Buku;
use App\Services\CoverImageService;
use Illuminate\Console\Command;

/**
 * Membuat varian ukuran untuk cover buku yang sudah terlanjur diunggah
 * sebelum fitur varian ada.
 *
 * Aman dijalankan berulang: secara bawaan cover yang variannya sudah lengkap
 * akan dilewati, jadi tidak ada pekerjaan ganda.
 */
class RegenerateCovers extends Command
{
    protected $signature = 'covers:regenerate
                            {--force : Buat ulang varian meski sudah ada}';

    protected $description = 'Buat varian ukuran (thumb/card) untuk cover buku yang sudah ada';

    public function handle(CoverImageService $covers): int
    {
        $bukuList = Buku::whereNotNull('cover')->where('cover', '!=', '')->get(['id', 'judul', 'cover']);

        if ($bukuList->isEmpty()) {
            $this->info('Tidak ada cover yang perlu diproses.');
            return self::SUCCESS;
        }

        $this->info("Memproses {$bukuList->count()} cover...");
        $this->newLine();

        $dibuat = 0;
        $dilewati = 0;
        $gagal = 0;

        foreach ($bukuList as $buku) {
            if (!$this->option('force') && $this->variantsExist($buku->cover)) {
                $dilewati++;
                continue;
            }

            if ($covers->generateVariants($buku->cover)) {
                $dibuat++;
                $this->line("  <fg=green>✓</> {$buku->judul}");
            } else {
                $gagal++;
                $this->line("  <fg=red>✗</> {$buku->judul} <fg=gray>({$buku->cover} tidak terbaca)</>");
            }
        }

        $this->newLine();
        $this->info("Selesai — dibuat: {$dibuat}, dilewati: {$dilewati}, gagal: {$gagal}");

        if ($gagal > 0) {
            $this->warn('Cover yang gagal tetap tampil memakai file aslinya, hanya belum teroptimasi.');
        }

        return self::SUCCESS;
    }

    protected function variantsExist(string $cover): bool
    {
        foreach (array_keys(CoverImageService::VARIANTS) as $variant) {
            $path = public_path('storage/' . Buku::coverVariantPath($cover, $variant));
            if (!is_file($path)) {
                return false;
            }
        }

        return true;
    }
}
