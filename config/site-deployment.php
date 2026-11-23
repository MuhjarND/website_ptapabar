<?php

return [
    'enforce_https' => env('SITE_ENFORCE_HTTPS', false),
    'wordpress_redirects' => env('SITE_WORDPRESS_REDIRECTS', false),
    'legacy_paths' => [
        'home' => 'home',
        'renstra' => 'renstra',
        'rkt' => 'rkt',
        'iku' => 'iku',
        'transparansi/anggaran/dipa' => 'dipa',
        'catatan-atas-laporan-keuangan-calk' => 'catatan-atas-laporan-keuangan-calk',
        'informasi-umum/informasi-layanan-pengadilan/jadwal-sidang' => 'court-hearings.index',
        'informasi-umum/layanan-informasi-perkara/statistik-perkara' => 'case-statistics.index',
        'informasi-umum/layanan-informasi-perkara/direktori-putusan' => 'court-verdicts.index',
    ],
];
