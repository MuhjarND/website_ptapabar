<?php

namespace Tests\Feature;

use App\Page;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PageManagementTest extends TestCase
{
    use DatabaseMigrations;

    public function test_page_slug_is_generated_uniquely_on_create()
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'Profil Layanan',
            'content' => 'Konten 1',
            'menu_group' => 'informasi',
            'is_active' => '1',
        ])->assertRedirect(route('admin.pages.index'));

        $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'Profil Layanan',
            'content' => 'Konten 2',
            'menu_group' => 'informasi',
            'is_active' => '1',
        ])->assertRedirect(route('admin.pages.index'));

        $this->assertDatabaseHas('pages', ['title' => 'Profil Layanan', 'slug' => 'profil-layanan']);
        $this->assertDatabaseHas('pages', ['title' => 'Profil Layanan', 'slug' => 'profil-layanan-2']);
    }

    public function test_page_slug_is_regenerated_uniquely_on_update()
    {
        $admin = $this->makeAdmin();

        $pageA = Page::create([
            'title' => 'Visi',
            'slug' => 'visi',
            'content' => 'Konten',
            'menu_group' => 'informasi',
            'order' => 0,
            'is_active' => true,
        ]);

        $pageB = Page::create([
            'title' => 'Misi',
            'slug' => 'misi',
            'content' => 'Konten',
            'menu_group' => 'informasi',
            'order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.pages.update', $pageB), [
            'title' => $pageA->title,
            'content' => 'Konten baru',
            'menu_group' => 'informasi',
            'is_active' => '1',
        ])->assertRedirect(route('admin.pages.index'));

        $this->assertDatabaseHas('pages', ['id' => $pageB->id, 'slug' => 'visi-2']);
    }

    public function test_admin_can_filter_pages_by_keyword_group_status_and_structure()
    {
        $admin = $this->makeAdmin();

        Page::create([
            'title' => 'Profil Pelayanan',
            'slug' => 'profil-pelayanan',
            'content' => 'Konten',
            'menu_group' => 'informasi',
            'order' => 0,
            'is_active' => true,
        ]);

        $parent = Page::create([
            'title' => 'Unit Induk',
            'slug' => 'unit-induk',
            'content' => 'Konten',
            'menu_group' => 'tentang-pengadilan',
            'order' => 1,
            'is_active' => true,
        ]);

        Page::create([
            'title' => 'Profil Pimpinan',
            'slug' => 'profil-pimpinan',
            'content' => 'Konten',
            'menu_group' => 'tentang-pengadilan',
            'parent_id' => $parent->id,
            'order' => 2,
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.pages.index', [
            'keyword' => 'Profil',
            'menu_group' => 'tentang-pengadilan',
            'status' => 'inactive',
            'structure' => 'child',
        ]));

        $response->assertOk();
        $response->assertSee('Profil Pimpinan');
        $response->assertDontSee('Profil Pelayanan');
        $response->assertSee('Unit Induk');
    }

    protected function makeAdmin()
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }
}
