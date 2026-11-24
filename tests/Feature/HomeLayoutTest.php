<?php

namespace Tests\Feature;

use App\Setting;
use App\Support\HomeLayout;
use App\User;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class HomeLayoutTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_default_editor_places_both_publication_pairs_in_separate_rows(): void
    {
        $blocks = collect(HomeLayout::defaults())->keyBy('type');
        foreach (['latest-news' => 'announcement', 'religious-court-news' => 'article'] as $news => $sidebar) {
            $this->assertSame($blocks[$news]['y'], $blocks[$sidebar]['y']);
            $this->assertSame([1, 8], [$blocks[$news]['x'], $blocks[$news]['w']]);
            $this->assertSame([9, 4], [$blocks[$sidebar]['x'], $blocks[$sidebar]['w']]);
        }
        $this->assertSame($blocks['latest-news']['y'] + 1, $blocks['religious-court-news']['y']);
    }

    public function test_admin_can_publish_and_reset_home_layout_without_changing_existing_content(): void
    {
        $this->get('/')->assertOk()->assertSee('Pengumuman')->assertDontSee('class="home-layout-grid"', false);
        Setting::set('home_popup', '{"image":"poster.png"}');

        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
        $this->get(route('admin.home-layout.index'))->assertOk()
            ->assertSee('id="home-editor-grid"', false)
            ->assertSee('Slider Utama')
            ->assertSee('Tambah Blok');

        $blocks = [
            ['type' => 'latest-news', 'x' => 5, 'y' => 1, 'w' => 8, 'h' => 2],
            ['type' => 'article', 'x' => 1, 'y' => 3, 'w' => 8, 'h' => 1],
            ['type' => 'text', 'x' => 1, 'y' => 1, 'w' => 4, 'h' => 1, 'title' => 'Jam Layanan', 'content' => '<script>test</script>'],
            ['type' => 'search', 'x' => 1, 'y' => 2, 'w' => 4, 'h' => 1],
            ['type' => 'banner', 'x' => 9, 'y' => 3, 'w' => 4, 'h' => 1,
                'title' => 'Poster layanan', 'image' => 'https://example.test/poster.png', 'link' => 'https://example.test/layanan'],
        ];

        $this->put(route('admin.home-layout.update'), ['layout' => json_encode($blocks)])
            ->assertRedirect(route('admin.home-layout.index'))->assertSessionHasNoErrors();
        $this->assertCount(5, HomeLayout::saved());
        $this->get('/')->assertOk()
            ->assertSeeInOrder(['data-home-block="text"', 'data-home-block="latest-news"', 'data-home-block="search"', 'data-home-block="article"'], false)
            ->assertSee('--block-width: 8; --block-height: 2', false)
            ->assertSee('Jam Layanan')
            ->assertSee('<script>test</script>')
            ->assertDontSee('<script>test</script>', false)
            ->assertSee('action="' . route('search') . '"', false)
            ->assertSee('href="https://example.test/layanan"', false)
            ->assertSee('alt="Poster layanan"', false)
            ->assertSee('id="homeImagePopup"', false)
            ->assertSee('Belum ada artikel.')
            ->assertDontSee('data-announcement-filter', false);

        $this->delete(route('admin.home-layout.destroy'))->assertRedirect(route('admin.home-layout.index'));
        $this->assertNull(HomeLayout::saved());
        $this->get('/')->assertOk()->assertDontSee('Jam Layanan')
            ->assertSee('data-announcement-filter', false)->assertSee('id="homeImagePopup"', false);
    }

    public function test_only_admin_can_manage_home_layout(): void
    {
        $url = route('admin.home-layout.index');
        $this->get($url)->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['role' => User::ROLE_AUTHOR]));
        $this->get($url)->assertRedirect(route('admin.dashboard'));
        $this->put($url, ['layout' => '[]'])->assertRedirect(route('admin.dashboard'));
        $this->delete($url)->assertRedirect(route('admin.dashboard'));
        $this->assertNull(HomeLayout::saved());
    }

    public function test_overlaps_invalid_dimensions_duplicate_module_and_unsafe_urls_are_rejected(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
        $url = route('admin.home-layout.update');
        $base = ['type' => 'announcement', 'x' => 1, 'y' => 1, 'w' => 6, 'h' => 1];
        $this->put($url, ['layout' => json_encode([$base])])->assertSessionHasNoErrors();

        foreach ([
            [[$base, ['type' => 'article', 'x' => 6] + $base], 'layout'],
            [[['x' => 8] + $base], 'layout'],
            [[$base, ['y' => 2] + $base], 'layout'],
            [[['type' => 'unknown'] + $base], 'blocks.0.type'],
            [[['type' => 'banner', 'image' => 'javascript:alert(1)'] + $base], 'blocks.0.image'],
        ] as [$blocks, $error]) {
            $this->put($url, ['layout' => json_encode($blocks)])->assertSessionHasErrors($error);
        }
        $this->assertSame([$base], HomeLayout::saved());
    }
}
