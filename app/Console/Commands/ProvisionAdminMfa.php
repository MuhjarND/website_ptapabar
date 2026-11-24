<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use PragmaRX\Google2FA\Google2FA;

class ProvisionAdminMfa extends Command
{
    protected $signature = 'admin:mfa {email : Email akun admin} {--reset : Ganti kunci MFA yang sudah terdaftar}';
    protected $description = 'Daftarkan autentikator TOTP admin secara langsung dari terminal hosting';

    public function handle(Google2FA $authenticator): int
    {
        $user = User::where('email', $this->argument('email'))->where('role', User::ROLE_ADMIN)->first();
        if (!$user || ($user->adminMfaVersion() && !$this->option('reset'))) {
            $this->error('Akun bukan admin atau sudah terdaftar. Gunakan --reset hanya untuk mengganti kunci.');
            return self::FAILURE;
        }

        $secret = $authenticator->generateSecretKey();
        $user->forceFill(['admin_mfa_secret' => $secret, 'admin_mfa_step' => null])->save();
        $this->info('Masukkan kunci ke akun berbasis waktu di autentikator (6 digit, 30 detik).');
        $this->line($secret);
        $this->warn('Kunci hanya ditampilkan sekarang. Jangan masukkan ke log, tiket, atau chat.');

        return self::SUCCESS;
    }
}
