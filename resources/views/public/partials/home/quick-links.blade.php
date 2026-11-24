@php
    $additionalLinks = $quickLinks->filter(fn ($link) => $link->url && !in_array(
        strtolower(trim($link->title)),
        ['sipp', 'sipp banding', 'penelusuran perkara', 'jadwal sidang', 'e-court', 'ecourt', 'direktori putusan', 'e-ac', 'eac', 'siwas'],
        true
    ));
@endphp
<section class="quick-links">
    <div class="container">
        <div class="quick-links-heading">
            <span>Layanan Pengadilan</span>
            <h2>Akses Cepat Layanan</h2>
        </div>
        <div class="quick-links-grid" data-quick-links-primary>
            @foreach(\App\Support\PublicNavigation::quickLinks() as [$title, $description, $url, $icon])
                <a href="{{ $url }}" class="quick-link-card">
                    <div class="quick-link-icon"><i class="{{ $icon }}" aria-hidden="true"></i></div>
                    <div class="quick-link-info"><h3>{{ $title }}</h3><p>{{ $description }}</p></div>
                </a>
            @endforeach
        </div>
        @if($additionalLinks->isNotEmpty())
            <details class="quick-links-extra">
                <summary>Layanan lainnya</summary>
                <div class="quick-links-grid">
                    @foreach($additionalLinks as $quickLink)
                        <a href="{{ $quickLink->url }}" class="quick-link-card" target="_blank" rel="noopener noreferrer">
                            <div class="quick-link-icon">
                                @if($quickLink->icon)
                                    <img src="{{ asset('storage/' . $quickLink->icon) }}" alt="" loading="lazy">
                                @else
                                    <i class="fas fa-link" aria-hidden="true"></i>
                                @endif
                            </div>
                            <div class="quick-link-info">
                                <h3>{{ $quickLink->title }}</h3>
                                @if($quickLink->description)
                                    <p>{{ $quickLink->description }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </details>
        @endif
    </div>
</section>
