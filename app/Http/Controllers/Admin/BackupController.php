<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AturanBisnisException;
use App\Http\Controllers\Controller;
use App\Services\CadanganService;

class BackupController extends Controller
{
    public function __construct(private CadanganService $cadangan)
    {
    }

    public function sql()
    {
        $this->pastikanSuperAdmin();

        $berkas = $this->cadangan->dumpSql();

        return response($berkas['isi'], 200, [
            'Content-Type'        => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$berkas['nama']}\"",
        ]);
    }

    public function lengkap()
    {
        $this->pastikanSuperAdmin();

        try {
            $lokasi = $this->cadangan->zipLengkap();
        } catch (AturanBisnisException $e) {
            return back()->with('error', $e->getMessage());
        }

        // Salinan sementara ini dihapus setelah terkirim supaya folder backup
        // di server tidak menumpuk setiap kali teknisi mengunduh.
        return response()->download($lokasi, basename($lokasi), [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Lapis kedua di belakang middleware `role:super_admin` pada rutenya.
     */
    private function pastikanSuperAdmin(): void
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Administrator yang berwenang mengunduh cadangan basis data.');
        }
    }
}
