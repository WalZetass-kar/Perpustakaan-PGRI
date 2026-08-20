<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;
    protected $table = 'buku';
    
    protected $fillable = [
        'isbn',
        'judul',
        'penulis_id',
        'penerbit_id',
        'kategori_id',
        'kelas_id',
        'rak_id',
        'rak_laci_id',
        'tahun_terbit',
        'total_quantity',
        'available_quantity',
        'sinopsis',
        'keterangan_posisi',
        'cover',
        'view_count',
        'status',
    ];

    public function penulis()
    {
        return $this->belongsTo(Penulis::class);
    }

    public function penerbit()
    {
        return $this->belongsTo(Penerbit::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function rak()
    {
        return $this->belongsTo(Rak::class);
    }

    public function laci()
    {
        return $this->belongsTo(RakLaci::class, 'rak_laci_id');
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function getCoverUrlAttribute()
    {
        if ($this->cover && file_exists(public_path('storage/' . $this->cover))) {
            return asset('storage/' . $this->cover);
        }
        return null;
    }

    /**
     * URL varian cover berukuran kecil.
     *
     * Varian disimpan berdampingan dengan file asli (covers/thumb/x.jpg untuk
     * covers/x.jpg), jadi tidak butuh kolom database tambahan. Kalau varian
     * belum dibuat -- misalnya cover lama yang belum di-backfill lewat
     * `php artisan covers:regenerate` -- otomatis jatuh ke file asli supaya
     * gambar tetap tampil, hanya belum teroptimasi.
     */
    protected function coverVariantUrl(string $variant)
    {
        if (!$this->cover) {
            return null;
        }

        $variantPath = static::coverVariantPath($this->cover, $variant);

        if (file_exists(public_path('storage/' . $variantPath))) {
            return asset('storage/' . $variantPath);
        }

        return $this->cover_url;
    }

    /**
     * Ubah "covers/abc.jpg" menjadi "covers/thumb/abc.jpg".
     */
    public static function coverVariantPath(string $cover, string $variant): string
    {
        $dir = trim(dirname($cover), '.');
        $dir = $dir === '' ? '' : $dir . '/';

        return $dir . $variant . '/' . basename($cover);
    }

    /** Untuk thumbnail tabel & list (dirender ~40-56px). */
    public function getCoverThumbUrlAttribute()
    {
        return $this->coverVariantUrl('thumb');
    }

    /** Untuk kartu grid (dirender ~180-240px). */
    public function getCoverCardUrlAttribute()
    {
        return $this->coverVariantUrl('card');
    }

    public function getLokasiLengkapAttribute()
    {
        $rakNama = $this->rak ? $this->rak->nama_rak : 'Belum diatur';
        $laciNama = $this->laci ? $this->laci->nama_laci : ($this->rak ? 'Laci 1' : '-');
        return $rakNama . ' - ' . $laciNama;
    }
}
