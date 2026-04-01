<section class="section section-white">
    <div class="container">
        <div class="announcement-section-layout {{ $articles->count() ? 'has-article-sidebar' : '' }}">
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
                <div class="announcements-wrap" data-announcement-pagination data-announcement-per-page="5">
                <ul class="pengumuman-list">
                    @forelse($pengumuman as $item)
                        <li
                            class="fade-up announcement-entry"
                            data-announcement-category="{{ $item->announcement_category ?: \App\Post::ANNOUNCEMENT_CATEGORY_LAIN_LAIN }}"
                        >
                            <a href="{{ route('pengumuman.detail', $item->slug) }}" class="pengumuman-item">
                                <div class="date-box">
                                    <span class="day">{{ $item->created_at->format('d') }}</span>
                                    <span class="month">{{ $item->created_at->format('M') }}</span>
                                </div>
                                <div class="item-content">
                                    <h4>{{ $item->title }}</h4>
                                    @if($item->announcement_category_label)
                                        <div class="section-category-pill announcement-category-pill">{{ $item->announcement_category_label }}</div>
                                    @endif
                                    <p>{{ \Illuminate\Support\Str::limit($item->excerpt_plain, 100) }}</p>
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
                @php($announcementPageCount = (int) ceil($pengumuman->count() / 5))
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
            @if($articles->count())
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
                    <ul class="pengumuman-list announcement-article-list" data-article-pagination data-article-per-page="5">
                        @foreach($articles as $article)
                            <li class="announcement-article-entry fade-up">
                                <a
                                    href="{{ $article->target_url }}"
                                    class="announcement-article-item pengumuman-item"
                                    data-article-category="{{ $article->article_category ?: \App\Article::CATEGORY_LAIN_LAIN }}"
                                    {{ $article->target_is_external ? 'target="_blank" rel="noopener"' : '' }}
                                >
                                <div class="date-box">
                                    <span class="day">{{ $article->created_at->format('d') }}</span>
                                    <span class="month">{{ $article->created_at->format('M') }}</span>
                                </div>
                                <div class="item-content announcement-article-content">
                                    <h4>{{ $article->title }}</h4>
                                    @if($article->article_category_label)
                                        <div class="section-category-pill announcement-category-pill">{{ $article->article_category_label }}</div>
                                    @endif
                                    <p>{{ \Illuminate\Support\Str::limit($article->excerpt_plain, 95) }}</p>
                                </div>
                                </a>
                            </li>
                        @endforeach
                        <li class="section-empty section-empty-compact article-empty-state" data-article-empty-message hidden>
                            Belum ada artikel untuk kategori ini.
                        </li>
                    </ul>
                    @php($articlePageCount = (int) ceil($articles->count() / 5))
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
