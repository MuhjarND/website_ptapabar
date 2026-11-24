<?php

namespace Tests\Feature;

use App\Article;
use App\FloatingIcon;
use App\Gallery;
use App\GalleryCategory;
use App\IntegrityZone;
use App\Post;
use App\PostCategory;
use App\QuickLink;
use App\Setting;
use App\SiteApplication;
use App\Slider;
use App\SurveyIndex;
use App\User;
use DOMDocument;
use DOMXPath;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class PublicHomePageTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_homepage_uses_fixed_layout_without_discarding_saved_settings(): void
    {
        $legacy = '[{"type":"text","title":"Konten dari editor lama","x":1,"y":1,"w":12,"h":1}]';
        Setting::set('home_layout', $legacy);
        Setting::set('home_popup', '{"image":"poster.png"}');

        $this->get(route('home'))->assertOk()
            ->assertSee('data-home-publications', false)
            ->assertSee('id="homeImagePopup"', false)
            ->assertDontSee('Konten dari editor lama');
        $this->assertSame($legacy, Setting::get('home_layout'));

        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
        $this->get(route('admin.dashboard'))->assertOk()->assertDontSee('Editor Beranda');
        $this->get('/admin/editor-beranda')->assertNotFound();
        $this->put('/admin/editor-beranda')->assertNotFound();
        $this->delete('/admin/editor-beranda')->assertNotFound();
    }

    public function test_homepage_shows_four_entries_before_javascript_and_keeps_later_pages(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_AUTHOR]);
        for ($i = 1; $i <= 9; $i++) {
            $entry = ['title' => "Publikasi {$i}", 'slug' => "publikasi-{$i}", 'content' => 'Isi publikasi'];
            Post::create($entry + ['category' => 'pengumuman', 'is_published' => true, 'user_id' => $author->id]);
            Article::create($entry + ['image' => '', 'is_active' => true, 'order' => $i]);
        }

        $response = $this->get(route('home'))->assertOk();
        $dom = new DOMDocument();
        $dom->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);

        foreach (['announcement', 'article'] as $type) {
            $this->assertSame(4.0, $xpath->evaluate("count(//li[@data-{$type}-category and not(@hidden)])"));
            $this->assertSame(5.0, $xpath->evaluate("count(//li[@data-{$type}-category and @hidden])"));
            $this->assertSame(3.0, $xpath->evaluate("count(//*[@data-{$type}-pagination-nav]//button)"));
            $this->assertSame('4', $xpath->evaluate("string(//*[@data-{$type}-pagination]/@data-{$type}-per-page)"));
        }
    }

    public function test_homepage_news_sections_show_three_posts_per_page(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_AUTHOR]);
        $sections = [
            'terkini' => Post::NEWS_SCOPE_TERKINI,
            'peradilan-agama' => Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT,
        ];

        foreach ($sections as $section => $scope) {
            for ($i = 1; $i <= 10; $i++) {
                Post::create([
                    'title' => "Berita {$section} {$i}",
                    'slug' => "{$section}-{$i}",
                    'content' => 'Isi berita',
                    'category' => 'berita',
                    'news_scope' => $scope,
                    'is_published' => true,
                    'user_id' => $author->id,
                ]);
            }
        }

        $response = $this->get(route('home'))->assertOk();
        $dom = new DOMDocument();
        $dom->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);

        foreach (array_keys($sections) as $section) {
            $items = "//a[@data-news-page-item='{$section}'";
            $this->assertSame(3.0, $xpath->evaluate("count({$items} and not(@hidden)])"));
            $this->assertSame(6.0, $xpath->evaluate("count({$items} and @hidden])"));
            $this->assertSame(3.0, $xpath->evaluate("count(//*[@data-news-pagination-nav='{$section}']//button)"));
            $this->assertSame('3', $xpath->evaluate("string(//*[@data-news-pagination='{$section}']/@data-news-per-page)"));
        }
    }

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
        Slider::create(['title' => 'Slider Berikutnya', 'image' => 'sliders/berikutnya.jpg', 'order' => 2, 'is_active' => true]);

        QuickLink::create([
            'title' => 'E-Court',
            'description' => 'Layanan peradilan elektronik',
            'url' => 'https://example.com/e-court',
            'order' => 1,
            'is_active' => true,
        ]);
        QuickLink::create([
            'title' => 'Layanan PTSP',
            'url' => 'https://example.test/ptsp',
            'order' => 2,
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

        Post::create([
            'title' => 'Kegiatan Pimpinan Homepage',
            'slug' => 'kegiatan-pimpinan-homepage',
            'content' => 'Kegiatan pimpinan untuk homepage.',
            'category' => 'berita',
            'post_category_id' => PostCategory::where('slug', PostCategory::LEADERSHIP_AGENDA)->firstOrFail()->id,
            'is_published' => true,
            'user_id' => $author->id,
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

        FloatingIcon::create([
            'title' => 'Buku Tamu',
            'text' => 'Ingin berkunjung ke PTA, lihat ketersediaan pegawai',
            'icon' => 'floating-icons/buku-tamu-default.svg',
            'url' => 'https://bukutamu.pta-papuabarat.go.id/employees',
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
        $response->assertSee('<link rel="icon" type="image/png" href="' . asset('logo_pta.png') . '">', false);
        $response->assertSeeText('MAJU: Modern, Akuntabel, Jujur, Unggul');
        $response->assertSee('Slider Utama');
        $dom = new DOMDocument();
        $dom->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);
        $this->assertSame(1.0, $xpath->evaluate("count(//*[@id='heroSlider']//img[@src and @fetchpriority='high'])"));
        $this->assertSame(1.0, $xpath->evaluate("count(//*[@id='heroSlider']//img[@data-slide-src and not(@src)])"));
        $response->assertSee('E-Court');
        $response->assertSee('Berita Homepage');
        $response->assertSee('Berita Peradilan Agama Homepage');
        $response->assertSee('Ringkasan berita homepage');
        $response->assertDontSee('Ringkasan berita peradilan agama homepage');
        $response->assertSee('Berita Terkini');
        $response->assertSee('Berita Peradilan Agama Papua Barat');
        $response->assertSee('PA Manokwari');
        $response->assertSee('Pengumuman Homepage');
        $response->assertSee('Pengumuman Badilag Homepage');
        $response->assertSee('Agenda Pimpinan');
        $response->assertSee('Kegiatan Pimpinan Homepage');
        $response->assertSee('Artikel Homepage');
        $response->assertSee('Artikel');
        $response->assertSee('Tinta Peradilan');
        $response->assertSee('Galeri Kegiatan');
        $response->assertDontSee('Galeri Layanan');
        $response->assertSee('IKM');
        $response->assertSee('98.76');
        $response->assertSee('Ingin berkunjung ke PTA, lihat ketersediaan pegawai');
        $response->assertSee('Aplikasi Inovasi PTA');
        $response->assertSee('e-Court');
        $response->assertSee('Eviden Zona Integritas');
        $response->assertSee('Area 1');
        $response->assertSee('Daftar Aplikasi');
        $response->assertSee('PTA Papua Barat');
        $response->assertSee('MA');
        $response->assertSee('Badilag');
        $response->assertSee('Rapat');
        $response->assertSee('Sosialisasi');
        $response->assertSee('Kegiatan');
        $response->assertSee('Layanan');
        $response->assertSee('data-announcement-filter', false);
        $response->assertSee('data-announcement-category="pta-papua-barat"', false);
        $response->assertSeeInOrder([
            'data-quick-links-primary',
            '<h3>Jadwal Sidang</h3>',
            '<h3>Direktori Putusan</h3>',
            '<h3>E-Court</h3>',
            '<h3>E-AC</h3>',
            '<h3>Penelusuran Perkara</h3>',
            '<h3>SIWAS</h3>',
            '<summary>Layanan lainnya</summary>',
            '<h3>Layanan PTSP</h3>',
            'data-home-publications',
            'data-mobile-publication="Berita Terkini"',
            '>Berita Terkini</h2>',
            'data-mobile-publication="Pengumuman"',
            '>Pengumuman</h2>',
            'data-home-publications',
            'data-mobile-publication="Berita Satker"',
            '>Berita Peradilan Agama Papua Barat</h2>',
            'data-mobile-publication="Tinta Peradilan"',
            '>Tinta Peradilan</h2>',
            'data-mobile-publication="Agenda Pimpinan"',
            '>Agenda Pimpinan</h2>',
            'data-mobile-publication="Kalender Kegiatan"',
            '>Kalender Kegiatan PTA Papua Barat</h2>',
            '>Video dan Campaign</h2>',
        ], false);
        $response->assertSee(route('berita.index', ['news_scope' => Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT]), false);
        $response->assertDontSee('https://example.com/e-court', false);
        $response->assertSee('data-showcase-video="dQw4w9WgXcQ"', false);
        $this->get(route('gallery.index'))->assertOk()->assertSee('Galeri Kegiatan')->assertSee('Galeri Layanan');
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
