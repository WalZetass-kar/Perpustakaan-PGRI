<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Rak;
use App\Models\Penulis;
use App\Models\Penerbit;
use App\Models\Anggota;
use App\Models\Peminjaman;
use App\Models\AuditLog;
use App\Models\Pengaturan;
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
            'total_anggota'          => Anggota::count(),
            'peminjaman_hari_ini'    => Peminjaman::whereDate('tanggal_pinjam', $today)->count(),
            'pengembalian_hari_ini'  => Peminjaman::where('status', 'dikembalikan')->whereDate('waktu_kembali', $today)->count(),
        ];

        $chartDates = [];
        $chartLoans = [];
        $chartReturns = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $chartDates[] = Carbon::today()->subDays($i)->format('d M');
            $chartLoans[] = Peminjaman::whereDate('tanggal_pinjam', $date)->count();
            $chartReturns[] = Peminjaman::where('status', 'dikembalikan')->whereDate('waktu_kembali', $date)->count();
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

        return view('admin.dashboard', compact('stats', 'chartDates', 'chartLoans', 'chartReturns', 'recentLoans', 'mostBorrowedBooks', 'recentAuditLogs'));
    }

    public function bukuIndex(Request $request)
    {
        $query = Buku::with(['penulis', 'penerbit', 'kategori', 'rak']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('penulis', function($qp) use ($search) {
                      $qp->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $bukuList = $query->latest()->paginate(10)->withQueryString();
        $penulisList = Penulis::orderBy('nama', 'asc')->get();
        $penerbitList = Penerbit::orderBy('nama', 'asc')->get();
        $kategoriList = Kategori::orderBy('nama', 'asc')->get();
        $rakList = Rak::orderBy('kode_rak', 'asc')->get();

        return view('admin.buku.index', compact('bukuList', 'penulisList', 'penerbitList', 'kategoriList', 'rakList'));
    }

    public function bukuStore(Request $request)
    {
        $request->validate([
            'isbn'           => 'nullable|string|max:50',
            'judul'          => 'required|string|max:255',
            'tahun_terbit'   => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'total_quantity' => 'required|integer|min:1|max:10000',
            'penulis_id'     => 'nullable|exists:penulis,id',
            'penerbit_id'    => 'nullable|exists:penerbit,id',
            'kategori_id'    => 'nullable|exists:kategori,id',
            'rak_id'         => 'nullable|exists:rak,id',
            'sinopsis'       => 'nullable|string',
            'cover'          => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('covers', 'public');
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
            'rak_id'             => $request->rak_id,
            'sinopsis'           => $request->sinopsis,
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
            'isbn'           => 'nullable|string|max:50',
            'judul'          => 'required|string|max:255',
            'tahun_terbit'   => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'total_quantity' => 'required|integer|min:1|max:10000',
            'penulis_id'     => 'nullable|exists:penulis,id',
            'penerbit_id'    => 'nullable|exists:penerbit,id',
            'kategori_id'    => 'nullable|exists:kategori,id',
            'rak_id'         => 'nullable|exists:rak,id',
            'sinopsis'       => 'nullable|string',
            'cover'          => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $borrowedNow = Peminjaman::where('buku_id', $buku->id)->where('status', 'dipinjam')->sum('jumlah');
        $newTotal = (int) $request->total_quantity;
        
        if ($newTotal < $borrowedNow) {
            return back()->with('error', "Total stok tidak boleh kurang dari jumlah buku yang sedang dipinjam ({$borrowedNow} buku).");
        }

        $newAvailable = max(0, $newTotal - $borrowedNow);

        $data = [
            'isbn'               => $request->isbn,
            'judul'              => $request->judul,
            'tahun_terbit'       => $request->tahun_terbit,
            'total_quantity'     => $newTotal,
            'available_quantity' => $newAvailable,
            'penulis_id'         => $request->penulis_id,
            'penerbit_id'        => $request->penerbit_id,
            'kategori_id'        => $request->kategori_id,
            'rak_id'             => $request->rak_id,
            'sinopsis'           => $request->sinopsis,
        ];

        if ($request->hasFile('cover')) {
            if ($buku->cover && Storage::disk('public')->exists($buku->cover)) {
                Storage::disk('public')->delete($buku->cover);
            }
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $buku->update($data);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'EDIT_BUKU',
            'deskripsi'  => "Memperbarui data buku: '{$buku->judul}'",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Data buku berhasil diperbarui.');
    }

    public function bukuDestroy(Request $request, $id)
    {
        $buku = Buku::withCount(['peminjaman' => function($q) {
            $q->where('status', 'dipinjam');
        }])->findOrFail($id);

        if ($buku->peminjaman_count > 0) {
            return back()->with('error', 'Buku tidak dapat dihapus karena masih ada yang sedang dalam status dipinjam.');
        }

        if ($buku->cover && Storage::disk('public')->exists($buku->cover)) {
            Storage::disk('public')->delete($buku->cover);
        }

        $judul = $buku->judul;
        $buku->delete();

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'HAPUS_BUKU',
            'deskripsi'  => "Menghapus buku: {$judul}",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Buku berhasil dihapus dari katalog.');
    }

    public function penulisIndex(Request $request)
    {
        $query = Penulis::withCount('buku');
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('nama', 'like', "%{$search}%");
        }
        $penulisList = $query->latest()->paginate(10)->withQueryString();
        return view('admin.penulis.index', compact('penulisList'));
    }

    public function penulisStore(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255|unique:penulis,nama',
            'biografi' => 'nullable|string',
        ], [
            'nama.unique' => 'Nama penulis ini sudah terdaftar.',
        ]);

        Penulis::create([
            'nama'     => $request->nama,
            'biografi' => $request->biografi,
        ]);

        return back()->with('success', 'Penulis baru berhasil ditambahkan.');
    }

    public function penulisUpdate(Request $request, $id)
    {
        $penulis = Penulis::findOrFail($id);
        $request->validate([
            'nama'     => 'required|string|max:255|unique:penulis,nama,' . $id,
            'biografi' => 'nullable|string',
        ]);

        $penulis->update([
            'nama'     => $request->nama,
            'biografi' => $request->biografi,
        ]);

        return back()->with('success', 'Data penulis berhasil diperbarui.');
    }

    public function penulisDestroy($id)
    {
        $penulis = Penulis::withCount('buku')->findOrFail($id);
        if ($penulis->buku_count > 0) {
            return back()->with('error', "Penulis tidak dapat dihapus karena masih digunakan oleh {$penulis->buku_count} buku.");
        }
        $penulis->delete();
        return back()->with('success', 'Penulis berhasil dihapus.');
    }

    public function penerbitIndex(Request $request)
    {
        $query = Penerbit::withCount('buku');
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('nama', 'like', "%{$search}%")->orWhere('kota', 'like', "%{$search}%");
        }
        $penerbitList = $query->latest()->paginate(10)->withQueryString();
        return view('admin.penerbit.index', compact('penerbitList'));
    }

    public function penerbitStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:penerbit,nama',
            'kota' => 'nullable|string|max:100',
        ], [
            'nama.unique' => 'Nama penerbit ini sudah terdaftar.',
        ]);

        Penerbit::create([
            'nama' => $request->nama,
            'kota' => $request->kota,
        ]);

        return back()->with('success', 'Penerbit baru berhasil ditambahkan.');
    }

    public function penerbitUpdate(Request $request, $id)
    {
        $penerbit = Penerbit::findOrFail($id);
        $request->validate([
            'nama' => 'required|string|max:255|unique:penerbit,nama,' . $id,
            'kota' => 'nullable|string|max:100',
        ]);

        $penerbit->update([
            'nama' => $request->nama,
            'kota' => $request->kota,
        ]);

        return back()->with('success', 'Data penerbit berhasil diperbarui.');
    }

    public function penerbitDestroy($id)
    {
        $penerbit = Penerbit::withCount('buku')->findOrFail($id);
        if ($penerbit->buku_count > 0) {
            return back()->with('error', "Penerbit tidak dapat dihapus karena masih digunakan oleh {$penerbit->buku_count} buku.");
        }
        $penerbit->delete();
        return back()->with('success', 'Penerbit berhasil dihapus.');
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

        return back()->with('success', 'Kategori berhasil diperbarui.');
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

    public function rakIndex(Request $request)
    {
        $query = Rak::with(['kategori'])->withCount('buku');
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('nama_rak', 'like', "%{$search}%")->orWhere('kode_rak', 'like', "%{$search}%");
        }
        $rakList = $query->latest()->paginate(10)->withQueryString();
        $kategoriList = Kategori::all();
        return view('admin.rak.index', compact('rakList', 'kategoriList'));
    }

    public function rakStore(Request $request)
    {
        $request->validate([
            'kode_rak'    => 'required|unique:rak,kode_rak|max:50',
            'nama_rak'    => 'required|string|max:255',
            'lokasi'      => 'nullable|string|max:255',
            'kategori_id' => 'nullable|exists:kategori,id',
        ]);

        Rak::create([
            'kode_rak'    => $request->kode_rak,
            'nama_rak'    => $request->nama_rak,
            'lokasi'      => $request->lokasi,
            'kategori_id' => $request->kategori_id,
        ]);

        return back()->with('success', 'Rak baru berhasil ditambahkan.');
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
            'kode_rak'    => $request->kode_rak,
            'nama_rak'    => $request->nama_rak,
            'lokasi'      => $request->lokasi,
            'kategori_id' => $request->kategori_id,
        ]);

        return back()->with('success', 'Data rak berhasil diperbarui.');
    }

    public function rakDestroy($id)
    {
        $rak = Rak::withCount('buku')->findOrFail($id);
        if ($rak->buku_count > 0) {
            return back()->with('error', 'Rak tidak dapat dihapus karena masih menampung ' . $rak->buku_count . ' buku.');
        }
        $rak->delete();
        return back()->with('success', 'Rak berhasil dihapus.');
    }

    public function peminjamanIndex(Request $request)
    {
        $query = Peminjaman::with(['user', 'buku', 'petugas'])
            ->where('status', 'dipinjam');

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

        $activeLoans = $query->latest('tanggal_pinjam')->paginate(10)->withQueryString();
        $booksList = Buku::where('available_quantity', '>', 0)->orderBy('judul', 'asc')->get();

        return view('admin.peminjaman.index', compact('activeLoans', 'booksList'));
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

        try {
            $loan = DB::transaction(function () use ($request) {
                $buku = Buku::where('id', $request->buku_id)->lockForUpdate()->first();
                $jumlahPinjam = (int) $request->jumlah;

                if (!$buku || $buku->available_quantity < $jumlahPinjam) {
                    throw new \Exception('STOCK_INSUFFICIENT');
                }

                $buku->available_quantity -= $jumlahPinjam;
                $buku->save();

                $kodePinjam = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(4));
                $today = Carbon::today()->toDateString();

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

    public function anggotaIndex(Request $request)
    {
        $query = User::with(['role', 'anggota']);
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('anggota', function($qa) use ($search) {
                      $qa->where('nomor_anggota', 'like', "%{$search}%")
                         ->orWhere('nim', 'like', "%{$search}%");
                  });
            });
        }
        $anggotaList = $query->latest()->paginate(10)->withQueryString();
        $roles = Role::all();
        return view('admin.anggota.index', compact('anggotaList', 'roles'));
    }

    public function anggotaStore(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email',
            'password'      => 'required|string|min:8',
            'phone'         => 'nullable|string|max:20',
            'nim'           => 'required|string|max:50|unique:anggota,nim',
            'program_studi' => 'required|string|max:150',
            'status'        => 'required|in:aktif,nonaktif,dibekukan',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => 1,
            'phone'    => $request->phone,
            'status'   => 'active',
        ]);

        $nomorAnggota = 'LIB-' . date('Y') . '-' . str_pad($user->id, 3, '0', STR_PAD_LEFT);

        Anggota::create([
            'user_id'       => $user->id,
            'nomor_anggota' => $nomorAnggota,
            'nim'           => $request->nim,
            'program_studi' => $request->program_studi,
            'status'        => $request->status,
        ]);

        return back()->with('success', 'Anggota/Pengguna baru berhasil didaftarkan.');
    }

    public function anggotaUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email,' . $id,
            'password'      => 'nullable|string|min:8',
            'phone'         => 'nullable|string|max:20',
            'nim'           => 'required|string|max:50|unique:anggota,nim,' . ($user->anggota->id ?? 0),
            'program_studi' => 'required|string|max:150',
            'status'        => 'required|in:aktif,nonaktif,dibekukan',
        ]);

        $userData = [
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role_id'  => 1,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        if ($user->anggota) {
            $user->anggota->update([
                'nim'           => $request->nim,
                'program_studi' => $request->program_studi,
                'status'        => $request->status,
            ]);
        } else {
            $nomorAnggota = 'LIB-' . date('Y') . '-' . str_pad($user->id, 3, '0', STR_PAD_LEFT);
            Anggota::create([
                'user_id'       => $user->id,
                'nomor_anggota' => $nomorAnggota,
                'nim'           => $request->nim,
                'program_studi' => $request->program_studi,
                'status'        => $request->status,
            ]);
        }

        return back()->with('success', 'Data anggota/pengguna berhasil diperbarui.');
    }

    public function anggotaDestroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $activeLoans = Peminjaman::where('user_id', $user->id)->where('status', 'dipinjam')->count();
        if ($activeLoans > 0) {
            return back()->with('error', "Pengguna tidak dapat dihapus karena masih memiliki {$activeLoans} buku yang sedang dipinjam.");
        }

        if ($user->anggota) {
            $user->anggota->delete();
        }
        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    public function auditLogIndex()
    {
        $logs = AuditLog::latest()->paginate(20);
        return view('admin.audit-log.index', compact('logs'));
    }

    public function pengaturanIndex()
    {
        $pengaturan = Pengaturan::all()->pluck('value', 'key');
        return view('admin.pengaturan.index', compact('pengaturan'));
    }

    public function pengaturanUpdate(Request $request)
    {
        $validated = $request->validate([
            'nama_perpustakaan'  => 'required|string|max:255',
            'jam_operasional'    => 'required|string|max:255',
            'alamat'             => 'nullable|string|max:500',
            'durasi_pinjam_hari' => 'required|integer|min:1|max:365',
            'max_buku_pinjam'    => 'required|integer|min:1|max:50',
        ]);

        $settingDefinitions = [
            'nama_perpustakaan'  => ['label' => 'Nama Resmi Perpustakaan', 'tipe' => 'text'],
            'jam_operasional'    => ['label' => 'Informasi Jam Operasional Perpustakaan', 'tipe' => 'text'],
            'alamat'             => ['label' => 'Alamat & Lokasi Gedung', 'tipe' => 'text'],
            'durasi_pinjam_hari' => ['label' => 'Durasi Peminjaman Standar (Hari)', 'tipe' => 'number'],
            'max_buku_pinjam'    => ['label' => 'Maksimal Buku Dipinjam Per Siswa', 'tipe' => 'number'],
        ];

        foreach ($validated as $key => $value) {
            $def = $settingDefinitions[$key] ?? ['label' => ucwords(str_replace('_', ' ', $key)), 'tipe' => 'text'];
            Pengaturan::updateOrCreate(
                ['key' => $key],
                [
                    'value' => strip_tags((string) $value),
                    'label' => $def['label'],
                    'tipe'  => $def['tipe'],
                ]
            );
        }

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'UPDATE_PENGATURAN',
            'deskripsi'  => 'Memperbarui konfigurasi sistem perpustakaan',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Pengaturan sistem perpustakaan berhasil diperbarui.');
    }
}
