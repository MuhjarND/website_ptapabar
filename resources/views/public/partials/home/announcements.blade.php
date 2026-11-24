@php($perPage = 4)
<section class="section section-white home-list-section home-publication-panel home-list-panel"
    data-mobile-publication="{{ $publicationPart === 'announcement' ? 'Pengumuman' : 'Tinta Peradilan' }}">
    <div class="container">
        <div class="announcement-section-layout">
            @if($publicationPart === 'announcement')
            <div class="announcement-column">
                <div class="section-header fade-up announcement-column-header">
                    <h2 class="section-title">Pengumuman</h2>
                    <a href="{{ route('pengumuman.index') }}" class="section-link">Selengkapnya <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="section-filter-bar announcement-filter-bar fade-up" data-announcement-filter>
                    <button type="button" class="section-filter-button announcement-filter-button is-active" data-filter="all">Semua</button>
                    @foreach(\App\Post::announcementCategoryOptions() as $value => $label)
                        <button type="button" class="section-filter-button announcement-filter-button" data-filter="{{ $value }}">{{ $label }}</button>
                    @endforeach
                </div>
                <div class="announcements-wrap" data-announcement-pagination data-announcement-per-page="{{ $perPage }}">
                <ul class="pengumuman-list">
                    @forelse($pengumuman as $item)
                        <li
                            class="fade-up announcement-entry"
                            data-announcement-category="{{ $item->announcement_category ?: \App\Post::ANNOUNCEMENT_CATEGORY_LAIN_LAIN }}"
                            @if($loop->index >= $perPage) hidden @endif
                        >
                            <a href="{{ route('pengumuman.detail', $item->slug) }}" class="pengumuman-item">
                                <div class="item-content">
                                    <h4>{{ $item->title }}</h4>
                                    <time datetime="{{ $item->created_at->toDateString() }}">{{ $item->created_at->locale('id')->translatedFormat('d F Y') }}</time>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="section-empty section-empty-compact">Belum ada pengumuman.</li>
                    @endforelse
                    @if($pengumuman->count() > 0)
                        <li class="section-empty section-empty-compact announcements-empty-state" data-empty-message hidden>
                            Belum ada pengumuman untuk kategori ini.
                        </li>
                    @endif
                </ul>
                @php($announcementPageCount = (int) ceil($pengumuman->count() / $perPage))
                @if($announcementPageCount > 1)
                    <div class="news-home-pagination fade-up" data-announcement-pagination-nav>
                        @for($page = 1; $page <= $announcementPageCount; $page++)
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
            </div>
            @else
                <aside class="announcement-column announcement-article-sidebar">
                    <div class="section-header fade-up announcement-column-header announcement-article-section-header">
                        <h2 class="section-title">Tinta Peradilan</h2>
                        <a href="{{ route('article.index') }}" class="section-link">Selengkapnya <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="section-filter-bar announcement-filter-bar fade-up" data-article-filter>
                        <button type="button" class="section-filter-button article-filter-button is-active" data-filter="all">Semua</button>
                        @foreach(\App\Article::categoryOptions() as $value => $label)
                            <button type="button" class="section-filter-button article-filter-button" data-filter="{{ $value }}">{{ $label }}</button>
                        @endforeach
                    </div>
                    <ul class="pengumuman-list announcement-article-list" data-article-pagination data-article-per-page="{{ $perPage }}">
                        @forelse($articles as $article)
                            <li class="announcement-article-entry fade-up"
                                data-article-category="{{ $article->article_category ?: \App\Article::CATEGORY_LAIN_LAIN }}"
                                @if($loop->index >= $perPage) hidden @endif
                            >
                                <a
                                    href="{{ $article->target_url }}"
                                    class="announcement-article-item pengumuman-item"
                                    {{ $article->target_is_external ? 'target="_blank" rel="noopener"' : '' }}
                                >
                                    <div class="item-content announcement-article-content">
                                        <h4>{{ $article->title }}</h4>
                                        <time datetime="{{ $article->created_at->toDateString() }}">{{ $article->created_at->locale('id')->translatedFormat('d F Y') }}</time>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="section-empty section-empty-compact">Belum ada artikel.</li>
                        @endforelse
                        @if($articles->count())
                            <li class="section-empty section-empty-compact article-empty-state" data-article-empty-message hidden>
                                Belum ada artikel untuk kategori ini.
                            </li>
                        @endif
                    </ul>
                    @php($articlePageCount = (int) ceil($articles->count() / $perPage))
                    @if($articlePageCount > 1)
                        <div class="news-home-pagination fade-up" data-article-pagination-nav>
                            @for($page = 1; $page <= $articlePageCount; $page++)
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
                </aside>
            @endif
        </div>
    </div>
</section>
