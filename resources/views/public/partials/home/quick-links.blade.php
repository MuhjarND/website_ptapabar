@if($quickLinks->count() > 0)
    <section class="quick-links">
        <div class="container">
            <div class="quick-links-grid">
                @foreach($quickLinks as $quickLink)
                    <a href="{{ $quickLink->url ?: '#' }}" class="quick-link-card fade-up" @if($quickLink->url) target="_blank" rel="noopener noreferrer" @endif>
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
@endif
