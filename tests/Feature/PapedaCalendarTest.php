<?php

namespace Tests\Feature;

use App\Post;
use App\PostCategory;
use App\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class PapedaCalendarTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_homepage_reads_and_caches_the_selected_month_of_public_papeda_events(): void
    {
        $this->travelTo(now('Asia/Jayapura')->setDate(2026, 11, 24));
        $url = 'https://papeda.example.test/api/kalender-publik';
        config(['services.papeda.public_calendar_url' => $url]);
        Http::fake([$url . '*' => Http::response(['events' => [
            ['title' => 'Kunjungan Kerja Publik', 'date' => '2026-12-08', 'end_date' => '2026-12-09', 'time' => '09:00', 'internal' => 'Catatan Pribadi'],
            ['title' => 'Kegiatan di Luar Bulan', 'date' => '2027-01-02', 'time' => '10:00'],
        ]])]);

        $page = route('home', ['bulan' => '2026-12']);
        $this->get($page)->assertOk()->assertSee('Desember 2026')->assertSee('Kunjungan Kerja Publik')
            ->assertSee('09:00 WIT')->assertSee('Sampai 09 Des')->assertDontSee('Catatan Pribadi')->assertDontSee('Kegiatan di Luar Bulan');
        $this->get($page)->assertOk()->assertSee('Kunjungan Kerja Publik');
        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request) => $request->url() === $url . '?month=2026-12');
    }

    public function test_a_failed_papeda_response_does_not_break_the_homepage(): void
    {
        $url = 'https://papeda.example.test/api/kalender-publik';
        config(['services.papeda.public_calendar_url' => $url]);
        Http::fake([$url . '*' => Http::response('Unavailable', 503)]);

        $this->get(route('home'))->assertOk()
            ->assertSee('Jadwal dari Papeda sedang tidak tersedia.')
            ->assertDontSee('Tersingkronisasi dengan aplikasi inovasi PAPEDA PTA Papua Barat.')
            ->assertDontSee('Belum ada kegiatan publik pada bulan ini.')
            ->assertSee('Video dan Campaign');
    }

    public function test_agenda_news_uses_the_existing_post_category_and_only_published_news(): void
    {
        $category = PostCategory::where('slug', PostCategory::LEADERSHIP_AGENDA)->firstOrFail();
        $author = User::factory()->create(['role' => User::ROLE_AUTHOR]);
        $defaults = ['category' => 'berita', 'content' => 'Ringkasan agenda', 'user_id' => $author->id];

        Post::create($defaults + [
            'title' => 'Agenda Terbit', 'slug' => 'agenda-terbit', 'post_category_id' => $category->id, 'is_published' => true,
        ]);
        Post::create($defaults + [
            'title' => 'Agenda Draf', 'slug' => 'agenda-draf', 'post_category_id' => $category->id,
            'workflow_status' => Post::STATUS_DRAFT, 'is_published' => false,
        ]);
        Post::create($defaults + [
            'title' => 'Berita Umum Biasa', 'slug' => 'berita-umum-biasa', 'is_published' => true,
        ]);

        $archive = route('berita.index', ['kategori' => PostCategory::LEADERSHIP_AGENDA]);
        $this->get(route('home'))->assertOk()
            ->assertSee($archive, false)->assertSee('Agenda Terbit')->assertDontSee('Agenda Draf');
        $this->get($archive)->assertOk()->assertSee('Agenda Terbit')
            ->assertDontSee('Agenda Draf')->assertDontSee('Berita Umum Biasa');
    }
}
