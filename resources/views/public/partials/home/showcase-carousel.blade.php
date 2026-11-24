<div class="home-showcase-carousel" data-showcase-carousel="{{ $carousel }}" data-showcase-delay="{{ $delay }}" aria-labelledby="{{ $headingId }}">
    @forelse($items as $item)
        @php($videoId = $item->youtube_id)
        <article class="home-showcase-slide" data-showcase-slide id="home-showcase-{{ $carousel }}-{{ $loop->index }}"
            @unless($loop->first) hidden @endunless>
            <a class="home-showcase-frame" href="{{ $videoId ? 'https://www.youtube.com/watch?v=' . $videoId : $item->thumbnail }}"
                @if($videoId) data-showcase-video="{{ $videoId }}" @else data-showcase-poster @endif
                aria-label="{{ $videoId ? 'Putar video: ' : 'Lihat gambar: ' }}{{ $item->title }}">
                <img src="{{ $item->thumbnail }}" alt="{{ $item->title }}" loading="lazy">
                @if($videoId)
                    <span class="home-showcase-play" aria-hidden="true"><i class="fas fa-play"></i></span>
                @endif
            </a>
            <div class="home-showcase-caption">
                <span class="section-category-pill">{{ $videoId ? 'Video' : \App\Gallery::typeOptions()[$item->type] }}</span>
                <h3>{{ $item->title }}</h3>
                @if($item->description)
                    <p>{{ $item->description }}</p>
                @endif
            </div>
        </article>
    @empty
        <p class="section-empty section-empty-compact">{{ $emptyText }}</p>
    @endforelse

    @if($items->count() > 1)
        <div data-showcase-navigation hidden>
            <div class="home-showcase-controls">
                <span class="home-showcase-position" data-showcase-position>1 / {{ $items->count() }}</span>
                <button type="button" data-showcase-step="-1" aria-label="Slide sebelumnya">
                    <i class="fas fa-chevron-left" aria-hidden="true"></i>
                </button>
                <button type="button" data-showcase-toggle>Jeda otomatis</button>
                <button type="button" data-showcase-step="1" aria-label="Slide berikutnya">
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </button>
            </div>
            <div class="home-showcase-titles" role="group" aria-label="Pilihan judul">
                @foreach($items as $item)
                    <button type="button" data-showcase-select="{{ $loop->index }}" aria-controls="home-showcase-{{ $carousel }}-{{ $loop->index }}"
                        aria-pressed="{{ $loop->first ? 'true' : 'false' }}" title="{{ $item->title }}">
                        <span>{{ $item->title }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    @endif
</div>
