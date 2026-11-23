<?php

namespace Tests\Feature;

use App\ActivityLog;
use App\User;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class AdminSettingsAccessTest extends TestCase
{
    use MigratesTestDatabase;

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
            'instagram_url' => 'https://instagram.com/ptapapuabarat',
            'facebook_url' => 'https://facebook.com/ptapapuabarat',
            'youtube_url' => 'https://youtube.com/@ptapapuabarat',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertDatabaseHas('settings', ['key' => 'site_name', 'value' => 'PTA Papua Barat Baru']);
        $this->assertDatabaseHas('settings', ['key' => 'email', 'value' => 'layanan@example.com']);
        $this->assertDatabaseHas('settings', ['key' => 'instagram_url', 'value' => 'https://instagram.com/ptapapuabarat']);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'setting.updated',
        ]);
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
