<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Rak;
use App\Models\Penulis;
use App\Models\Penerbit;
use App\Models\Pengaturan;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\AuditLog;
use Carbon\Carbon;

class PublicController extends Controller
{
    /** Kunci sesi berisi id pengajuan yang boleh dipantau peramban ini. */
    private const SESI_PENGAJUAN = 'pengajuan_dipantau';

    public function home()
    {
        try {
            $stats = [
                'total_koleksi'   => Buku::count(),
                'buku_tersedia'   => (int) Buku::sum('available_quantity'),
                'sedang_dipinjam' => (int) Peminjaman::where('status', 'dipinjam')->sum('jumlah'),
                'anggota_aktif'   => User::where('status', 'active')->count(),
            ];

            $buku_terbaru = Buku::with(['penulis', 'penerbit', 'kategori', 'rak', 'laci'])
                ->latest()
                ->take(6)
                ->get();

            $buku_populer = Buku::with(['penulis', 'penerbit', 'kategori', 'rak', 'laci'])
                ->orderBy('view_count', 'desc')
                ->take(6)
                ->get();

            $kategori_list = Kategori::orderBy('nama', 'asc')->get();
            $penulis_list = Penulis::orderBy('nama', 'asc')->get();
            $tahun_list = Buku::select('tahun_terbit')->whereNotNull('tahun_terbit')->distinct()->orderBy('tahun_terbit', 'desc')->pluck('tahun_terbit');

            $jam_operasional = Pengaturan::ambil('jam_operasional', 'Senin - Jumat: 07.00 - 15.30 WIB');
            $nama_perpustakaan = Pengaturan::ambil('nama_perpustakaan', 'Perpustakaan Sekolah');
        } catch (\Throwable $e) {
            $stats = [
                'total_koleksi'   => 0,
                'buku_tersedia'   => 0,
                'sedang_dipinjam' => 0,
                'anggota_aktif'   => 0,
            ];
            $buku_terbaru = collect();
            $buku_populer = collect();
            $kategori_list = collect();
            $penulis_list = collect();
            $tahun_list = collect();
            $jam_operasional = 'Senin - Jumat: 07.00 - 15.30 WIB';
            $nama_perpustakaan = 'Perpustakaan Sekolah';
        }

        return view('public.home', compact('stats', 'buku_terbaru', 'buku_populer', 'kategori_list', 'penulis_list', 'tahun_list', 'jam_operasional', 'nama_perpustakaan'));
    }

    public function katalog(Request $request)
    {
        try {
            $query = Buku::with(['penulis', 'penerbit', 'kategori', 'kelas', 'rak', 'laci'])->withAntreanPending();

            if ($request->filled('search')) {
                $search = substr(trim($request->search), 0, 100);
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

            if ($request->filled('kategori_id') && is_numeric($request->kategori_id)) {
                $query->where('kategori_id', (int) $request->kategori_id);
            }

            if ($request->filled('penulis_id') && is_numeric($request->penulis_id)) {
                $query->where('penulis_id', (int) $request->penulis_id);
            }

            if ($request->filled('rak_id') && is_numeric($request->rak_id)) {
                $query->where('rak_id', (int) $request->rak_id);
            }

            if ($request->filled('tahun') && is_numeric($request->tahun)) {
                $query->where('tahun_terbit', (int) $request->tahun);
            }

            if ($request->filled('status')) {
                $status = strtolower(trim($request->status));
                if ($status === 'tersedia') {
                    $query->where('available_quantity', '>', 0);
                } elseif ($status === 'dipinjam') {
                    $query->where('available_quantity', '<=', 0);
                }
            }

            $sort = strtolower(trim($request->get('sort', 'terbaru')));
            $validSorts = ['terbaru', 'terlama', 'judul_asc', 'judul_desc', 'populer'];
            if (!in_array($sort, $validSorts)) {
                $sort = 'terbaru';
            }

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

            // Setelan ini bisa diubah Super Admin lewat menu Pengaturan
            // ("Jumlah Koleksi Buku Per Halaman Katalog"). Sebelumnya nilainya
            // tersimpan tetapi tidak pernah dibaca siapa pun, sehingga
            // mengubahnya sama sekali tidak berpengaruh di katalog.
            // Batasnya disamakan dengan aturan validasi di pengaturanUpdate.
            $perHalaman = (int) Pengaturan::ambil('buku_per_halaman', 12);
            $perHalaman = $perHalaman > 0 ? max(4, min(100, $perHalaman)) : 12;

            $buku = $query->paginate($perHalaman)->withQueryString();

            $total_buku_count = Buku::count();
            $total_kategori_count = Kategori::count();
            $total_rak_count = Rak::count();

            $kategori_list = Kategori::orderBy('nama', 'asc')->get();
            $penulis_list = Penulis::orderBy('nama', 'asc')->get();
            $rak_list = Rak::with('laci')->orderBy('kode_rak', 'asc')->get();
            $tahun_list = Buku::select('tahun_terbit')->whereNotNull('tahun_terbit')->distinct()->orderBy('tahun_terbit', 'desc')->pluck('tahun_terbit');
        } catch (\Throwable $e) {
            $buku = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12);
            $total_buku_count = 0;
            $total_kategori_count = 0;
            $total_rak_count = 0;
            $kategori_list = collect();
            $penulis_list = collect();
            $rak_list = collect();
            $tahun_list = collect();
        }

        return view('public.katalog', compact('buku', 'kategori_list', 'penulis_list', 'rak_list', 'tahun_list', 'total_buku_count', 'total_kategori_count', 'total_rak_count'));
    }

    public function detailBuku($id)
    {
        $buku = Buku::with(['penulis', 'penerbit', 'kategori', 'kelas', 'rak.laci', 'laci'])->withAntreanPending()->findOrFail((int) $id);
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

        $allRaks = Rak::with('laci')->orderBy('kode_rak', 'asc')->get();

        return view('public.detail-buku', compact('buku', 'userLoan', 'relatedBooks', 'allRaks'));
    }

    public function searchSuggestions(Request $request)
    {
        $q = substr(trim($request->get('q', '')), 0, 100);
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
                    // Saran pencarian dirender kecil (w-10 h-14), cukup varian thumb.
                    'cover_url'          => $buku->cover_thumb_url,
                    'detail_url'         => route('buku.detail', $buku->id),
                ];
            });

        return response()->json($bukuList);
    }

    public function ajukanPeminjaman(Request $request)
    {
        $validated = $request->validate([
            'buku_id'       => 'required|exists:buku,id',
            'nama_peminjam' => 'required|string|max:255',
            'jurusan'       => 'required|string|max:150',
            'nomor_induk'   => 'nullable|string|max:50',
            'no_wa'         => 'required|string|max:30',
            'catatan'       => 'nullable|string|max:500',
            'jumlah'        => 'nullable|integer|min:1',
        ], [
            'no_wa.required' => 'Nomor WhatsApp wajib diisi agar petugas dapat menghubungi Anda.',
            'no_wa.max'      => 'Nomor WhatsApp terlalu panjang (maksimal 30 karakter).',
        ]);

        $buku = Buku::findOrFail($validated['buku_id']);

        $jumlah = isset($validated['jumlah']) ? (int) $validated['jumlah'] : 1;
        $nomorInduk = !empty($validated['nomor_induk']) ? trim($validated['nomor_induk']) : null;
        $namaPeminjam = trim($validated['nama_peminjam']);

        $gagal = function (string $pesan) use ($request) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $pesan], 422);
            }
            return back()->with('error', $pesan);
        };

        $durasiHari = (int) Pengaturan::ambil('durasi_pinjam_hari', 7);
        $today = Carbon::today()->toDateString();
        $due = Carbon::today()->addDays($durasiHari)->toDateString();

        // Seluruh pemeriksaan dan penyimpanannya dijadikan satu langkah yang
        // tidak bisa disela. Memeriksa antrean lalu menyimpan sebagai dua
        // langkah terpisah masih bocor: dua siswa dari dua komputer bisa
        // sama-sama membaca "sisa 10" sebelum salah satunya sempat tersimpan,
        // lalu keduanya lolos.
        //
        // Yang dikunci baris bukunya, walau pengajuan `pending` tidak mengubah
        // stok sama sekali. Baris itu dipakai sebagai titik antre: pengajuan
        // atas buku yang sama jadi terlayani satu per satu, sementara pengajuan
        // untuk buku lain tetap jalan tanpa saling menunggu.
        try {
            $loan = DB::transaction(function () use ($buku, $validated, $jumlah, $nomorInduk, $namaPeminjam, $today, $due) {
                $lockedBook = Buku::where('id', $buku->id)->lockForUpdate()->firstOrFail();

                if ((int) $lockedBook->available_quantity <= 0) {
                    throw new \RuntimeException('HABIS');
                }

                // Pengajuan kembar: siswa yang menekan tombol dua kali, atau
                // membuka katalog dari dua komputer sekaligus. Penyaring lama
                // hanya bekerja kalau nomor induk diisi, padahal isian itu
                // opsional — tanpa nomor induk, lima klik jadi lima pengajuan.
                $kembar = Peminjaman::where('buku_id', $lockedBook->id)
                    ->where('status', 'pending')
                    ->when(
                        $nomorInduk !== null,
                        fn ($q) => $q->where('nomor_induk', $nomorInduk),
                        fn ($q) => $q->whereNull('nomor_induk')->where('nama_peminjam', $namaPeminjam)
                    )
                    ->exists();

                if ($kembar) {
                    throw new \RuntimeException('KEMBAR');
                }

                // Stok fisik dikurangi antrean yang belum diproses petugas.
                $antrean = (int) Peminjaman::where('buku_id', $lockedBook->id)
                    ->where('status', 'pending')
                    ->sum('jumlah');

                $sisa = max(0, (int) $lockedBook->available_quantity - $antrean);

                if ($sisa <= 0) {
                    throw new \RuntimeException('ANTREAN_PENUH');
                }

                if ($jumlah > $sisa) {
                    throw new \RuntimeException('MELEBIHI:' . $sisa);
                }

                return Peminjaman::create([
                    'kode_peminjaman'     => Peminjaman::buatKode('REQ'),
                    'sumber'              => 'opac',
                    'nama_peminjam'       => $namaPeminjam,
                    'jurusan'             => trim($validated['jurusan']),
                    'nomor_induk'         => $nomorInduk,
                    'no_wa'               => trim($validated['no_wa']),
                    'user_id'             => auth()->id(),
                    'buku_id'             => $lockedBook->id,
                    'jumlah'              => $jumlah,
                    'tanggal_pinjam'      => $today,
                    'tanggal_jatuh_tempo' => $due,
                    'status'              => 'pending',
                    'catatan'             => !empty($validated['catatan']) ? trim($validated['catatan']) : null,
                ]);
            });
        } catch (\RuntimeException $e) {
            $sebab = $e->getMessage();

            if ($sebab === 'HABIS') {
                return $gagal('Maaf, seluruh stok buku ini sedang habis dipinjam.');
            }
            if ($sebab === 'KEMBAR') {
                return $gagal('Anda sudah memiliki pengajuan peminjaman yang sedang menunggu konfirmasi untuk buku ini.');
            }
            if ($sebab === 'ANTREAN_PENUH') {
                return $gagal('Seluruh eksemplar yang tersisa sudah diantre siswa lain dan sedang menunggu konfirmasi petugas. Silakan coba lagi nanti.');
            }
            if (str_starts_with($sebab, 'MELEBIHI:')) {
                $sisa = (int) substr($sebab, strlen('MELEBIHI:'));
                return $gagal("Jumlah yang diminta melebihi eksemplar yang masih bisa diantre. Saat ini hanya tersisa {$sisa} eksemplar.");
            }

            report($e);
            return $gagal('Pengajuan gagal disimpan karena gangguan sistem. Silakan coba lagi sebentar.');
        } catch (\Throwable $e) {
            report($e);
            return $gagal('Pengajuan gagal disimpan karena gangguan sistem. Silakan coba lagi sebentar.');
        }

        $requestCode = $loan->kode_peminjaman;

        $this->ingatPengajuan($request, $loan->id);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => $loan->nama_peminjam,
            'aktivitas'  => 'PENGAJUAN_PINJAM_OPAC',
            'deskripsi'  => "Siswa mengajukan peminjaman buku: '{$buku->judul}' ({$loan->kode_peminjaman})",
            'ip_address' => $request->ip(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'     => true,
                // `id` dipakai halaman untuk memantau keputusan petugas. Aman
                // dikirim apa adanya karena alamat pemantauannya hanya melayani
                // pengajuan yang tercatat di sesi peramban pengaju sendiri.
                'id'          => $loan->id,
                'kode'        => $requestCode,
                'judul_buku'  => $buku->judul,
                'message'     => 'Pengajuan peminjaman berhasil dikirim. Silakan tunggu konfirmasi petugas perpustakaan.'
            ]);
        }

        return back()->with('success', "Pengajuan peminjaman untuk buku '{$buku->judul}' berhasil dikirim! Kode Referensi: {$requestCode}");
    }

    /**
     * Catat pengajuan yang baru dikirim pada sesi peramban pengaju.
     *
     * Daftar inilah satu-satunya kunci ke halaman status: tanpa tercatat di
     * sini, sebuah id pengajuan tidak bisa dibaca siapa pun. Dengan begitu
     * tidak ada alamat yang bisa ditebak orang lain untuk mengintip pengajuan
     * siswa lain — berbeda dengan memakai kode pengajuan sebagai kunci.
     */
    private function ingatPengajuan(Request $request, int $id): void
    {
        $daftar = array_values(array_unique(array_merge(
            array_map('intval', (array) $request->session()->get(self::SESI_PENGAJUAN, [])),
            [$id]
        )));

        // Hanya sebagian terakhir yang disimpan: sesi berbasis cookie punya
        // batas ukuran, dan siswa hanya perlu memantau pengajuan yang baru
        // saja dikirim, bukan seluruh riwayatnya.
        $request->session()->put(self::SESI_PENGAJUAN, array_slice($daftar, -20));
    }

    /**
     * Keputusan petugas atas sebuah pengajuan, untuk dipantau halaman katalog
     * selama siswa masih menunggu.
     *
     * Yang dikembalikan sengaja seminim mungkin — status, kode, judul buku,
     * dan alasan penolakan — tanpa identitas pengaju, supaya isinya tetap
     * tidak berarti apa-apa seandainya sesi seseorang berpindah tangan.
     */
    public function statusPengajuan(Request $request, $id)
    {
        $id = (int) $id;
        $dipantau = array_map('intval', (array) $request->session()->get(self::SESI_PENGAJUAN, []));

        if (!in_array($id, $dipantau, true)) {
            return response()->json(['message' => 'Pengajuan tidak ditemukan.'], 404);
        }

        $pengajuan = Peminjaman::with('buku')->find($id);

        if (!$pengajuan) {
            return response()->json(['message' => 'Pengajuan tidak ditemukan.'], 404);
        }

        return response()->json([
            'status'           => $pengajuan->status,
            'kode'             => $pengajuan->kode_peminjaman,
            'judul_buku'       => $pengajuan->buku->judul ?? null,
            'jumlah'           => (int) $pengajuan->jumlah,
            'jatuh_tempo'      => $pengajuan->tanggal_jatuh_tempo
                ? Carbon::parse($pengajuan->tanggal_jatuh_tempo)->translatedFormat('d F Y')
                : null,
            // Alasan hanya berarti pada pengajuan yang benar-benar ditolak.
            'alasan_penolakan' => $pengajuan->status === 'ditolak'
                ? $pengajuan->alasan_penolakan
                : null,
        ]);
    }
}
