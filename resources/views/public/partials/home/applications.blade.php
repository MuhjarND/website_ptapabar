@if($ptaInnovationApps->count() > 0 || $mahkamahAgungApps->count() > 0)
    <section class="section applications-section">
        <div class="container">
            <div class="section-header fade-up">
                <h2 class="section-title">Daftar Aplikasi</h2>
            </div>
            <div class="applications-layout">
                @if($ptaInnovationApps->count() > 0)
                    <div class="app-group-card fade-up">
                        <div class="app-group-header">
                            <span class="app-group-kicker">Inovasi Daerah</span>
                            <h3>Aplikasi Inovasi PTA Papua Barat</h3>
                        </div>
                        <div class="app-logo-list">
                            @foreach($ptaInnovationApps as $application)
                                <a href="{{ $application->url ?: '#' }}" class="app-logo-item" @if($application->url) target="_blank" rel="noopener noreferrer" @endif>
                                    <div class="app-logo-badge">
                                        @if($application->icon)
                                            <img src="{{ asset('storage/' . $application->icon) }}" alt="{{ $application->title }}">
                                        @else
                                            <i class="fas fa-link"></i>
                                        @endif
                                    </div>
                                    <span class="app-logo-title">{{ $application->title }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($mahkamahAgungApps->count() > 0)
                    <div class="app-group-card fade-up">
                        <div class="app-group-header">
                            <span class="app-group-kicker">Nasional</span>
                            <h3>Aplikasi yang Digunakan di Mahkamah Agung</h3>
                        </div>
                        <div class="app-logo-list">
                            @foreach($mahkamahAgungApps as $application)
                                <a href="{{ $application->url ?: '#' }}" class="app-logo-item" @if($application->url) target="_blank" rel="noopener noreferrer" @endif>
                                    <div class="app-logo-badge">
                                        @if($application->icon)
                                            <img src="{{ asset('storage/' . $application->icon) }}" alt="{{ $application->title }}">
                                        @else
                                            <i class="fas fa-link"></i>
                                        @endif
                                    </div>
                                    <span class="app-logo-title">{{ $application->title }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif
