@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
    $publication = [
        ['Berita', 'newspaper', 'admin.berita'],
        ...($user->canManageAnnouncements() ? [['Pengumuman', 'bullhorn', 'admin.pengumuman']] : []),
        ...($isAdmin ? [
            ['Tinta Peradilan', 'book-open', 'admin.articles'],
            ['Slider Beranda', 'images', 'admin.sliders'],
            ['Popup Beranda', 'image', 'admin.home-popup'],
            ['Pintasan Layanan', 'th-large', 'admin.quicklinks'],
            ['Foto, Video & Banner', 'photo-video', 'admin.galleries'],
        ] : []),
    ];
    $groups = [];
    if ($isAdmin) {
        $groups['Konten'] = [
            ['Halaman', 'file-alt', 'admin.pages'],
            ['Menu Navigasi', 'bars', 'admin.navigation'],
        ];
    }
    $groups['Berita & Publikasi'] = $publication;
    if ($isAdmin) {
        $groups += [
            'Dokumen' => [
                ['Arsip Dokumen', 'file-pdf', 'admin.transparency-documents'],
                ['Jenis Dokumen', 'tags', 'admin.document-types'],
            ],
            'Kepaniteraan' => [
                ['Jadwal Sidang Banding', 'calendar-check', 'admin.court-information', ['type' => 'hearings']],
                ['Panggilan Ghaib', 'bullhorn', 'admin.pages.managed', ['slug' => 'panggilan-ghaib']],
                ['Statistik Perkara', 'chart-bar', 'admin.court-information', ['type' => 'statistics']],
            ],
            'Kesekretariatan' => [
                ['Pejabat & Pegawai', 'id-badge', 'admin.pages.managed', ['slug' => 'daftar-nama-pejabat-dan-hakim']],
                ['LHKPN / LHKASN', 'folder', 'admin.pages.managed', ['slug' => 'laporan-lhkpn-dan-lhkasn']],
                ['Wilayah Yurisdiksi', 'map-marker-alt', 'admin.pages.managed', ['slug' => 'wilayah-yurisdiksi']],
                ['Agenda Pimpinan', 'calendar-alt', 'admin.agendas'],
            ],
            'Layanan Publik' => [
                ['Inbox Layanan Publik', 'inbox', 'admin.service-requests'],
                ['Rekap Pengaduan', 'clipboard-list', 'admin.pages.managed', ['slug' => 'laporan-rekapitulasi-pengaduan']],
                ['Hukuman Disiplin', 'shield-alt', 'admin.pages.managed', ['slug' => 'hukuman-disiplin']],
                ['Laporan Akses Informasi', 'info-circle', 'admin.pages.managed', ['slug' => 'laporan-akses-informasi']],
                ['Survei & Indeks', 'chart-bar', 'admin.surveys'],
                ['Zona Integritas', 'shield-alt', 'admin.integrity-zones'],
            ],
            'Master Data' => [
                ['Kategori Post', 'tags', 'admin.post-categories'],
                ['Kategori Galeri', 'photo-film', 'admin.gallery-categories'],
                ['Pengguna', 'users', 'admin.users'],
            ],
            'Pengaturan' => [
                ['Aplikasi', 'th-large', 'admin.site-applications'],
                ['Ikon Mengambang', 'location-arrow', 'admin.floating-icons'],
                ['Tautan Footer', 'link', 'admin.footer-links'],
                ['Pengaturan Situs', 'cog', 'admin.settings'],
            ],
        ];
    }
    $dashboardActive = request()->routeIs('admin.dashboard');
@endphp
<nav class="sidebar-nav" aria-label="Navigasi admin">
    <a href="{{ route('admin.dashboard') }}" @if($dashboardActive) class="active" aria-current="page" @endif>
        <i class="fas fa-tachometer-alt" aria-hidden="true"></i> Dashboard Utama
    </a>
    @if($isAdmin)
        <a href="{{ route('admin.sinova.index') }}" @if(request()->routeIs('admin.sinova.index')) class="active" aria-current="page" @endif>
            <i class="fas fa-clipboard-check" aria-hidden="true"></i> Dashboard SINOVA
        </a>
    @endif
    @foreach($groups as $heading => $items)
        <div class="nav-label">{{ $heading }}</div>
        @foreach($items as $item)
            @php
                [$label, $icon, $prefix] = $item;
                $params = $item[3] ?? [];
                $name = $prefix === 'admin.pages.managed' ? $prefix : $prefix . '.index';
                $active = $prefix === 'admin.pages'
                    ? request()->routeIs('admin.pages.*') && !request()->routeIs('admin.pages.managed')
                    : request()->routeIs($name, $prefix . '.*') && collect($params)->every(fn ($value, $key) => request()->route($key) === $value);
            @endphp
            <a href="{{ route($name, $params) }}" @if($active) class="active" aria-current="page" @endif>
                <i class="fas fa-{{ $icon }}" aria-hidden="true"></i> {{ $label }}
            </a>
        @endforeach
    @endforeach
</nav>
