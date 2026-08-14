<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;
    protected $table = 'buku';
    protected $fillable = ['isbn', 'judul', 'penulis_id', 'penerbit_id', 'kategori_id', 'rak_id', 'tahun_terbit', 'sinopsis', 'cover', 'file_pdf', 'view_count'];

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

    public function eksemplar()
    {
        return $this->hasMany(Eksemplar::class);
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function reservasi()
    {
        return $this->hasMany(Reservasi::class);
    }

    public function getJumlahEksemplarAttribute()
    {
        return $this->eksemplar()->count();
    }

    public function getJumlahTersediaAttribute()
    {
        return $this->eksemplar()->where('status', 'tersedia')->count();
    }
}
