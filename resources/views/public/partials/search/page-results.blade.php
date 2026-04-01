@if($pages->count() > 0)
    <h3 class="search-section-title">
        <i class="fas fa-file-alt"></i> Halaman ({{ $pages->count() }})
    </h3>
    <div class="search-page-list">
        @foreach($pages as $pageResult)
            <a href="{{ route('page.show', $pageResult->slug) }}" class="search-page-card">
                <strong class="search-page-title">{{ $pageResult->title }}</strong>
                <p class="search-page-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($pageResult->content), 120) }}</p>
            </a>
        @endforeach
    </div>
@endif
