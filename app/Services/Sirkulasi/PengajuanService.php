<?php

namespace App\Services\Sirkulasi;

use App\Exceptions\AturanBisnisException;
use App\Models\AuditLog;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Pengajuan peminjaman yang masuk dari katalog (OPAC) dan menunggu keputusan
 * petugas.
 *
 * Menyetujui dan menolak sama-sama menulis dengan syarat `status = pending`
 * yang menempel pada UPDATE-nya. Syarat itulah yang menjaga kebenaran ketika
 * dua petugas menekan "Setujui" dan "Tolak" pada saat yang hampir bersamaan —
 * bukan sekadar hasil pembacaan sebelumnya.
 */
class PengajuanService
{
    /** Status yang boleh dipakai sebagai tab penyaring di halaman pengajuan. */
    private const STATUS_VALID = ['pending', 'dipinjam', 'ditolak', 'all'];

    /**
     * Bersihkan pilihan tab dari query string; apa pun yang tidak dikenal
     * dianggap 'pending'.
     */
    public function statusValid(?string $status): string
    {
        $status = strtolower(trim((string) $status));

        return in_array($status, self::STATUS_VALID) ? $status : 'pending';
    }

    public function daftar(string $status, ?string $cari = null)
    {
        $query = Peminjaman::with(['user', 'buku.rak', 'buku.laci', 'petugas']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if (filled($cari)) {
            // Dipotong 100 karakter: kotak pencarian ini terbuka untuk siapa
            // pun yang bisa membuka halaman, dan LIKE dengan pola sangat
            // panjang membebani database tanpa memberi hasil yang berguna.
            $cari = substr(trim($cari), 0, 100);
            $query->where(function ($q) use ($cari) {
                $q->where('kode_peminjaman', 'like', "%{$cari}%")
                  ->orWhere('nama_peminjam', 'like', "%{$cari}%")
                  ->orWhere('jurusan', 'like', "%{$cari}%")
                  ->orWhere('nomor_induk', 'like', "%{$cari}%")
                  ->orWhere('no_wa', 'like', "%{$cari}%")
                  ->orWhereHas('buku', function ($qb) use ($cari) {
                      $qb->where('judul', 'like', "%{$cari}%");
                  });
            });
        }

        return $query->latest('created_at')->paginate(15)->withQueryString();
    }

    /**
     * Jumlah pengajuan per status, untuk angka pada tab.
     */
    public function jumlahPerStatus(): array
    {
        return [
            'pending'  => Peminjaman::where('status', 'pending')->count(),
            'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'ditolak'  => Peminjaman::where('status', 'ditolak')->count(),
            'all'      => Peminjaman::count(),
        ];
    }

    /**
     * Setujui pengajuan: stok dipotong dan transaksinya mulai berjalan.
     *
     * @throws AturanBisnisException bila sudah diproses petugas lain atau stok kurang
     */
    public function setujui(int $id): Peminjaman
    {
        $peminjaman = DB::transaction(function () use ($id) {
            $terkunci = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($terkunci->status !== 'pending') {
                throw new AturanBisnisException('Pengajuan ini sudah diproses sebelumnya.');
            }

            $buku = Buku::where('id', $terkunci->buku_id)->lockForUpdate()->firstOrFail();
            $jumlah = max(1, (int) $terkunci->jumlah);

            if ($buku->available_quantity < $jumlah) {
                throw new AturanBisnisException('Stok fisik buku tidak mencukupi untuk menyetujui peminjaman.');
            }

            $buku->available_quantity -= $jumlah;
            $buku->save();

            $durasiHari = (int) Pengaturan::ambil('durasi_pinjam_hari', 7);

            // Bersyarat, dengan alasan yang sama seperti pada penolakan:
            // kalau petugas lain sudah lebih dulu memproses pengajuan ini,
            // UPDATE-nya tidak mengenai baris apa pun dan seluruh transaksi
            // — termasuk pemotongan stok di atas — ikut dibatalkan.
            $terubah = Peminjaman::where('id', $terkunci->id)
                ->where('status', 'pending')
                ->update([
                    'status'              => 'dipinjam',
                    'kode_peminjaman'     => Peminjaman::buatKode('PJ'),
                    'tanggal_pinjam'      => Carbon::today()->toDateString(),
                    'tanggal_jatuh_tempo' => Carbon::today()->addDays($durasiHari)->toDateString(),
                    'petugas_id'          => auth()->id(),
                ]);

            if ($terubah === 0) {
                throw new AturanBisnisException('Pengajuan ini sudah diproses sebelumnya.');
            }

            return $terkunci->refresh();
        });

        AuditLog::catat('APPROVE_REQUEST_PINJAM', "Menyetujui pengajuan peminjaman untuk {$peminjaman->nama_peminjam} (Kode: {$peminjaman->kode_peminjaman})");

        return $peminjaman;
    }

    /**
     * Tolak pengajuan, dengan alasan yang bisa dibaca kembali oleh pengaju.
     *
     * Penolakan harus mengunci baris pengajuannya dulu. Tanpa kunci, dua
     * petugas di dua komputer yang menekan "Setujui" dan "Tolak" bersamaan
     * bisa membuat penolakan menimpa persetujuan yang sudah mengurangi stok —
     * dan stok itu tidak akan pernah kembali, karena pengajuan berstatus
     * `ditolak` tidak muncul di halaman pengembalian.
     *
     * @throws AturanBisnisException bila pengajuannya tidak lagi berstatus pending
     */
    public function tolak(int $id, ?string $alasan = null): Peminjaman
    {
        $alasan = filled($alasan)
            ? trim($alasan)
            : 'Permintaan tidak dapat diproses oleh petugas perpustakaan.';

        $peminjaman = DB::transaction(function () use ($id, $alasan) {
            $terkunci = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($terkunci->status !== 'pending') {
                throw new AturanBisnisException('Hanya pengajuan dengan status pending yang dapat ditolak.');
            }

            // Penulisannya bersyarat: baris hanya berubah kalau statusnya
            // MASIH `pending` pada saat UPDATE dijalankan. Syarat inilah
            // yang menjaga kebenaran, bukan hasil pembacaan di atas —
            // sehingga tetap benar walau kunci baris tidak tersedia
            // (tabel non-InnoDB, atau MySQL yang dikonfigurasi lain).
            $terubah = Peminjaman::where('id', $terkunci->id)
                ->where('status', 'pending')
                ->update([
                    'status'           => 'ditolak',
                    'alasan_penolakan' => $alasan,
                    'petugas_id'       => auth()->id(),
                ]);

            if ($terubah === 0) {
                throw new AturanBisnisException('Hanya pengajuan dengan status pending yang dapat ditolak.');
            }

            return $terkunci->refresh();
        });

        AuditLog::catat('REJECT_REQUEST_PINJAM', "Menolak pengajuan peminjaman {$peminjaman->kode_peminjaman} untuk {$peminjaman->nama_peminjam}");

        return $peminjaman;
    }
}
