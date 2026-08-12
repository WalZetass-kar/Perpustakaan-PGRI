<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;
    protected $table = 'buku';
    protected $guarded = ['id'];

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
