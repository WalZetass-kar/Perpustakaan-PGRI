<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Rak;
use App\Models\Eksemplar;
use App\Models\Anggota;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Denda;
use App\Models\Reservasi;
use App\Models\Notifikasi;
use App\Models\AuditLog;
use App\Models\Pengaturan;
use App\Models\Penulis;
use App\Models\Penerbit;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today()->toDateString();

        $stats = [
            'total_buku' => Buku::count(),
            'total_eksemplar' => Eksemplar::count(),
            'buku_tersedia' => Eksemplar::where('status', 'tersedia')->count(),
            'buku_dipinjam' => Eksemplar::where('status', 'dipinjam')->count(),
            'total_anggota' => Anggota::count(),
            'peminjaman_hari_ini' => Peminjaman::whereDate('tanggal_pinjam', $today)->count(),
            'pengembalian_hari_ini' => Pengembalian::whereDate('tanggal_kembali', $today)->count(),
            'buku_terlambat' => Peminjaman::where('status', 'dipinjam')->where('tanggal_jatuh_tempo', '<', $today)->count(),
            'total_denda' => Denda::sum('jumlah_denda'),
        ];

        // Chart Data (7 hari terakhir)
        $chartDates = [];
        $chartLoans = [];
        $chartReturns = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $chartDates[] = Carbon::today()->subDays($i)->format('d M');
            $chartLoans[] = Peminjaman::whereDate('tanggal_pinjam', $date)->count();
            $chartReturns[] = Pengembalian::whereDate('tanggal_kembali', $date)->count();
        }

        $mostBorrowedBooks = Buku::withCount('peminjaman')->orderBy('peminjaman_count', 'desc')->take(5)->get();
        $recentAuditLogs = AuditLog::latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'chartDates', 'chartLoans', 'chartReturns', 'mostBorrowedBooks', 'recentAuditLogs'));
    }

    // --- KOLEKSI BUKU --- //
    public function bukuIndex()
    {
        $bukuList = Buku::with(['penulis', 'penerbit', 'kategori', 'rak'])->latest()->paginate(10);
        $kategoriList = Kategori::all();
        $rakList = Rak::all();
        $penulisList = Penulis::all();
        $penerbitList = Penerbit::all();

        return view('admin.buku.index', compact('bukuList', 'kategoriList', 'rakList', 'penulisList', 'penerbitList'));
    }

    public function bukuStore(Request $request)
    {
        $request->validate([
            'isbn' => 'required|unique:buku,isbn',
            'judul' => 'required|string|max:255',
            'penulis_id' => 'required|exists:penulis,id',
            'penerbit_id' => 'required|exists:penerbit,id',
            'kategori_id' => 'required|exists:kategori,id',
            'rak_id' => 'required|exists:rak,id',
            'tahun_terbit' => 'required|integer',
            'sinopsis' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'file_pdf' => 'nullable|mimes:pdf|max:10240',
        ]);

        $data = [
            'isbn'        => $request->isbn,
            'judul'       => $request->judul,
            'penulis_id'  => $request->penulis_id,
            'penerbit_id' => $request->penerbit_id,
            'kategori_id' => $request->kategori_id,
            'rak_id'      => $request->rak_id,
            'tahun_terbit'=> $request->tahun_terbit,
            'sinopsis'    => $request->sinopsis,
        ];

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        if ($request->hasFile('file_pdf')) {
            $data['file_pdf'] = $request->file('file_pdf')->store('ebooks', 'public');
        }

        $buku = Buku::create($data);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'aktivitas' => 'TAMBAH_BUKU',
            'deskripsi' => "Menambahkan buku baru: {$buku->judul} (ISBN: {$buku->isbn})",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Buku baru berhasil ditambahkan.');
    }

    public function bukuUpdate(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);
        $request->validate([
            'isbn' => 'required|unique:buku,isbn,' . $id,
            'judul' => 'required|string|max:255',
            'penulis_id' => 'required|exists:penulis,id',
            'penerbit_id' => 'required|exists:penerbit,id',
            'kategori_id' => 'required|exists:kategori,id',
            'rak_id' => 'required|exists:rak,id',
            'tahun_terbit' => 'required|integer',
            'sinopsis' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'file_pdf' => 'nullable|mimes:pdf|max:10240',
        ]);

        $data = [
            'isbn'        => $request->isbn,
            'judul'       => $request->judul,
            'penulis_id'  => $request->penulis_id,
            'penerbit_id' => $request->penerbit_id,
            'kategori_id' => $request->kategori_id,
            'rak_id'      => $request->rak_id,
            'tahun_terbit'=> $request->tahun_terbit,
            'sinopsis'    => $request->sinopsis,
        ];

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        if ($request->hasFile('file_pdf')) {
            $data['file_pdf'] = $request->file('file_pdf')->store('ebooks', 'public');
        }

        $buku->update($data);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'aktivitas' => 'EDIT_BUKU',
            'deskripsi' => "Memperbarui data buku: {$buku->judul}",
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
            return back()->with('error', 'Buku tidak dapat dihapus karena masih ada eksemplar yang sedang dipinjam oleh anggota.');
        }

        $judul = $buku->judul;
        $buku->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'aktivitas' => 'HAPUS_BUKU',
            'deskripsi' => "Menghapus buku: {$judul}",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Buku berhasil dihapus.');
    }

    // --- KATEGORI BUKU --- //
    public function kategoriIndex()
    {
        $kategoriList = Kategori::withCount('buku')->get();
        return view('admin.kategori.index', compact('kategoriList'));
    }

    public function kategoriStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|unique:kategori,nama|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        Kategori::create([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
        ]);

        return back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function kategoriUpdate(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);
        $request->validate([
            'nama' => 'required|max:255|unique:kategori,nama,' . $id,
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
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

    // --- RAK PERPUSTAKAAN --- //
    public function rakIndex()
    {
        $rakList = Rak::with(['kategori', 'eksemplar'])->get();
        $kategoriList = Kategori::all();
        return view('admin.rak.index', compact('rakList', 'kategoriList'));
    }

    public function rakStore(Request $request)
    {
        $request->validate([
            'kode_rak' => 'required|unique:rak,kode_rak',
            'nama_rak' => 'required|string',
            'lokasi' => 'required|string',
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
            'kode_rak' => 'required|unique:rak,kode_rak,' . $id,
            'nama_rak' => 'required|string',
            'lokasi' => 'required|string',
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
        $rak = Rak::withCount('eksemplar')->findOrFail($id);
        if ($rak->eksemplar_count > 0) {
            return back()->with('error', 'Rak tidak dapat dihapus karena masih menampung ' . $rak->eksemplar_count . ' eksemplar buku.');
        }
        $rak->delete();
        return back()->with('success', 'Rak berhasil dihapus.');
    }

    // --- EKSEMPLAR BUKU --- //
    public function eksemplarIndex()
    {
        $eksemplarList = Eksemplar::with(['buku', 'rak'])->latest()->paginate(10);
        $bukuList = Buku::all();
        $rakList = Rak::all();
        return view('admin.eksemplar.index', compact('eksemplarList', 'bukuList', 'rakList'));
    }

    public function eksemplarStore(Request $request)
    {
        $request->validate([
            'buku_id' => 'required|exists:buku,id',
            'kode_eksemplar' => 'required|unique:eksemplar,kode_eksemplar',
            'barcode' => 'required|unique:eksemplar,barcode',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'rak_id' => 'required|exists:rak,id',
        ]);

        Eksemplar::create([
            'buku_id' => $request->buku_id,
            'kode_eksemplar' => $request->kode_eksemplar,
            'barcode' => $request->barcode,
            'kondisi' => $request->kondisi,
            'rak_id' => $request->rak_id,
            'status' => 'tersedia',
        ]);

        return back()->with('success', 'Eksemplar baru berhasil didaftarkan.');
    }

    public function eksemplarUpdate(Request $request, $id)
    {
        $eksemplar = Eksemplar::findOrFail($id);
        $request->validate([
            'kode_eksemplar' => 'required|unique:eksemplar,kode_eksemplar,' . $id,
            'barcode' => 'required|unique:eksemplar,barcode,' . $id,
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'rak_id' => 'required|exists:rak,id',
            'status' => 'required|in:tersedia,dipinjam,hilang,rusak',
        ]);

        $eksemplar->update([
            'kode_eksemplar' => $request->kode_eksemplar,
            'barcode'        => $request->barcode,
            'kondisi'        => $request->kondisi,
            'rak_id'         => $request->rak_id,
            'status'         => $request->status,
        ]);
        return back()->with('success', 'Data eksemplar berhasil diperbarui.');
    }

    public function eksemplarDestroy($id)
    {
        $eksemplar = Eksemplar::findOrFail($id);
        if ($eksemplar->status === 'dipinjam') {
            return back()->with('error', 'Eksemplar tidak dapat dihapus karena sedang dalam status dipinjam.');
        }
        $eksemplar->delete();
        return back()->with('success', 'Eksemplar berhasil dihapus.');
    }

    public function cetakBarcodeEksemplar($id = null)
    {
        if ($id) {
            $eksemplarList = Eksemplar::with(['buku', 'rak'])->where('id', $id)->get();
        } else {
            $eksemplarList = Eksemplar::with(['buku', 'rak'])->latest()->get();
        }
        return view('admin.eksemplar.cetak_barcode', compact('eksemplarList'));
    }

    // --- ANGGOTA & USER MANAGEMENT --- //
    public function anggotaIndex(Request $request)
    {
        $query = User::with(['role', 'anggota']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('anggota', function($qa) use ($search) {
                      $qa->where('nomor_anggota', 'like', "%{$search}%")
                         ->orWhere('nim', 'like', "%{$search}%")
                         ->orWhere('program_studi', 'like', "%{$search}%");
                  });
            });
        }

        $anggotaList = $query->latest()->paginate(10);

        // Ensure every user has an Anggota record synced
        foreach ($anggotaList as $user) {
            if (!$user->anggota) {
                $nomorAnggota = 'LIB-' . date('Y') . '-' . str_pad($user->id, 3, '0', STR_PAD_LEFT);
                Anggota::create([
                    'user_id' => $user->id,
                    'nomor_anggota' => $nomorAnggota,
                    'nim' => '1022' . date('Y') . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                    'program_studi' => 'Teknik Komputer & Jaringan',
                    'status' => 'aktif',
                ]);
                $user->load('anggota');
            }
        }

        $roles = Role::all();
        return view('admin.anggota.index', compact('anggotaList', 'roles'));
    }

    public function anggotaStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string',
            'nim' => 'required|unique:anggota,nim',
            'program_studi' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'phone' => $request->phone,
            'status' => 'active',
        ]);

        // Auto-generate Nomor Anggota ID
        $nomorAnggota = 'LIB-' . date('Y') . '-' . str_pad($user->id, 3, '0', STR_PAD_LEFT);

        Anggota::create([
            'user_id' => $user->id,
            'nomor_anggota' => $nomorAnggota,
            'nim' => $request->nim,
            'program_studi' => $request->program_studi,
            'status' => 'aktif',
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'aktivitas' => 'TAMBAH_USER',
            'deskripsi' => "Mendaftarkan pengguna/anggota baru: {$user->name} ({$user->email})",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Anggota/Pengguna baru berhasil didaftarkan.');
    }

    public function anggotaUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $anggota = $user->anggota;
        $anggotaId = $anggota ? $anggota->id : 0;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string',
            'nim' => 'required|unique:anggota,nim,' . $anggotaId,
            'program_studi' => 'required|string',
            'status' => 'required|in:aktif,nonaktif,dibekukan',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'phone' => $request->phone,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if (!$anggota) {
            $nomorAnggota = 'LIB-' . date('Y') . '-' . str_pad($user->id, 3, '0', STR_PAD_LEFT);
            $anggota = Anggota::create([
                'user_id' => $user->id,
                'nomor_anggota' => $nomorAnggota,
                'nim' => $request->nim,
                'program_studi' => $request->program_studi,
                'status' => $request->status,
            ]);
        } else {
            $anggota->update([
                'nim' => $request->nim,
                'program_studi' => $request->program_studi,
                'status' => $request->status,
            ]);
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'aktivitas' => 'EDIT_USER',
            'deskripsi' => "Memperbarui data pengguna/anggota: {$user->name} ({$user->email})",
            'ip_address' => $request->ip(),
        ]);

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
            return back()->with('error', 'Pengguna tidak dapat dihapus karena masih memiliki ' . $activeLoans . ' buku yang sedang dipinjam.');
        }

        $unpaidFines = Denda::where('user_id', $user->id)->where('status_pembayaran', 'belum_lunas')->sum('jumlah_denda');
        if ($unpaidFines > 0) {
            return back()->with('error', 'Pengguna tidak dapat dihapus karena masih memiliki tanggungan denda sebesar Rp ' . number_format($unpaidFines) . '.');
        }

        if ($user->anggota) {
            $user->anggota->delete();
        }
        $user->delete();

        return back()->with('success', 'Data anggota/pengguna berhasil dihapus.');
    }

    public function dendaStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'jumlah_denda' => 'required|numeric|min:500',
            'alasan' => 'required|string|max:255',
            'status_pembayaran' => 'required|in:belum_lunas,lunas',
        ]);

        $user = User::with('role')->findOrFail($request->user_id);
        if (in_array($user->role->name ?? '', ['admin', 'pustakawan'])) {
            return back()->with('error', 'Akun Pengelola (Admin & Pustakawan) dibebaskan dari denda perpustakaan.');
        }

        $peminjaman = Peminjaman::where('user_id', $request->user_id)->latest()->first();

        Denda::create([
            'user_id' => $request->user_id,
            'peminjaman_id' => $peminjaman ? $peminjaman->id : null,
            'jumlah_denda' => $request->jumlah_denda,
            'alasan' => $request->alasan,
            'status_pembayaran' => $request->status_pembayaran,
        ]);

        $user = User::find($request->user_id);
        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'aktivitas' => 'TAMBAH_DENDA',
            'deskripsi' => "Menetapkan denda Rp " . number_format($request->jumlah_denda) . " kepada {$user->name} ({$request->alasan})",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Denda berhasil ditetapkan untuk anggota/siswa.');
    }

    public function dendaBayar($id)
    {
        $denda = Denda::findOrFail($id);

        // VULN-006 FIX: Prevent re-confirming already paid fines
        if ($denda->status_pembayaran === 'lunas') {
            return back()->with('error', 'Denda ini sudah berstatus lunas sebelumnya.');
        }

        $denda->status_pembayaran = 'lunas';
        $denda->save();

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'aktivitas'  => 'BAYAR_DENDA',
            'deskripsi'  => "Admin konfirmasi pembayaran denda ID#{$denda->id} sebesar Rp " . number_format($denda->jumlah_denda),
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'Status denda berhasil diubah menjadi LUNAS.');
    }

    // --- LAPORAN & EXPORT --- //
    public function laporanIndex(Request $request)
    {
        $type = $request->get('type', 'peminjaman');
        $startDate = $request->get('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());

        $reportData = [];

        if ($type === 'peminjaman') {
            $reportData = Peminjaman::with(['user', 'buku', 'eksemplar'])
                ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
                ->get();
        } else if ($type === 'pengembalian') {
            $reportData = Pengembalian::with(['peminjaman.user', 'peminjaman.buku'])
                ->whereBetween('tanggal_kembali', [$startDate, $endDate])
                ->get();
        } else if ($type === 'denda') {
            $reportData = Denda::with(['user', 'peminjaman.buku'])
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->get();
        }

        return view('admin.laporan.index', compact('type', 'startDate', 'endDate', 'reportData'));
    }

    public function laporanCetak(Request $request)
    {
        $type = $request->get('type', 'peminjaman');
        $startDate = $request->get('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());

        $reportData = [];

        if ($type === 'peminjaman') {
            $reportData = Peminjaman::with(['user', 'buku', 'eksemplar'])
                ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
                ->get();
        } else if ($type === 'pengembalian') {
            $reportData = Pengembalian::with(['peminjaman.user', 'peminjaman.buku'])
                ->whereBetween('tanggal_kembali', [$startDate, $endDate])
                ->get();
        } else if ($type === 'denda') {
            $reportData = Denda::with(['user', 'peminjaman.buku'])
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->get();
        }

        return view('admin.laporan.cetak', compact('type', 'startDate', 'endDate', 'reportData'));
    }

    // --- AUDIT LOGS --- //
    public function auditLogIndex()
    {
        $logs = AuditLog::latest()->paginate(15);
        return view('admin.audit-log.index', compact('logs'));
    }

    // --- PENGATURAN --- //
    public function pengaturanIndex()
    {
        $settings = Pengaturan::all();
        return view('admin.pengaturan.index', compact('settings'));
    }

    public function pengaturanUpdate(Request $request)
    {
        // VULN-010 FIX: Whitelist of allowed keys to prevent arbitrary key injection
        $allowedKeys = [
            'nama_perpustakaan', 'jam_operasional', 'alamat',
            'denda_per_hari', 'durasi_pinjam_hari', 'max_buku_pinjam', 'max_perpanjangan',
        ];

        // Numeric keys that require positive integer validation
        $numericKeys = ['denda_per_hari', 'durasi_pinjam_hari', 'max_buku_pinjam', 'max_perpanjangan'];

        foreach ($request->except('_token') as $key => $value) {
            // Skip keys not in whitelist
            if (!in_array($key, $allowedKeys)) {
                continue;
            }

            // Validate numeric keys
            if (in_array($key, $numericKeys)) {
                if (!is_numeric($value) || (float)$value < 0 || (float)$value > 999999) {
                    continue;
                }
            }

            Pengaturan::where('key', $key)->update(['value' => strip_tags($value)]);
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'aktivitas' => 'UPDATE_PENGATURAN',
            'deskripsi' => "Memperbarui konfigurasi sistem perpustakaan.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Konfigurasi perpustakaan berhasil diperbarui.');
    }
}
