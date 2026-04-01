@if($integrityZones->count() > 0)
    <section class="section section-white">
        <div class="container">
            <div class="section-header fade-up">
                <h2 class="section-title">Eviden Zona Integritas</h2>
            </div>
            <div class="integrity-zone-grid">
                @foreach($integrityZones as $integrityZone)
                    <a
                        href="{{ $integrityZone->url }}"
                        class="integrity-zone-card fade-up"
                        target="_blank"
                        rel="noopener"
                    >
                        <div class="integrity-zone-poster">
                            <img src="{{ asset('storage/' . $integrityZone->image) }}" alt="{{ $integrityZone->title }}">
                        </div>
                        <div class="integrity-zone-title">{{ $integrityZone->title }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
