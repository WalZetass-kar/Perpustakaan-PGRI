<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;
    protected $table = 'peminjaman';
    
    protected $fillable = [
        'kode_peminjaman',
        'sumber',
        'nama_peminjam',
        'jurusan',
        'nomor_induk',
        'no_wa',
        'user_id',
        'buku_id',
        'jumlah',
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'waktu_kembali',
        'jumlah_perpanjangan',
        'status',
        'catatan',
        'alasan_penolakan',
        'petugas_id',
    ];

    /**
     * Kode peminjaman yang dipastikan belum terpakai.
     *
     * `kode_peminjaman` ber-index unique, jadi kode kembar bukan sekadar
     * membingungkan — ia melempar QueryException, dan di jalur OPAC yang
     * tidak membungkus create-nya, itu berubah jadi layar error 500 di depan
     * siswa. Bentuk lama `strtoupper(Str::random(4))` juga melebur huruf besar
     * dan kecil, sehingga ruang kodenya jauh lebih sempit dari yang terlihat:
     * hanya 4 karakter dari 36, dengan sebaran yang tidak rata.
     */
    public static function buatKode(string $prefix): string
    {
        for ($i = 0; $i < 10; $i++) {
            $kode = $prefix . '-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));

            if (!static::where('kode_peminjaman', $kode)->exists()) {
                return $kode;
            }
        }

        // Sepuluh kali bentrok beruntun praktis mustahil. Kalau sampai terjadi,
        // pakai yang tidak mungkin kembar daripada mengembalikan kode dobel.
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(5)));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function getBorrowerDisplayNameAttribute()
    {
        return $this->nama_peminjam ?: ($this->user->name ?? 'Siswa / Anggota');
    }

    /**
     * Nomor WhatsApp dalam bentuk yang bisa dibuka wa.me: `08xx` menjadi
     * `628xx`, tanpa spasi atau tanda hubung. Mengembalikan null kalau yang
     * terisi jelas bukan nomor telepon.
     */
    public function getNomorWaInternasionalAttribute(): ?string
    {
        $digit = preg_replace('/\D/', '', (string) $this->no_wa);

        if (strlen($digit) < 8) {
            return null;
        }

        if (str_starts_with($digit, '0')) {
            return '62' . substr($digit, 1);
        }

        if (str_starts_with($digit, '62')) {
            return $digit;
        }

        if (str_starts_with($digit, '8')) {
            return '62' . $digit;
        }

        return $digit;
    }

    /**
     * Ringkasan lengkap satu peminjaman untuk ditampilkan di modal "Detail"
     * halaman sirkulasi. Dikumpulkan di sini, bukan di Blade, supaya tabel
     * dan kartu mobile memakai data yang persis sama.
     */
    public function getDataDetailAttribute(): array
    {
        $jatuhTempo = $this->tanggal_jatuh_tempo
            ? \Carbon\Carbon::parse($this->tanggal_jatuh_tempo)
            : null;

        return [
            'kode'            => $this->kode_peminjaman,
            'dari_opac'       => $this->isDariOpac(),
            'nama'            => $this->borrower_display_name,
            'jurusan'         => $this->jurusan ?: null,
            'nomor_induk'     => $this->nomor_induk ?: null,
            'no_wa'           => $this->no_wa ?: null,
            'wa_link'         => $this->nomor_wa_internasional
                ? 'https://wa.me/' . $this->nomor_wa_internasional
                : null,
            'catatan'         => $this->catatan ?: null,
            'buku'            => $this->buku->judul ?? null,
            'penulis'         => $this->buku->penulis->nama ?? null,
            'jumlah'          => (int) $this->jumlah,
            'tanggal_pinjam'  => $this->tanggal_pinjam
                ? \Carbon\Carbon::parse($this->tanggal_pinjam)->format('d M Y')
                : null,
            'jatuh_tempo'     => $jatuhTempo?->format('d M Y'),
            'terlambat'       => $this->isOverdue(),
            'hari_terlambat'  => $this->isOverdue()
                ? (int) $jatuhTempo->diffInDays(\Carbon\Carbon::today())
                : 0,
            'petugas'         => $this->petugas->name ?? null,
            'diajukan_pada'   => $this->created_at?->format('d M Y, H:i'),
        ];
    }

    /** Peminjaman ini diajukan sendiri oleh siswa lewat katalog OPAC. */
    public function isDariOpac(): bool
    {
        return $this->sumber === 'opac';
    }

    public function isOverdue(): bool
    {
        if ($this->status !== 'dipinjam' || empty($this->tanggal_jatuh_tempo)) {
            return false;
        }
        return \Carbon\Carbon::parse($this->tanggal_jatuh_tempo)->lt(\Carbon\Carbon::today());
    }
}
