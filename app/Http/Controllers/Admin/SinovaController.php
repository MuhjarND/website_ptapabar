<?php

namespace App\Http\Controllers\Admin;

use App\CaseStatistic;
use App\CourtHearing;
use App\Http\Controllers\Controller;
use App\Page;
use App\Support\PublicNavigation;
use App\SurveyIndex;
use App\TransparencyDocument;

class SinovaController extends Controller
{
    public function index()
    {
        $groups = config('sinova.groups');
        $slugs = collect($groups)->collapse()->pluck(2)
            ->filter(fn (string $source) => str_starts_with($source, 'page:'))
            ->map(fn (string $source) => substr($source, 5))->unique();
        $year = now()->year;
        $pages = Page::whereIn('slug', $slugs)
            ->withCount(['transparencyDocuments' => fn ($query) => $query->published()])
            ->withExists(['transparencyDocuments as has_current_year_document' => fn ($query) => $query->published()->where('year', $year)])
            ->get()->keyBy('slug');

        $hearing = CourtHearing::where('is_published', true)->max('hearing_date');
        $period = CaseStatistic::where('is_published', true)->max('period');
        $publishedSurveyTypes = SurveyIndex::where('is_active', true)->whereNotNull('report_url')->get()
            ->filter(fn (SurveyIndex $survey) => filled($survey->period) && $survey->public_report_url)
            ->map(fn (SurveyIndex $survey) => $survey->evidenceType())->filter()->unique()->all();
        $missingSurveyTypes = array_diff(array_keys(SurveyIndex::EVIDENCE_TYPES), $publishedSurveyTypes);
        $home = $this->publicRoute('home');
        $verdicts = PublicNavigation::verdictsUrl();
        $sources = [
            'sipp' => [PublicNavigation::SIPP_BANDING_URL, null, ''],
            'hearings' => [$hearing ? $this->publicRoute('court-hearings.index', ['tanggal' => $hearing]) : null,
                route('admin.court-information.index', 'hearings'), $hearing ? '' : 'Belum ada jadwal sidang banding yang terbit.'],
            'statistics' => [$period ? $this->publicRoute('case-statistics.index', ['tahun' => substr($period, 0, 4)]) : null,
                route('admin.court-information.index', 'statistics'), $period ? '' : 'Belum ada statistik perkara yang terbit.'],
            'visitors' => [$home . '#statistik-web', null, ''],
            'surveys' => [$publishedSurveyTypes ? $this->publicRoute('surveys.public') : null, route('admin.surveys.index'),
                $missingSurveyTypes ? 'Lengkapi nilai, periode, dan tautan laporan: ' . implode(', ', $missingSurveyTypes) . '.' : ''],
            'ecourt' => [$this->publicRoute('court-ecourt.index'), null, ''],
            'verdicts' => [$verdicts, null, $verdicts ? '' : 'Atur COURT_VERDICTS_URL ke tautan koleksi putusan PTA Papua Barat.'],
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
                    $hasFallback = isset(Page::PUBLIC_FALLBACKS[$slug]);
                    $usesFallback = $hasFallback && (!$page || ($page->is_active && !$page->hasPublicContent()));
                    $note = $usesFallback ? match ($slug) {
                        'standar-dan-maklumat-pelayanan' => 'Periksa apakah dokumen yang ditautkan masih berlaku.',
                        default => '',
                    } : match (true) {
                        !$page => 'Halaman belum dibuat.',
                        !$page->is_active => 'Halaman belum aktif.',
                        !$this->hasPublishedContent($page) => 'Isi halaman atau terbitkan dokumen terlebih dahulu.',
                        default => '',
                    };
                    if ($note === '' || $usesFallback) {
                        $url = $this->publicRoute('page.show', ['slug' => $slug]);
                    }
                    if ($url && $code === '1.a.3.h' && !$page->has_current_year_document) {
                        $note = "Belum ada dokumen Rencana Aksi tahun {$year} di arsip. Periksa isi halaman sebelum memakai tautan ini.";
                    }
                    if ($url && $slug === 'dipa') {
                        $note = 'Periksa ketiga komponen: DIPA, rencana penggunaan anggaran, dan realisasi DIPA.';
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

        return $page->hasPublicContent();
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
