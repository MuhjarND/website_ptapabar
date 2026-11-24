@if($applications->count() > 0)
    <section class="survey-section applications-section">
        <div class="container">
            <div class="section-header fade-up">
                <h2 class="section-title">Daftar Aplikasi</h2>
            </div>
            <div class="applications-layout">
                <div class="app-logo-list fade-up">
                    @foreach($applications as $application)
                        <a href="{{ \App\Support\PublicNavigation::serviceUrl($application->url) ?: '#' }}" class="app-logo-item" @if($application->url) target="_blank" rel="noopener noreferrer" @endif>
                            <div class="app-logo-badge">
                                @if($application->icon)
                                    <img src="{{ asset('storage/' . $application->icon) }}" alt="{{ $application->title }}" loading="lazy" decoding="async">
                                @else
                                    <i class="fas fa-link"></i>
                                @endif
                            </div>
                            <span class="app-logo-title">{{ $application->title }}</span>
                            <span class="app-logo-source">{{ $application->source_label }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
