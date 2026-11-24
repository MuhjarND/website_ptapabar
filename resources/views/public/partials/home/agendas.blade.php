<div class="container home-publications-board" data-home-publications>
    @include('public.partials.home.news-section', [
        'sectionTitle' => 'Agenda Pimpinan',
        'newsItems' => $leadershipNews,
        'newsFilterKey' => 'agenda-pimpinan',
        'newsArchiveUrl' => route('berita.index', ['kategori' => \App\PostCategory::LEADERSHIP_AGENDA]),
        'showSourceAgency' => false,
        'emptyMessage' => 'Belum ada berita kategori Agenda Pimpinan.',
    ])

    <section class="section home-publication-panel home-calendar-panel" data-mobile-publication="Kalender Kegiatan">
        <div class="container" id="kalender-kegiatan">
            <div class="section-header">
                <h2 class="section-title">Kalender Kegiatan PTA Papua Barat</h2>
            </div>
            @include('public.partials.home.papeda-calendar')
        </div>
    </section>
</div>
