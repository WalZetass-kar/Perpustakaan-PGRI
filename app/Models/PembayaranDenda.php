<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranDenda extends Model
{
    use HasFactory;
    protected $table = 'pembayaran_denda';
    protected $guarded = ['id'];

    public function denda()
    {
        return $this->belongsTo(Denda::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
