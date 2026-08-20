<?php

namespace App\Services;

use App\Models\Buku;
use Illuminate\Support\Str;

/**
 * Mengelola gambar cover buku beserta varian ukurannya.
 *
 * Kenapa ada varian: satu cover dipakai di banyak tempat dengan ukuran tampil
 * yang jauh berbeda -- thumbnail tabel dirender ~40x56px, sedangkan file
 * aslinya ratusan piksel. Tanpa varian, browser mengunduh gambar penuh lalu
 * membuang 99% pikselnya. Pada tabel 100 baris itu sekitar 4,7 MB terbuang
 * percuma. Varian dibuat sekali saat upload, bukan saat request, supaya tidak
 * membebani CPU tiap halaman dibuka.
 *
 * Varian disimpan berdampingan dengan file asli:
 *   covers/abc.jpg          <- asli (dipakai halaman detail)
 *   covers/card/abc.jpg     <- kartu grid
 *   covers/thumb/abc.jpg    <- baris tabel & list
 *
 * Dengan pola itu jalur varian bisa dihitung dari nama file asli, jadi tidak
 * perlu kolom database tambahan.
 */
class CoverImageService
{
    /**
     * Lebar maksimum tiap varian, dalam piksel.
     *
     * thumb 160px: slot terbesar yang memakainya dirender 80px (w-20), jadi
     * 160px membuatnya tetap tajam di layar retina 2x. Ukuran ini sengaja
     * dipilih agar satu varian cukup untuk semua slot kecil (36px sampai 80px)
     * tanpa perlu menambah varian ketiga.
     */
    public const VARIANTS = [
        'thumb' => 160,
        'card'  => 400,
    ];

    /**
     * Lebar maksimum file asli. Halaman detail merender cover selebar ~280px,
     * jadi 600px sudah cukup tajam untuk layar retina (2x).
     */
    public const FULL_MAX_WIDTH = 600;

    public const QUALITY = 80;

    /**
     * Simpan file cover yang diunggah, sekalian buat semua variannya.
     *
     * @return string Jalur relatif file asli, mis. "covers/abc.jpg"
     */
    public function store($file): string
    {
        $source = $this->readImage($file->getRealPath(), $file->getMimeType());

        // Format tak dikenal atau file rusak: simpan apa adanya supaya upload
        // tidak gagal total. Cover tetap tampil, hanya tanpa varian.
        if (!$source) {
            return $file->store('covers', 'public');
        }

        $coverPath = 'covers/' . Str::random(40) . '.jpg';

        $this->writeResized($source, $this->absolutePath($coverPath), self::FULL_MAX_WIDTH);
        $this->writeVariantsFrom($source, $coverPath);

        imagedestroy($source);

        return $coverPath;
    }

    /**
     * Buat ulang varian untuk cover yang sudah ada (backfill).
     *
     * @return bool true kalau varian berhasil dibuat.
     */
    public function generateVariants(string $coverPath): bool
    {
        $absolute = $this->absolutePath($coverPath);

        if (!is_file($absolute)) {
            return false;
        }

        $source = $this->readImage($absolute, mime_content_type($absolute) ?: '');

        if (!$source) {
            return false;
        }

        $this->writeVariantsFrom($source, $coverPath);
        imagedestroy($source);

        return true;
    }

    /**
     * Hapus file asli beserta seluruh variannya.
     *
     * Dipanggil saat cover diganti atau buku dihapus supaya varian lama tidak
     * menumpuk jadi file yatim yang memakan disk.
     */
    public function delete(?string $coverPath): void
    {
        if (!$coverPath) {
            return;
        }

        $paths = [$coverPath];

        foreach (array_keys(self::VARIANTS) as $variant) {
            $paths[] = Buku::coverVariantPath($coverPath, $variant);
        }

        foreach ($paths as $path) {
            $absolute = $this->absolutePath($path);
            if (is_file($absolute)) {
                @unlink($absolute);
            }
        }
    }

    /**
     * Tulis seluruh varian dari satu sumber gambar yang sudah dimuat.
     */
    protected function writeVariantsFrom($source, string $coverPath): void
    {
        foreach (self::VARIANTS as $variant => $maxWidth) {
            $this->writeResized(
                $source,
                $this->absolutePath(Buku::coverVariantPath($coverPath, $variant)),
                $maxWidth
            );
        }
    }

    /**
     * Perkecil gambar ke lebar maksimum lalu simpan sebagai JPEG.
     *
     * Gambar yang sudah lebih kecil dari batas tidak diperbesar -- memperbesar
     * hanya menambah ukuran file tanpa menambah detail.
     */
    protected function writeResized($source, string $absolutePath, int $maxWidth): void
    {
        $origWidth  = imagesx($source);
        $origHeight = imagesy($source);

        if ($origWidth > $maxWidth) {
            $newWidth  = $maxWidth;
            $newHeight = max(1, (int) round($origHeight * $maxWidth / $origWidth));
        } else {
            $newWidth  = $origWidth;
            $newHeight = $origHeight;
        }

        $directory = dirname($absolutePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $canvas = imagecreatetruecolor($newWidth, $newHeight);

        // PNG bisa transparan, sedangkan JPEG tidak mengenal alpha. Tanpa alas
        // putih, area transparan akan jadi hitam pekat.
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, 0, 0, $newWidth, $newHeight, $white);

        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        imagejpeg($canvas, $absolutePath, self::QUALITY);

        imagedestroy($canvas);
    }

    /**
     * Muat gambar dari disk, sekaligus luruskan orientasinya.
     */
    protected function readImage(string $path, string $mime)
    {
        $source = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png'               => @imagecreatefrompng($path),
            'image/webp'              => @imagecreatefromwebp($path),
            default                   => null,
        };

        if (!$source) {
            return null;
        }

        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            $source = $this->applyExifOrientation($source, $path);
        }

        return $source;
    }

    /**
     * Putar gambar sesuai tanda orientasi EXIF.
     *
     * Kamera ponsel biasanya menyimpan foto dalam orientasi sensor lalu
     * menitipkan arah putarnya di EXIF. GD mengabaikan tanda itu, jadi tanpa
     * koreksi ini cover hasil foto bisa tampil miring atau terbalik.
     */
    protected function applyExifOrientation($source, string $path)
    {
        if (!function_exists('exif_read_data')) {
            return $source;
        }

        $exif = @exif_read_data($path);
        $orientation = $exif['Orientation'] ?? null;

        $degrees = match ($orientation) {
            3       => 180,
            6       => -90,
            8       => 90,
            default => 0,
        };

        if ($degrees === 0) {
            return $source;
        }

        $rotated = @imagerotate($source, $degrees, 0);

        if (!$rotated) {
            return $source;
        }

        imagedestroy($source);

        return $rotated;
    }

    protected function absolutePath(string $relativePath): string
    {
        return storage_path('app/public/' . $relativePath);
    }
}
