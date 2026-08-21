<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Rak;
use App\Models\RakLaci;
use App\Models\Kelas;
use App\Models\Penulis;
use App\Models\Penerbit;
use App\Models\Peminjaman;
use App\Models\AuditLog;
use App\Models\Pengaturan;
use App\Services\CoverImageService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today()->toDateString();

        $stats = [
            'total_judul'            => Buku::count(),
            'total_buku'             => (int) Buku::sum('total_quantity'),
            'buku_tersedia'          => (int) Buku::sum('available_quantity'),
            'buku_sedang_dipinjam'   => (int) Peminjaman::where('status', 'dipinjam')->sum('jumlah'),
            'total_kategori'         => Kategori::count(),
            'total_penulis'          => Penulis::count(),
            'total_penerbit'         => Penerbit::count(),
            'total_rak'              => Rak::count(),
            'total_anggota'          => User::count(),
            'peminjaman_hari_ini'    => Peminjaman::whereDate('tanggal_pinjam', $today)->count(),
            'pengembalian_hari_ini'  => Peminjaman::where('status', 'dikembalikan')->whereDate('waktu_kembali', $today)->count(),
        ];

        $currentYear = (int) date('Y');
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $monthlyLoansData = Peminjaman::whereYear('tanggal_pinjam', $currentYear)
            ->selectRaw('MONTH(tanggal_pinjam) as bulan, SUM(jumlah) as total_buku')
            ->groupBy('bulan')
            ->pluck('total_buku', 'bulan')
            ->all();

        $monthlyReturnsData = Peminjaman::where('status', 'dikembalikan')
            ->whereYear('waktu_kembali', $currentYear)
            ->selectRaw('MONTH(waktu_kembali) as bulan, SUM(jumlah) as total_buku')
            ->groupBy('bulan')
            ->pluck('total_buku', 'bulan')
            ->all();

        $chartMonthly = [
            'labels'  => $monthNames,
            'loans'   => [],
            'returns' => [],
            'year'    => $currentYear,
        ];

        for ($m = 1; $m <= 12; $m++) {
            $chartMonthly['loans'][] = (int) ($monthlyLoansData[$m] ?? 0);
            $chartMonthly['returns'][] = (int) ($monthlyReturnsData[$m] ?? 0);
        }

        $yearsRange = range($currentYear - 4, $currentYear);
        $yearlyLoansData = Peminjaman::whereIn(DB::raw('YEAR(tanggal_pinjam)'), $yearsRange)
            ->selectRaw('YEAR(tanggal_pinjam) as tahun, SUM(jumlah) as total_buku')
            ->groupBy('tahun')
            ->pluck('total_buku', 'tahun')
            ->all();

        $yearlyReturnsData = Peminjaman::where('status', 'dikembalikan')
            ->whereIn(DB::raw('YEAR(waktu_kembali)'), $yearsRange)
            ->selectRaw('YEAR(waktu_kembali) as tahun, SUM(jumlah) as total_buku')
            ->groupBy('tahun')
            ->pluck('total_buku', 'tahun')
            ->all();

        $chartYearly = [
            'labels'  => array_map('strval', $yearsRange),
            'loans'   => [],
            'returns' => [],
        ];

        foreach ($yearsRange as $y) {
            $chartYearly['loans'][] = (int) ($yearlyLoansData[$y] ?? 0);
            $chartYearly['returns'][] = (int) ($yearlyReturnsData[$y] ?? 0);
        }

        $recentLoans = Peminjaman::with(['user', 'buku'])
            ->latest()
            ->take(6)
            ->get();

        $mostBorrowedBooks = Buku::withCount('peminjaman')
            ->orderBy('peminjaman_count', 'desc')
            ->take(5)
            ->get();

        $recentAuditLogs = AuditLog::latest()->take(6)->get();

        return view('admin.dashboard', compact('stats', 'chartMonthly', 'chartYearly', 'recentLoans', 'mostBorrowedBooks', 'recentAuditLogs'));
    }

    public function temukanBukuIndex(Request $request)
    {
        $query = Buku::with([
            'penulis',
            'penerbit',
            'kategori',
            'kelas',
            'rak.laci',
            'laci.rak'
        ]);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('penulis', function($qp) use ($search) {
                      $qp->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('penerbit', function($qp) use ($search) {
                      $qp->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('kategori', function($qk) use ($search) {
                      $qk->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('rak', function($qr) use ($search) {
                      $qr->where('kode_rak', 'like', "%{$search}%")
                         ->orWhere('nama_rak', 'like', "%{$search}%")
                         ->orWhere('lokasi', 'like', "%{$search}%");
                  })
                  ->orWhereHas('laci', function($ql) use ($search) {
                      $ql->where('nama_laci', 'like', "%{$search}%")
                         ->orWhere('keterangan', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('rak_id')) {
            $query->where('rak_id', $request->rak_id);
        }

        if ($request->filled('status_stok')) {
            if ($request->status_stok === 'tersedia') {
                $query->where('available_quantity', '>', 0);
            } elseif ($request->status_stok === 'penuh') {
                $query->whereColumn('available_quantity', '=', 'total_quantity')->where('total_quantity', '>', 0);
            } elseif ($request->status_stok === 'habis') {
                $query->where('available_quantity', '<=', 0);
            }
        }

        $bukuList = $query->orderBy('judul', 'asc')->paginate(12)->withQueryString();
        $kategoriList = Kategori::orderBy('nama', 'asc')->get();
        $rakList = Rak::with('laci')->orderBy('kode_rak', 'asc')->get();

        $metrics = [
            'total_koleksi'  => Buku::count(),
            'total_buku'     => (int) Buku::sum('total_quantity'),
            'buku_tersedia'  => (int) Buku::sum('available_quantity'),
            'sedang_pinjam'  => (int) Peminjaman::where('status', 'dipinjam')->sum('jumlah'),
            'total_rak'      => Rak::count(),
            'total_laci'     => RakLaci::count(),
        ];

        return view('admin.temukan-buku.index', compact('bukuList', 'kategoriList', 'rakList', 'metrics'));
    }

    public function dataBukuIndex(Request $request)
    {
        $query = Rak::with('kategori')
            ->withCount('buku')
            ->withSum('buku', 'total_quantity')
            ->withSum('buku', 'available_quantity');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_rak', 'like', "%{$search}%")
                  ->orWhere('kode_rak', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $rakList = $query->orderBy('kode_rak', 'asc')->paginate(12)->withQueryString();

        $stats = [
            'total_rak'     => Rak::count(),
            'total_judul'   => Buku::count(),
            'total_stok'    => (int) Buku::sum('total_quantity'),
            'buku_tersedia' => (int) Buku::sum('available_quantity'),
        ];

        return view('admin.data-buku.index', compact('rakList', 'stats'));
    }

    public function dataBukuByRak(Request $request, $rakId)
    {
        $rak = Rak::with('kategori')->findOrFail($rakId);

        $query = Buku::with([
            'penulis',
            'penerbit',
            'kategori',
            'kelas',
            'rak.laci',
            'laci.rak'
        ])->where('rak_id', $rakId);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        $bukuList = $query->orderBy('judul', 'asc')->paginate(12)->withQueryString();

        return view('admin.data-buku.rak', compact('bukuList', 'rak'));
    }

    public function bukuIndex(Request $request)
    {
        if ($request->ajax() || $request->wantsJson() || $request->has('draw')) {
            $totalData = Buku::count();
            $query = Buku::with(['penulis', 'penerbit', 'kategori', 'kelas', 'rak', 'laci']);

            $searchValue = $request->input('search.value');
            if (!empty($searchValue)) {
                $search = trim($searchValue);
                $query->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('isbn', 'like', "%{$search}%")
                      ->orWhere('tahun_terbit', 'like', "%{$search}%")
                      ->orWhereHas('penulis', function($qp) use ($search) {
                          $qp->where('nama', 'like', "%{$search}%");
                      })
                      ->orWhereHas('penerbit', function($qp) use ($search) {
                          $qp->where('nama', 'like', "%{$search}%");
                      })
                      ->orWhereHas('kategori', function($qk) use ($search) {
                          $qk->where('nama', 'like', "%{$search}%");
                      })
                      ->orWhereHas('rak', function($qr) use ($search) {
                          $qr->where('kode_rak', 'like', "%{$search}%")
                            ->orWhere('nama_rak', 'like', "%{$search}%");
                      })
                      ->orWhereHas('laci', function($ql) use ($search) {
                          $ql->where('nama_laci', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('kategori_id')) {
                $query->where('kategori_id', $request->kategori_id);
            }
            if ($request->filled('rak_id')) {
                $query->where('rak_id', $request->rak_id);
            }
            if ($request->filled('kelas_id')) {
                $query->where('kelas_id', $request->kelas_id);
            }
            if ($request->filled('status_stok')) {
                if ($request->status_stok === 'tersedia') {
                    $query->where('available_quantity', '>', 0);
                } elseif ($request->status_stok === 'habis') {
                    $query->where('available_quantity', '<=', 0);
                }
            }

            $totalFiltered = (clone $query)->count();

            $orderColumnIndex = (int) $request->input('order.0.column', 0);
            $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

            $columns = [
                0 => 'judul',
                1 => 'penulis_id',
                2 => 'kategori_id',
                3 => 'available_quantity',
                4 => 'id',
            ];

            $orderColumn = $columns[$orderColumnIndex] ?? 'id';
            $query->orderBy($orderColumn, $orderDir);

            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            if ($length > 0) {
                $query->skip($start)->take($length);
            }

            $bukuItems = $query->get();

            $data = [];
            foreach ($bukuItems as $buku) {
                $coverUrl = $buku->cover_url;
                $coverHtml = $coverUrl
                    ? '<img src="' . e($buku->cover_thumb_url) . '" alt="Cover" width="40" height="56" loading="lazy" class="w-full h-full object-cover">'
                    : '<div class="w-full h-full bg-gradient-to-br from-brand-900 via-brand-800 to-red-950 text-white font-black text-xs flex flex-col items-center justify-center p-1 border-l-2 border-amber-400/50 shadow-inner"><i class="fa-solid fa-book text-[11px] opacity-40"></i><span class="text-[7.5px] mt-0.5">' . e(substr($buku->judul, 0, 1)) . '</span></div>';

                $bukuHtml = '<div class="flex items-center gap-3">
                    <div class="w-10 h-14 bg-gray-100 rounded-lg overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center">
                        ' . $coverHtml . '
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 line-clamp-2 text-xs">' . e($buku->judul) . '</p>
                        <div class="flex items-center gap-2 mt-0.5 text-[10px] text-gray-500 font-mono">
                            <span>ISBN: ' . e($buku->isbn ?? 'Tanpa ISBN') . '</span>
                            <span>•</span>
                            <span>Tahun ' . e($buku->tahun_terbit) . '</span>
                        </div>
                    </div>
                </div>';

                $penulisHtml = '<p class="font-bold text-gray-800 text-xs">' . e($buku->penulis->nama ?? '-') . '</p>
                    <p class="text-[10.5px] text-gray-500">' . e($buku->penerbit->nama ?? '-') . '</p>';

                $laciName = $buku->laci->nama_laci ?? ($buku->rak ? 'Laci 1' : 'Tanpa Laci');
                $kategoriHtml = '<span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-brand-50 text-brand-700 border border-brand-200 inline-block">'
                    . e($buku->kategori->nama ?? 'Umum')
                    . '</span>';

                $stokClass = $buku->available_quantity > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200';
                $stokText = $buku->available_quantity > 0 ? 'Tersedia' : 'Habis Dipinjam';
                $stokHtml = '<div class="inline-flex flex-col items-center">
                    <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-black ' . $stokClass . '">
                        ' . e($buku->available_quantity) . ' / ' . e($buku->total_quantity) . ' Eks
                    </span>
                    <span class="text-[9.5px] text-gray-400 font-medium mt-0.5">
                        ' . $stokText . '
                    </span>
                </div>';

                $jsonData = htmlspecialchars(json_encode([
                    'id'                 => $buku->id,
                    'isbn'               => $buku->isbn ?? '',
                    'judul'              => $buku->judul,
                    'tahun_terbit'       => $buku->tahun_terbit,
                    'total_quantity'     => $buku->total_quantity,
                    'penulis_id'         => $buku->penulis_id,
                    'penerbit_id'        => $buku->penerbit_id,
                    'kategori_id'        => $buku->kategori_id,
                    'kelas_id'           => $buku->kelas_id,
                    'rak_id'             => $buku->rak_id,
                    'rak_laci_id'        => $buku->rak_laci_id,
                    'sinopsis'           => $buku->sinopsis ?? '',
                    'keterangan_posisi'  => $buku->keterangan_posisi ?? '',
                    'cover_url'          => $coverUrl
                ]), ENT_QUOTES, 'UTF-8');

                $deleteUrl = route('admin.buku.delete', $buku->id);
                $csrfToken = csrf_token();

                $aksiHtml = '<div class="flex items-center justify-end" x-data="{ open: false, menuStyle: \'\' }" @scroll.window="open = false">
                    <button @click.stop="open = !open; $nextTick(() => { const r = $el.getBoundingClientRect(); menuStyle = `top:${r.bottom + 6}px; left:${r.right - 144}px;` })" type="button" class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 transition">
                        <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                    </button>
                    <template x-teleport="body">
                        <div x-show="open" x-cloak @click.outside="open = false" :style="menuStyle"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="fixed z-[100] w-36 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                            <button type="button" data-buku=\'' . $jsonData . '\' class="btn-edit-buku w-full flex items-center gap-2 px-3 py-2 text-[11px] font-bold text-amber-700 hover:bg-amber-50 transition">
                                <i class="fa-solid fa-pen-to-square w-3.5 text-center"></i>
                                <span>Edit Buku</span>
                            </button>
                            <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirmDelete(event, \'Hapus Judul Buku?\', \'Master buku ini akan dihapus dari katalog.\')">
                                <input type="hidden" name="_token" value="' . $csrfToken . '">
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-[11px] font-bold text-rose-600 hover:bg-rose-50 transition">
                                    <i class="fa-solid fa-trash-can w-3.5 text-center"></i>
                                    <span>Hapus Buku</span>
                                </button>
                            </form>
                        </div>
                    </template>
                </div>';

                $data[] = [
                    'buku' => $bukuHtml,
                    'penulis' => $penulisHtml,
                    'kategori' => $kategoriHtml,
                    'stok' => $stokHtml,
                    'aksi' => $aksiHtml,
                    // field mentah untuk mode tampilan Grid
                    'judul_raw' => $buku->judul,
                    'cover_url' => $buku->cover_card_url,
                    'penulis_nama' => $buku->penulis->nama ?? '-',
                    'kategori_nama' => $buku->kategori->nama ?? 'Umum',
                    'kelas_nama' => $buku->kelas->nama_kelas ?? '',
                    'rak_text' => $buku->rak ? trim($buku->rak->kode_rak . ' - ' . $buku->rak->nama_rak) : 'Belum Ditentukan',
                    'laci_nama' => $laciName,
                    'total_quantity' => $buku->total_quantity,
                    'available_quantity' => $buku->available_quantity,
                ];
            }

            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data,
            ]);
        }

        $penulisList = Penulis::orderBy('nama', 'asc')->get();
        $penerbitList = Penerbit::orderBy('nama', 'asc')->get();
        $kategoriList = Kategori::orderBy('nama', 'asc')->get();
        $kelasList = Kelas::orderBy('nama_kelas', 'asc')->get();
        $rakList = Rak::with('laci')->orderBy('kode_rak', 'asc')->get();

        return view('admin.buku.index', compact('penulisList', 'penerbitList', 'kategoriList', 'kelasList', 'rakList'));
    }

    /**
     * Simpan cover beserta varian ukurannya (thumb/card) lewat CoverImageService.
     */
    private function compressAndStoreCover($file)
    {
        return app(CoverImageService::class)->store($file);
    }

    public function bukuStore(Request $request)
    {
        $request->validate([
            'isbn'                => 'nullable|string|max:50',
            'judul'               => 'required|string|max:255',
            'tahun_terbit'        => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'total_quantity'      => 'required|integer|min:1|max:10000',
            'penulis_id'          => 'nullable|exists:penulis,id',
            'penerbit_id'         => 'nullable|exists:penerbit,id',
            'kategori_id'         => 'nullable|exists:kategori,id',
            'kelas_id'            => 'nullable|exists:kelas,id',
            'rak_id'              => 'nullable|exists:rak,id',
            'rak_laci_id'         => 'nullable|exists:rak_laci,id',
            'sinopsis'            => 'nullable|string',
            'keterangan_posisi'   => 'nullable|string|max:500',
            'cover'               => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $this->compressAndStoreCover($request->file('cover'));
        }

        $buku = Buku::create([
            'isbn'               => $request->isbn,
            'judul'              => $request->judul,
            'tahun_terbit'       => $request->tahun_terbit,
            'total_quantity'     => $request->total_quantity,
            'available_quantity' => $request->total_quantity,
            'penulis_id'         => $request->penulis_id,
            'penerbit_id'        => $request->penerbit_id,
            'kategori_id'        => $request->kategori_id,
            'kelas_id'           => $request->kelas_id,
            'rak_id'             => $request->rak_id,
            'rak_laci_id'        => $request->rak_laci_id,
            'sinopsis'           => $request->sinopsis,
            'keterangan_posisi'  => $request->keterangan_posisi,
            'cover'              => $coverPath,
            'status'             => 'tersedia',
        ]);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'TAMBAH_BUKU',
            'deskripsi'  => "Menambahkan buku baru: '{$buku->judul}' (Stok: {$buku->total_quantity})",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Buku baru berhasil ditambahkan ke katalog.');
    }

    public function bukuUpdate(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);

        $request->validate([
            'isbn'              => 'nullable|string|max:50',
            'judul'             => 'required|string|max:255',
            'tahun_terbit'      => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'total_quantity'    => 'required|integer|min:1|max:10000',
            'penulis_id'        => 'nullable|exists:penulis,id',
            'penerbit_id'       => 'nullable|exists:penerbit,id',
            'kategori_id'       => 'nullable|exists:kategori,id',
            'kelas_id'          => 'nullable|exists:kelas,id',
            'rak_id'            => 'nullable|exists:rak,id',
            'rak_laci_id'       => 'nullable|exists:rak_laci,id',
            'sinopsis'          => 'nullable|string',
            'keterangan_posisi' => 'nullable|string|max:500',
            'cover'             => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $coverPath = $buku->cover;
        if ($request->hasFile('cover')) {
            // Hapus berikut variannya, jangan hanya file aslinya.
            app(CoverImageService::class)->delete($buku->cover);
            $coverPath = $this->compressAndStoreCover($request->file('cover'));
        }

        $qtyDiff = $request->total_quantity - $buku->total_quantity;
        $newAvailable = max(0, $buku->available_quantity + $qtyDiff);

        $buku->update([
            'isbn'              => $request->isbn,
            'judul'             => $request->judul,
            'tahun_terbit'      => $request->tahun_terbit,
            'total_quantity'    => $request->total_quantity,
            'available_quantity'=> $newAvailable,
            'penulis_id'        => $request->penulis_id,
            'penerbit_id'       => $request->penerbit_id,
            'kategori_id'       => $request->kategori_id,
            'kelas_id'          => $request->kelas_id,
            'rak_id'            => $request->rak_id,
            'rak_laci_id'       => $request->rak_laci_id,
            'sinopsis'          => $request->sinopsis,
            'keterangan_posisi' => $request->keterangan_posisi,
            'cover'             => $coverPath,
        ]);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'UPDATE_BUKU',
            'deskripsi'  => "Memperbarui data buku: '{$buku->judul}'",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Data buku berhasil diperbarui.');
    }

    public function bukuDestroy($id)
    {
        $buku = Buku::findOrFail($id);

        $activeLoans = Peminjaman::where('buku_id', $buku->id)->where('status', 'dipinjam')->count();
        if ($activeLoans > 0) {
            return back()->with('error', "Buku tidak dapat dihapus karena masih memiliki {$activeLoans} transaksi peminjaman aktif.");
        }

        // Hapus berikut variannya, jangan hanya file aslinya.
        app(CoverImageService::class)->delete($buku->cover);

        $bukuTitle = $buku->judul;
        $buku->delete();

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'HAPUS_BUKU',
            'deskripsi'  => "Menghapus buku: '{$bukuTitle}'",
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'Buku berhasil dihapus dari katalog.');
    }

    public function bukuExportExcel(Request $request)
    {
        $query = Buku::with(['penulis', 'penerbit', 'kategori', 'rak', 'laci']);

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }
        if ($request->filled('rak_id')) {
            $query->where('rak_id', $request->rak_id);
        }

        $bukuItems = $query->orderBy('judul', 'asc')->get();
        $pengaturan = Pengaturan::all()->pluck('value', 'key');

        $totalJudul = $bukuItems->count();
        $totalEksemplar = (int) $bukuItems->sum('total_quantity');
        $totalTersedia = (int) $bukuItems->sum('available_quantity');
        $totalDipinjam = $totalEksemplar - $totalTersedia;

        $namaSekolah = $pengaturan['nama_sekolah'] ?? 'SMK PGRI PEKANBARU';
        $namaPerpus = $pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI Pekanbaru';
        $alamat = $pengaturan['alamat'] ?? 'Jl. Bangau No. 16 Sukajadi, Pekanbaru, Riau';
        $npsn = $pengaturan['npsn'] ?? '10404457';
        $kepalaPerpus = $pengaturan['kepala_perpustakaan'] ?? 'Dra. Hj. Perpustakaan';
        $nipKepala = $pengaturan['nip_kepala_perpustakaan'] ?? '-';
        $tanggalCetak = date('d/m/Y H:i');
        $namaPetugas = auth()->user()->name ?? 'Petugas Perpustakaan';

        $filename = 'Laporan_Data_Buku_' . preg_replace('/[^A-Za-z0-9]/', '_', $namaSekolah) . '_' . date('Ymd_His') . '.xls';

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'EXPORT_BUKU_EXCEL',
            'deskripsi'  => "Mengekspor {$totalJudul} data buku ke format Excel",
            'ip_address' => $request->ip(),
        ]);

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Data Buku Perpustakaan</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        body { font-family: "Calibri", "Segoe UI", Arial, sans-serif; font-size: 11pt; color: #1e293b; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .title-main { font-size: 16pt; font-weight: bold; color: #881337; }
        .title-sub { font-size: 11pt; color: #475569; }
        .stat-label { background-color: #f1f5f9; font-weight: bold; }
        .stat-value { font-weight: bold; color: #0f172a; }
        .table-data { border-collapse: collapse; width: 100%; }
        .table-data th { background-color: #881337; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #6b0c2a; padding: 8px 6px; font-size: 10pt; }
        .table-data td { border: 1px solid #cbd5e1; padding: 6px; font-size: 10pt; vertical-align: top; }
        .row-even { background-color: #f8fafc; }
        .badge-tersedia { background-color: #ecfdf5; color: #065f46; font-weight: bold; }
        .badge-habis { background-color: #fff1f2; color: #9f1239; font-weight: bold; }
        .mso-text { mso-number-format:"\@"; }
        .mso-num { mso-number-format:"\#\,\#\#0"; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="12" class="text-center title-main">' . strtoupper(e($namaPerpus)) . '</td>
        </tr>
        <tr>
            <td colspan="12" class="text-center title-sub">' . e($namaSekolah) . ' | NPSN: ' . e($npsn) . ' | ' . e($alamat) . '</td>
        </tr>
        <tr>
            <td colspan="12" class="text-center" style="font-size: 13pt; font-weight: bold; color: #0f172a; padding-top: 8px; padding-bottom: 12px;">
                LAPORAN INVENTARIS DATA BUKU &amp; KOLEKSI PERPUSTAKAAN
            </td>
        </tr>
        <tr><td colspan="12"></td></tr>
        <tr>
            <td colspan="2" class="stat-label">Tanggal Cetak</td>
            <td colspan="3" class="stat-value">' . $tanggalCetak . ' WIB</td>
            <td colspan="2" class="stat-label">Total Judul Buku</td>
            <td colspan="5" class="stat-value mso-num">' . number_format($totalJudul, 0, ',', '.') . ' Judul</td>
        </tr>
        <tr>
            <td colspan="2" class="stat-label">Petugas Pencetak</td>
            <td colspan="3" class="stat-value">' . e($namaPetugas) . '</td>
            <td colspan="2" class="stat-label">Total Eksemplar Fisik</td>
            <td colspan="5" class="stat-value mso-num">' . number_format($totalEksemplar, 0, ',', '.') . ' Eksemplar</td>
        </tr>
        <tr>
            <td colspan="2" class="stat-label">Status Koleksi</td>
            <td colspan="3" class="stat-value">Resmi Terverifikasi Sistem</td>
            <td colspan="2" class="stat-label">Tersedia / Dipinjam</td>
            <td colspan="5" class="stat-value">' . number_format($totalTersedia, 0, ',', '.') . ' Tersedia / ' . number_format($totalDipinjam, 0, ',', '.') . ' Dipinjam</td>
        </tr>
        <tr><td colspan="12"></td></tr>
    </table>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 250px;">Judul Buku</th>
                <th style="width: 120px;">ISBN</th>
                <th style="width: 150px;">Penulis / Pengarang</th>
                <th style="width: 140px;">Penerbit</th>
                <th style="width: 60px;">Tahun</th>
                <th style="width: 120px;">Kategori</th>
                <th style="width: 120px;">Lokasi Rak</th>
                <th style="width: 100px;">Laci Rak</th>
                <th style="width: 70px;">Total</th>
                <th style="width: 70px;">Tersedia</th>
                <th style="width: 90px;">Status</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($bukuItems as $idx => $buku) {
            $rowClass = ($idx % 2 === 1) ? ' class="row-even"' : '';
            $tersedia = (int) $buku->available_quantity;
            $total = (int) $buku->total_quantity;
            $statusText = ($tersedia > 0) ? 'Tersedia' : 'Habis Dipinjam';
            $statusClass = ($tersedia > 0) ? 'badge-tersedia' : 'badge-habis';
            $rakName = $buku->rak ? ($buku->rak->kode_rak . ' - ' . $buku->rak->nama_rak) : 'Tanpa Rak';
            $laciName = $buku->laci ? $buku->laci->nama_laci : ($buku->rak ? 'Laci 1' : '-');

            $html .= '<tr' . $rowClass . '>
                <td class="text-center">' . ($idx + 1) . '</td>
                <td class="text-left font-bold">' . e($buku->judul) . '</td>
                <td class="text-center mso-text">' . e($buku->isbn ?? '-') . '</td>
                <td class="text-left">' . e($buku->penulis->nama ?? '-') . '</td>
                <td class="text-left">' . e($buku->penerbit->nama ?? '-') . '</td>
                <td class="text-center mso-text">' . e($buku->tahun_terbit ?? '-') . '</td>
                <td class="text-left">' . e($buku->kategori->nama ?? 'Umum') . '</td>
                <td class="text-left">' . e($rakName) . '</td>
                <td class="text-left">' . e($laciName) . '</td>
                <td class="text-center font-bold mso-num">' . $total . '</td>
                <td class="text-center font-bold mso-num" style="color: ' . ($tersedia > 0 ? '#059669' : '#dc2626') . ';">' . $tersedia . '</td>
                <td class="text-center ' . $statusClass . '">' . $statusText . '</td>
            </tr>';
        }

        $html .= '</tbody>
    </table>

    <table>
        <tr><td colspan="12"></td></tr>
        <tr><td colspan="12"></td></tr>
        <tr>
            <td colspan="7"></td>
            <td colspan="5" class="text-center" style="font-size: 10pt;">
                Pekanbaru, ' . date('d F Y') . '<br/>
                Mengetahui,<br/>
                <strong>Kepala Perpustakaan</strong><br/><br/><br/><br/>
                <strong><u>' . e($kepalaPerpus) . '</u></strong><br/>
                NIP. ' . e($nipKepala) . '
            </td>
        </tr>
    </table>
</body>
</html>';

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    public function bukuExportPdf(Request $request)
    {
        $query = Buku::with(['penulis', 'penerbit', 'kategori', 'rak', 'laci']);

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }
        if ($request->filled('rak_id')) {
            $query->where('rak_id', $request->rak_id);
        }

        $bukuItems = $query->orderBy('judul', 'asc')->get();
        $pengaturan = Pengaturan::all()->pluck('value', 'key');

        $totalJudul = $bukuItems->count();
        $totalEksemplar = (int) $bukuItems->sum('total_quantity');
        $totalTersedia = (int) $bukuItems->sum('available_quantity');
        $totalDipinjam = $totalEksemplar - $totalTersedia;

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'EXPORT_BUKU_PDF',
            'deskripsi'  => "Membuka dan mencetak laporan inventaris PDF ({$totalJudul} buku)",
            'ip_address' => $request->ip(),
        ]);

        return view('admin.buku.export-pdf', compact(
            'bukuItems',
            'pengaturan',
            'totalJudul',
            'totalEksemplar',
            'totalTersedia',
            'totalDipinjam'
        ));
    }

    public function kategoriIndex(Request $request)
    {
        $query = Kategori::withCount('buku');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('nama', 'like', "%{$search}%");
        }

        $kategoriList = $query->latest()->paginate(10)->withQueryString();
        return view('admin.kategori.index', compact('kategoriList'));
    }

    public function kategoriStore(Request $request)
    {
        $request->validate([
            'nama'      => 'required|unique:kategori,nama|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        Kategori::create([
            'nama'      => $request->nama,
            'slug'      => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
        ]);

        return back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function kategoriUpdate(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);
        $request->validate([
            'nama'      => 'required|max:255|unique:kategori,nama,' . $id,
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update([
            'nama'      => $request->nama,
            'slug'      => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
        ]);

        return back()->with('success', 'Data kategori berhasil diperbarui.');
    }

    public function kategoriDestroy($id)
    {
        $kategori = Kategori::withCount('buku')->findOrFail($id);
        if ($kategori->buku_count > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh ' . $kategori->buku_count . ' buku.');
        }
        $kategori->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    public function penulisIndex()
    {
        $penulisList = Penulis::withCount('buku')->latest()->paginate(10);
        return view('admin.penulis.index', compact('penulisList'));
    }

    public function penulisStore(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'biografi' => 'nullable|string',
        ]);

        Penulis::create($request->only('nama', 'biografi'));
        return back()->with('success', 'Penulis baru berhasil ditambahkan.');
    }

    public function penulisUpdate(Request $request, $id)
    {
        $penulis = Penulis::findOrFail($id);
        $request->validate([
            'nama'     => 'required|string|max:255',
            'biografi' => 'nullable|string',
        ]);

        $penulis->update($request->only('nama', 'biografi'));
        return back()->with('success', 'Data penulis berhasil diperbarui.');
    }

    public function penulisDestroy($id)
    {
        $penulis = Penulis::withCount('buku')->findOrFail($id);
        if ($penulis->buku_count > 0) {
            return back()->with('error', 'Penulis tidak dapat dihapus karena masih terkait dengan ' . $penulis->buku_count . ' buku.');
        }
        $penulis->delete();
        return back()->with('success', 'Penulis berhasil dihapus.');
    }

    public function penerbitIndex(Request $request)
    {
        $query = Penerbit::withCount('buku');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kota', 'like', "%{$search}%");
            });
        }

        $penerbitList = $query->latest()->paginate(10)->withQueryString();
        return view('admin.penerbit.index', compact('penerbitList'));
    }

    public function penerbitStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kota' => 'nullable|string|max:255',
        ]);

        Penerbit::create($request->only('nama', 'kota'));
        return back()->with('success', 'Penerbit baru berhasil ditambahkan.');
    }

    public function penerbitUpdate(Request $request, $id)
    {
        $penerbit = Penerbit::findOrFail($id);
        $request->validate([
            'nama' => 'required|string|max:255',
            'kota' => 'nullable|string|max:255',
        ]);

        $penerbit->update($request->only('nama', 'kota'));
        return back()->with('success', 'Data penerbit berhasil diperbarui.');
    }

    public function penerbitDestroy($id)
    {
        $penerbit = Penerbit::withCount('buku')->findOrFail($id);
        if ($penerbit->buku_count > 0) {
            return back()->with('error', 'Penerbit tidak dapat dihapus karena masih terkait dengan ' . $penerbit->buku_count . ' buku.');
        }
        $penerbit->delete();
        return back()->with('success', 'Penerbit berhasil dihapus.');
    }

    public function kelasIndex(Request $request)
    {
        $query = Kelas::withCount('buku');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('nama_kelas', 'like', "%{$search}%");
        }

        $kelasList = $query->orderBy('nama_kelas', 'asc')->paginate(10)->withQueryString();
        return view('admin.kelas.index', compact('kelasList'));
    }

    public function kelasStore(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:255',
        ]);

        Kelas::create($request->only('nama_kelas', 'keterangan'));
        return back()->with('success', 'Kelas baru berhasil ditambahkan.');
    }

    public function kelasUpdate(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $kelas->update($request->only('nama_kelas', 'keterangan'));
        return back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function kelasDestroy($id)
    {
        $kelas = Kelas::withCount('buku')->findOrFail($id);
        if ($kelas->buku_count > 0) {
            return back()->with('error', 'Kelas tidak dapat dihapus karena masih terkait dengan ' . $kelas->buku_count . ' buku.');
        }
        $kelas->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    public function rakIndex(Request $request)
    {
        $query = Rak::with([
            'kategori',
            'buku.kategori',
            'laci.buku.kategori',
            'laci.buku.penulis'
        ])->withCount(['buku', 'laci']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('nama_rak', 'like', "%{$search}%")
                  ->orWhere('kode_rak', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhereHas('kategori', function($qk) use ($search) {
                      $qk->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('buku', function($qb) use ($search) {
                      $qb->where('judul', 'like', "%{$search}%")
                         ->orWhere('isbn', 'like', "%{$search}%")
                         ->orWhereHas('kategori', function($qbk) use ($search) {
                             $qbk->where('nama', 'like', "%{$search}%");
                         });
                  })
                  ->orWhereHas('laci', function($ql) use ($search) {
                      $ql->where('nama_laci', 'like', "%{$search}%")
                         ->orWhere('keterangan', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'berisi') {
                $query->has('buku');
            } elseif ($request->status === 'kosong') {
                $query->doesntHave('buku');
            }
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi', $request->lokasi);
        }

        $rakList = $query->orderBy('kode_rak', 'asc')->paginate(12)->withQueryString();
        $kategoriList = Kategori::orderBy('nama', 'asc')->get();

        $statsSummary = [
            'total_rak'       => Rak::count(),
            'total_laci'      => RakLaci::count(),
            'total_judul'     => Buku::whereNotNull('rak_id')->count(),
            'total_eksemplar' => (int) Buku::whereNotNull('rak_id')->sum('total_quantity'),
        ];

        $lokasiList = Rak::whereNotNull('lokasi')->where('lokasi', '!=', '')->distinct()->pluck('lokasi');

        return view('admin.rak.index', compact('rakList', 'kategoriList', 'statsSummary', 'lokasiList'));
    }

    public function rakStore(Request $request)
    {
        $request->validate([
            'kode_rak'    => 'required|unique:rak,kode_rak|max:50',
            'nama_rak'    => 'required|string|max:255',
            'lokasi'      => 'nullable|string|max:255',
            'kategori_id' => 'nullable|exists:kategori,id',
            'jumlah_laci' => 'nullable|integer|min:1|max:20',
        ]);

        $rak = Rak::create([
            'kode_rak'    => strtoupper(trim($request->kode_rak)),
            'nama_rak'    => trim($request->nama_rak),
            'lokasi'      => $request->filled('lokasi') ? trim($request->lokasi) : null,
            'kategori_id' => $request->kategori_id,
        ]);

        $jumlahLaci = (int) $request->input('jumlah_laci', 3);
        for ($i = 1; $i <= $jumlahLaci; $i++) {
            RakLaci::create([
                'rak_id'     => $rak->id,
                'nomor_laci' => $i,
                'nama_laci'  => 'Laci ' . $i,
                'keterangan' => 'Tingkat ' . $i . ' pada ' . $rak->nama_rak,
            ]);
        }

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'TAMBAH_RAK',
            'deskripsi'  => "Menambahkan rak '{$rak->nama_rak}' ({$rak->kode_rak}) dengan {$jumlahLaci} laci awal",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Rak '{$rak->nama_rak}' ({$rak->kode_rak}) beserta {$jumlahLaci} laci berhasil ditambahkan.");
    }

    public function rakUpdate(Request $request, $id)
    {
        $rak = Rak::findOrFail($id);

        $request->validate([
            'kode_rak'    => 'required|max:50|unique:rak,kode_rak,' . $id,
            'nama_rak'    => 'required|string|max:255',
            'lokasi'      => 'nullable|string|max:255',
            'kategori_id' => 'nullable|exists:kategori,id',
        ]);

        $rak->update([
            'kode_rak'    => strtoupper(trim($request->kode_rak)),
            'nama_rak'    => trim($request->nama_rak),
            'lokasi'      => $request->filled('lokasi') ? trim($request->lokasi) : null,
            'kategori_id' => $request->kategori_id,
        ]);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'UPDATE_RAK',
            'deskripsi'  => "Memperbarui rak '{$rak->nama_rak}' ({$rak->kode_rak})",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Data rak '{$rak->nama_rak}' berhasil diperbarui.");
    }

    public function rakDestroy($id)
    {
        $rak = Rak::withCount('buku')->findOrFail($id);

        if ($rak->buku_count > 0) {
            return back()->with('error', "Rak '{$rak->nama_rak}' tidak dapat dihapus karena masih menampung {$rak->buku_count} judul buku. Pindahkan atau ubah lokasi rak buku terlebih dahulu.");
        }

        RakLaci::where('rak_id', $rak->id)->delete();
        $rakName = $rak->nama_rak;
        $rakCode = $rak->kode_rak;
        $rak->delete();

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'HAPUS_RAK',
            'deskripsi'  => "Menghapus rak '{$rakName}' ({$rakCode})",
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Rak '{$rakName}' ({$rakCode}) dan seluruh lacinya berhasil dihapus.");
    }

    public function laciStore(Request $request, $rakId)
    {
        $rak = Rak::findOrFail($rakId);

        $request->validate([
            'nama_laci'   => 'required|string|max:100',
            'nomor_laci'  => 'nullable|integer|min:1',
            'keterangan'  => 'nullable|string|max:255',
        ]);

        $nomorLaci = $request->nomor_laci ?? (RakLaci::where('rak_id', $rakId)->max('nomor_laci') + 1);

        $laci = RakLaci::create([
            'rak_id'     => $rak->id,
            'nomor_laci' => $nomorLaci,
            'nama_laci'  => trim($request->nama_laci),
            'keterangan' => $request->filled('keterangan') ? trim($request->keterangan) : null,
        ]);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'TAMBAH_LACI',
            'deskripsi'  => "Menambahkan laci '{$laci->nama_laci}' pada rak '{$rak->nama_rak}'",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Laci '{$laci->nama_laci}' berhasil ditambahkan ke rak {$rak->nama_rak}.");
    }

    public function laciUpdate(Request $request, $id)
    {
        $laci = RakLaci::with('rak')->findOrFail($id);

        $request->validate([
            'nama_laci'  => 'required|string|max:100',
            'nomor_laci' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $laci->update([
            'nama_laci'  => trim($request->nama_laci),
            'nomor_laci' => (int) $request->nomor_laci,
            'keterangan' => $request->filled('keterangan') ? trim($request->keterangan) : null,
        ]);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'UPDATE_LACI',
            'deskripsi'  => "Memperbarui laci '{$laci->nama_laci}' pada rak '{$laci->rak->nama_rak}'",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Data laci '{$laci->nama_laci}' berhasil diperbarui.");
    }

    public function laciDestroy($id)
    {
        $laci = RakLaci::with('rak')->withCount('buku')->findOrFail($id);

        if ($laci->buku_count > 0) {
            Buku::where('rak_laci_id', $laci->id)->update(['rak_laci_id' => null]);
        }

        $laciName = $laci->nama_laci;
        $rakName = $laci->rak->nama_rak ?? 'Rak';
        $laci->delete();

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'HAPUS_LACI',
            'deskripsi'  => "Menghapus laci '{$laciName}' dari '{$rakName}'",
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Laci '{$laciName}' berhasil dihapus.");
    }

    public function getLacisByRak($rakId)
    {
        $lacis = RakLaci::with('buku.kategori')->where('rak_id', $rakId)->orderBy('nomor_laci')->get()->map(function($laci) {
            $categories = $laci->buku->pluck('kategori.nama')->filter()->unique()->values()->all();
            return [
                'id'          => $laci->id,
                'nomor_laci'  => $laci->nomor_laci,
                'nama_laci'   => $laci->nama_laci,
                'keterangan'  => $laci->keterangan,
                'buku_count'  => $laci->buku->count(),
                'categories'  => $categories,
            ];
        });

        return response()->json($lacis);
    }

    public function peminjamanIndex(Request $request)
    {
        $query = Peminjaman::with(['user', 'buku', 'petugas'])->where('status', 'dipinjam');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('kode_peminjaman', 'like', "%{$search}%")
                  ->orWhere('nama_peminjam', 'like', "%{$search}%")
                  ->orWhere('jurusan', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%")
                  ->orWhereHas('buku', function($qb) use ($search) {
                      $qb->where('judul', 'like', "%{$search}%");
                  });
            });
        }

        $activeLoans = $query->latest()->paginate(10)->withQueryString();
        $peminjamanList = $activeLoans;
        $bukuList = Buku::where('status', 'tersedia')->where('available_quantity', '>', 0)->orderBy('judul', 'asc')->get();
        $booksList = $bukuList;

        return view('admin.peminjaman.index', compact('activeLoans', 'peminjamanList', 'bukuList', 'booksList'));
    }

    public function peminjamanStore(Request $request)
    {
        $request->validate([
            'nama_peminjam' => 'required|string|max:255',
            'jurusan'       => 'required|string|max:150',
            'nomor_induk'   => 'nullable|string|max:50',
            'buku_id'       => 'required|exists:buku,id',
            'jumlah'        => 'required|integer|min:1|max:10',
        ]);

        $buku = Buku::findOrFail($request->buku_id);
        $jumlahPinjam = (int) $request->jumlah;

        if ($buku->available_quantity < $jumlahPinjam) {
            return back()->with('error', "Stok buku tidak mencukupi. Sisa stok tersedia saat ini: {$buku->available_quantity} buku.");
        }

        $kodePinjam = 'PJ-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $today = Carbon::today()->toDateString();

        try {
            $loan = DB::transaction(function () use ($buku, $request, $jumlahPinjam, $kodePinjam, $today) {
                $lockedBook = Buku::where('id', $buku->id)->lockForUpdate()->first();
                if ($lockedBook->available_quantity < $jumlahPinjam) {
                    throw new \Exception('STOCK_INSUFFICIENT');
                }

                $lockedBook->available_quantity -= $jumlahPinjam;
                $lockedBook->save();

                return Peminjaman::create([
                    'kode_peminjaman'     => $kodePinjam,
                    'nama_peminjam'       => trim($request->nama_peminjam),
                    'jurusan'             => trim($request->jurusan),
                    'nomor_induk'         => $request->filled('nomor_induk') ? trim($request->nomor_induk) : null,
                    'user_id'             => auth()->id(),
                    'buku_id'             => $buku->id,
                    'jumlah'              => $jumlahPinjam,
                    'tanggal_pinjam'      => $today,
                    'tanggal_jatuh_tempo' => $today,
                    'status'              => 'dipinjam',
                    'petugas_id'          => auth()->id(),
                ]);
            });
        } catch (\Exception $e) {
            if ($e->getMessage() === 'STOCK_INSUFFICIENT') {
                return back()->with('error', 'Stok buku tidak mencukupi untuk jumlah peminjaman yang diminta.');
            }
            return back()->with('error', 'Terjadi kesalahan sistem saat mencatat peminjaman.');
        }

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'TRANSAKSI_PINJAM',
            'deskripsi'  => "Mencatat peminjaman {$loan->kode_peminjaman} untuk {$loan->nama_peminjam} ({$loan->jumlah} buku)",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Peminjaman berhasil dicatat untuk {$loan->nama_peminjam}! Kode: {$loan->kode_peminjaman}");
    }

    public function peminjamanKembali(Request $request, $id)
    {
        try {
            $loan = DB::transaction(function () use ($id) {
                $lockedLoan = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

                if ($lockedLoan->status === 'dikembalikan') {
                    throw new \Exception('ALREADY_RETURNED');
                }

                $buku = Buku::where('id', $lockedLoan->buku_id)->lockForUpdate()->first();
                if ($buku) {
                    $buku->available_quantity = min($buku->total_quantity, $buku->available_quantity + $lockedLoan->jumlah);
                    $buku->save();
                }

                $lockedLoan->status = 'dikembalikan';
                $lockedLoan->waktu_kembali = Carbon::now();
                $lockedLoan->save();

                return $lockedLoan;
            });
        } catch (\Exception $e) {
            if ($e->getMessage() === 'ALREADY_RETURNED') {
                return back()->with('error', 'Transaksi peminjaman ini sudah berstatus dikembalikan sebelumnya.');
            }
            return back()->with('error', 'Terjadi kesalahan saat memproses pengembalian buku.');
        }

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'TRANSAKSI_KEMBALI',
            'deskripsi'  => "Buku transaksi {$loan->kode_peminjaman} telah berhasil dikembalikan.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Pengembalian buku berhasil diproses.");
    }

    public function peminjamanExportExcel(Request $request)
    {
        $query = Peminjaman::with(['user', 'buku.rak', 'buku.laci', 'petugas']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_pinjam', $request->tanggal);
        }
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('kode_peminjaman', 'like', "%{$search}%")
                  ->orWhere('nama_peminjam', 'like', "%{$search}%")
                  ->orWhere('jurusan', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%")
                  ->orWhereHas('buku', function($qb) use ($search) {
                      $qb->where('judul', 'like', "%{$search}%");
                  });
            });
        }

        $loanItems = $query->latest('tanggal_pinjam')->get();
        $pengaturan = Pengaturan::all()->pluck('value', 'key');

        $totalTransaksi = $loanItems->count();
        $totalDipinjam = $loanItems->where('status', 'dipinjam')->count();
        $totalKembali = $loanItems->where('status', 'dikembalikan')->count();
        $totalBukuPinjam = (int) $loanItems->sum('jumlah');

        $namaSekolah = $pengaturan['nama_sekolah'] ?? 'SMK PGRI PEKANBARU';
        $namaPerpus = $pengaturan['nama_perpustakaan'] ?? 'Perpustakaan SMK PGRI Pekanbaru';
        $alamat = $pengaturan['alamat'] ?? 'Jl. Bangau No. 16 Sukajadi, Pekanbaru, Riau';
        $npsn = $pengaturan['npsn'] ?? '10404457';
        $kepalaPerpus = $pengaturan['kepala_perpustakaan'] ?? 'Dra. Hj. Perpustakaan';
        $nipKepala = $pengaturan['nip_kepala_perpustakaan'] ?? '-';
        $tanggalCetak = date('d/m/Y H:i');
        $namaPetugas = auth()->user()->name ?? 'Petugas Perpustakaan';

        $filename = 'Laporan_Sirkulasi_Peminjaman_' . preg_replace('/[^A-Za-z0-9]/', '_', $namaSekolah) . '_' . date('Ymd_His') . '.xls';

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'EXPORT_PINJAM_EXCEL',
            'deskripsi'  => "Mengekspor {$totalTransaksi} data sirkulasi peminjaman ke format Excel",
            'ip_address' => $request->ip(),
        ]);

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Sirkulasi Peminjaman</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        body { font-family: "Calibri", "Segoe UI", Arial, sans-serif; font-size: 10pt; color: #1e293b; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .title-main { font-size: 15pt; font-weight: bold; color: #881337; }
        .title-sub { font-size: 10pt; color: #475569; }
        .stat-label { background-color: #f1f5f9; font-weight: bold; }
        .stat-value { font-weight: bold; color: #0f172a; }
        .table-data { border-collapse: collapse; width: 100%; }
        .table-data th { background-color: #881337; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #6b0c2a; padding: 7px 5px; font-size: 9pt; }
        .table-data td { border: 1px solid #cbd5e1; padding: 5px; font-size: 9pt; vertical-align: middle; }
        .row-even { background-color: #f8fafc; }
        .badge-kembali { background-color: #ecfdf5; color: #065f46; font-weight: bold; }
        .badge-dipinjam { background-color: #fffbeb; color: #92400e; font-weight: bold; }
        .mso-text { mso-number-format:"\@"; }
        .mso-num { mso-number-format:"\#\,\#\#0"; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="10" class="text-center title-main">' . strtoupper(e($namaPerpus)) . '</td>
        </tr>
        <tr>
            <td colspan="10" class="text-center title-sub">' . e($namaSekolah) . ' | NPSN: ' . e($npsn) . ' | ' . e($alamat) . '</td>
        </tr>
        <tr>
            <td colspan="10" class="text-center" style="font-size: 12pt; font-weight: bold; color: #0f172a; padding-top: 6px; padding-bottom: 10px;">
                LAPORAN REKAPITULASI SIRKULASI PEMINJAMAN &amp; PENGEMBALIAN BUKU
            </td>
        </tr>
        <tr><td colspan="10"></td></tr>
        <tr>
            <td colspan="2" class="stat-label">Tanggal Cetak</td>
            <td colspan="3" class="stat-value">' . $tanggalCetak . ' WIB</td>
            <td colspan="2" class="stat-label">Total Transaksi</td>
            <td colspan="3" class="stat-value mso-num">' . number_format($totalTransaksi, 0, ',', '.') . ' Transaksi</td>
        </tr>
        <tr>
            <td colspan="2" class="stat-label">Petugas Pencetak</td>
            <td colspan="3" class="stat-value">' . e($namaPetugas) . '</td>
            <td colspan="2" class="stat-label">Sedang Dipinjam</td>
            <td colspan="3" class="stat-value mso-num">' . number_format($totalDipinjam, 0, ',', '.') . ' Transaksi (' . $totalBukuPinjam . ' Buku)</td>
        </tr>
        <tr>
            <td colspan="2" class="stat-label">Status Arsip</td>
            <td colspan="3" class="stat-value">Resmi Terverifikasi Sistem</td>
            <td colspan="2" class="stat-label">Sudah Dikembalikan</td>
            <td colspan="3" class="stat-value mso-num">' . number_format($totalKembali, 0, ',', '.') . ' Transaksi</td>
        </tr>
        <tr><td colspan="10"></td></tr>
    </table>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 100px;">Kode Pinjam</th>
                <th style="width: 150px;">Nama Peminjam</th>
                <th style="width: 110px;">Jurusan / NISN</th>
                <th style="width: 220px;">Judul Buku</th>
                <th style="width: 45px;">Jml</th>
                <th style="width: 80px;">Tgl Pinjam</th>
                <th style="width: 90px;">Tgl Kembali</th>
                <th style="width: 80px;">Status</th>
                <th style="width: 100px;">Petugas</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($loanItems as $idx => $loan) {
            $rowClass = ($idx % 2 === 1) ? ' class="row-even"' : '';
            $isReturned = ($loan->status === 'dikembalikan');
            $statusText = $isReturned ? 'Dikembalikan' : 'Sedang Dipinjam';
            $statusClass = $isReturned ? 'badge-kembali' : 'badge-dipinjam';
            $tglKembaliText = $loan->waktu_kembali ? \Carbon\Carbon::parse($loan->waktu_kembali)->format('d/m/Y H:i') : '-';

            $html .= '<tr' . $rowClass . '>
                <td class="text-center">' . ($idx + 1) . '</td>
                <td class="text-center font-bold mso-text">' . e($loan->kode_peminjaman) . '</td>
                <td class="text-left font-bold">' . e($loan->nama_peminjam) . '</td>
                <td class="text-left">' . e($loan->jurusan) . ($loan->nomor_induk ? ' (' . e($loan->nomor_induk) . ')' : '') . '</td>
                <td class="text-left font-bold">' . e($loan->buku->judul ?? '-') . '</td>
                <td class="text-center font-bold mso-num">' . (int)$loan->jumlah . '</td>
                <td class="text-center mso-text">' . \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d/m/Y') . '</td>
                <td class="text-center mso-text">' . $tglKembaliText . '</td>
                <td class="text-center ' . $statusClass . '">' . $statusText . '</td>
                <td class="text-left">' . e($loan->petugas->name ?? '-') . '</td>
            </tr>';
        }

        $html .= '</tbody>
    </table>

    <table>
        <tr><td colspan="10"></td></tr>
        <tr><td colspan="10"></td></tr>
        <tr>
            <td colspan="6"></td>
            <td colspan="4" class="text-center" style="font-size: 9.5pt;">
                Pekanbaru, ' . date('d F Y') . '<br/>
                Mengetahui,<br/>
                <strong>Kepala Perpustakaan</strong><br/><br/><br/><br/>
                <strong><u>' . e($kepalaPerpus) . '</u></strong><br/>
                NIP. ' . e($nipKepala) . '
            </td>
        </tr>
    </table>
</body>
</html>';

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    public function peminjamanExportPdf(Request $request)
    {
        $query = Peminjaman::with(['user', 'buku.rak', 'buku.laci', 'petugas']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_pinjam', $request->tanggal);
        }
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('kode_peminjaman', 'like', "%{$search}%")
                  ->orWhere('nama_peminjam', 'like', "%{$search}%")
                  ->orWhere('jurusan', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%")
                  ->orWhereHas('buku', function($qb) use ($search) {
                      $qb->where('judul', 'like', "%{$search}%");
                  });
            });
        }

        $loanItems = $query->latest('tanggal_pinjam')->get();
        $pengaturan = Pengaturan::all()->pluck('value', 'key');

        $totalTransaksi = $loanItems->count();
        $totalDipinjam = $loanItems->where('status', 'dipinjam')->count();
        $totalKembali = $loanItems->where('status', 'dikembalikan')->count();
        $totalBukuPinjam = (int) $loanItems->sum('jumlah');

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'EXPORT_PINJAM_PDF',
            'deskripsi'  => "Membuka dan mencetak laporan sirkulasi peminjaman PDF ({$totalTransaksi} transaksi)",
            'ip_address' => $request->ip(),
        ]);

        return view('admin.peminjaman.export-pdf', compact(
            'loanItems',
            'pengaturan',
            'totalTransaksi',
            'totalDipinjam',
            'totalKembali',
            'totalBukuPinjam'
        ));
    }

    public function riwayatIndex(Request $request)
    {
        $query = Peminjaman::with(['user', 'buku', 'petugas']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_pinjam', $request->tanggal);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('kode_peminjaman', 'like', "%{$search}%")
                  ->orWhere('nama_peminjam', 'like', "%{$search}%")
                  ->orWhere('jurusan', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('buku', function($qb) use ($search) {
                      $qb->where('judul', 'like', "%{$search}%");
                  });
            });
        }

        $riwayatList = $query->latest('tanggal_pinjam')->paginate(15)->withQueryString();

        return view('admin.peminjaman.riwayat', compact('riwayatList'));
    }

    public function peminjamanRequestIndex(Request $request)
    {
        $status = strtolower(trim($request->get('status', 'pending')));
        $validStatuses = ['pending', 'dipinjam', 'ditolak', 'all'];
        if (!in_array($status, $validStatuses)) {
            $status = 'pending';
        }

        $query = Peminjaman::with(['user', 'buku.rak', 'buku.laci', 'petugas']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $search = substr(trim($request->search), 0, 100);
            $query->where(function($q) use ($search) {
                $q->where('kode_peminjaman', 'like', "%{$search}%")
                  ->orWhere('nama_peminjam', 'like', "%{$search}%")
                  ->orWhere('jurusan', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%")
                  ->orWhere('no_wa', 'like', "%{$search}%")
                  ->orWhereHas('buku', function($qb) use ($search) {
                      $qb->where('judul', 'like', "%{$search}%");
                  });
            });
        }

        $requestList = $query->latest('created_at')->paginate(15)->withQueryString();

        $counts = [
            'pending'  => Peminjaman::where('status', 'pending')->count(),
            'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'ditolak'  => Peminjaman::where('status', 'ditolak')->count(),
            'all'      => Peminjaman::count(),
        ];

        return view('admin.peminjaman.request', compact('requestList', 'counts', 'status'));
    }

    public function peminjamanRequestApprove(Request $request, $id)
    {
        try {
            $loan = DB::transaction(function () use ($id) {
                $lockedLoan = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

                if ($lockedLoan->status !== 'pending') {
                    throw new \Exception('NOT_PENDING');
                }

                $book = Buku::where('id', $lockedLoan->buku_id)->lockForUpdate()->firstOrFail();
                $qty = max(1, (int) $lockedLoan->jumlah);

                if ($book->available_quantity < $qty) {
                    throw new \Exception('STOCK_INSUFFICIENT');
                }

                $book->available_quantity -= $qty;
                $book->save();

                $today = Carbon::today()->toDateString();
                $lockedLoan->status = 'dipinjam';
                $lockedLoan->kode_peminjaman = 'PJ-' . date('Ymd') . '-' . strtoupper(Str::random(4));
                $lockedLoan->tanggal_pinjam = $today;
                $lockedLoan->tanggal_jatuh_tempo = Carbon::today()->addDays(7)->toDateString();
                $lockedLoan->petugas_id = auth()->id();
                $lockedLoan->save();

                return $lockedLoan;
            });
        } catch (\Exception $e) {
            if ($e->getMessage() === 'NOT_PENDING') {
                return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
            }
            if ($e->getMessage() === 'STOCK_INSUFFICIENT') {
                return back()->with('error', 'Stok fisik buku tidak mencukupi untuk menyetujui peminjaman.');
            }
            return back()->with('error', 'Terjadi kesalahan sistem saat menyetujui pengajuan.');
        }

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'APPROVE_REQUEST_PINJAM',
            'deskripsi'  => "Menyetujui pengajuan peminjaman untuk {$loan->nama_peminjam} (Kode: {$loan->kode_peminjaman})",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Pengajuan peminjaman untuk {$loan->nama_peminjam} berhasil disetujui! Kode: {$loan->kode_peminjaman}");
    }

    public function peminjamanRequestReject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'nullable|string|max:500',
        ]);

        $loan = Peminjaman::findOrFail($id);

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan dengan status pending yang dapat ditolak.');
        }

        $loan->update([
            'status'           => 'ditolak',
            'alasan_penolakan' => $request->filled('alasan_penolakan') ? trim($request->alasan_penolakan) : 'Permintaan tidak dapat diproses oleh petugas perpustakaan.',
            'petugas_id'       => auth()->id(),
        ]);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'REJECT_REQUEST_PINJAM',
            'deskripsi'  => "Menolak pengajuan peminjaman {$loan->kode_peminjaman} untuk {$loan->nama_peminjam}",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Pengajuan peminjaman untuk {$loan->nama_peminjam} telah ditolak.");
    }

    public function anggotaIndex(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $anggotaList = $query->latest()->paginate(10)->withQueryString();
        $roles = Role::all();
        return view('admin.anggota.index', compact('anggotaList', 'roles'));
    }

    public function anggotaStore(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Administrator yang berwenang menambahkan akun pengelola baru.');
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email',
            'password'      => 'required|string|min:6',
            'phone'         => 'nullable|string|max:20',
            'role_id'       => 'required|exists:roles,id',
            'status'        => 'required|in:active,inactive',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
            'phone'    => $request->phone,
            'status'   => $request->status,
        ]);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'TAMBAH_ADMIN',
            'deskripsi'  => "Menambahkan akun pengelola baru: '{$user->name}' ({$user->email})",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Akun pengelola/admin baru berhasil didaftarkan.');
    }

    public function anggotaUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Administrator yang berwenang mengubah data akun admin.');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone'    => 'nullable|string|max:20',
            'role_id'  => 'nullable|exists:roles,id',
            'status'   => 'nullable|in:active,inactive',
        ]);

        $userData = [
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($user->id !== 1) {
            if ($request->filled('role_id')) {
                $userData['role_id'] = $request->role_id;
            }
            if ($request->filled('status')) {
                $userData['status'] = $request->status;
            }
        }

        $user->update($userData);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'UPDATE_ADMIN',
            'deskripsi'  => "Memperbarui data akun pengelola: '{$user->name}'",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Data akun pengelola berhasil diperbarui.');
    }

    public function anggotaResetPassword(Request $request, $id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Administrator yang berwenang mereset password akun admin.');
        }

        $user = User::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'RESET_PASSWORD_ADMIN',
            'deskripsi'  => "Mereset password untuk akun admin: '{$user->name}' ({$user->email})",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Password untuk akun {$user->name} berhasil diubah.");
    }

    public function anggotaToggleStatus(Request $request, $id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Administrator yang berwenang mengubah status aktif akun admin.');
        }

        $user = User::findOrFail($id);

        if ($user->id === 1 || $user->id === auth()->id()) {
            return back()->with('error', 'Akun Super Admin Utama atau akun Anda sendiri tidak dapat dinonaktifkan.');
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan / diblokir';

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'TOGGLE_STATUS_ADMIN',
            'deskripsi'  => "Status akun admin '{$user->name}' diubah menjadi {$newStatus}",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }

    public function anggotaDestroy(Request $request, $id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Administrator yang berwenang menghapus akun pengelola.');
        }

        $user = User::findOrFail($id);

        if ($user->id === 1 || $user->id === auth()->id()) {
            return back()->with('error', 'Akun Super Admin Utama atau akun Anda sendiri tidak dapat dihapus.');
        }

        $activeLoans = Peminjaman::where('user_id', $user->id)->where('status', 'dipinjam')->count();
        if ($activeLoans > 0) {
            return back()->with('error', "Pengguna tidak dapat dihapus karena masih memiliki {$activeLoans} buku yang sedang dipinjam.");
        }

        $userName = $user->name;
        $user->delete();

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'HAPUS_ADMIN',
            'deskripsi'  => "Menghapus akun admin: '{$userName}'",
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Akun admin {$userName} berhasil dihapus.");
    }

    public function auditLogIndex()
    {
        $logs = AuditLog::latest()->paginate(20);
        return view('admin.audit-log.index', compact('logs'));
    }

    public function pengaturanIndex()
    {
        $pengaturan = Pengaturan::all()->pluck('value', 'key');
        $systemInfo = [
            'laravel_version' => app()->version(),
            'php_version'     => PHP_VERSION,
            'db_driver'       => config('database.default'),
            'app_env'         => config('app.env'),
            'total_buku'      => Buku::count(),
            'total_pinjam'    => Peminjaman::count(),
            'total_pengguna'  => User::count(),
        ];
        return view('admin.pengaturan.index', compact('pengaturan', 'systemInfo'));
    }

    public function pengaturanUpdate(Request $request)
    {
        $validated = $request->validate([
            'nama_perpustakaan'       => 'required|string|max:255',
            'nama_sekolah'            => 'nullable|string|max:255',
            'npsn'                    => 'nullable|string|max:50',
            'kepala_perpustakaan'     => 'nullable|string|max:255',
            'nip_kepala_perpustakaan' => 'nullable|string|max:50',
            'alamat'                  => 'nullable|string|max:500',
            'email_perpustakaan'      => 'nullable|string|max:255',
            'telepon'                 => 'nullable|string|max:50',
            'website_sekolah'         => 'nullable|string|max:255',
            'jam_operasional'         => 'required|string|max:255',
            'jam_operasional_jumat'   => 'nullable|string|max:255',
            'pesan_sirkulasi'         => 'nullable|string|max:500',
            'max_buku_pinjam'         => 'required|integer|min:1|max:50',
            'durasi_pinjam_hari'      => 'required|integer|min:1|max:365',
            'syarat_peminjaman'       => 'nullable|string|max:500',
            'judul_hero'              => 'nullable|string|max:255',
            'subjudul_hero'           => 'nullable|string|max:500',
            'buku_per_halaman'        => 'nullable|integer|min:4|max:100',
        ]);

        foreach ($validated as $key => $value) {
            Pengaturan::updateOrCreate(
                ['key' => $key],
                [
                    'value' => strip_tags((string) $value),
                    'label' => ucwords(str_replace('_', ' ', $key)),
                    'tipe'  => is_numeric($value) ? 'number' : 'text',
                ]
            );
        }

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'UPDATE_PENGATURAN',
            'deskripsi'  => 'Memperbarui konfigurasi sistem & identitas perpustakaan',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Pengaturan sistem perpustakaan berhasil diperbarui.');
    }
}
