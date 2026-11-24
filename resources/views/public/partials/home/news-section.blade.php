@php($satkerNews = $newsFilterKey === 'peradilan-agama')
<section class="section home-news-section home-publication-panel home-news-panel"
    data-mobile-publication="{{ $satkerNews ? 'Berita Satker' : $sectionTitle }}">
    <div class="container">
        <div class="section-header fade-up">
            <h2 class="section-title">{{ $sectionTitle }}</h2>
            <a href="{{ $newsArchiveUrl ?? route('berita.index', ['news_scope' => $satkerNews ? \App\Post::NEWS_SCOPE_PERADILAN_AGAMA_PAPUA_BARAT : \App\Post::NEWS_SCOPE_TERKINI]) }}" class="section-link">Selengkapnya <i class="fas fa-arrow-right"></i></a>
        </div>
        <div
            class="posts-grid"
            data-news-pagination="{{ $newsFilterKey }}"
            data-news-per-page="{{ $newsPerPage }}"
            data-news-max-pages="{{ $newsMaxPages }}"
        >
            @forelse($newsItems as $post)
                <a
                    href="{{ $post->public_url ?? route('post.detail', $post->slug) }}"
                    class="post-card fade-up"
                    data-news-page-item="{{ $newsFilterKey }}"
                    @if($loop->index >= $newsPerPage) hidden @endif
                >
                    <div class="card-img">
                        @if($post->image_url)
                            <img src="{{ \App\Support\HomeImage::url($post->image, 640, $post->image_url) }}" alt="{{ $post->title }}" loading="lazy" decoding="async">
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
                        @unless($satkerNews)
                            <p class="card-excerpt">{{ $post->excerpt_plain }}</p>
                        @endunless
                        <span class="home-news-more" aria-hidden="true">Selengkapnya <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
            @empty
                <div class="section-empty">
                    <i class="fas fa-newspaper"></i>
                    {{ $emptyMessage }}
                </div>
            @endforelse
        </div>
        @php($newsPageCount = min($newsMaxPages, (int) ceil($newsItems->count() / $newsPerPage)))
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
