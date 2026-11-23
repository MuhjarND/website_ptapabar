<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackVisitors;
use App\Setting;
use App\VisitorCount;
use App\VisitorSession;
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

    public function test_footer_displays_detailed_visitor_statistics()
    {
        Carbon::setTestNow(Carbon::parse('2026-03-31 12:00:00'));
        $this->withoutMiddleware(TrackVisitors::class);

        Setting::set('site_name', 'PTA Test');
        Setting::set('address', 'Alamat Test');
        Setting::set('email', 'test@example.com');

        VisitorCount::create(['visit_date' => now()->toDateString(), 'count' => 484]);
        VisitorCount::create(['visit_date' => now()->subDays(2)->toDateString(), 'count' => 4941]);
        VisitorCount::create(['visit_date' => now()->subDays(10)->toDateString(), 'count' => 24372]);
        VisitorCount::create(['visit_date' => now()->subDays(35)->toDateString(), 'count' => 28944]);

        VisitorSession::create([
            'visitor_key' => 'sess-mobile-us',
            'device_type' => 'mobile',
            'country_code' => 'US',
            'country_name' => 'United States',
            'total_requests' => 10,
            'first_seen_at' => now()->subDays(2),
            'last_seen_at' => now(),
        ]);
        VisitorSession::create([
            'visitor_key' => 'sess-desktop-sg',
            'device_type' => 'desktop',
            'country_code' => 'SG',
            'country_name' => 'Singapore',
            'total_requests' => 8,
            'first_seen_at' => now()->subDays(3),
            'last_seen_at' => now(),
        ]);
        VisitorSession::create([
            'visitor_key' => 'sess-tablet-id',
            'device_type' => 'tablet',
            'country_code' => 'ID',
            'country_name' => 'Indonesia',
            'total_requests' => 3,
            'first_seen_at' => now()->subDays(4),
            'last_seen_at' => now()->subMinutes(2),
        ]);
        VisitorSession::create([
            'visitor_key' => 'sess-unknown-gb',
            'device_type' => 'unknown',
            'country_code' => 'GB',
            'country_name' => 'United Kingdom',
            'total_requests' => 2,
            'first_seen_at' => now()->subDays(5),
            'last_seen_at' => now()->subMinutes(3),
        ]);
        VisitorSession::create([
            'visitor_key' => 'sess-offline-de',
            'device_type' => 'mobile',
            'country_code' => 'DE',
            'country_name' => 'Germany',
            'total_requests' => 2,
            'first_seen_at' => now()->subDays(6),
            'last_seen_at' => now()->subMinutes(12),
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('STATISTIK WEB');
        $response->assertSee('484');
        $response->assertSee('5,425');
        $response->assertSee('29,797');
        $response->assertSee('58,741');
        $response->assertSee('Mobile');
        $response->assertSee('Desktop');
        $response->assertSee('Tablet');
        $response->assertSee('Unknown');
        $response->assertSee('United States');
        $response->assertSee('Singapore');
        $response->assertSee('United Kingdom');
        $response->assertSee('Germany');
        $response->assertSee('Online: 4');

        Carbon::setTestNow();
    }
}
