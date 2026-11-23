<?php

namespace Tests\Feature;

use App\Post;
use Tests\TestCase;

class WordPressNewsImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);

        $this->artisan('migrate', ['--path' => [
            'database/migrations/2014_10_12_000000_create_users_table.php',
            'database/migrations/2024_01_01_000002_create_posts_table.php',
            'database/migrations/2026_03_30_000002_create_post_categories_table.php',
            'database/migrations/2026_03_30_000005_add_announcement_category_to_posts_table.php',
            'database/migrations/2026_03_31_000001_add_news_scope_to_posts_table.php',
            'database/migrations/2026_04_02_000002_add_workflow_status_to_posts_table.php',
            'database/migrations/2026_11_20_000001_add_wordpress_origin_to_posts.php',
        ]])->assertExitCode(0);
    }

    public function test_preview_does_not_write_and_apply_requires_matching_database()
    {
        $this->artisan('wordpress:import-news', $this->arguments())->assertExitCode(0);
        $this->artisan('wordpress:import-news', $this->arguments([
            '--apply' => true, '--expect-database' => 'wrong-database',
        ]))->assertExitCode(1);
        $this->assertSame(0, Post::count());
    }

    public function test_existing_content_is_preserved_and_public_wordpress_news_is_imported_once()
    {
        $local = Post::create([
            'title' => 'Berita dari Laravel',
            'slug' => 'rapat-pelayanan',
            'content' => 'Berita yang sudah ada.',
            'category' => 'berita',
            'is_published' => true,
        ]);
        $args = $this->arguments(['--apply' => true, '--expect-database' => ':memory:']);
        $this->artisan('wordpress:import-news', $args)->assertExitCode(0);
        $this->assertSame([100, 101], Post::whereNotNull('wordpress_id')->orderBy('wordpress_id')
            ->pluck('wordpress_id')->map(function ($id) { return (int) $id; })->all());
        $this->assertSame('Berita dari Laravel', $local->fresh()->title);

        $post = Post::where('wordpress_id', 100)->firstOrFail();
        $this->assertSame('Rapat & Pelayanan', $post->title);
        $this->assertSame('rapat-pelayanan-2', $post->slug);
        $this->assertSame('2024-01-10 09:00:00', $post->published_at->format('Y-m-d H:i:s'));
        $this->assertSame('https://pta-papuabarat.go.id/wp-content/uploads/2024/01/Gambar%20Lama.jpg', $post->image_url);
        $this->assertStringContainsString("Isi berita dengan 'kutip'.", $post->content);
        foreach (['unsafe marker', '<script', 'javascript:', 'style=', 'example.test'] as $marker) {
            $this->assertStringNotContainsString($marker, $post->content);
        }

        $child = Post::where('wordpress_id', 101)->firstOrFail();
        $this->assertSame('rapat', $child->postCategory->slug);
        $this->assertSame('https://pta-papuabarat.go.id/wp-content/uploads/2024/01/lanjutan.jpg', $child->image_url);
        $this->assertStringNotContainsString('onerror', $child->content);

        $post->update(['title' => 'Judul arsip disunting']);
        $this->artisan('wordpress:import-news', $args)->assertExitCode(0);
        $this->assertSame(3, Post::count());
        $this->assertSame('Judul arsip disunting', $post->fresh()->title);
        $this->get('/berita?sumber=wordpress')->assertOk()
            ->assertSee('Arsip Website Lama')->assertSee('Judul arsip disunting')
            ->assertDontSee('Berita dari Laravel');
    }

    private function arguments(array $flags = []): array
    {
        return ['dump' => base_path('tests/Fixtures/wordpress-news.sql')] + $flags;
    }
}
