@if($surveys->count() > 0)
    <section class="survey-section">
        <div class="container">
            <div class="survey-header fade-up">
                <h2>Survei & Indeks Pelayanan Publik</h2>
                <p>Lihat hasil survei terkini dan berikan suara Anda untuk membantu kami terus meningkatkan kualitas layanan.</p>
            </div>
            <div class="survey-grid">
                @foreach($surveys as $survey)
                    <div class="survey-card fade-up">
                        <div class="survey-icon"><i class="{{ $survey->icon }}"></i></div>
                        <div class="survey-title">{{ $survey->title }}</div>
                        <div class="survey-value" data-target="{{ $survey->value }}">0</div>
                        <span class="survey-label">{{ $survey->label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
