@if($agendas->count() > 0)
<section class="section section-white">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">Agenda Pimpinan</h2>
                <div class="section-accent"></div>
            </div>
            <a href="{{ route('agenda.index') }}" class="section-more-link section-more-button">Selengkapnya <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="agenda-home-grid">
            @foreach($agendas as $agenda)
                <article class="agenda-home-card">
                    <div class="agenda-home-top">
                        <div class="section-category-pill announcement-category-pill">{{ $agenda->agenda_type_label }}</div>
                        <div class="agenda-home-time">{{ $agenda->start_at->format('d M Y') }}</div>
                    </div>
                    <h3>{{ $agenda->title }}</h3>
                    <p>{{ $agenda->summary ?: \Illuminate\Support\Str::limit(strip_tags($agenda->description), 120) }}</p>
                    <div class="agenda-home-meta">
                        <span><i class="fas fa-clock"></i> {{ $agenda->start_at->format('H:i') }}@if($agenda->end_at) - {{ $agenda->end_at->format('H:i') }}@endif</span>
                        @if($agenda->location)
                            <span><i class="fas fa-map-marker-alt"></i> {{ $agenda->location }}</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
