<?php

namespace Tests\Feature;

use App\Page;
use App\Post;
use App\PostPhoto;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PublicPostFlowTest extends TestCase
{
    use DatabaseMigrations;

    public function test_post_detail_renders_documentation_related_posts_and_page_assets()
    {
        $author = $this->makeUser('author-post@example.com', 'author');

        $post = Post::create([
            'title' => 'Berita Dengan Dokumentasi',
            'slug' => 'berita-dengan-dokumentasi',
            'content' => '<p>Isi berita detail.</p>',
            'excerpt' => 'Ringkasan berita detail',
            'category' => 'berita',
            'image' => 'posts/cover.jpg',
            'is_published' => true,
            'user_id' => $author->id,
        ]);

        $related = Post::create([
            'title' => 'Berita Terkait',
            'slug' => 'berita-terkait',
            'content' => 'Isi berita terkait',
            'excerpt' => 'Ringkasan berita terkait',
            'category' => 'berita',
            'is_published' => true,
            'user_id' => $author->id,
        ]);

        PostPhoto::create([
            'post_id' => $post->id,
            'image' => 'post-photos/foto-kegiatan.jpg',
            'caption' => 'Foto Kegiatan',
            'order' => 1,
        ]);

        PostPhoto::create([
            'post_id' => $post->id,
            'image' => 'post-photos/lampiran.pdf',
            'caption' => 'Lampiran PDF',
            'order' => 2,
        ]);

        $response = $this->get(route('post.detail', $post->slug));

        $response->assertOk();
        $response->assertSee('assets/css/public-post-detail.css', false);
        $response->assertSee('assets/js/public-post-detail.js', false);
        $response->assertSee('Berita Dengan Dokumentasi');
        $response->assertSee('Dokumentasi');
        $response->assertSee('Foto Kegiatan');
        $response->assertSee('Lampiran PDF');
        $response->assertSee('Artikel Terkait');
        $response->assertSee(route('post.detail', $related->slug), false);
    }

    public function test_search_shows_pages_and_category_specific_post_links()
    {
        $author = $this->makeUser('author-search@example.com', 'author');

        Page::create([
            'title' => 'Profil Pelayanan',
            'slug' => 'profil-pelayanan',
            'content' => 'Informasi pelayanan masyarakat',
            'menu_group' => 'profil',
            'order' => 1,
            'is_active' => true,
        ]);

        $announcement = Post::create([
            'title' => 'Pengumuman Layanan',
            'slug' => 'pengumuman-layanan',
            'content' => 'Pelayanan berubah minggu ini',
            'excerpt' => 'Pelayanan berubah',
            'category' => 'pengumuman',
            'is_published' => true,
            'user_id' => $author->id,
        ]);

        $response = $this->get(route('search', ['q' => 'layanan']));

        $response->assertOk();
        $response->assertSee('assets/css/public-content.css', false);
        $response->assertSee('Profil Pelayanan');
        $response->assertSee('Pengumuman Layanan');
        $response->assertSee(route('page.show', 'profil-pelayanan'), false);
        $response->assertSee(route('pengumuman.detail', $announcement->slug), false);
    }

    public function test_pengumuman_page_can_be_filtered_by_announcement_category()
    {
        $author = $this->makeUser('author-announcement@example.com', 'author');

        $ptaAnnouncement = Post::create([
            'title' => 'Pengumuman PTA Papua Barat',
            'slug' => 'pengumuman-pta-papua-barat',
            'content' => 'Konten pengumuman PTA',
            'excerpt' => 'Ringkasan PTA',
            'category' => 'pengumuman',
            'announcement_category' => Post::ANNOUNCEMENT_CATEGORY_PTA_PAPUA_BARAT,
            'is_published' => true,
            'user_id' => $author->id,
        ]);

        $badilagAnnouncement = Post::create([
            'title' => 'Pengumuman Badilag',
            'slug' => 'pengumuman-badilag',
            'content' => 'Konten pengumuman Badilag',
            'excerpt' => 'Ringkasan Badilag',
            'category' => 'pengumuman',
            'announcement_category' => Post::ANNOUNCEMENT_CATEGORY_BADILAG,
            'is_published' => true,
            'user_id' => $author->id,
        ]);

        $response = $this->get(route('pengumuman.index', [
            'announcement_category' => Post::ANNOUNCEMENT_CATEGORY_PTA_PAPUA_BARAT,
        ]));

        $response->assertOk();
        $response->assertSee('PTA Papua Barat');
        $response->assertSee('Mahkamah Agung');
        $response->assertSee($ptaAnnouncement->title);
        $response->assertDontSee($badilagAnnouncement->title);
    }

    public function test_berita_page_can_be_filtered_by_news_scope()
    {
        $author = $this->makeUser('author-news-scope@example.com', 'author');

        $terkiniNews = Post::create([
            'title' => 'Berita Terkini Arsip',
            'slug' => 'berita-terkini-arsip',
            'content' => 'Konten berita terkini',
            'excerpt' => 'Ringkasan terkini',
            'category' => 'berita',
            'news_scope' => Post::NEWS_SCOPE_TERKINI,
            'is_published' => true,
            'user_id' => $author->id,
        ]);

        $paNews = Post::create([
            'title' => 'Berita Peradilan Agama Arsip',
            'slug' => 'berita-peradilan-agama-arsip',
            'content' => 'Konten berita peradilan agama',
            'excerpt' => 'Ringkasan peradilan agama',
            'category' => 'berita',
            'news_scope' => Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT,
            'is_published' => true,
            'user_id' => $author->id,
        ]);

        $response = $this->get(route('berita.index', [
            'news_scope' => Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT,
        ]));

        $response->assertOk();
        $response->assertSee('Berita Terkini');
        $response->assertSee('Berita Peradilan Agama Papua Barat');
        $response->assertSee($paNews->title);
        $response->assertDontSee($terkiniNews->title);
    }

    public function test_public_page_renders_sidebar_and_shared_content_asset()
    {
        $parent = Page::create([
            'title' => 'Profil',
            'slug' => 'profil',
            'content' => 'Profil utama',
            'menu_group' => 'profil',
            'order' => 1,
            'is_active' => true,
        ]);

        Page::create([
            'parent_id' => $parent->id,
            'title' => 'Sejarah',
            'slug' => 'sejarah',
            'content' => 'Sejarah lembaga',
            'menu_group' => 'profil',
            'order' => 1,
            'is_active' => true,
        ]);

        $response = $this->get(route('page.show', $parent->slug));

        $response->assertOk();
        $response->assertSee('assets/css/public-content.css', false);
        $response->assertSee('Profil');
        $response->assertSee('Sejarah');
        $response->assertSee('|-');
    }

    protected function makeUser($email, $role)
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => $role,
        ]);
    }
}
