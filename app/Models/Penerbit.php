<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penerbit extends Model
{
    use HasFactory;
    protected $table = 'penerbit';
    protected $fillable = ['nama', 'kota', 'website'];

    public function buku()
    {
        return $this->hasMany(Buku::class);
    }
}
