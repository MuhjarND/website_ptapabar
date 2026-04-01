<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AdminSettingsAccessTest extends TestCase
{
    use DatabaseMigrations;

    public function test_author_is_redirected_from_admin_only_settings_routes()
    {
        $author = $this->makeUser('author-settings@example.com', 'author');

        $this->actingAs($author)
            ->get(route('admin.settings.index'))
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($author)
            ->post(route('admin.settings.update'), [
                'site_name' => 'Tidak Boleh',
            ])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_update_settings()
    {
        $admin = $this->makeUser('admin-settings@example.com', 'admin');

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'site_name' => 'PTA Papua Barat Baru',
            'site_description' => 'Deskripsi situs baru',
            'address' => 'Jalan Pengadilan No. 1',
            'phone' => '08123456789',
            'email' => 'layanan@example.com',
            'fax' => '0987654321',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertDatabaseHas('settings', ['key' => 'site_name', 'value' => 'PTA Papua Barat Baru']);
        $this->assertDatabaseHas('settings', ['key' => 'email', 'value' => 'layanan@example.com']);
    }

    protected function makeUser($email, $role)
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => $role,
        ]);
    }
}
