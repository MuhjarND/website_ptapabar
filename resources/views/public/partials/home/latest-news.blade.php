@include('public.partials.home.news-section', [
    'sectionTitle' => 'Berita Terkini',
    'newsItems' => $beritaTerkini,
    'newsFilterKey' => 'terkini',
    'showSourceAgency' => false,
    'emptyMessage' => 'Belum ada berita terkini.',
    'emptyFilterMessage' => 'Belum ada berita terkini untuk kategori ini.',
])
