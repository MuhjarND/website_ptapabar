<?php

namespace App\Support;

use App\Setting;

class HomeLayout
{
    public const KEY = 'home_layout';
    public const ROWS = 40;
    public const LIMIT = 24;

    public const BLOCKS = [
        'hero-slider' => ['Slider Utama', 'images', 12],
        'welcome-strip' => ['Sambutan Beranda', 'align-left', 12],
        'quick-links' => ['Pintasan Layanan', 'th-large', 12],
        'announcement' => ['Pengumuman', 'bullhorn', 6],
        'article' => ['Tinta Peradilan', 'book-open', 6],
        'latest-news' => ['Berita Terkini', 'newspaper', 12],
        'religious-court-news' => ['Berita Peradilan Agama Papua Barat', 'newspaper', 12],
        'agendas' => ['Agenda Pimpinan', 'calendar-alt', 12],
        'gallery-showcase' => ['Galeri PTA Papua Barat', 'photo-video', 12],
        'integrity-zones' => ['Zona Integritas', 'shield-alt', 12],
        'surveys' => ['Indeks Survei Pelayanan', 'chart-bar', 12],
        'applications' => ['Daftar Aplikasi', 'layer-group', 12],
    ];

    public const CUSTOM = [
        'text' => ['Teks Bebas', 'align-left', 6],
        'search' => ['Kotak Pencarian', 'search', 6],
        'banner' => ['Gambar / Banner', 'image', 6],
    ];

    public static function saved(): ?array
    {
        $layout = json_decode(Setting::get(self::KEY) ?? 'null', true);

        return is_array($layout) && array_is_list($layout) ? $layout : null;
    }

    public static function defaults(): array
    {
        $row = 1;
        $blocks = [];

        foreach (self::BLOCKS as $type => [, , $w]) {
            $x = $type === 'article' ? 7 : 1;
            $blocks[] = ['type' => $type, 'x' => $x, 'y' => $row, 'w' => $w, 'h' => 1];
            if ($type !== 'announcement') {
                $row++;
            }
        }

        return $blocks;
    }
}
