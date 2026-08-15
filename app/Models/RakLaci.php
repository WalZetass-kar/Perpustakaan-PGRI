<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RakLaci extends Model
{
    use HasFactory;

    protected $table = 'rak_laci';

    protected $fillable = [
        'rak_id',
        'nomor_laci',
        'nama_laci',
        'keterangan',
    ];

    public function rak()
    {
        return $this->belongsTo(Rak::class);
    }

    public function buku()
    {
        return $this->hasMany(Buku::class, 'rak_laci_id');
    }
}
