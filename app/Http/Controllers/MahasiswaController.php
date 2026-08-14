<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Denda;
use App\Models\Reservasi;
use App\Models\Buku;
use App\Models\Eksemplar;
use App\Models\Notifikasi;
use App\Models\Anggota;
use App\Models\Pengaturan;
use App\Models\AuditLog;
use App\Models\Kategori;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MahasiswaController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        
        $activeLoans = Peminjaman::with(['buku', 'eksemplar'])
            ->where('user_id', $user->id)
            ->where('status', 'dipinjam')
            ->get();

        $today = Carbon::today();
        $nearingDueLoans = $activeLoans->filter(function($loan) use ($today) {
            $dueDate = Carbon::parse($loan->tanggal_jatuh_tempo);
            $diffDays = $today->diffInDays($dueDate, false);
            return $diffDays >= 0 && $diffDays <= 3;
        });

        $totalFines = Denda::where('user_id', $user->id)
            ->where('status_pembayaran', 'belum_lunas')
            ->sum('jumlah_denda');

        $activeReservations = Reservasi::with('buku')
            ->where('user_id', $user->id)
            ->whereIn('status', ['menunggu', 'tersedia'])
            ->get();

        $search = $request->get('search');
        $kategoriId = $request->get('kategori_id');

        $query = Buku::with(['penulis', 'penerbit', 'kategori', 'rak']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('penulis', function($p) use ($search) {
                      $p->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }

        $koleksiBuku = $query->latest()->paginate(8)->withQueryString();
        $kategoriList = Kategori::orderBy('nama', 'asc')->get();

        $recentActivities = AuditLog::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('mahasiswa.dashboard', compact('activeLoans', 'nearingDueLoans', 'totalFines', 'activeReservations', 'koleksiBuku', 'kategoriList', 'recentActivities'));
    }

    public function peminjamanSaya()
    {
        $user = auth()->user();
        $loans = Peminjaman::with(['buku', 'eksemplar'])
            ->where('user_id', $user->id)
            ->where('status', 'dipinjam')
            ->latest()
            ->get();

        $maxPerpanjangan = (int) (Pengaturan::where('key', 'max_perpanjangan')->value('value') ?? 2);

        return view('mahasiswa.peminjaman', compact('loans', 'maxPerpanjangan'));
    }

    public function perpanjangPeminjaman(Request $request, $id)
    {
        $user = auth()->user();
        $loan = Peminjaman::where('user_id', $user->id)->where('id', $id)->firstOrFail();

        $maxPerpanjangan = (int) (Pengaturan::where('key', 'max_perpanjangan')->value('value') ?? 2);
        $durasiPinjam = (int) (Pengaturan::where('key', 'durasi_pinjam_hari')->value('value') ?? 7);

        if ($loan->jumlah_perpanjangan >= $maxPerpanjangan) {
            return back()->with('error', "Buku ini sudah mencapai batas maksimal perpanjangan ({$maxPerpanjangan}x).");
        }

        if (Carbon::parse($loan->tanggal_jatuh_tempo)->isPast()) {
            return back()->with('error', 'Buku yang sudah melewati tanggal jatuh tempo tidak dapat diperpanjang secara online.');
        }

        $loan->tanggal_jatuh_tempo = Carbon::parse($loan->tanggal_jatuh_tempo)->addDays($durasiPinjam);
        $loan->jumlah_perpanjangan += 1;
        $loan->save();

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'aktivitas' => 'PERPANJANG_PEMINJAMAN',
            'deskripsi' => "Perpanjangan peminjaman {$loan->kode_peminjaman} hingga {$loan->tanggal_jatuh_tempo}",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Peminjaman berhasil diperpanjang 7 hari.');
    }

    public function riwayat()
    {
        $user = auth()->user();
        $history = Peminjaman::with(['buku', 'eksemplar', 'pengembalian'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('mahasiswa.riwayat', compact('history'));
    }

    public function reservasi()
    {
        $user = auth()->user();
        $reservations = Reservasi::with('buku')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('mahasiswa.reservasi', compact('reservations'));
    }

    public function buatReservasi(Request $request, $bukuId)
    {
        $user = auth()->user();
        $buku = Buku::findOrFail($bukuId);

        $existing = Reservasi::where('user_id', $user->id)
            ->where('buku_id', $bukuId)
            ->whereIn('status', ['menunggu', 'tersedia'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah melakukan reservasi untuk buku ini.');
        }

        $queuePosition = Reservasi::where('buku_id', $bukuId)
            ->where('status', 'menunggu')
            ->count() + 1;

        $kodeReservasi = 'RES-' . date('Ym') . '-' . Str::random(5);

        Reservasi::create([
            'kode_reservasi' => strtoupper($kodeReservasi),
            'user_id' => $user->id,
            'buku_id' => $buku->id,
            'posisi_antrean' => $queuePosition,
            'status' => 'menunggu',
            'tanggal_reservasi' => Carbon::now(),
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'aktivitas' => 'BUAT_RESERVASI',
            'deskripsi' => "Reservasi buku '{$buku->judul}' dibuat.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Reservasi berhasil dibuat. Posisi antrean Anda: ' . $queuePosition);
    }

    public function batalkanReservasi(Request $request, $id)
    {
        $user = auth()->user();
        $reservation = Reservasi::where('user_id', $user->id)->where('id', $id)->firstOrFail();
        $reservation->status = 'dibatalkan';
        $reservation->save();

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }

    public function denda()
    {
        $user = auth()->user();
        $fines = Denda::with(['peminjaman.buku', 'pembayaran'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $totalActive = $fines->where('status_pembayaran', 'belum_lunas')->sum('jumlah_denda');
        $totalPaid = $fines->where('status_pembayaran', 'lunas')->sum('jumlah_denda');

        return view('mahasiswa.denda', compact('fines', 'totalActive', 'totalPaid'));
    }

    public function kartuPerpustakaan()
    {
        $user = auth()->user();
        $anggota = Anggota::where('user_id', $user->id)->first();

        return view('mahasiswa.kartu', compact('user', 'anggota'));
    }

    public function notifikasi()
    {
        $user = auth()->user();
        $notifications = Notifikasi::where('user_id', $user->id)->latest()->get();
        Notifikasi::where('user_id', $user->id)->update(['dibaca' => true]);

        return view('mahasiswa.notifikasi', compact('notifications'));
    }

    public function profil()
    {
        $user = auth()->user();
        $anggota = Anggota::where('user_id', $user->id)->first();

        return view('mahasiswa.profil', compact('user', 'anggota'));
    }

    public function updateProfil(Request $request)
    {
        $user = auth()->user();
        $anggota = Anggota::where('user_id', $user->id)->first();
        $anggotaId = $anggota ? $anggota->id : 0;

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'nim' => 'nullable|string|max:50|unique:anggota,nim,' . $anggotaId,
            'program_studi' => 'nullable|string|max:150',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'nim.unique' => 'NISN/NIM ini sudah digunakan oleh siswa lain.',
            'foto.max' => 'Ukuran pas foto maksimal 2 MB.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        // Change password if provided
        if ($request->filled('password')) {
            if ($request->filled('current_password') && !\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Kata sandi saat ini tidak cocok.');
            }
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->save();

        if (!$anggota) {
            $nomorAnggota = 'LIB-' . date('Y') . '-' . str_pad($user->id, 3, '0', STR_PAD_LEFT);
            $anggota = Anggota::create([
                'user_id' => $user->id,
                'nomor_anggota' => $nomorAnggota,
                'nim' => $request->nim ?? ('102201' . $user->id),
                'program_studi' => $request->program_studi ?? 'Teknik Komputer & Jaringan',
                'status' => 'aktif',
            ]);
        } else {
            if ($request->filled('nim')) $anggota->nim = $request->nim;
            if ($request->filled('program_studi')) $anggota->program_studi = $request->program_studi;
        }

        // Upload Pas Foto Permanen
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('foto_anggota', 'public');
            $anggota->foto = $path;
        }

        $anggota->save();

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'aktivitas' => 'UPDATE_PROFIL',
            'deskripsi' => "Siswa memperbarui data profil & pas foto resmi.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Profil dan pas foto siswa berhasil diperbarui secara permanen.');
    }
}
