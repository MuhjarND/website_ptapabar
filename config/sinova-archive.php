<?php

$uploads = 'https://pta-papuabarat.go.id/wp-content/uploads/';
$drive = 'https://drive.google.com/file/d/';

// Only PDFs already published on the official website are included.
$pages = [
    'renstra' => 'Renstra',
    'rencana-dan-kinerja' => 'Rencana Aksi Kinerja',
    'rkt' => 'Rencana Kinerja Tahunan',
    'iku' => 'Indikator Kinerja Utama',
    'perjanjian-kinerja' => 'Perjanjian Kinerja',
    'lkjip' => 'Laporan Kinerja Instansi Pemerintah',
    'laporan-tahunan' => 'Laporan Tahunan',
    'dipa' => 'Daftar Isian Pelaksanaan Anggaran (DIPA)',
    'realisasi-anggaran' => 'Realisasi Anggaran',
    'catatan-atas-laporan-keuangan-calk' => 'Catatan atas Laporan Keuangan',
    'arsip-survei-pelayanan' => 'Arsip Survei Pelayanan',
];

// Filename => [page, title, reporting year, source, SHA-256, period, version, budget code].
$entries = [
    'renstra-reviu-2025-2029.pdf' => ['renstra', 'Reviu Renstra 2025-2029', 2025, $uploads . '2026/07/01_REVIU-RENSTRA-2025-2029.pdf',
        '36f4b8005ee3e59d7f8f1ea132358a3e27bde8f33b5cbc09728c1f46cd1e0285', '2025-2029', 'Reviu'],
    'renstra-rancangan-2025-2029.pdf' => ['renstra', 'Rancangan Renstra 2025-2029', 2025, $uploads . '2025/09/2_RENSTRA-2025-2029.pdf',
        'da0b233a7a0a346625e8185376c54832ae3691502383a192e5599a607897f683', '2025-2029', 'Rancangan'],
    'renstra-rancangan-2020-2024.pdf' => ['renstra', 'Rancangan Renstra 2020-2024', 2020, $drive . '17N5ljxu4z62k1T33OvKcwGFvF-eQsLW4/view',
        '48794d6af9ff655b64658c45841f29bcb3a21513878b000a8b5135be5625e7d5', '2020-2024', 'Rancangan'],
    'renaksi-2025.pdf' => ['rencana-dan-kinerja', 'Rencana Aksi Perjanjian Kinerja 2025', 2025, $uploads . '2025/09/6_RENCANA-AKSI-2025.pdf',
        'b0197656777a01ea23250fb3d13a298087dcb927fbd85d1481bc25f7c25374e6'],
    'renaksi-2024.pdf' => ['rencana-dan-kinerja', 'Rencana Aksi Kinerja 2024', 2024, $uploads . '2024/05/RENCANA_AKSI_3300_526718_PENGADILAN-TINGGI-AGAMA-PAPUA-BARAT_2024__VUtDL1lPOXMvVFhN.pdf',
        'ad2b18c2a73738b6ddada8e3f0ac303a691d7416497cbc7953398c5d4a2ec36c'],
    'rkt-reviu-2026.pdf' => ['rkt', 'Reviu Rencana Kinerja Tahunan 2026', 2026, $uploads . '2026/07/02_REVIU-RENCANA-KINERJA-2026.pdf',
        '37e8c58c32dcc0ed4d3b343993eb110a3b04e9769bee1c48675e1ad594a1c363', null, 'Reviu'],
    'rkt-2025.pdf' => ['rkt', 'Rencana Kinerja Tahunan 2025', 2025, $drive . '1HJmEsFgHmAx_LEdOJ1PdZfTs03zEKZVt/view',
        'eaec75be033b6520f9d0df287bd4f875a2d8e65d579e2d8dccdc914a479b4233'],
    'rkt-2023.pdf' => ['rkt', 'Rencana Kinerja Tahunan 2023', 2023, $drive . '1KjZXdclI7ZBfkc99eJRNs6LqLx401s77/view',
        'bd08b85e0d40ec0cf4cd9e156b1b7754c304c8bc0119d4b59b10d735984038ff'],
    'iku-reviu-2025.pdf' => ['iku', 'Penetapan Hasil Reviu IKU 2025', 2025, $uploads . '2026/07/00_REVIU-IKU-2025.pdf',
        '3894867157e7f03f50a339f2c684e64292e3b1ca1ce0809e1c1876d40172f9b0', null, 'Reviu'],
    'iku-reviu-2024.pdf' => ['iku', 'Reviu IKU 2024', 2024, $drive . '1_1dwFK3HctfQodAk5cTptL-6TQm4j9tm/view',
        '23f6ea585fafbb377227c4f07751eb1a53b3ac6ffbd5b3b5c87a6c11ed4c41e2', null, 'Reviu'],
    'iku-reviu-2023.pdf' => ['iku', 'Reviu IKU 2023', 2023, $drive . '1mLDMpkvKrSSwZPxu1RDGewfDiWKNnrhm/view',
        'e3cdbb564bc661c06584cfc5f305dd21253c5986802b75188de67dda8ad7180f', null, 'Reviu'],
    'pk-reviu-2025.pdf' => ['perjanjian-kinerja', 'Reviu Perjanjian Kinerja 2025', 2025, $uploads . '2026/07/03_REVIU-PERJANJIAN-KINERJA-2025.pdf',
        '58158535f8043d7da9e0f08b2c948069ffc98aea38c5c180cc7bead6eec9dd70', null, 'Reviu'],
    'pk-2024.pdf' => ['perjanjian-kinerja', 'Perjanjian Kinerja 2024', 2024, $drive . '1dhC4o7TzxduIh3RG-fXPRmsb1R4YAUNu/view',
        'ee573e88eaee29dfde31d5d9ab685ec36b786eb67ec1c658e4e9c15898a1d23d'],
    'pk-2023.pdf' => ['perjanjian-kinerja', 'Perjanjian Kinerja 2023', 2023, $drive . '1V64wn2xKPAtRvscKBn9SJMarKCn8YXS8/view',
        '3e95fbbf2ea8462c26b6405bfb3adc955cb7b8c2809b26eb288586e9dc719999'],
    'lkjip-2024.pdf' => ['lkjip', 'LKjIP Tahun Kinerja 2024', 2024, $drive . '1Go81NOXpOHt_1oaWjYKmdQDBLDkBxmaA/view',
        'd7e4799e2ac69497530e05a290d3ce898dcb2a2220bba5cc158ccf797776973f'],
    'laporan-tahunan-2025.pdf' => ['laporan-tahunan', 'Laporan Pelaksanaan Kegiatan 2025', 2025, $drive . '1MiWUtMvwPgQWyx_vbwbMetDwRrWiB-jc/view',
        '72a96963313afb74d34abb460a5db8cff5d19e4b4fc2c895b060e88f908c1f0f'],
    'laporan-tahunan-2024.pdf' => ['laporan-tahunan', 'Laporan Pelaksanaan Kegiatan 2024', 2024, $drive . '1wikwUX9ZIHtCmVXS2yqvi1eC8jLLfIqw/view',
        'd0e3b26642d713ff1ae01585c51e52a2f9c3d09d41221d5b6f482a2b8a8c5c23'],
    'laporan-tahunan-2023.pdf' => ['laporan-tahunan', 'Laporan Pelaksanaan Kegiatan 2023', 2023, $drive . '1HOaI1hBIeLG9Z-6hLlh6x7qi0rfbblxe/view',
        '071d8512ea9a34aecfb8f4bfdf6f2dc3082f4303c8baf26b1f605bb07780dd7a'],
    'dipa-01-2026.pdf' => ['dipa', 'DIPA 01 TA 2026', 2026, $uploads . '2026/07/DIPA_01_2026.pdf',
        'f79349d1660e7b001f9e65118f92ee092d4d530349117ea3ad71ef6a9885fa95', null, null, '01 / 526718'],
    'dipa-04-2026.pdf' => ['dipa', 'DIPA 04 TA 2026', 2026, $uploads . '2026/07/DIPA_04_2026.pdf',
        '26ef6dfa95b7ff61a295d02ecbc6d54ff9e6fa5b3d6b813b33f96c73c2db70d4', null, null, '04 / 526719'],
    'dipa-01-2025.pdf' => ['dipa', 'DIPA 01 TA 2025', 2025, $uploads . '2025/01/dipa-1.pdf',
        '30a11c0084cd2b168eb6295af57c6c31e5b1193cc7c8fdd82ec558eaaa037b63', null, null, '01 / 526718'],
    'dipa-04-2025.pdf' => ['dipa', 'DIPA 04 TA 2025', 2025, $uploads . '2025/01/dipa-2.pdf',
        '92f0fe2401960c0276870e5cdaf00b591a6617b1393e98b3931abebcd01c4c9e', null, null, '04 / 526719'],
    'dipa-01-2024.pdf' => ['dipa', 'DIPA 01 TA 2024', 2024, $uploads . '2024/05/2024-01-005.01-526718-33-065-2-03.pdf',
        'fd2459d63ef4718412c48c7d8ec50103ec036808d6050e0a40a4fb1feca49dc6', null, 'Revisi 03', '01 / 526718'],
    'dipa-04-2024.pdf' => ['dipa', 'DIPA 04 TA 2024', 2024, $uploads . '2024/05/2024-01-005.04-526719-33-065-2-02-1.pdf',
        '443121099b4f65ae3893a1ffb4c3b388282a7b3e579c08188ed460d85aa25ed3', null, 'Revisi 02', '04 / 526719'],
    'lra-01-sep-2025.pdf' => ['realisasi-anggaran', 'Laporan Realisasi SP2D DIPA 01', 2025, $uploads . '2025/09/LRA-September_Dipa01_2025.pdf',
        '4e736acb2b74a787705cb1db552a73f8accc77d13247ed015616d1e53e33f650', 'September 2025', null, '01 / 526718'],
    'lra-04-sep-2025.pdf' => ['realisasi-anggaran', 'Laporan Ketersediaan Dana DIPA 04', 2025, $uploads . '2025/09/LRA-September_Dipa04_2025.pdf',
        'b54b8daecfe7af4bb49d341388a1bbe45fcc25926e1f10c9f4ee9cbb15fecd8d', 'September 2025', null, '04 / 526719'],
    'lra-01-des-2024.pdf' => ['realisasi-anggaran', 'Laporan Ketersediaan Dana DIPA 01', 2024, $uploads . '2025/01/LRA-Des-2024-DIPA-01.pdf',
        '9dc21abfb91e9002bdc8c0c26b30d45bfff0c3430b0215a7d208173da8825c06', 'Desember 2024', null, '01 / 526718'],
    'lra-04-des-2024.pdf' => ['realisasi-anggaran', 'Laporan Ketersediaan Dana DIPA 04', 2024, $uploads . '2025/01/LRA-Des-2024-DIPA-04.pdf',
        '7cf42f78655beb1b0ae3e5b4e3a17ec2b148de41edca806bdb881088dfd54ee3', 'Desember 2024', null, '04 / 526719'],
    'calk-526719-juni-2025.pdf' => ['catatan-atas-laporan-keuangan-calk', 'Laporan Keuangan UAKPA dan CALK', 2025, $uploads . '2025/08/CaLK_526719_2025_SEMESTER-I-JUNI.pdf',
        '7ad89824f3b7494fce7a5acbd20a2fda7f5eb01e7794b0a25f2c27d2d29b0364', 'Semester I / Juni 2025', null, '04 / 526719'],
    'survei-badilag-2025-triwulan-2.pdf' => ['arsip-survei-pelayanan', 'Ringkasan Survei Badilag Triwulan II 2025', 2025, $uploads . '2025/08/Dashboard-1.pdf',
        '85d3b8fa0515687e26b30692f2213fd8ead667ff38ddecd1019960142f325e27', 'Triwulan II 2025'],
];

$keys = ['page', 'title', 'year', 'source', 'sha256', 'period', 'version', 'budget_code'];

return [
    'pages' => $pages,
    'documents' => array_map(fn (array $entry) => array_combine($keys, array_pad($entry, count($keys), null)), $entries),
    'survey' => [
        'file' => 'survei-badilag-2025-triwulan-2.pdf',
        'period' => 'Triwulan II 2025',
        'values' => [
            'Survei Kepuasan Masyarakat' => 3.71,
            'Survei Persepsi Kualitas Layanan' => 3.74,
            'Survei Persepsi Anti Korupsi' => 3.82,
        ],
    ],
];
