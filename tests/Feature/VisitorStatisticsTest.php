<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackVisitors;
use App\VisitorCount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\MigratesTestDatabase;
use Tests\TestCase;

class VisitorStatisticsTest extends TestCase
{
    use MigratesTestDatabase;

    public function test_different_sessions_share_the_same_daily_count(): void
    {
        $this->freezeTime(function () {
            $this->get('/')->assertOk();
            $this->get('/')->assertOk();
            $this->assertSame(1, (int) VisitorCount::sole()->count);

            $this->flushSession();
            $this->get('/')->assertOk();
            $this->assertSame(2, (int) VisitorCount::sole()->count);
        });
    }

    public function test_date_only_record_is_reused_without_creating_a_duplicate(): void
    {
        $this->freezeTime(function () {
            DB::table('visitor_counts')->insert(['visit_date' => now()->toDateString(), 'count' => 3]);

            $this->get('/')->assertOk();
            $this->assertSame(4, (int) VisitorCount::sole()->count);
        });
    }

    public function test_footer_displays_today_yesterday_and_total_across_the_month_boundary(): void
    {
        $this->travelTo(Carbon::parse('2026-04-01 12:00:00'), function () {
            $this->withoutMiddleware(TrackVisitors::class);

            VisitorCount::create(['visit_date' => '2026-04-01', 'count' => 484]);
            VisitorCount::create(['visit_date' => '2026-03-31', 'count' => 5678]);
            VisitorCount::create(['visit_date' => '2026-03-30', 'count' => 4250]);

            $this->get('/')->assertOk()
                ->assertSeeInOrder([
                    'id="statistik-web"', '<h4>Statistik Pengunjung</h4>',
                    '<dt>Hari Ini</dt><dd>484</dd>', '<dt>Kemarin</dt><dd>5.678</dd>', '<dt>Total</dt><dd>10.412</dd>',
                ], false)
                ->assertSee('Buka Lokasi di Google Maps')
                ->assertSee('https://www.google.com/maps/search/?api=1&amp;query=', false)
                ->assertSee('title="Peta lokasi kantor PTA Papua Barat"', false)
                ->assertSee('&amp;output=embed', false)
                ->assertDontSee('Pengunjung Online')
                ->assertDontSee('Negara Teratas');
        });
    }
}
