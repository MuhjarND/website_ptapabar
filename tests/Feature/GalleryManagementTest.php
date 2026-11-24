<?php

namespace Tests\Feature;

use App\Gallery;
use App\GalleryCategory;
use App\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class GalleryManagementTest extends TestCase
{
    use MigratesTestDatabase;

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

    public function test_admin_can_publish_banners_and_greetings_on_the_homepage_only(): void
    {
        Storage::fake('public');
        $this->actingAs($this->makeAdmin())->get(route('admin.galleries.create'))->assertOk()
            ->assertSee('Banner Beranda')->assertSee('Ucapan Selamat')->assertSee('Turut Berduka');

        $items = [
            Gallery::TYPE_BANNER => 'Informasi Perubahan Jam Layanan',
            Gallery::TYPE_GREETING => 'Selamat atas Prestasi PA Manokwari',
            Gallery::TYPE_CONDOLENCE => 'Duka Cita Keluarga Besar PTA',
        ];

        foreach ($items as $type => $title) {
            $this->post(route('admin.galleries.store'), [
                'title' => $title,
                'type' => $type,
                'file' => UploadedFile::fake()->image($type . '.png'),
                'is_active' => '1',
            ])->assertRedirect(route('admin.galleries.index'));

            $item = Gallery::where('type', $type)->firstOrFail();
            $this->assertNull($item->gallery_category_id);
            Storage::disk('public')->assertExists($item->file);
        }

        $banner = Gallery::where('type', Gallery::TYPE_BANNER)->firstOrFail();
        $this->put(route('admin.galleries.update', $banner), [
            'title' => 'Banner Baru',
            'type' => Gallery::TYPE_BANNER,
            'is_active' => '1',
        ])->assertRedirect(route('admin.galleries.index'));
        $this->assertSame($banner->file, $banner->fresh()->file);

        Gallery::create(['type' => Gallery::TYPE_GREETING, 'title' => 'Ucapan Nonaktif', 'file' => 'private.png', 'is_active' => false]);
        Gallery::create(['type' => Gallery::TYPE_GREETING, 'title' => 'Selamat Hari Jadi', 'file' => 'galleries/hari-jadi.png', 'is_active' => true, 'order' => 1]);
        Gallery::create(['type' => 'video', 'title' => 'Video Layanan', 'video_url' => 'https://youtu.be/dQw4w9WgXcQ', 'is_active' => true, 'order' => 1]);

        $home = $this->get(route('home'))->assertOk()
            ->assertSeeInOrder(['Video dan Campaign', 'Banner Baru', 'Video Layanan', 'Ucapan Selamat & Duka', $items[Gallery::TYPE_CONDOLENCE], $items[Gallery::TYPE_GREETING], 'Selamat Hari Jadi'])
            ->assertDontSee('Ucapan Nonaktif');
        $dom = new DOMDocument();
        $dom->loadHTML($home->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);
        foreach (['media' => 2.0, 'greetings' => 3.0] as $carousel => $count) {
            $root = "//*[@data-showcase-carousel='{$carousel}']";
            $this->assertSame($count, $xpath->evaluate("count({$root}//*[@data-showcase-slide])"));
            $this->assertSame(1.0, $xpath->evaluate("count({$root}//*[@data-showcase-slide and not(@hidden)])"));
            $this->assertSame(0.0, $xpath->evaluate("count({$root}//*[@data-showcase-slide]//*[contains(@class,'section-category-pill')])"));
            $this->assertSame($count, $xpath->evaluate("count({$root}//button[@data-showcase-select and @aria-controls])"));
            $this->assertSame(1.0, $xpath->evaluate("count({$root}//button[@data-showcase-select and @aria-pressed='true'])"));
        }

        $this->get(route('gallery.index'))->assertOk()->assertDontSee('Banner Baru')->assertDontSee($items[Gallery::TYPE_GREETING]);
    }

    public function test_video_requires_a_valid_youtube_link_without_an_upload(): void
    {
        $category = GalleryCategory::where('slug', 'kegiatan')->firstOrFail();
        $this->actingAs($this->makeAdmin());
        $data = ['title' => 'Video Profil', 'type' => 'video', 'gallery_category_id' => $category->id, 'is_active' => '1'];

        $this->post(route('admin.galleries.store'), $data + ['video_url' => 'https://example.test/youtube.com/watch?v=dQw4w9WgXcQ'])
            ->assertSessionHasErrors('video_url');

        $this->post(route('admin.galleries.store'), $data + ['video_url' => 'https://www.youtube.com/watch?feature=share&v=dQw4w9WgXcQ'])
            ->assertRedirect(route('admin.galleries.index'));

        $video = Gallery::where('title', $data['title'])->firstOrFail();
        $this->assertNull($video->file);
        $this->get(route('home'))->assertOk()->assertSee('data-showcase-video="dQw4w9WgXcQ"', false);
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
