<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;
    protected $table = 'pengaturan';
    protected $fillable = ['key', 'value', 'label', 'deskripsi'];

    public static function ambil(string $key, $default = null)
    {
        $item = static::where('key', $key)->first();
        return ($item && $item->value !== null && $item->value !== '') ? $item->value : $default;
    }
}
