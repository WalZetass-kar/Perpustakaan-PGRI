<?php

namespace App\Services\Pengguna;

use App\Exceptions\AturanBisnisException;
use App\Models\AuditLog;
use App\Models\Peminjaman;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Akun pengelola perpustakaan.
 *
 * Dua akun tidak boleh dikunci keluar dari sistem: akun id 1 sebagai jaring
 * pengaman terakhir, dan akun petugas yang sedang login itu sendiri. Aturan
 * itu dijaga di setiap jalur perubahan — ubah data, ubah status, dan hapus.
 */
class AnggotaService
{
    /** Akun bawaan sistem; peran dan statusnya tidak boleh disentuh siapa pun. */
    private const ID_SUPER_ADMIN_UTAMA = 1;

    public function temukan(int $id): User
    {
        return User::findOrFail($id);
    }

    public function daftar(?string $cari = null)
    {
        $query = User::with('role');

        if (filled($cari)) {
            $cari = trim($cari);
            $query->where(function ($q) use ($cari) {
                $q->where('name', 'like', "%{$cari}%")
                  ->orWhere('email', 'like', "%{$cari}%")
                  ->orWhere('phone', 'like', "%{$cari}%");
            });
        }

        return $query->latest()->paginate(10)->withQueryString();
    }

    public function peranTersedia()
    {
        return Role::all();
    }

    public function simpan(array $data): User
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'  => $data['role_id'],
            'phone'    => $data['phone'] ?? null,
            'status'   => $data['status'],
        ]);

        AuditLog::catat('TAMBAH_ADMIN', "Menambahkan akun pengelola baru: '{$user->name}' ({$user->email})");

        return $user;
    }

    /**
     * Perbarui data akun. Nama, email, dan telepon selalu boleh diubah; peran
     * dan status hanya untuk akun orang lain yang bukan akun bawaan sistem.
     *
     * @throws AturanBisnisException bila petugas mencoba mengubah peran atau
     *                               status akunnya sendiri
     */
    public function perbarui(User $user, array $data): User
    {
        // Menurunkan pangkat atau menonaktifkan diri sendiri langsung mengunci
        // petugas keluar, dan tidak ada jalan kembali lewat antarmuka karena
        // menu pemulihannya justru butuh peran yang barusan dilepas.
        $akunSendiri = $user->id === auth()->id();
        $bolehUbahPeranDanStatus = $user->id !== self::ID_SUPER_ADMIN_UTAMA && !$akunSendiri;

        if ($akunSendiri && $this->mintaUbahPeranAtauStatus($user, $data)) {
            throw new AturanBisnisException('Peran dan status akun Anda sendiri tidak dapat diubah dari sini, agar Anda tidak terkunci keluar dari sistem. Mintalah Super Administrator lain untuk melakukannya.');
        }

        $kolom = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ];

        if ($bolehUbahPeranDanStatus) {
            if (filled($data['role_id'] ?? null)) {
                $kolom['role_id'] = $data['role_id'];
            }
            if (filled($data['status'] ?? null)) {
                $kolom['status'] = $data['status'];
            }
        }

        $user->update($kolom);

        AuditLog::catat('UPDATE_ADMIN', "Memperbarui data akun pengelola: '{$user->name}'");

        return $user;
    }

    public function resetPassword(User $user, string $password): User
    {
        $user->update(['password' => Hash::make($password)]);

        AuditLog::catat('RESET_PASSWORD_ADMIN', "Mereset password untuk akun admin: '{$user->name}' ({$user->email})");

        return $user;
    }

    /**
     * Aktifkan atau blokir sebuah akun.
     *
     * @return array{status: string, teks: string}
     * @throws AturanBisnisException bila sasarannya akun bawaan atau akun sendiri
     */
    public function ubahStatus(User $user): array
    {
        $this->pastikanBukanAkunTerlindungi($user, 'Akun Super Admin Utama atau akun Anda sendiri tidak dapat dinonaktifkan.');

        $statusBaru = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $statusBaru]);

        AuditLog::catat('TOGGLE_STATUS_ADMIN', "Status akun admin '{$user->name}' diubah menjadi {$statusBaru}");

        return [
            'status' => $statusBaru,
            'teks'   => $statusBaru === 'active' ? 'diaktifkan' : 'dinonaktifkan / diblokir',
        ];
    }

    /**
     * Hapus akun pengelola.
     *
     * @return string nama akun yang dihapus
     * @throws AturanBisnisException bila akunnya terlindungi atau masih
     *                               terkait peminjaman berjalan
     */
    public function hapus(User $user): string
    {
        $this->pastikanBukanAkunTerlindungi($user, 'Akun Super Admin Utama atau akun Anda sendiri tidak dapat dihapus.');

        // Transaksi yang dicatat lewat meja sirkulasi menyimpan id petugasnya di
        // user_id, jadi hitungan ini mencakup peminjaman yang ia catatkan untuk
        // siswa — bukan hanya buku yang ia pinjam sendiri.
        $peminjamanBerjalan = Peminjaman::where('user_id', $user->id)->where('status', 'dipinjam')->count();
        if ($peminjamanBerjalan > 0) {
            throw new AturanBisnisException("Akun tidak dapat dihapus karena masih terkait {$peminjamanBerjalan} peminjaman yang belum dikembalikan. Selesaikan pengembaliannya lebih dulu.");
        }

        $nama = $user->name;
        $user->delete();

        AuditLog::catat('HAPUS_ADMIN', "Menghapus akun admin: '{$nama}'");

        return $nama;
    }

    /**
     * Ganti password akun yang sedang dipakai.
     *
     * Password lama sengaja tidak diminta: pengguna sudah terbukti memegang
     * akun ini karena sedang login, jadi menanyakannya lagi hanya menambah
     * langkah tanpa menambah keamanan yang berarti.
     *
     * Risiko yang diterima secara sadar: sesi yang ditinggalkan terbuka di
     * komputer bersama bisa dipakai orang lain untuk mengganti password.
     * Penggantinya adalah jejak di audit log di bawah.
     */
    public function ubahPasswordSendiri(string $password): User
    {
        $user = auth()->user();
        $user->update(['password' => Hash::make($password)]);

        AuditLog::catat('UBAH_PASSWORD_MANDIRI', "Pengguna memperbarui password akunnya sendiri ({$user->email})");

        return $user;
    }

    private function mintaUbahPeranAtauStatus(User $user, array $data): bool
    {
        $peranBerubah = filled($data['role_id'] ?? null) && (int) $data['role_id'] !== (int) $user->role_id;
        $statusBerubah = filled($data['status'] ?? null) && $data['status'] !== $user->status;

        return $peranBerubah || $statusBerubah;
    }

    private function pastikanBukanAkunTerlindungi(User $user, string $pesan): void
    {
        if ($user->id === self::ID_SUPER_ADMIN_UTAMA || $user->id === auth()->id()) {
            throw new AturanBisnisException($pesan);
        }
    }
}
