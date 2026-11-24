<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PapedaCalendar
{
    public function month(mixed $selection): array
    {
        $today = CarbonImmutable::today('Asia/Jayapura');
        $month = $today->startOfMonth();
        $first = $month->subYear();
        $last = $month->addYear();

        if (is_string($selection) && preg_match('/\A\d{4}-(0[1-9]|1[0-2])\z/', $selection)) {
            $candidate = CarbonImmutable::parse($selection . '-01', 'Asia/Jayapura');
            if ($candidate->betweenIncluded($first, $last)) {
                $month = $candidate;
            }
        }

        return [
            'month' => $month,
            'today' => $today,
            'previous' => $month > $first ? $month->subMonth()->format('Y-m') : null,
            'next' => $month < $last ? $month->addMonth()->format('Y-m') : null,
            'events' => $this->events($month),
        ];
    }

    private function events(CarbonImmutable $month): ?array
    {
        $url = config('services.papeda.public_calendar_url');
        if (!is_string($url) || !filter_var($url, FILTER_VALIDATE_URL) ||
            !in_array(parse_url($url, PHP_URL_SCHEME), ['https', 'http'], true)) {
            return null;
        }

        $key = 'papeda-public-calendar:' . sha1($url . '|' . $month->format('Y-m'));
        $cached = Cache::get($key);
        if (is_array($cached)) {
            return $cached['events'];
        }

        try {
            $response = Http::acceptJson()->withoutRedirecting()->connectTimeout(1)->timeout(2)
                ->get($url, ['month' => $month->format('Y-m')]);
            $data = $response->successful() ? $response->json('events') : null;
        } catch (ConnectionException) {
            $data = null;
        }

        $events = is_array($data) ? $this->validEvents($data, $month) : null;
        Cache::put($key, ['events' => $events], $events === null ? 30 : 300);

        return $events;
    }

    private function validEvents(array $data, CarbonImmutable $month): array
    {
        $dates = array_map(fn ($day) => $month->day($day)->toDateString(), range(1, $month->daysInMonth));

        return collect($data)->filter(fn ($event) => is_array($event)
            && is_string($event['title'] ?? null)
            && trim($event['title']) !== ''
            && in_array($event['date'] ?? null, $dates, true))
            ->map(fn ($event) => [
                'title' => $event['title'],
                'date' => $event['date'],
                'end_date' => in_array($event['end_date'] ?? null, $dates, true) && $event['end_date'] >= $event['date']
                    ? $event['end_date'] : $event['date'],
                'time' => is_string($event['time'] ?? null) && preg_match('/\A(?:[01]\d|2[0-3]):[0-5]\d\z/', $event['time'])
                    ? $event['time'] : null,
            ])
            ->sortBy(['date', 'time'])
            ->values()
            ->all();
    }
}
