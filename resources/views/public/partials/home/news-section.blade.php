<section class="section">
    <div class="container">
        <div class="section-header fade-up">
            <h2 class="section-title">{{ $sectionTitle }}</h2>
            <a href="{{ route('berita.index') }}" class="section-link">Selengkapnya <i class="fas fa-arrow-right"></i></a>
        </div>
        <div
            class="posts-grid posts-grid-home-news"
            data-news-pagination="{{ $newsFilterKey }}"
            data-news-per-page="3"
            data-news-max-pages="3"
        >
            @forelse($newsItems as $post)
                <a
                    href="{{ route('post.detail', $post->slug) }}"
                    class="post-card fade-up"
                    data-news-page-item="{{ $newsFilterKey }}"
                >
                    <div class="card-img">
                        @if($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
                        @else
                            <div class="post-card-placeholder">
                                <i class="fas fa-newspaper"></i>
                            </div>
                        @endif
                        <span class="category-badge">Berita</span>
                    </div>
                    <div class="card-body">
                        <div class="card-date"><i class="far fa-calendar"></i> {{ $post->created_at->format('d M Y') }}</div>
                        @if(!empty($showSourceAgency) && $post->source_agency_label)
                            <div class="section-category-pill news-source-pill">{{ $post->source_agency_label }}</div>
                        @endif
                        <h3 class="card-title">{{ $post->title }}</h3>
                        @if($post->postCategory)
                            <div class="section-category-pill news-category-pill">{{ $post->postCategory->name }}</div>
                        @endif
                        <p class="card-excerpt">{{ $post->excerpt_plain }}</p>
                    </div>
                </a>
            @empty
                <div class="section-empty">
                    <i class="fas fa-newspaper"></i>
                    {{ $emptyMessage }}
                </div>
            @endforelse
        </div>
        @php($newsPageCount = min(3, (int) ceil($newsItems->count() / 3)))
        @if($newsPageCount > 1)
            <div class="news-home-pagination fade-up" data-news-pagination-nav="{{ $newsFilterKey }}">
                @for($page = 1; $page <= $newsPageCount; $page++)
                    <button
                        type="button"
                        class="news-home-pagination-button {{ $page === 1 ? 'is-active' : '' }}"
                        data-page="{{ $page }}"
                    >
                        {{ $page }}
                    </button>
                @endfor
            </div>
        @endif
    </div>
</section>
