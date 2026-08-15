<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Rak;
use App\Models\Penulis;
use App\Models\Penerbit;
use App\Models\Anggota;
use App\Models\Pengaturan;
use App\Models\Peminjaman;

class PublicController extends Controller
{
    public function home()
    {
        $stats = [
            'total_koleksi'   => Buku::count(),
            'buku_tersedia'   => (int) Buku::sum('available_quantity'),
            'sedang_dipinjam' => (int) Peminjaman::where('status', 'dipinjam')->sum('jumlah'),
            'anggota_aktif'   => Anggota::where('status', 'aktif')->count(),
        ];

        $buku_terbaru = Buku::with(['penulis', 'penerbit', 'kategori', 'rak', 'laci'])
            ->latest()
            ->take(6)
            ->get();

        $buku_populer = Buku::with(['penulis', 'penerbit', 'kategori', 'rak', 'laci'])
            ->orderBy('view_count', 'desc')
            ->take(6)
            ->get();

        $kategori_list = Kategori::all();
        $penulis_list = Penulis::all();
        $tahun_list = Buku::select('tahun_terbit')->distinct()->orderBy('tahun_terbit', 'desc')->pluck('tahun_terbit');

        $jam_operasional = Pengaturan::where('key', 'jam_operasional')->value('value') ?? 'Senin - Jumat: 07.00 - 15.30 WIB';
        $nama_perpustakaan = Pengaturan::where('key', 'nama_perpustakaan')->value('value') ?? 'Perpustakaan SMK PGRI';

        return view('public.home', compact('stats', 'buku_terbaru', 'buku_populer', 'kategori_list', 'penulis_list', 'tahun_list', 'jam_operasional', 'nama_perpustakaan'));
    }

    public function katalog(Request $request)
    {
        $query = Buku::with(['penulis', 'penerbit', 'kategori', 'rak', 'laci']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('penulis', function($qp) use ($search) {
                      $qp->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('kategori', function($qk) use ($search) {
                      $qk->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('penerbit', function($qpb) use ($search) {
                      $qpb->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('penulis_id')) {
            $query->where('penulis_id', $request->penulis_id);
        }

        if ($request->filled('rak_id')) {
            $query->where('rak_id', $request->rak_id);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun_terbit', $request->tahun);
        }

        if ($request->filled('status')) {
            if ($request->status === 'tersedia') {
                $query->where('available_quantity', '>', 0);
            } else if ($request->status === 'dipinjam') {
                $query->where('available_quantity', '<=', 0);
            }
        }

        $sort = $request->get('sort', 'terbaru');
        switch ($sort) {
            case 'terlama':
                $query->oldest();
                break;
            case 'judul_asc':
                $query->orderBy('judul', 'asc');
                break;
            case 'judul_desc':
                $query->orderBy('judul', 'desc');
                break;
            case 'populer':
                $query->orderBy('view_count', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $buku = $query->paginate(12)->withQueryString();

        $total_buku_count = Buku::count();
        $total_kategori_count = Kategori::count();
        $total_rak_count = Rak::count();

        $kategori_list = Kategori::orderBy('nama', 'asc')->get();
        $penulis_list = Penulis::orderBy('nama', 'asc')->get();
        $rak_list = Rak::with('laci')->orderBy('kode_rak', 'asc')->get();
        $tahun_list = Buku::select('tahun_terbit')->whereNotNull('tahun_terbit')->distinct()->orderBy('tahun_terbit', 'desc')->pluck('tahun_terbit');

        return view('public.katalog', compact('buku', 'kategori_list', 'penulis_list', 'rak_list', 'tahun_list', 'total_buku_count', 'total_kategori_count', 'total_rak_count'));
    }

    public function detailBuku($id)
    {
        $buku = Buku::with(['penulis', 'penerbit', 'kategori', 'rak.laci', 'laci'])->findOrFail($id);
        $buku->increment('view_count');

        $userLoan = null;
        if (auth()->check()) {
            $userLoan = Peminjaman::where('user_id', auth()->id())
                ->where('buku_id', $buku->id)
                ->where('status', 'dipinjam')
                ->first();
        }

        $relatedBooks = Buku::with(['penulis', 'kategori', 'rak', 'laci'])
            ->where('id', '!=', $buku->id)
            ->where(function($query) use ($buku) {
                if ($buku->kategori_id) {
                    $query->where('kategori_id', $buku->kategori_id);
                }
                if ($buku->penulis_id) {
                    $query->orWhere('penulis_id', $buku->penulis_id);
                }
            })
            ->take(4)
            ->get();

        if ($relatedBooks->isEmpty()) {
            $relatedBooks = Buku::with(['penulis', 'kategori', 'rak', 'laci'])
                ->where('id', '!=', $buku->id)
                ->latest()
                ->take(4)
                ->get();
        }

        return view('public.detail-buku', compact('buku', 'userLoan', 'relatedBooks'));
    }

    public function searchSuggestions(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $bukuList = Buku::with(['penulis', 'kategori', 'rak', 'laci'])
            ->where(function($query) use ($q) {
                $query->where('judul', 'like', "%{$q}%")
                      ->orWhere('isbn', 'like', "%{$q}%")
                      ->orWhereHas('penulis', function($qp) use ($q) {
                          $qp->where('nama', 'like', "%{$q}%");
                      })
                      ->orWhereHas('kategori', function($qk) use ($q) {
                          $qk->where('nama', 'like', "%{$q}%");
                      });
            })
            ->take(8)
            ->get()
            ->map(function($buku) {
                return [
                    'id'                 => $buku->id,
                    'judul'              => $buku->judul,
                    'penulis'            => $buku->penulis ? $buku->penulis->nama : 'Penulis Tidak Diketahui',
                    'kategori'           => $buku->kategori ? $buku->kategori->nama : 'Umum',
                    'rak'                => $buku->rak ? $buku->rak->nama_rak : 'Belum Ditentukan',
                    'kode_rak'           => $buku->rak ? $buku->rak->kode_rak : '-',
                    'laci'               => $buku->laci ? $buku->laci->nama_laci : ($buku->rak ? 'Laci 1' : '-'),
                    'lokasi_lengkap'     => $buku->lokasi_lengkap,
                    'total_quantity'     => $buku->total_quantity,
                    'available_quantity' => $buku->available_quantity,
                    'status'             => $buku->available_quantity > 0 ? 'Tersedia' : 'Dipinjam',
                    'cover_url'          => $buku->cover_url,
                    'detail_url'         => route('buku.detail', $buku->id),
                ];
            });

        return response()->json($bukuList);
    }
}
