<?php

namespace Tests\Feature;

use App\Article;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleManagementTest extends TestCase
{
    use DatabaseMigrations;

    public function test_admin_can_create_and_update_article()
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.articles.store'), [
            'title' => 'Artikel Homepage',
            'excerpt' => 'Ringkasan artikel homepage',
            'content' => '',
            'pdf_file' => UploadedFile::fake()->create('artikel.pdf', 240, 'application/pdf'),
            'article_category' => Article::CATEGORY_BADILAG,
            'order' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('admin.articles.index'));

        $article = Article::where('title', 'Artikel Homepage')->firstOrFail();

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'slug' => 'artikel-homepage',
            'article_category' => Article::CATEGORY_BADILAG,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.articles.update', $article), [
            'title' => 'Artikel Homepage Update',
            'excerpt' => '',
            'content' => '',
            'pdf_url' => 'https://example.com/artikel-homepage-update.pdf',
            'article_category' => Article::CATEGORY_MAHKAMAH_AGUNG,
            'order' => 2,
        ])->assertRedirect(route('admin.articles.index'));

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Artikel Homepage Update',
            'slug' => 'artikel-homepage-update',
            'pdf_url' => 'https://example.com/artikel-homepage-update.pdf',
            'article_category' => Article::CATEGORY_MAHKAMAH_AGUNG,
            'is_active' => false,
        ]);
    }

    protected function makeAdmin()
    {
        return User::create([
            'name' => 'Admin Artikel',
            'email' => 'admin-artikel@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);
    }
}
