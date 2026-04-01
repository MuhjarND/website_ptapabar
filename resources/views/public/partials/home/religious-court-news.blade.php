@include('public.partials.home.news-section', [
    'sectionTitle' => 'Berita Peradilan Agama Papua Barat',
    'newsItems' => $beritaPeradilanAgama,
    'newsFilterKey' => 'peradilan-agama',
    'showSourceAgency' => true,
    'emptyMessage' => 'Belum ada berita Peradilan Agama Papua Barat.',
    'emptyFilterMessage' => 'Belum ada berita Peradilan Agama Papua Barat untuk kategori ini.',
])
