<?php

namespace App\Support;

use App\PostCategory;

class PublicNavigation
{
    public const SIPP_BANDING_URL = 'https://banding.mahkamahagung.go.id/';
    public const ECOURT_URL = 'https://ecourt.mahkamahagung.go.id/';

    public static function serviceUrl(?string $url): ?string
    {
        return match (rtrim($url ?? '', '/')) {
            'https://sipp.mahkamahagung.go.id', 'http://sipp.mahkamahagung.go.id' => self::SIPP_BANDING_URL,
            default => $url,
        };
    }

    public static function verdictsUrl(): ?string
    {
        $url = config('court.verdicts_url');
        if (!is_string($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($url);
        return $parts['scheme'] === 'https'
            && $parts['host'] === 'putusan3.mahkamahagung.go.id'
            && !isset($parts['user'])
            && !isset($parts['pass'])
            && trim($parts['path'] ?? '', '/') !== ''
            ? $url : null;
    }

    public static function groups($pages)
    {
        $publicServicePages = [
            'layanan-permintaan-informasi', 'pengaduan-masyarakat', 'fasilitas-publik',
            'standar-dan-maklumat-pelayanan', 'sop-khusus-pelayanan-publik', 'maklumat-layanan-informasi-publik',
            'petugas-informasi-pelayanan-terpadu-satu-pintu',
        ];
        $general = $pages->get('informasi-umum', collect());
        $services = $general->whereIn('slug', $publicServicePages);
        $agendaNews = request()->routeIs('berita.index') && request('kategori') === PostCategory::LEADERSHIP_AGENDA;

        return [
            ['label' => 'Profil Pengadilan', 'pages' => $pages->get('tentang-pengadilan', collect()), 'links' => []],
            ['label' => 'Layanan Hukum', 'pages' => $pages->get('informasi-hukum', collect())
                ->concat($general->whereNotIn('slug', $publicServicePages))->concat($pages->get('peraturan-kebijakan', collect())),
                'links' => [
                    'Jadwal Sidang Banding' => self::link('court-hearings.index'),
                    'Statistik Perkara' => self::link('case-statistics.index'),
                    'Direktori Putusan' => self::link('court-verdicts.index'),
                    'e-Court' => self::link('court-ecourt.index'),
                ]],
            ['label' => 'Transparansi', 'pages' => $pages->get('transparansi', collect()), 'links' => []],
            ['label' => 'Layanan Publik & Pengaduan', 'pages' => $services,
                'links' => [
                    'Form Layanan Publik' => self::link('service-request.form'),
                    'Hasil Survei Pelayanan' => self::link('surveys.public'),
                ]],
            ['label' => 'Berita dan Publikasi', 'pages' => $pages->get('informasi', collect()), 'links' => [
                'Berita' => $agendaNews ? ['url' => route('berita.index'), 'current' => null] : self::link('berita.index', 'post.detail'),
                'Pengumuman' => self::link('pengumuman.index', 'pengumuman.detail'),
                'Tinta Peradilan' => self::link('article.index', 'article.detail'),
                'Galeri' => self::link('gallery.index'),
                'Agenda Pimpinan' => [
                    'url' => route('berita.index', ['kategori' => PostCategory::LEADERSHIP_AGENDA]),
                    'current' => $agendaNews ? 'page' : (request()->routeIs('agenda.wordpress.show', 'agenda.index') ? 'location' : null),
                ],
            ]],
        ];
    }

    private static function link(string $route, string ...$relatedRoutes): array
    {
        return [
            'url' => route($route),
            'current' => request()->routeIs($route) ? 'page' : (request()->routeIs(...$relatedRoutes) ? 'location' : null),
        ];
    }

    public static function quickLinks()
    {
        return [
            ['Jadwal Sidang', 'Jadwal persidangan banding per hari.', route('court-hearings.index'), 'fas fa-calendar-alt'],
            ['Direktori Putusan', 'Arsip putusan Mahkamah Agung.', route('court-verdicts.index'), 'fas fa-balance-scale'],
            ['E-Court', 'Pendaftaran dan persidangan elektronik.', route('court-ecourt.index'), 'far fa-file-alt'],
            ['E-AC', 'Layanan akta cerai dan salinan putusan.', 'https://eac.mahkamahagung.go.id/', 'fas fa-book'],
            ['Penelusuran Perkara', 'Lacak status perkara tingkat banding.', static::SIPP_BANDING_URL, 'fas fa-search'],
            ['SIWAS', 'Sistem Informasi Pengawasan Mahkamah Agung.', 'https://siwas.mahkamahagung.go.id/', 'fas fa-shield-alt'],
        ];
    }
}
