<?php

namespace App\Support;

use App\VisitorCount;
use App\VisitorSession;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VisitorAnalytics
{
    public static function buildStatistics()
    {
        $today = Carbon::today();
        $weekStart = Carbon::today()->subDays(6);
        $monthStart = Carbon::now()->startOfMonth();
        $onlineSince = Carbon::now()->subMinutes(5);

        $devices = VisitorSession::query()
            ->selectRaw('device_type, COUNT(*) as total')
            ->groupBy('device_type')
            ->pluck('total', 'device_type');

        $countries = VisitorSession::query()
            ->selectRaw('country_code, country_name, COUNT(*) as total')
            ->whereNotNull('country_name')
            ->groupBy('country_code', 'country_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($country) {
                return [
                    'code' => $country->country_code,
                    'name' => $country->country_name,
                    'flag' => static::flagEmoji($country->country_code),
                    'total' => (int) $country->total,
                ];
            });

        return [
            'today' => (int) (VisitorCount::whereDate('visit_date', $today)->sum('count') ?? 0),
            'week' => (int) (VisitorCount::whereDate('visit_date', '>=', $weekStart->toDateString())->sum('count') ?? 0),
            'month' => (int) (VisitorCount::whereDate('visit_date', '>=', $monthStart->toDateString())->sum('count') ?? 0),
            'total' => (int) (VisitorCount::sum('count') ?? 0),
            'devices' => [
                'mobile' => (int) ($devices->get('mobile', 0)),
                'desktop' => (int) ($devices->get('desktop', 0)),
                'tablet' => (int) ($devices->get('tablet', 0)),
                'unknown' => (int) ($devices->get('unknown', 0)),
            ],
            'countries' => $countries,
            'online' => (int) VisitorSession::where('last_seen_at', '>=', $onlineSince)->count(),
        ];
    }

    public static function deviceTypeFromUserAgent($userAgent)
    {
        $userAgent = strtolower((string) $userAgent);

        if ($userAgent === '') {
            return 'unknown';
        }

        if (strpos($userAgent, 'ipad') !== false || strpos($userAgent, 'tablet') !== false) {
            return 'tablet';
        }

        if (
            strpos($userAgent, 'mobile') !== false ||
            strpos($userAgent, 'android') !== false ||
            strpos($userAgent, 'iphone') !== false
        ) {
            return 'mobile';
        }

        if (
            strpos($userAgent, 'windows') !== false ||
            strpos($userAgent, 'macintosh') !== false ||
            strpos($userAgent, 'linux') !== false
        ) {
            return 'desktop';
        }

        return 'unknown';
    }

    public static function countryFromRequest(Request $request)
    {
        $countryCode = static::countryCodeFromHeaders($request);

        if (!$countryCode) {
            return ['code' => null, 'name' => 'Unknown'];
        }

        return [
            'code' => $countryCode,
            'name' => static::countryName($countryCode),
        ];
    }

    public static function countryName($countryCode)
    {
        $countryCode = strtoupper((string) $countryCode);

        if ($countryCode === '' || $countryCode === 'XX') {
            return 'Unknown';
        }

        if (class_exists('\Locale')) {
            $name = \Locale::getDisplayRegion('-' . $countryCode, 'en');
            if ($name) {
                return $name;
            }
        }

        $fallbacks = [
            'US' => 'United States',
            'SG' => 'Singapore',
            'GB' => 'United Kingdom',
            'DE' => 'Germany',
            'ID' => 'Indonesia',
        ];

        return $fallbacks[$countryCode] ?? $countryCode;
    }

    public static function flagEmoji($countryCode)
    {
        $countryCode = strtoupper((string) $countryCode);

        if (strlen($countryCode) !== 2 || !preg_match('/^[A-Z]{2}$/', $countryCode)) {
            return '🌐';
        }

        $first = 127397 + ord($countryCode[0]);
        $second = 127397 + ord($countryCode[1]);

        return html_entity_decode('&#' . $first . ';&#' . $second . ';', ENT_NOQUOTES, 'UTF-8');
    }

    protected static function countryCodeFromHeaders(Request $request)
    {
        $headers = [
            'CF-IPCountry',
            'CloudFront-Viewer-Country',
            'X-Country-Code',
            'X-Country',
            'X-AppEngine-Country',
        ];

        foreach ($headers as $header) {
            $value = strtoupper((string) $request->header($header));
            if (preg_match('/^[A-Z]{2}$/', $value)) {
                return $value;
            }
        }

        return null;
    }
}
