<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Rak;
use App\Models\Eksemplar;
use App\Models\Anggota;
use App\Models\Penulis;
use App\Models\User;
use App\Models\Pengaturan;
use App\Models\Peminjaman;
use App\Models\Reservasi;

class PublicController extends Controller
{
    public function home()
    {
        $stats = [
            'total_koleksi' => Buku::count(),
            'buku_tersedia' => Eksemplar::where('status', 'tersedia')->count(),
            'sedang_dipinjam' => Eksemplar::where('status', 'dipinjam')->count(),
            'anggota_aktif' => Anggota::where('status', 'aktif')->count(),
        ];

        $buku_terbaru = Buku::with(['penulis', 'penerbit', 'kategori', 'rak', 'eksemplar'])
            ->latest()
            ->take(6)
            ->get();

        $buku_populer = Buku::with(['penulis', 'penerbit', 'kategori', 'rak', 'eksemplar'])
            ->orderBy('view_count', 'desc')
            ->take(6)
            ->get();

        $kategori_list = Kategori::all();
        $penulis_list = Penulis::all();
        $tahun_list = Buku::select('tahun_terbit')->distinct()->orderBy('tahun_terbit', 'desc')->pluck('tahun_terbit');

        $jam_operasional = Pengaturan::where('key', 'jam_operasional')->value('value') ?? 'Senin - Jumat: 07.00 - 15.30 WIB | Sabtu: 07.00 - 12.00 WIB';
        $nama_perpustakaan = Pengaturan::where('key', 'nama_perpustakaan')->value('value') ?? 'Perpustakaan SMK PGRI';

        return view('public.home', compact('stats', 'buku_terbaru', 'buku_populer', 'kategori_list', 'penulis_list', 'tahun_list', 'jam_operasional', 'nama_perpustakaan'));
    }

    public function katalog(Request $request)
    {
        $query = Buku::with(['penulis', 'penerbit', 'kategori', 'rak', 'eksemplar']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('penulis', function($qp) use ($search) {
                      $qp->where('nama', 'like', "%{$search}%");
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
                $query->whereHas('eksemplar', function($q) {
                    $q->where('status', 'tersedia');
                });
            } else if ($request->status === 'dipinjam') {
                $query->whereDoesntHave('eksemplar', function($q) {
                    $q->where('status', 'tersedia');
                });
            }
        }

        // Sorting
        $sort = $request->get('sort', 'terbaru');
        if ($sort === 'terlama') {
            $query->oldest();
        } else if ($sort === 'populer') {
            $query->orderBy('view_count', 'desc');
        } else if ($sort === 'judul_asc') {
            $query->orderBy('judul', 'asc');
        } else {
            $query->latest();
        }

        $buku = $query->paginate(8)->withQueryString();

        $kategori_list = Kategori::all();
        $penulis_list = Penulis::all();
        $rak_list = Rak::all();
        $tahun_list = Buku::select('tahun_terbit')->distinct()->orderBy('tahun_terbit', 'desc')->pluck('tahun_terbit');

        return view('public.katalog', compact('buku', 'kategori_list', 'penulis_list', 'rak_list', 'tahun_list'));
    }

    public function detailBuku($id)
    {
        $buku = Buku::with(['penulis', 'penerbit', 'kategori', 'rak', 'eksemplar.rak'])->findOrFail($id);
        $buku->increment('view_count');

        $userLoan = null;
        $userReservation = null;

        if (auth()->check()) {
            $userId = auth()->id();
            $userLoan = Peminjaman::where('user_id', $userId)
                ->where('buku_id', $buku->id)
                ->where('status', 'dipinjam')
                ->first();

            $userReservation = Reservasi::where('user_id', $userId)
                ->where('buku_id', $buku->id)
                ->whereIn('status', ['menunggu', 'tersedia'])
                ->first();
        }

        return view('public.detail-buku', compact('buku', 'userLoan', 'userReservation'));
    }
}
