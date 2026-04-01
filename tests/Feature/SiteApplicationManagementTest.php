<?php

namespace Tests\Feature;

use App\SiteApplication;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class SiteApplicationManagementTest extends TestCase
{
    use DatabaseMigrations;

    public function test_admin_can_create_and_update_site_application()
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.site-applications.store'), [
            'title' => 'Sistem Inovasi PTA',
            'description' => 'Portal inovasi daerah',
            'url' => 'https://example.com/inovasi',
            'group_type' => SiteApplication::GROUP_PTA_INOVASI,
            'order' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('admin.site-applications.index'));

        $application = SiteApplication::where('title', 'Sistem Inovasi PTA')->firstOrFail();

        $this->assertDatabaseHas('site_applications', [
            'id' => $application->id,
            'group_type' => SiteApplication::GROUP_PTA_INOVASI,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.site-applications.update', $application), [
            'title' => 'Sistem Inovasi PTA Terpadu',
            'description' => 'Portal inovasi terbaru',
            'url' => 'https://example.com/inovasi-terpadu',
            'group_type' => SiteApplication::GROUP_MAHKAMAH_AGUNG,
            'order' => 2,
        ])->assertRedirect(route('admin.site-applications.index'));

        $this->assertDatabaseHas('site_applications', [
            'id' => $application->id,
            'title' => 'Sistem Inovasi PTA Terpadu',
            'group_type' => SiteApplication::GROUP_MAHKAMAH_AGUNG,
            'is_active' => false,
        ]);
    }

    protected function makeAdmin()
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin-aplikasi@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }
}
