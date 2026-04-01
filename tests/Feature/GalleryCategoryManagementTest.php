<?php

namespace Tests\Feature;

use App\Gallery;
use App\GalleryCategory;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GalleryCategoryManagementTest extends TestCase
{
    use DatabaseMigrations;

    public function test_admin_can_create_and_update_gallery_category()
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.gallery-categories.store'), [
            'name' => 'Kegiatan Internal',
            'order' => 2,
            'is_active' => '1',
        ])->assertRedirect(route('admin.gallery-categories.index'));

        $category = GalleryCategory::where('name', 'Kegiatan Internal')->firstOrFail();

        $this->assertDatabaseHas('gallery_categories', [
            'id' => $category->id,
            'slug' => 'kegiatan-internal',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.gallery-categories.update', $category), [
            'name' => 'Kegiatan Pimpinan',
            'order' => 1,
        ])->assertRedirect(route('admin.gallery-categories.index'));

        $this->assertDatabaseHas('gallery_categories', [
            'id' => $category->id,
            'name' => 'Kegiatan Pimpinan',
            'slug' => 'kegiatan-pimpinan',
            'is_active' => false,
        ]);
    }

    public function test_gallery_category_in_use_cannot_be_deleted()
    {
        $admin = $this->makeAdmin();
        $category = GalleryCategory::where('slug', 'kegiatan')->firstOrFail();

        Gallery::create([
            'title' => 'Galeri Uji',
            'type' => 'image',
            'file' => 'galleries/uji.jpg',
            'gallery_category_id' => $category->id,
            'order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->delete(route('admin.gallery-categories.destroy', $category))
            ->assertRedirect(route('admin.gallery-categories.index'));

        $this->assertDatabaseHas('gallery_categories', ['id' => $category->id]);
    }

    protected function makeAdmin()
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin-kategori-galeri@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }
}
