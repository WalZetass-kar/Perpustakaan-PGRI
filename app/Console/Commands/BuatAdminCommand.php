<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BuatAdminCommand extends Command
{
    protected $signature = 'perpus:buat-admin';
    protected $description = 'Buat akun Administrator / Petugas Perpustakaan baru secara interaktif';

    public function handle(): int
    {
        $this->info('=== PEMBUATAN AKUN ADMINISTRATOR PERPUSTAKAAN ===');

        $name = trim($this->ask('Nama Lengkap'));
        while (empty($name)) {
            $this->error('Nama tidak boleh kosong.');
            $name = trim($this->ask('Nama Lengkap'));
        }

        $email = trim($this->ask('Alamat Email'));
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email|unique:users,email'
        ]);

        while ($validator->fails()) {
            foreach ($validator->errors()->all() as $err) {
                $this->error($err);
            }
            $email = trim($this->ask('Alamat Email'));
            $validator = Validator::make(['email' => $email], [
                'email' => 'required|email|unique:users,email'
            ]);
        }

        $password = $this->secret('Password (minimal 8 karakter)');
        while (strlen($password) < 8) {
            $this->error('Password minimal 8 karakter.');
            $password = $this->secret('Password (minimal 8 karakter)');
        }

        $passwordConfirm = $this->secret('Konfirmasi Password');
        while ($password !== $passwordConfirm) {
            $this->error('Konfirmasi password tidak cocok.');
            $passwordConfirm = $this->secret('Konfirmasi Password');
        }

        $roleChoice = $this->choice(
            'Pilih Hak Akses / Role',
            ['Super Administrator', 'Admin Perpustakaan'],
            0
        );

        $roleName = ($roleChoice === 'Super Administrator') ? 'super_admin' : 'admin';
        $role = Role::firstOrCreate(
            ['name' => $roleName],
            ['display_name' => $roleChoice]
        );

        $phone = $this->ask('Nomor Telepon / WhatsApp (opsional)');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => $role->id,
            'phone' => !empty($phone) ? trim($phone) : null,
            'status' => 'active',
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'aktivitas' => 'BUAT_AKUN_CLI',
            'deskripsi' => "Akun {$roleChoice} dibuat via artisan command ({$user->email})",
            'ip_address' => '127.0.0.1',
        ]);

        $this->newLine();
        $this->info('Akun Administrator berhasil dibuat!');
        $this->line("Nama  : {$user->name}");
        $this->line("Email : {$user->email}");
        $this->line("Role  : {$roleChoice}");
        $this->line("Status: Aktif");
        $this->newLine();

        return Command::SUCCESS;
    }
}
