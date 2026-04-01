<?php

namespace App\Http\Middleware;

use App\Support\VisitorAnalytics;
use App\VisitorCount;
use App\VisitorSession;
use Closure;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TrackVisitors
{
    public function handle($request, Closure $next)
    {
        if (!Schema::hasTable('visitor_counts') || !Schema::hasTable('visitor_sessions')) {
            return $next($request);
        }

        $today = now()->toDateString();
        $sessionKey = 'visitor_counted_' . $today;
        $visitorKey = session('visitor_tracking_key');

        if (!$visitorKey) {
            $visitorKey = (string) Str::uuid();
            session(['visitor_tracking_key' => $visitorKey]);
        }

        if (!session()->has($sessionKey)) {
            $visitorCount = VisitorCount::firstOrCreate(
                ['visit_date' => $today],
                ['count' => 0]
            );
            $visitorCount->increment('count');
            session([$sessionKey => true]);
        }

        $country = VisitorAnalytics::countryFromRequest($request);

        $visitorSession = VisitorSession::firstOrNew(['visitor_key' => $visitorKey]);
        $visitorSession->ip_address = $request->ip();
        $visitorSession->user_agent = $request->userAgent();
        $visitorSession->device_type = VisitorAnalytics::deviceTypeFromUserAgent($request->userAgent());
        $visitorSession->country_code = $country['code'];
        $visitorSession->country_name = $country['name'];
        $visitorSession->first_seen_at = $visitorSession->first_seen_at ?: now();
        $visitorSession->last_seen_at = now();
        $visitorSession->total_requests = (int) $visitorSession->total_requests + 1;
        $visitorSession->save();

        return $next($request);
    }
}
