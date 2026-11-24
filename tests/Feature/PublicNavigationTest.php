<?php

namespace Tests\Feature;

use App\Page;
use App\Post;
use DOMDocument;
use DOMXPath;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class PublicNavigationTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_home_and_search_are_marked_independently(): void
    {
        $home = $this->navbar(route('home'));
        $this->assertSame(route('home'), $home->evaluate('string(//a[@aria-current="page"]/@href)'));
        $this->assertSame(0.0, $home->evaluate('count(//details[contains(@class, "is-current")])'));

        $search = $this->navbar(route('search', ['q' => 'pengadilan']));
        $this->assertSame(0.0, $search->evaluate('count(//a[@aria-current])'));
        $this->assertSame('Cari', trim($search->evaluate('string(//details[contains(@class, "is-current")]/summary)')));
    }

    public function test_publication_archive_and_detail_highlight_the_same_group(): void
    {
        $post = Post::create([
            'title' => 'Pengumuman untuk navigasi',
            'slug' => 'pengumuman-untuk-navigasi',
            'content' => 'Isi pengumuman',
            'category' => 'pengumuman',
            'is_published' => true,
        ]);

        foreach ([
            ['page', route('pengumuman.index', ['announcement_category' => 'badilag'])],
            ['location', route('pengumuman.detail', $post->slug)],
        ] as [$state, $url]) {
            $nav = $this->navbar($url);
            $this->assertSame(1.0, $nav->evaluate('count(//a[@aria-current])'));
            $this->assertSame(route('pengumuman.index'), $nav->evaluate('string(//a[@aria-current="' . $state . '"]/@href)'));
            $this->assertSame('Berita dan Publikasi', trim($nav->evaluate('string(//details[contains(@class, "is-current")]/summary)')));
        }
    }

    public function test_nested_page_marks_only_the_leaf_and_its_menu_ancestors(): void
    {
        $parent = Page::create([
            'title' => 'Informasi Pengadilan',
            'slug' => 'informasi-pengadilan-uji',
            'menu_group' => 'tentang-pengadilan',
            'is_active' => true,
        ]);
        $child = Page::create([
            'title' => 'Informasi Satuan Kerja',
            'slug' => 'informasi-satuan-kerja-uji',
            'menu_group' => 'tentang-pengadilan',
            'parent_id' => $parent->id,
            'is_active' => true,
        ]);

        $url = route('page.show', $child->slug);
        $nav = $this->navbar($url);

        $this->assertSame(1.0, $nav->evaluate('count(//a[@aria-current])'));
        $this->assertSame($url, $nav->evaluate('string(//a[@aria-current="page"]/@href)'));
        $this->assertSame('Profil Pengadilan', trim($nav->evaluate('string(//details[@class="site-nav-group is-current"]/summary)')));
        $this->assertSame($parent->title, trim($nav->evaluate('string(//details[@class="site-nav-subgroup is-current"]/summary)')));
    }

    private function navbar(string $url): DOMXPath
    {
        $response = $this->get($url)->assertOk();
        $document = new DOMDocument();
        $document->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);

        $xpath = new DOMXPath($document);
        $nav = $xpath->query('//*[@id="site-navigation"]')->item(0);
        $this->assertNotNull($nav);

        $navDocument = new DOMDocument();
        $navDocument->appendChild($navDocument->importNode($nav, true));

        return new DOMXPath($navDocument);
    }
}
