<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eksemplar extends Model
{
    use HasFactory;
    protected $table = 'eksemplar';
    protected $fillable = ['buku_id', 'kode_eksemplar', 'barcode', 'kondisi', 'rak_id', 'status'];

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function rak()
    {
        return $this->belongsTo(Rak::class);
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }
}
