<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    use HasFactory;

    protected $table = 'rak';

    protected $fillable = [
        'kode_rak',
        'nama_rak',
        'lokasi',
        'kategori_id',
        'deskripsi',
        'status',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function laci()
    {
        return $this->hasMany(RakLaci::class, 'rak_id')->orderBy('nomor_laci');
    }

    public function buku()
    {
        return $this->hasMany(Buku::class);
    }
}
