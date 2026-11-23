<?php

namespace App\Http\Middleware;

use App\Page;
use Closure;
use Illuminate\Support\Facades\Schema;

class SiteDeployment
{
    public function handle($request, Closure $next)
    {
        if (config('site-deployment.enforce_https') && !$request->isSecure()) {
            $base = rtrim(config('app.url'), '/');
            abort_unless(parse_url($base, PHP_URL_SCHEME) === 'https' && parse_url($base, PHP_URL_HOST), 503);
            return redirect()->away($base . '/' . ltrim($request->getRequestUri(), '/'), 308);
        }

        $path = trim($request->path(), '/');
        $legacy = config('site-deployment.legacy_paths')[$path] ?? null;
        if (config('site-deployment.wordpress_redirects') && $legacy && $request->isMethodSafe()) {
            if ($legacy === 'home' || strpos($legacy, '.') !== false) {
                return redirect()->route($legacy, [], 301);
            }
            if (Schema::hasTable('pages') && Page::where('slug', $legacy)->where('is_active', true)->exists()) {
                return redirect()->route('page.show', $legacy, 301);
            }
        }

        $response = $next($request);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
