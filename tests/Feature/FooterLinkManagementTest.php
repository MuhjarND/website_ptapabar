<?php

namespace Tests\Feature;

use App\FooterLink;
use App\User;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class FooterLinkManagementTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_admin_can_create_and_update_footer_link()
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.footer-links.store'), [
            'section' => FooterLink::SECTION_QUICK,
            'title' => 'Dokumen',
            'url' => '/dokumen',
            'order' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('admin.footer-links.index'));

        $link = FooterLink::where('title', 'Dokumen')->firstOrFail();

        $this->assertDatabaseHas('footer_links', [
            'id' => $link->id,
            'section' => FooterLink::SECTION_QUICK,
            'url' => '/dokumen',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'footer-link.created',
            'subject_id' => $link->id,
        ]);

        $this->actingAs($admin)->put(route('admin.footer-links.update', $link), [
            'section' => FooterLink::SECTION_EXTERNAL,
            'title' => 'Dokumen Eksternal',
            'url' => 'https://example.com/dokumen',
            'order' => 2,
        ])->assertRedirect(route('admin.footer-links.index'));

        $this->assertDatabaseHas('footer_links', [
            'id' => $link->id,
            'section' => FooterLink::SECTION_EXTERNAL,
            'title' => 'Dokumen Eksternal',
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'footer-link.updated',
            'subject_id' => $link->id,
        ]);
    }

    protected function makeAdmin()
    {
        return User::create([
            'name' => 'Admin Footer',
            'email' => 'admin-footer@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);
    }
}
