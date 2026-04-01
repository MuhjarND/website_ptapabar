<?php

namespace Tests\Feature;

use App\Gallery;
use App\GalleryCategory;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GalleryManagementTest extends TestCase
{
    use DatabaseMigrations;

    public function test_admin_can_create_and_update_gallery_with_category()
    {
        $admin = $this->makeAdmin();
        $kegiatan = GalleryCategory::where('slug', 'kegiatan')->firstOrFail();
        $layanan = GalleryCategory::where('slug', 'layanan')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.galleries.store'), [
            'title' => 'Galeri Kegiatan',
            'type' => 'video',
            'gallery_category_id' => $kegiatan->id,
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'order' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('admin.galleries.index'));

        $gallery = Gallery::where('title', 'Galeri Kegiatan')->firstOrFail();

        $this->assertDatabaseHas('galleries', [
            'id' => $gallery->id,
            'gallery_category_id' => $kegiatan->id,
        ]);

        $this->actingAs($admin)->put(route('admin.galleries.update', $gallery), [
            'title' => 'Galeri Layanan',
            'type' => 'video',
            'gallery_category_id' => $layanan->id,
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'order' => 2,
        ])->assertRedirect(route('admin.galleries.index'));

        $this->assertDatabaseHas('galleries', [
            'id' => $gallery->id,
            'title' => 'Galeri Layanan',
            'gallery_category_id' => $layanan->id,
            'is_active' => false,
        ]);
    }

    protected function makeAdmin()
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin-galeri@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }
}
