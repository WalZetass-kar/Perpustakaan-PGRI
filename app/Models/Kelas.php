<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    protected $table = 'kelas';
    protected $fillable = ['tingkat', 'nama_kelas', 'deskripsi'];

    public function buku()
    {
        return $this->hasMany(Buku::class);
    }

    /**
     * Label siap pakai untuk laporan & dropdown: "10 - X RPL 1". Kalau tingkat
     * belum diisi (kelas lama sebelum migrasi, atau kelas non-tingkat), cukup
     * tampilkan nama kelasnya saja.
     */
    public function getLabelLengkapAttribute(): string
    {
        return $this->tingkat
            ? $this->tingkat . ' - ' . $this->nama_kelas
            : $this->nama_kelas;
    }
}
