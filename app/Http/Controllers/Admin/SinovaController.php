<?php

namespace App\Http\Controllers\Admin;

use App\CaseStatistic;
use App\CourtHearing;
use App\Http\Controllers\Controller;
use App\Page;
use App\Post;
use App\Support\PublicNavigation;
use App\TransparencyDocument;

class SinovaController extends Controller
{
    public function index()
    {
        $groups = config('sinova.groups');
        $slugs = collect($groups)->collapse()->pluck(2)
            ->filter(fn (string $source) => str_starts_with($source, 'page:'))
            ->map(fn (string $source) => substr($source, 5))->unique();
        $pages = Page::whereIn('slug', $slugs)
            ->withCount(['transparencyDocuments' => fn ($query) => $query->published()])
            ->get()->keyBy('slug');

        $hearing = CourtHearing::where('is_published', true)->max('hearing_date');
        $period = CaseStatistic::where('is_published', true)->max('period');
        $home = $this->publicRoute('home');
        $sources = [
            'sipp' => [PublicNavigation::SIPP_BANDING_URL, null, ''],
            'hearings' => [$hearing ? $this->publicRoute('court-hearings.index', ['tanggal' => $hearing]) : null,
                route('admin.court-information.index', 'hearings'), 'Belum ada jadwal sidang banding yang terbit.'],
            'statistics' => [$period ? $this->publicRoute('case-statistics.index', ['tahun' => substr($period, 0, 4)]) : null,
                route('admin.court-information.index', 'statistics'), 'Belum ada statistik perkara yang terbit.'],
            'visitors' => [$home . '#statistik-web', null, ''],
            'verdicts' => [PublicNavigation::verdictsUrl(), null, 'Atur COURT_VERDICTS_URL ke tautan koleksi putusan PTA Papua Barat.'],
            'home' => [$home, null, ''],
        ];
        $assessment = [
            'not-applicable' => ['Tidak berlaku untuk PTA', 'Butir ini khusus pengadilan tingkat pertama.'],
            'checklist' => ['Checklist', 'Diisi saat penilaian.'],
            'choice' => ['Pilihan', 'Diisi saat penilaian.'],
            'api' => ['Angka / API', 'Dinilai via API pada sistem pelaporan.'],
            'manual' => ['Pemeriksaan', 'Periksa tautan saat penilaian.'],
        ];

        $report = [];
        foreach ($groups as $group => $items) {
            foreach ($items as $code => [$title, $description, $source]) {
                $url = $manageUrl = null;
                $type = 'URL Sistem';
                if (isset($assessment[$source])) {
                    [$type, $note] = $assessment[$source];
                } elseif (str_starts_with($source, 'page:')) {
                    $slug = substr($source, 5);
                    $page = $pages->get($slug);
                    $manageUrl = route('admin.pages.managed', $slug);
                    $note = match (true) {
                        !$page => 'Halaman belum dibuat.',
                        !$page->is_active => 'Halaman belum aktif.',
                        !$this->hasPublishedContent($page) => 'Isi halaman atau terbitkan dokumen terlebih dahulu.',
                        default => '',
                    };
                    if ($note === '') {
                        $url = $this->publicRoute('page.show', ['slug' => $slug]);
                    }
                } else {
                    [$url, $manageUrl, $note] = $sources[$source];
                }

                $report[$group][$code] = compact('title', 'description', 'type', 'url', 'manageUrl', 'note');
            }
        }

        return view('admin.sinova', [
            'report' => $report,
            'hasHttpsBase' => parse_url($home, PHP_URL_SCHEME) === 'https',
        ]);
    }

    private function hasPublishedContent(Page $page): bool
    {
        if ($page->menu_group === TransparencyDocument::PAGE_GROUP && $page->transparency_documents_count > 0) {
            return true;
        }

        $text = strtolower(Post::sanitizeExcerpt($page->content));
        return !str_contains($text, 'konten halaman ini sedang dalam proses pembaruan')
            && ($text !== '' || preg_match('/<(?:img|iframe)\b[^>]*\bsrc\s*=/i', (string) $page->content));
    }

    private function publicRoute(string $route, array $parameters = []): string
    {
        $base = config('sinova.public_url');
        $parts = is_string($base) && filter_var($base, FILTER_VALIDATE_URL) ? parse_url($base) : [];
        if (($parts['scheme'] ?? null) !== 'https' || isset($parts['query']) || isset($parts['fragment']) || isset($parts['user']) || isset($parts['pass'])) {
            return route($route, $parameters);
        }

        return rtrim($base, '/') . route($route, $parameters, false);
    }
}
