<?php

namespace App\Http\Middleware;

use App\VisitorCount;
use Closure;

class TrackVisitors
{
    public function handle($request, Closure $next)
    {
        $today = now()->toDateString();
        $sessionKey = 'visitor_counted_' . $today;

        if (!session()->has($sessionKey)) {
            VisitorCount::updateOrCreate(
                ['visit_date' => $today],
                ['count' => \DB::raw('count + 1')]
            );
            // For new records, the raw expression won't work properly, so handle it
            $record = VisitorCount::where('visit_date', $today)->first();
            if (!$record) {
                VisitorCount::create(['visit_date' => $today, 'count' => 1]);
            }
            session([$sessionKey => true]);
        }

        return $next($request);
    }
}
