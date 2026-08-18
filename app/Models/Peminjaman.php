<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;
    protected $table = 'peminjaman';
    
    protected $fillable = [
        'kode_peminjaman',
        'nama_peminjam',
        'jurusan',
        'nomor_induk',
        'no_wa',
        'user_id',
        'buku_id',
        'jumlah',
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'waktu_kembali',
        'jumlah_perpanjangan',
        'status',
        'catatan',
        'alasan_penolakan',
        'petugas_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function getBorrowerDisplayNameAttribute()
    {
        return $this->nama_peminjam ?: ($this->user->name ?? 'Siswa / Anggota');
    }
}
