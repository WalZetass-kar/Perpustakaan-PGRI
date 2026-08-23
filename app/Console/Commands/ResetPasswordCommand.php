<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;

class ResetPasswordCommand extends Command
{
    protected $signature = 'perpus:reset-password {email? : Alamat email akun yang ingin di-reset}';
    protected $description = 'Reset password akun pengguna secara darurat via command line';

    public function handle(): int
    {
        $email = $this->argument('email');
        if (empty($email)) {
            $email = trim($this->ask('Masukkan alamat email akun yang akan di-reset'));
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Akun dengan email '{$email}' tidak ditemukan.");
            return Command::FAILURE;
        }

        $this->line("Akun ditemukan: {$user->name} ({$user->email})");

        $password = $this->secret('Masukkan Password Baru (minimal 8 karakter)');
        while (strlen($password) < 8) {
            $this->error('Password minimal 8 karakter.');
            $password = $this->secret('Masukkan Password Baru (minimal 8 karakter)');
        }

        $passwordConfirm = $this->secret('Konfirmasi Password Baru');
        while ($password !== $passwordConfirm) {
            $this->error('Konfirmasi password tidak cocok.');
            $passwordConfirm = $this->secret('Konfirmasi Password Baru');
        }

        $user->update([
            'password' => Hash::make($password),
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'aktivitas' => 'RESET_PASSWORD_CLI',
            'deskripsi' => "Password akun di-reset secara darurat via CLI ({$user->email})",
            'ip_address' => '127.0.0.1',
        ]);

        $this->newLine();
        $this->info("Password untuk akun '{$user->email}' berhasil diperbarui!");
        $this->newLine();

        return Command::SUCCESS;
    }
}
