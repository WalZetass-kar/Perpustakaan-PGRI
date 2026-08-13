<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Denda;
use App\Models\Buku;
use App\Models\Eksemplar;
use App\Models\Anggota;
use App\Models\User;
use App\Models\Rak;
use App\Models\Kategori;
use App\Models\Reservasi;
use App\Models\Pengaturan;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PustakawanController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today()->toDateString();

        $stats = [
            'peminjaman_hari_ini' => Peminjaman::whereDate('tanggal_pinjam', $today)->count(),
            'pengembalian_hari_ini' => Pengembalian::whereDate('tanggal_kembali', $today)->count(),
            'buku_terlambat' => Peminjaman::where('status', 'dipinjam')->where('tanggal_jatuh_tempo', '<', $today)->count(),
            'denda_belum_lunas' => Denda::where('status_pembayaran', 'belum_lunas')->sum('jumlah_denda'),
            'total_buku' => Buku::count(),
            'buku_tersedia' => Eksemplar::where('status', 'tersedia')->count(),
        ];

        $recentLoans = Peminjaman::with(['user', 'buku', 'eksemplar'])->latest()->take(6)->get();

        return view('pustakawan.dashboard', compact('stats', 'recentLoans'));
    }

    public function peminjamanForm(Request $request)
    {
        $selectedUser = null;
        $selectedExemplar = null;

        if ($request->filled('scan_nim')) {
            $anggota = Anggota::where('nim', $request->scan_nim)->orWhere('nomor_anggota', $request->scan_nim)->first();
            if ($anggota) {
                $selectedUser = $anggota->user;
            }
        }

        if ($request->filled('scan_barcode')) {
            $selectedExemplar = Eksemplar::with('buku')->where('barcode', $request->scan_barcode)->orWhere('kode_eksemplar', $request->scan_barcode)->first();
        }

        $durasiPinjam = (int) (Pengaturan::where('key', 'durasi_pinjam_hari')->value('value') ?? 7);
        $defaultDueDate = Carbon::today()->addDays($durasiPinjam)->format('Y-m-d');

        return view('pustakawan.peminjaman', compact('selectedUser', 'selectedExemplar', 'defaultDueDate'));
    }

    public function prosesPeminjaman(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'barcode' => 'required|string',
            'tanggal_jatuh_tempo' => 'required|date',
        ]);

        $exemplar = Eksemplar::with('buku')->where('barcode', $request->barcode)->orWhere('kode_eksemplar', $request->barcode)->first();
        if (!$exemplar || $exemplar->status !== 'tersedia') {
            return back()->with('error', 'Eksemplar buku tidak ditemukan atau sedang tidak tersedia!');
        }

        $maxBuku = (int) (Pengaturan::where('key', 'max_buku_pinjam')->value('value') ?? 3);
        $activeLoansCount = Peminjaman::where('user_id', $request->user_id)->where('status', 'dipinjam')->count();

        if ($activeLoansCount >= $maxBuku) {
            return back()->with('error', "Anggota telah mencapai batas maksimal peminjaman ({$maxBuku} buku).");
        }

        $kodePeminjaman = 'TRX-' . date('Ymd') . '-' . Str::upper(Str::random(4));

        $peminjaman = Peminjaman::create([
            'kode_peminjaman' => $kodePeminjaman,
            'user_id' => $request->user_id,
            'buku_id' => $exemplar->buku_id,
            'eksemplar_id' => $exemplar->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
            'status' => 'dipinjam',
            'petugas_id' => auth()->id(),
        ]);

        $exemplar->status = 'dipinjam';
        $exemplar->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'aktivitas' => 'TRANSAKSI_PEMINJAMAN',
            'deskripsi' => "Peminjaman kode {$kodePeminjaman} berhasil dikonfirmasi.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('pustakawan.peminjaman')->with('success', "Peminjaman berhasil dicatat! Kode TRX: {$kodePeminjaman}");
    }

    public function pengembalianForm(Request $request)
    {
        $peminjaman = null;
        $hariTerlambat = 0;
        $dendaEstimasi = 0;

        if ($request->filled('scan_barcode')) {
            $exemplar = Eksemplar::where('barcode', $request->scan_barcode)->orWhere('kode_eksemplar', $request->scan_barcode)->first();
            if ($exemplar) {
                $peminjaman = Peminjaman::with(['user', 'buku', 'eksemplar'])
                    ->where('eksemplar_id', $exemplar->id)
                    ->where('status', 'dipinjam')
                    ->first();

                if ($peminjaman) {
                    $today = Carbon::today();
                    $dueDate = Carbon::parse($peminjaman->tanggal_jatuh_tempo);
                    if ($today->gt($dueDate)) {
                        $hariTerlambat = $today->diffInDays($dueDate);
                        $dendaPerHari = (float) (Pengaturan::where('key', 'denda_per_hari')->value('value') ?? 2000);
                        $dendaEstimasi = $hariTerlambat * $dendaPerHari;
                    }
                }
            }
        }

        return view('pustakawan.pengembalian', compact('peminjaman', 'hariTerlambat', 'dendaEstimasi'));
    }

    public function prosesPengembalian(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'kondisi_buku' => 'required|string',
            'denda_kerusakan' => 'nullable|numeric|min:0',
        ]);

        $peminjaman = Peminjaman::with(['eksemplar', 'user.role'])->findOrFail($request->peminjaman_id);
        $today = Carbon::today();
        $dueDate = Carbon::parse($peminjaman->tanggal_jatuh_tempo);
        $hariTerlambat = 0;
        $dendaTerlambat = 0;

        if ($today->gt($dueDate)) {
            $hariTerlambat = $today->diffInDays($dueDate);
            $dendaPerHari = (float) (Pengaturan::where('key', 'denda_per_hari')->value('value') ?? 2000);
            $dendaTerlambat = $hariTerlambat * $dendaPerHari;
        }

        $dendaKerusakan = (float) ($request->denda_kerusakan ?? 0);
        $totalDenda = $dendaTerlambat + $dendaKerusakan;

        // Exempt Admin & Pustakawan roles from fines
        if (in_array($peminjaman->user->role->name ?? '', ['admin', 'pustakawan'])) {
            $dendaTerlambat = 0;
            $dendaKerusakan = 0;
            $totalDenda = 0;
        }

        $peminjaman->status = 'dikembalikan';
        $peminjaman->save();

        // Update eksemplar
        $eksemplar = $peminjaman->eksemplar;
        if ($request->kondisi_buku === 'rusak') {
            $eksemplar->status = 'rusak';
            $eksemplar->kondisi = 'rusak_berat';
        } else if ($request->kondisi_buku === 'hilang') {
            $eksemplar->status = 'hilang';
        } else {
            $eksemplar->status = 'tersedia';
            $eksemplar->kondisi = 'baik';
        }
        $eksemplar->save();

        // Catat pengembalian
        Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tanggal_kembali' => $today->toDateString(),
            'hari_keterlambatan' => $hariTerlambat,
            'denda_keterlambatan' => $dendaTerlambat,
            'denda_kerusakan_kehilangan' => $dendaKerusakan,
            'total_denda' => $totalDenda,
            'petugas_id' => auth()->id(),
        ]);

        if ($totalDenda > 0) {
            Denda::create([
                'peminjaman_id' => $peminjaman->id,
                'user_id' => $peminjaman->user_id,
                'jumlah_denda' => $totalDenda,
                'alasan' => $hariTerlambat > 0 ? "Terlambat {$hariTerlambat} hari" . ($dendaKerusakan > 0 ? " + denda kondisi {$request->kondisi_buku}" : "") : "Denda kondisi {$request->kondisi_buku}",
                'status_pembayaran' => 'belum_lunas',
            ]);
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'aktivitas' => 'TRANSAKSI_PENGEMBALIAN',
            'deskripsi' => "Pengembalian peminjaman {$peminjaman->kode_peminjaman} sukses.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('pustakawan.pengembalian')->with('success', 'Pengembalian buku berhasil diproses!');
    }

    public function anggotaIndex()
    {
        $anggotaList = Anggota::with('user')->paginate(10);
        return view('pustakawan.anggota', compact('anggotaList'));
    }

    public function bukuIndex()
    {
        $bukuList = Buku::with(['penulis', 'kategori', 'rak', 'eksemplar'])->paginate(10);
        return view('pustakawan.buku', compact('bukuList'));
    }

    public function eksemplarIndex()
    {
        $eksemplarList = Eksemplar::with(['buku', 'rak'])->paginate(10);
        return view('pustakawan.eksemplar', compact('eksemplarList'));
    }

    public function rakIndex()
    {
        $rakList = Rak::with('kategori')->get();
        return view('pustakawan.rak', compact('rakList'));
    }

    public function reservasiIndex()
    {
        $reservasiList = Reservasi::with(['user', 'buku'])->latest()->paginate(10);
        return view('pustakawan.reservasi', compact('reservasiList'));
    }

    public function prosesReservasi(Request $request, $id)
    {
        $reservasi = Reservasi::with(['user', 'buku.eksemplar'])->findOrFail($id);
        
        $eksemplar = $reservasi->buku->eksemplar()->where('status', 'tersedia')->first();
        if (!$eksemplar) {
            return back()->with('error', 'Tidak ada eksemplar buku yang sedang tersedia untuk dipinjamkan saat ini.');
        }

        $durasiPinjam = (int) (Pengaturan::where('key', 'durasi_pinjam_hari')->value('value') ?? 7);
        $kodePeminjaman = 'TRX-' . date('Ymd') . '-' . Str::random(5);

        $peminjaman = Peminjaman::create([
            'kode_peminjaman' => strtoupper($kodePeminjaman),
            'user_id' => $reservasi->user_id,
            'buku_id' => $reservasi->buku_id,
            'eksemplar_id' => $eksemplar->id,
            'tanggal_pinjam' => Carbon::now()->toDateString(),
            'tanggal_jatuh_tempo' => Carbon::now()->addDays($durasiPinjam)->toDateString(),
            'jumlah_perpanjangan' => 0,
            'status' => 'dipinjam',
            'petugas_id' => auth()->id(),
        ]);

        $eksemplar->status = 'dipinjam';
        $eksemplar->save();

        $reservasi->status = 'selesai';
        $reservasi->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'aktivitas' => 'PROSES_RESERVASI',
            'deskripsi' => "Reservasi {$reservasi->kode_reservasi} disetujui & diubah ke Peminjaman {$peminjaman->kode_peminjaman}.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Reservasi berhasil disetujui & transaksi peminjaman fisik aktif!');
    }

    public function dendaIndex()
    {
        $dendaList = Denda::with(['user', 'peminjaman.buku'])->latest()->paginate(10);
        return view('pustakawan.denda', compact('dendaList'));
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
            'deskripsi' => "Pustakawan menetapkan denda Rp " . number_format($request->jumlah_denda) . " kepada {$user->name} ({$request->alasan})",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Denda berhasil ditetapkan untuk anggota/siswa.');
    }

    public function bayarDenda(Request $request, $id)
    {
        $denda = Denda::findOrFail($id);
        $denda->status_pembayaran = 'lunas';
        $denda->save();

        return back()->with('success', 'Pembayaran denda berhasil dikonfirmasi lunas.');
    }
}
