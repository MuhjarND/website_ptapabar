@php($mainQuickLinks = \App\Support\PublicNavigation::quickLinks())
    <section class="quick-links">
        <div class="container">
            <div class="quick-links-heading">
                <span>Layanan Pengadilan</span>
                <h2>Akses Cepat Layanan</h2>
                <p>Temukan layanan peradilan dan informasi yang paling sering digunakan.</p>
            </div>
            <div class="quick-links-grid">
                @foreach($mainQuickLinks as [$title, $description, $url, $icon])
                    <a href="{{ $url }}" class="quick-link-card">
                        <div class="quick-link-icon"><i class="{{ $icon }}" aria-hidden="true"></i></div>
                        <div class="quick-link-info"><h4>{{ $title }}</h4><p>{{ $description }}</p></div>
                    </a>
                @endforeach
                @foreach($quickLinks->whereNotNull('url') as $quickLink)
                    @continue(!$quickLink->url || in_array(strtolower(trim($quickLink->title)), ['sipp', 'sipp banding', 'jadwal sidang', 'e-court', 'ecourt', 'direktori putusan', 'survei pelayanan', 'pengaduan', 'layanan publik'], true))
                    <a href="{{ $quickLink->url }}" class="quick-link-card fade-up" target="_blank" rel="noopener noreferrer">
                        <div class="quick-link-icon">
                            @if($quickLink->icon)
                                <img src="{{ asset('storage/' . $quickLink->icon) }}" alt="{{ $quickLink->title }}">
                            @else
                                <i class="fas fa-link"></i>
                            @endif
                        </div>
                        <div class="quick-link-info">
                            <h4>{{ $quickLink->title }}</h4>
                            @if($quickLink->description)
                                <p>{{ $quickLink->description }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
