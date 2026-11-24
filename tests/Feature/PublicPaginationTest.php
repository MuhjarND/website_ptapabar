<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class PublicPaginationTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_public_pagination_preserves_selection_and_identifies_the_current_page(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_AUTHOR]);
        for ($i = 1; $i <= 25; $i++) {
            Post::create([
                'title' => "Berita Pelayanan $i", 'slug' => "berita-pelayanan-$i", 'content' => 'Informasi publik.',
                'category' => 'berita', 'is_published' => true, 'news_scope' => Post::NEWS_SCOPE_TERKINI,
                'user_id' => $author->id,
            ]);
        }

        foreach (['berita.index' => ['news_scope' => Post::NEWS_SCOPE_TERKINI], 'search' => ['q' => 'Berita Pelayanan']] as $route => $filters) {
            $this->get(route($route, $filters + ['page' => 2]))->assertOk()
                ->assertSee('Halaman 2 dari 3')
                ->assertSee('aria-current="page">2</span>', false)
                ->assertSee(e(route($route, $filters + ['page' => 1])) . '" rel="prev"', false)
                ->assertSee(e(route($route, $filters + ['page' => 3])) . '" rel="next"', false);
        }

        $this->get(route('search', ['q' => 'hasil tidak ditemukan']))->assertOk()->assertDontSee('aria-label="Paginasi"', false);
    }
}
