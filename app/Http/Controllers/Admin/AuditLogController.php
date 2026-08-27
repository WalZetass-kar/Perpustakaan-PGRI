<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\JejakAuditService;

class AuditLogController extends Controller
{
    public function __construct(private JejakAuditService $jejakAudit)
    {
    }

    public function index()
    {
        return view('admin.audit-log.index', ['logs' => $this->jejakAudit->daftar()]);
    }
}
