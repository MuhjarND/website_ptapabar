<?php

namespace App\Support;

class PublicNavigation
{
    public const SIPP_BANDING_URL = 'https://banding.mahkamahagung.go.id/';

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

        return [
            ['label' => 'Profil Pengadilan', 'pages' => $pages->get('tentang-pengadilan', collect()), 'links' => []],
            ['label' => 'Layanan Hukum', 'pages' => $pages->get('informasi-hukum', collect())
                ->concat($general->whereNotIn('slug', $publicServicePages))->concat($pages->get('peraturan-kebijakan', collect())),
                'links' => ['Jadwal Sidang Banding' => route('court-hearings.index'), 'Statistik Perkara' => route('case-statistics.index')]],
            ['label' => 'Transparansi', 'pages' => $pages->get('transparansi', collect()), 'links' => []],
            ['label' => 'Layanan Publik & Pengaduan', 'pages' => $services,
                'links' => ['Form Layanan Publik' => route('service-request.form'), 'Survei Pelayanan' => route('home') . '#survei-pelayanan']],
            ['label' => 'Berita dan Publikasi', 'pages' => $pages->get('informasi', collect()), 'links' => [
                'Berita' => route('berita.index'), 'Pengumuman' => route('pengumuman.index'),
                'Tinta Peradilan' => route('article.index'), 'Galeri' => route('gallery.index'), 'Agenda Pimpinan' => route('agenda.index'),
            ]],
        ];
    }

    public static function quickLinks()
    {
        return [
            ['SIPP Banding', 'Penelusuran perkara tingkat banding.', static::SIPP_BANDING_URL, 'fas fa-search'],
            ['Jadwal Sidang', 'Jadwal persidangan banding per hari.', route('court-hearings.index'), 'fas fa-calendar-alt'],
            ['E-Court', 'Layanan administrasi perkara elektronik.', 'https://ecourt.mahkamahagung.go.id/', 'fas fa-scale-balanced'],
            ['Direktori Putusan', 'Informasi dan tautan penelusuran putusan.', route('court-verdicts.index'), 'fas fa-folder-open'],
            ['Survei Pelayanan', 'Lihat indeks pelayanan pengadilan.', route('home') . '#survei-pelayanan', 'fas fa-chart-bar'],
            ['Pengaduan', 'Sampaikan pengaduan atau permohonan informasi.', route('service-request.form'), 'fas fa-headset'],
        ];
    }
}
