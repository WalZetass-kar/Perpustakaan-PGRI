<?php

namespace App\Services;

use App\Models\AuditLog;

/**
 * Jejak aktivitas petugas. Pencatatannya sendiri dilakukan lewat
 * AuditLog::catat() dari masing-masing service; di sini hanya pembacaannya.
 */
class JejakAuditService
{
    public function daftar()
    {
        return AuditLog::latest()->paginate(20);
    }
}
