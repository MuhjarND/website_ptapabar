<div class="home-publications home-showcase">
    <div class="container home-publications-board">
        <section class="section home-publication-panel home-news-section home-news-panel home-media-panel">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Video dan Campaign</h2>
                    <a href="{{ route('gallery.index') }}" class="section-link">Arsip Galeri <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                </div>
                <div class="posts-grid">
                    @forelse($homeMedia as $item)
                        @php($videoId = $item->youtube_id)
                        <a class="post-card" href="{{ $videoId ? 'https://www.youtube.com/watch?v=' . $videoId : $item->thumbnail }}"
                            @if($videoId) data-showcase-video="{{ $videoId }}" @else data-showcase-poster @endif>
                            <div class="card-img">
                                <img src="{{ $item->thumbnail }}" alt="" loading="lazy" @class(['home-showcase-poster' => !$videoId])>
                                <span class="category-badge">{{ $videoId ? 'Video' : 'Banner' }}</span>
                                @if($videoId)
                                    <span class="fv-play" aria-hidden="true"><i class="fas fa-play"></i></span>
                                @endif
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">{{ $item->title }}</h3>
                                @if($item->description)
                                    <p class="card-excerpt">{{ $item->description }}</p>
                                @endif
                                <span class="home-news-more">{{ $videoId ? 'Putar Video' : 'Lihat Banner' }} <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
                            </div>
                        </a>
                    @empty
                        <p class="section-empty section-empty-compact">Belum ada video atau banner.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="section home-publication-panel home-greetings-panel">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Ucapan Selamat &amp; Duka</h2>
                </div>
                <div class="home-greeting-list">
                    @forelse($homeGreetings as $item)
                        <a href="{{ $item->thumbnail }}" class="post-card home-greeting-card" data-showcase-poster>
                            <img src="{{ $item->thumbnail }}" alt="" loading="lazy">
                            <div class="home-greeting-copy">
                                <span @class(['section-category-pill', 'home-condolence-pill' => $item->type === \App\Gallery::TYPE_CONDOLENCE])>
                                    {{ \App\Gallery::typeOptions()[$item->type] }}
                                </span>
                                <h3 class="card-title">{{ $item->title }}</h3>
                                @if($item->description)
                                    <p class="card-excerpt">{{ $item->description }}</p>
                                @endif
                            </div>
                        </a>
                    @empty
                        <p class="section-empty section-empty-compact">Belum ada ucapan.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</div>

<div id="videoPopup" class="media-popup" onclick="closeVideoPopup()">
    <button type="button" class="media-popup-close" aria-label="Tutup video" onclick="closeVideoPopup()">
        <i class="fas fa-times" aria-hidden="true"></i>
    </button>
    <div class="media-popup-frame" onclick="event.stopPropagation()">
        <iframe id="videoFrame" title="Pemutar video YouTube" allow="autoplay" allowfullscreen></iframe>
    </div>
</div>

<div id="posterPopup" class="media-popup" onclick="closePosterPopup()">
    <button type="button" class="media-popup-close" aria-label="Tutup gambar" onclick="closePosterPopup()">
        <i class="fas fa-times" aria-hidden="true"></i>
    </button>
    <img id="posterImg" class="poster-popup-image" alt="Poster beranda" onclick="event.stopPropagation()">
</div>
