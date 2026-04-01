<?php

namespace Tests\Feature;

use App\Gallery;
use App\GalleryCategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PublicGalleryFlowTest extends TestCase
{
    use DatabaseMigrations;

    public function test_public_gallery_archive_can_be_filtered_by_category()
    {
        $kegiatan = GalleryCategory::where('slug', 'kegiatan')->firstOrFail();
        $layanan = GalleryCategory::where('slug', 'layanan')->firstOrFail();

        Gallery::create([
            'title' => 'Video Kegiatan',
            'type' => 'video',
            'gallery_category_id' => $kegiatan->id,
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'order' => 1,
            'is_active' => true,
        ]);

        Gallery::create([
            'title' => 'Foto Layanan',
            'type' => 'image',
            'gallery_category_id' => $layanan->id,
            'file' => 'galleries/layanan.jpg',
            'order' => 2,
            'is_active' => true,
        ]);

        $response = $this->get(route('gallery.index', ['gallery_category_id' => $kegiatan->id]));

        $response->assertOk();
        $response->assertSee('assets/css/public-content.css', false);
        $response->assertSee('assets/css/public-home.css', false);
        $response->assertSee('assets/js/public-home.js', false);
        $response->assertSee('Galeri PTA Papua Barat');
        $response->assertSee('Video Kegiatan');
        $response->assertDontSee('Foto Layanan');
        $response->assertSee('Kegiatan');
        $response->assertSee('Layanan');
    }
}
