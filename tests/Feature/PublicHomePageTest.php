<?php

namespace Tests\Feature;

use App\Article;
use App\Gallery;
use App\GalleryCategory;
use App\IntegrityZone;
use App\Post;
use App\PostCategory;
use App\QuickLink;
use App\SiteApplication;
use App\Slider;
use App\SurveyIndex;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PublicHomePageTest extends TestCase
{
    use DatabaseMigrations;

    public function test_homepage_renders_key_public_modules_and_page_assets()
    {
        $author = $this->makeUser('author-home@example.com', 'author');
        $satkerAuthor = $this->makeUser('satker-home@example.com', User::ROLE_AUTHOR_SATKER, 'PA Manokwari');
        $rapatCategory = PostCategory::where('slug', 'rapat')->firstOrFail();
        $sosialisasiCategory = PostCategory::where('slug', 'sosialisasi')->firstOrFail();
        $kegiatanGalleryCategory = GalleryCategory::where('slug', 'kegiatan')->firstOrFail();
        $layananGalleryCategory = GalleryCategory::where('slug', 'layanan')->firstOrFail();

        Slider::create([
            'title' => 'Slider Utama',
            'description' => 'Informasi slider',
            'image' => 'sliders/utama.jpg',
            'text_position' => 'bottom-left',
            'order' => 1,
            'is_active' => true,
        ]);

        QuickLink::create([
            'title' => 'E-Court',
            'description' => 'Layanan peradilan elektronik',
            'url' => 'https://example.com/e-court',
            'order' => 1,
            'is_active' => true,
        ]);

        Post::create([
            'title' => 'Berita Homepage',
            'slug' => 'berita-homepage',
            'content' => 'Isi berita homepage',
            'excerpt' => 'Ringkasan berita homepage',
            'category' => 'berita',
            'post_category_id' => $rapatCategory->id,
            'news_scope' => Post::NEWS_SCOPE_TERKINI,
            'is_published' => true,
            'user_id' => $author->id,
        ]);

        Post::create([
            'title' => 'Berita Peradilan Agama Homepage',
            'slug' => 'berita-peradilan-agama-homepage',
            'content' => 'Isi berita peradilan agama homepage',
            'excerpt' => 'Ringkasan berita peradilan agama homepage',
            'category' => 'berita',
            'post_category_id' => $sosialisasiCategory->id,
            'news_scope' => Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT,
            'is_published' => true,
            'user_id' => $satkerAuthor->id,
        ]);

        Post::create([
            'title' => 'Pengumuman Homepage',
            'slug' => 'pengumuman-homepage',
            'content' => 'Isi pengumuman homepage',
            'excerpt' => 'Ringkasan pengumuman homepage',
            'category' => 'pengumuman',
            'announcement_category' => Post::ANNOUNCEMENT_CATEGORY_PTA_PAPUA_BARAT,
            'is_published' => true,
            'user_id' => $author->id,
        ]);

        Post::create([
            'title' => 'Pengumuman Badilag Homepage',
            'slug' => 'pengumuman-badilag-homepage',
            'content' => 'Isi pengumuman badilag homepage',
            'excerpt' => 'Ringkasan pengumuman badilag homepage',
            'category' => 'pengumuman',
            'announcement_category' => Post::ANNOUNCEMENT_CATEGORY_BADILAG,
            'is_published' => true,
            'user_id' => $author->id,
        ]);

        Gallery::create([
            'title' => 'Galeri Kegiatan',
            'type' => 'video',
            'gallery_category_id' => $kegiatanGalleryCategory->id,
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'order' => 1,
            'is_active' => true,
        ]);

        Gallery::create([
            'title' => 'Galeri Layanan',
            'type' => 'image',
            'gallery_category_id' => $layananGalleryCategory->id,
            'file' => 'galleries/layanan.jpg',
            'order' => 2,
            'is_active' => true,
        ]);

        SurveyIndex::create([
            'title' => 'IKM',
            'icon' => 'fas fa-chart-line',
            'value' => 98.76,
            'label' => 'Sangat Baik',
            'order' => 1,
            'is_active' => true,
        ]);

        SiteApplication::create([
            'title' => 'Aplikasi Inovasi PTA',
            'description' => 'Aplikasi unggulan internal',
            'url' => 'https://example.com/pta',
            'group_type' => SiteApplication::GROUP_PTA_INOVASI,
            'order' => 1,
            'is_active' => true,
        ]);

        SiteApplication::create([
            'title' => 'e-Court',
            'description' => 'Aplikasi Mahkamah Agung',
            'url' => 'https://example.com/ecourt',
            'group_type' => SiteApplication::GROUP_MAHKAMAH_AGUNG,
            'order' => 2,
            'is_active' => true,
        ]);

        Article::create([
            'title' => 'Artikel Homepage',
            'slug' => 'artikel-homepage',
            'excerpt' => 'Ringkasan artikel homepage',
            'content' => '',
            'image' => '',
            'pdf_url' => 'https://example.com/artikel-homepage.pdf',
            'article_category' => Article::CATEGORY_PTA_PAPUA_BARAT,
            'order' => 1,
            'is_active' => true,
        ]);

        IntegrityZone::create([
            'title' => 'Area 1',
            'image' => 'integrity-zones/area-1.jpg',
            'url' => 'https://example.com/zi/area-1',
            'order' => 1,
            'is_active' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('assets/css/public-home.css', false);
        $response->assertSee('assets/js/public-home.js', false);
        $response->assertSee('Slider Utama');
        $response->assertSee('E-Court');
        $response->assertSee('Berita Homepage');
        $response->assertSee('Berita Peradilan Agama Homepage');
        $response->assertSee('Berita Terkini');
        $response->assertSee('Berita Peradilan Agama Papua Barat');
        $response->assertSee('PA Manokwari');
        $response->assertSee('Pengumuman Homepage');
        $response->assertSee('Pengumuman Badilag Homepage');
        $response->assertSee('Artikel Homepage');
        $response->assertSee('Artikel');
        $response->assertSee('Tinta Peradilan');
        $response->assertSee('Galeri Kegiatan');
        $response->assertSee('Galeri Layanan');
        $response->assertSee('IKM');
        $response->assertSee('98.76');
        $response->assertSee('Aplikasi Inovasi PTA');
        $response->assertSee('e-Court');
        $response->assertSee('Eviden Zona Integritas');
        $response->assertSee('Area 1');
        $response->assertSee('Aplikasi Inovasi PTA Papua Barat');
        $response->assertSee('Aplikasi yang Digunakan di Mahkamah Agung');
        $response->assertSee('PTA Papua Barat');
        $response->assertSee('Mahkamah Agung');
        $response->assertSee('Badilag');
        $response->assertSee('Rapat');
        $response->assertSee('Sosialisasi');
        $response->assertSee('Kegiatan');
        $response->assertSee('Layanan');
        $response->assertSee('data-announcement-filter', false);
        $response->assertSee('data-announcement-category="pta-papua-barat"', false);
        $response->assertSee('data-gallery-filter', false);
        $response->assertSee('data-gallery-category="' . $kegiatanGalleryCategory->id . '"', false);
    }

    protected function makeUser($email, $role, $name = null)
    {
        return User::create([
            'name' => $name ?: ucfirst($role),
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => $role,
        ]);
    }
}
