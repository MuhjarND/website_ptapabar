<?php

namespace App\Support;

use App\VisitorCount;
use Illuminate\Http\Request;

class VisitorAnalytics
{
    public static function buildStatistics()
    {
        $today = today();

        return [
            'today' => (int) VisitorCount::whereDate('visit_date', $today)->sum('count'),
            'yesterday' => (int) VisitorCount::whereDate('visit_date', $today->copy()->subDay())->sum('count'),
            'total' => (int) VisitorCount::sum('count'),
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
