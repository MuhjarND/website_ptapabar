<?php

namespace Tests\Feature;

use App\ActivityLog;
use App\FooterLink;
use App\Page;
use App\Post;
use App\ServiceRequest;
use App\TransparencyDocument;
use App\User;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_admin_dashboard_shows_workflow_service_requests_and_activities()
    {
        $admin = User::create([
            'name' => 'Admin Dashboard',
            'email' => 'admin-dashboard@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        Post::create([
            'title' => 'Post Review',
            'slug' => 'post-review',
            'content' => 'Konten review',
            'excerpt' => 'Ringkasan review',
            'category' => 'berita',
            'workflow_status' => Post::STATUS_REVIEW,
            'is_published' => false,
            'user_id' => $admin->id,
        ]);

        Post::create([
            'title' => 'Post Published',
            'slug' => 'post-published',
            'content' => 'Konten publish',
            'excerpt' => 'Ringkasan publish',
            'category' => 'berita',
            'workflow_status' => Post::STATUS_PUBLISHED,
            'published_at' => now(),
            'is_published' => true,
            'user_id' => $admin->id,
        ]);

        ServiceRequest::create([
            'request_type' => ServiceRequest::TYPE_ASPIRASI,
            'name' => 'Pemohon Dashboard',
            'email' => 'pemohon-dashboard@example.com',
            'subject' => 'Aspirasi dashboard',
            'message' => 'Pesan dashboard',
            'status' => ServiceRequest::STATUS_BARU,
        ]);

        FooterLink::create([
            'section' => FooterLink::SECTION_QUICK,
            'title' => 'Beranda',
            'url' => '/',
            'order' => 1,
            'is_active' => true,
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'service-request.updated',
            'subject_type' => ServiceRequest::class,
            'subject_id' => 1,
            'subject_title' => 'Aspirasi dashboard',
            'description' => 'Memperbarui status layanan publik.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Utama')
            ->assertSee('Dashboard SINOVA')
            ->assertDontSee('Tautan Pelaporan SINOVA')
            ->assertSee('Menu Navigasi')
            ->assertSee('Jenis Dokumen')
            ->assertSee('Workflow Publikasi')
            ->assertSee('Menunggu Review')
            ->assertSee('Inbox Layanan Publik')
            ->assertSee('Aktivitas Terbaru')
            ->assertSee('Aspirasi dashboard')
            ->assertSee('Post Review');
    }

    public function test_special_content_can_be_created_as_draft_then_published_under_its_existing_link(): void
    {
        $slug = 'wilayah-yurisdiksi';
        $route = route('admin.pages.managed', $slug);
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
        $this->get($route)->assertOk()
            ->assertSee('Isi daftar wilayah hukum')
            ->assertSee('name="managed_slug" value="wilayah-yurisdiksi"', false);
        $this->get(route('admin.pages.managed', 'tidak-dikenal'))->assertNotFound();

        $this->post(route('admin.pages.store'), [
            'title' => 'Wilayah Hukum PTA',
            'managed_slug' => $slug,
            'menu_group' => 'tentang-pengadilan',
            'content' => '<p>Konten uji wilayah hukum.</p>',
        ])->assertRedirect($route);
        $page = Page::where('slug', $slug)->firstOrFail();
        $this->get(route('page.show', $slug))->assertNotFound();

        $this->put(route('admin.pages.update', $page), [
            'title' => 'Wilayah Hukum Pengadilan',
            'menu_group' => 'tentang-pengadilan',
            'content' => '<p>Konten uji wilayah hukum.</p>',
            'is_active' => '1',
        ])->assertRedirect($route);
        $this->get(route('page.show', $slug))->assertOk()->assertSee('Konten uji wilayah hukum.');
        $this->get(route('admin.navigation.index', ['menu_group' => 'tentang-pengadilan']))
            ->assertOk()->assertSee('Susunan Halaman pada Navigasi')->assertSee('Wilayah Hukum Pengadilan');
        $this->get(route('admin.sinova.index'))->assertOk()->assertSee(route('page.show', $slug), false);
    }

    public function test_document_types_use_existing_transparency_pages_and_archive_filters(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
        $type = Page::create(['title' => 'DIPA Uji', 'slug' => 'dipa-uji', 'menu_group' => TransparencyDocument::PAGE_GROUP]);
        Page::create(['title' => 'Profil Uji', 'slug' => 'profil-uji', 'menu_group' => 'tentang-pengadilan']);
        $archive = route('admin.transparency-documents.index', ['page_id' => $type->id]);

        $this->get(route('admin.document-types.index'))->assertOk()
            ->assertSee('DIPA Uji')->assertDontSee('Profil Uji')->assertSee($archive, false);
        $this->get($archive)->assertOk()->assertSee(route('admin.transparency-documents.create', ['page_id' => $type->id]), false);
        $this->get(route('admin.transparency-documents.create', ['page_id' => $type->id]))
            ->assertOk()->assertSee('value="' . $type->id . '" selected', false);
    }

    public function test_author_satker_does_not_see_or_access_admin_only_menus(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_AUTHOR_SATKER]));
        $this->get(route('admin.dashboard'))->assertOk()
            ->assertDontSee('Dashboard SINOVA')->assertDontSee('Jenis Dokumen')
            ->assertDontSee('Menu Navigasi')->assertDontSee(route('admin.pengumuman.index'), false);
        foreach (['admin.navigation.index', 'admin.document-types.index'] as $name) {
            $this->get(route($name))->assertRedirect(route('admin.dashboard'));
        }
        $this->get(route('admin.pages.managed', 'hukuman-disiplin'))->assertRedirect(route('admin.dashboard'));
    }
}
