<?php

namespace Tests\Feature;

use App\Setting;
use App\User;
use Illuminate\Support\Facades\Hash;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use MigratesTestDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        Setting::set('site_name', 'PTA Test');
        Setting::set('address', 'Alamat Test');
        Setting::set('email', 'test@example.com');

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('PTA Test');
        $response->assertSee('Alamat Test');
        $response->assertSee('test@example.com');
    }

    public function test_existing_bcrypt_password_can_log_in_to_admin(): void
    {
        $password = 'local-test-password';
        $user = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'password' => Hash::make($password, ['rounds' => 10]),
        ]);

        $this->get('/admin')->assertRedirect(route('login'));
        $this->get('/login')->assertOk();
        $this->post('/login', ['email' => $user->email, 'password' => $password])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->get('/admin')->assertOk();
    }
}
