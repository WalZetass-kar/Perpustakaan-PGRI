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

    public function getLokasiLengkapAttribute()
    {
        $rakNama = $this->rak ? $this->rak->nama_rak : 'Belum diatur';
        $laciNama = $this->laci ? $this->laci->nama_laci : ($this->rak ? 'Laci 1' : '-');
        return $rakNama . ' - ' . $laciNama;
    }
}
