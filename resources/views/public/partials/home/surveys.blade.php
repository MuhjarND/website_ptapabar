    <section class="survey-section" id="survei-pelayanan">
        <div class="container">
            <div class="survey-header fade-up">
                <h2>Survei & Indeks Pelayanan Publik</h2>
                <p>Lihat ringkasan hasil survei pelayanan pengadilan.</p>
            </div>
            <div class="survey-grid">
                @forelse($surveys as $survey)
                    <div class="survey-card fade-up">
                        <div class="survey-icon"><i class="{{ $survey->icon }}"></i></div>
                        <div class="survey-title">{{ $survey->title }}</div>
                        <div class="survey-value" data-target="{{ $survey->value }}">0</div>
                        <span class="survey-label">{{ $survey->label }}</span>
                        @if($survey->period)<p class="survey-period">{{ $survey->period }}</p>@endif
                        @if($survey->report_url)
                            <a class="survey-report-link" href="{{ $survey->report_url }}" aria-label="Lihat laporan {{ $survey->title }}">Lihat Laporan</a>
                        @endif
                    </div>
                @empty
                    <p class="survey-empty">Belum ada hasil survei yang dipublikasikan.</p>
                @endforelse
            </div>
        </div>
    </section>
