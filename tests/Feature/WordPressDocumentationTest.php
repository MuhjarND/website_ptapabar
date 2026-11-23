<?php

namespace Tests\Feature;

use App\Article;
use App\Post;
use App\User;
use Illuminate\Support\Facades\Storage;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class WordPressDocumentationTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_archive_reuses_related_media_links_and_does_not_overwrite_existing_posts(): void
    {
        Storage::fake('public');
        $args = [
            'dump' => base_path('tests/Fixtures/wordpress-news.sql'),
            '--expect-database' => ':memory:',
            '--apply' => true,
        ];
        $this->artisan('wordpress:import-news', $args)->assertExitCode(0);
        $post = Post::where('wordpress_id', 100)->firstOrFail();
        $post->update(['title' => 'Judul telah diedit di Laravel']);
        $post->photos()->create(['image' => 'post-photos/foto-baru.jpg', 'order' => 1]);

        $args['--all'] = true;
        $this->artisan('wordpress:import-news', $args)->assertExitCode(0);
        $this->artisan('wordpress:import-news', $args)->assertExitCode(0);
        $this->assertSame('Judul telah diedit di Laravel', $post->fresh()->title);
        $this->assertEqualsCanonicalizing(
            ['2024/01/rapat.jpg', '2024/01/galeri.jpg', '2024/01/referensi.pdf', '2024/01/inline.jpg'],
            $post->photos()->whereNotNull('wordpress_path')->pluck('wordpress_path')->all()
        );
        $this->assertSame(5, $post->photos()->count());
        $this->assertSame([], Storage::disk('public')->allFiles());

        config(['wordpress.media_url' => 'https://archive.example.test/wp-uploads']);
        $this->get(route('post.detail', $post->slug))->assertOk()
            ->assertSee('Dokumentasi')
            ->assertSee('https://archive.example.test/wp-uploads/2024/01/rapat.jpg', false)
            ->assertSee('https://archive.example.test/wp-uploads/2024/01/referensi.pdf', false)
            ->assertDontSee('tidak-terkait.jpg')->assertDontSee('file-terkunci.jpg')
            ->assertSee('storage/post-photos/foto-baru.jpg', false);
        $article = Article::where('wordpress_id', 105)->firstOrFail();
        $this->assertSame('https://archive.example.test/wp-uploads/2024/01/artikel.pdf', $article->pdf_href);

        $remote = $post->photos()->whereNotNull('wordpress_path')->firstOrFail();
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]))
            ->delete(route('admin.posts.photo.destroy', $remote))->assertRedirect();
        $this->assertModelMissing($remote);
        $this->assertSame(4, $post->photos()->count());
    }
}
