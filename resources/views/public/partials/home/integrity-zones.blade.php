@if($integrityZones->count() > 0)
    <section class="survey-section" id="zona-integritas">
        <div class="container">
            <div class="section-header fade-up">
                <h2 class="section-title">Eviden Zona Integritas</h2>
            </div>
            <div class="integrity-zone-grid">
                @foreach($integrityZones as $integrityZone)
                    <a
                        href="{{ $integrityZone->url }}"
                        class="integrity-zone-link fade-up"
                        target="_blank"
                        rel="noopener"
                    >
                        <span class="integrity-zone-poster">
                            <img src="{{ \App\Support\HomeImage::url($integrityZone->image, 640) }}" alt="" loading="lazy" decoding="async">
                        </span>
                        <span class="integrity-zone-title">{{ $integrityZone->title }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
