<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Buku\PenelusuranBukuService;
use Illuminate\Http\Request;

class TemukanBukuController extends Controller
{
    public function __construct(private PenelusuranBukuService $penelusuran)
    {
    }

    public function index(Request $request)
    {
        $bukuList = $this->penelusuran->cari([
            'cari'        => $request->input('search'),
            'kategori_id' => $request->input('kategori_id'),
            'rak_id'      => $request->input('rak_id'),
            'status_stok' => $request->input('status_stok'),
        ]);

        return view('admin.temukan-buku.index', ['bukuList' => $bukuList] + $this->penelusuran->konteksPencarian());
    }
}
