<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;
    protected $table = 'audit_logs';
    protected $fillable = ['user_id', 'user_name', 'aktivitas', 'deskripsi', 'ip_address'];

    /**
     * Catat satu aktivitas petugas ke jejak audit.
     *
     * Pencatatnya selalu petugas yang sedang login dan IP-nya selalu diambil
     * dari request berjalan, jadi keduanya tidak perlu dioper dari controller.
     */
    public static function catat(string $aktivitas, string $deskripsi): self
    {
        return static::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()?->name,
            'aktivitas'  => $aktivitas,
            'deskripsi'  => $deskripsi,
            'ip_address' => request()->ip(),
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
