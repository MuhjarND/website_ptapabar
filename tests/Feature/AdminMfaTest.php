<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class AdminMfaTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_password_alone_cannot_access_admin_and_a_consumed_code_cannot_be_reused(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'password' => Hash::make('test-password')]);
        $authenticator = new Google2FA;
        $secret = $authenticator->generateSecretKey();
        $admin->forceFill(['admin_mfa_secret' => $secret])->save();
        $this->assertNotSame($secret, $admin->getRawOriginal('admin_mfa_secret'));
        $credentials = ['email' => $admin->email, 'password' => 'test-password'];
        $dashboard = route('admin.dashboard');
        $challenge = route('admin-mfa.show');

        $this->post(route('login'), $credentials)->assertRedirect($dashboard);
        $this->get(route('admin.sinova.index'))->assertRedirect($challenge);
        $this->get($challenge)->assertOk()->assertSee('aplikasi autentikator')->assertDontSee($secret);
        $this->post($challenge, ['otp_code' => 'not-a-code'])->assertSessionHasErrors('otp_code');

        $code = $authenticator->getCurrentOtp($secret);
        $this->post($challenge, ['otp_code' => $code])->assertRedirect($dashboard);
        $this->get(route('admin.sinova.index'))->assertOk();
        $this->post(route('logout'))->assertRedirect();
        $this->post(route('login'), $credentials)->assertRedirect($dashboard);
        $this->post($challenge, ['otp_code' => $code])->assertSessionHasErrors('otp_code');
        $this->get(route('admin.sinova.index'))->assertRedirect($challenge);
    }

    public function test_mandatory_mfa_blocks_unenrolled_admins_but_does_not_apply_to_authors(): void
    {
        config(['auth.admin_mfa_required' => true]);
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]))
            ->get(route('admin.dashboard'))->assertRedirect(route('admin-mfa.show'));
        $this->get(route('admin-mfa.show'))->assertOk()->assertSee('belum didaftarkan');

        $this->actingAs(User::factory()->create(['role' => User::ROLE_AUTHOR]))
            ->get(route('admin-mfa.show'))->assertForbidden();
        $this->get(route('admin.berita.index'))->assertOk();
    }
}
