<?php

namespace Tests\Feature;

use App\Article;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PublicArticleFlowTest extends TestCase
{
    use DatabaseMigrations;

    public function test_public_article_detail_renders()
    {
        $article = Article::create([
            'title' => 'Artikel Detail Publik',
            'slug' => 'artikel-detail-publik',
            'excerpt' => 'Ringkasan artikel detail publik',
            'content' => '<p>Isi artikel detail publik.</p>',
            'image' => '',
            'article_category' => Article::CATEGORY_PTA_PAPUA_BARAT,
            'order' => 1,
            'is_active' => true,
        ]);

        $this->get(route('article.detail', $article))
            ->assertOk()
            ->assertSee('Artikel Detail Publik')
            ->assertSee('Isi artikel detail publik.', false);
    }

    public function test_pdf_only_article_redirects_to_pdf()
    {
        $article = Article::create([
            'title' => 'Artikel PDF Publik',
            'slug' => 'artikel-pdf-publik',
            'excerpt' => 'Dokumen artikel publik',
            'content' => '',
            'image' => '',
            'pdf_url' => 'https://example.com/artikel-publik.pdf',
            'article_category' => Article::CATEGORY_BADILAG,
            'order' => 1,
            'is_active' => true,
        ]);

        $this->get(route('article.detail', $article))
            ->assertRedirect('https://example.com/artikel-publik.pdf');
    }

    public function test_article_archive_can_be_filtered_by_category()
    {
        Article::create([
            'title' => 'Artikel PTA',
            'slug' => 'artikel-pta',
            'excerpt' => 'Artikel kategori PTA',
            'content' => '<p>Isi artikel pta</p>',
            'image' => '',
            'article_category' => Article::CATEGORY_PTA_PAPUA_BARAT,
            'order' => 1,
            'is_active' => true,
        ]);

        Article::create([
            'title' => 'Artikel Badilag',
            'slug' => 'artikel-badilag',
            'excerpt' => 'Artikel kategori Badilag',
            'content' => '<p>Isi artikel badilag</p>',
            'image' => '',
            'article_category' => Article::CATEGORY_BADILAG,
            'order' => 2,
            'is_active' => true,
        ]);

        $this->get(route('article.index', ['article_category' => Article::CATEGORY_BADILAG]))
            ->assertOk()
            ->assertSee('Artikel Badilag')
            ->assertDontSee('Artikel PTA');
    }
}
