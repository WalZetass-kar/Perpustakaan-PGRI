<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Buku\PenelusuranBukuService;
use Illuminate\Http\Request;

class DataBukuController extends Controller
{
    public function __construct(private PenelusuranBukuService $penelusuran)
    {
    }

    public function index(Request $request)
    {
        return view('admin.data-buku.index', [
            'rakList' => $this->penelusuran->daftarRak($request->input('search')),
            'stats'   => $this->penelusuran->statistikKoleksi(),
        ]);
    }

    public function rak($rakId)
    {
        $rak = $this->penelusuran->rak((int) $rakId);

        return view('admin.data-buku.rak', [
            'rak'             => $rak,
            'laciList'        => $this->penelusuran->laciDiRak($rak),
            'jumlahTanpaLaci' => $this->penelusuran->jumlahTanpaLaci($rak),
        ]);
    }

    public function laci(Request $request, $rakId, $laciId)
    {
        $rak = $this->penelusuran->rak((int) $rakId);
        $laci = $this->penelusuran->laci($rak, (int) $laciId);

        return view('admin.data-buku.laci', [
            'bukuList' => $this->penelusuran->bukuDiLaci($rak, $laci, $request->input('search')),
            'rak'      => $rak,
            'laci'     => $laci,
        ]);
    }

    public function tanpaLaci(Request $request, $rakId)
    {
        $rak = $this->penelusuran->rak((int) $rakId);

        return view('admin.data-buku.laci', [
            'bukuList' => $this->penelusuran->bukuTanpaLaci($rak, $request->input('search')),
            'rak'      => $rak,
            'laci'     => null,
        ]);
    }
}
