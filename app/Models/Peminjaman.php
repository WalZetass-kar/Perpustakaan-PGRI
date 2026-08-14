<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;
    protected $table = 'peminjaman';
    protected $fillable = ['kode_peminjaman', 'user_id', 'buku_id', 'eksemplar_id', 'tanggal_pinjam', 'tanggal_jatuh_tempo', 'jumlah_perpanjangan', 'status', 'petugas_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function eksemplar()
    {
        return $this->belongsTo(Eksemplar::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class);
    }

    public function denda()
    {
        return $this->hasOne(Denda::class);
    }
}
