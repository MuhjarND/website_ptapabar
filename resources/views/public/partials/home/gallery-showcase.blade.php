@if($galleries->count() > 0)
    <section class="section section-white">
        <div class="container">
            <div class="section-header fade-up">
                <h2 class="section-title">Galeri PTA Papua Barat</h2>
                <a href="{{ route('gallery.index') }}" class="section-link">Selengkapnya <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="section-filter-bar gallery-filter-bar fade-up" data-gallery-filter>
                @foreach($galleryCategories as $galleryCategory)
                    <button type="button" class="section-filter-button gallery-filter-button" data-filter="{{ $galleryCategory->id }}">{{ $galleryCategory->name }}</button>
                @endforeach
            </div>
            <div class="fv-tile-grid fv-tile-grid-home">
                @foreach($galleries as $item)
                    <div class="fv-card fade-up" data-gallery-category="{{ $item->gallery_category_id ?: 'uncategorized' }}">
                        @if($item->type === 'video' && $item->youtube_id)
                            <div class="fv-media fv-video" data-preview-kind="video" onclick="openVideoPopup('{{ $item->youtube_id }}')">
                                <img src="https://img.youtube.com/vi/{{ $item->youtube_id }}/hqdefault.jpg" alt="{{ $item->title }}">
                                <div class="fv-play"><i class="fas fa-play"></i></div>
                            </div>
                        @elseif($item->file)
                            <div class="fv-media fv-poster" data-preview-kind="image" onclick="openPosterPopup(this)">
                                <img src="{{ asset('storage/' . $item->file) }}" alt="{{ $item->title }}">
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="section-empty section-empty-compact gallery-empty-state" data-gallery-empty-message hidden>
                Belum ada foto atau video untuk kategori ini.
            </div>
        </div>
    </section>

    <div id="videoPopup" class="media-popup" onclick="closeVideoPopup()">
        <button type="button" class="media-popup-close" onclick="closeVideoPopup()">
            <i class="fas fa-times"></i>
        </button>
        <div class="media-popup-frame" onclick="event.stopPropagation()">
            <iframe id="videoFrame" src="" allowfullscreen></iframe>
        </div>
    </div>

    <div id="posterPopup" class="media-popup" onclick="closePosterPopup()">
        <button type="button" class="media-popup-close" onclick="closePosterPopup()">
            <i class="fas fa-times"></i>
        </button>
        <img id="posterImg" src="" class="poster-popup-image" alt="Poster galeri" onclick="event.stopPropagation()">
    </div>
@endif
