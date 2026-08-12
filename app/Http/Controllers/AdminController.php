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
        ]);

        $buku = Buku::create($request->all());

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
        ]);

        $buku->update($request->all());

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
        $buku = Buku::findOrFail($id);
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

        Rak::create($request->all());
        return back()->with('success', 'Rak baru berhasil ditambahkan.');
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

    // --- ANGGOTA --- //
    public function anggotaIndex()
    {
        $anggotaList = Anggota::with('user')->paginate(10);
        return view('admin.anggota.index', compact('anggotaList'));
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
        foreach ($request->except('_token') as $key => $value) {
            Pengaturan::where('key', $key)->update(['value' => $value]);
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
