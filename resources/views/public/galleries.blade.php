@extends('layouts.public')
@section('title', 'Galeri PTA Papua Barat')

@push('styles')
<link href="{{ asset('assets/css/public-content.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/public-home.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container">
    <div class="content-shell-wide">
        <div class="section-header">
            <h2 class="section-title">Galeri PTA Papua Barat</h2>
        </div>

        <div class="archive-filter-bar section-filter-bar">
            <a href="{{ route('gallery.index') }}" class="archive-filter-button section-filter-button {{ !$activeGalleryCategory ? 'is-active' : '' }}">
                Semua
            </a>
            @foreach($galleryCategories as $galleryCategory)
                <a
                    href="{{ route('gallery.index', ['gallery_category_id' => $galleryCategory->id]) }}"
                    class="archive-filter-button section-filter-button {{ $activeGalleryCategory === $galleryCategory->id ? 'is-active' : '' }}"
                >
                    {{ $galleryCategory->name }}
                </a>
            @endforeach
        </div>

        <div class="fv-tile-grid gallery-archive-grid">
            @forelse($galleries as $item)
                @php
                    $tileClasses = 'fv-card gallery-archive-card';
                    if ($loop->iteration % 6 === 1) {
                        $tileClasses .= ' fv-card--wide';
                    } elseif ($loop->iteration % 5 === 0) {
                        $tileClasses .= ' fv-card--tall';
                    }
                @endphp
                <div class="{{ $tileClasses }}">
                    @if($item->type === 'video' && $item->youtube_id)
                        <div class="fv-media fv-video" onclick="openVideoPopup('{{ $item->youtube_id }}')">
                            <img src="https://img.youtube.com/vi/{{ $item->youtube_id }}/hqdefault.jpg" alt="{{ $item->title }}">
                            <div class="fv-play"><i class="fas fa-play"></i></div>
                        </div>
                    @elseif($item->file)
                        <div class="fv-media fv-poster" onclick="openPosterPopup(this)">
                            <img src="{{ asset('storage/' . $item->file) }}" alt="{{ $item->title }}">
                        </div>
                    @endif
                    @if($item->galleryCategory)
                        <div class="section-category-pill fv-category-pill">{{ $item->galleryCategory->name }}</div>
                    @endif
                    <div class="fv-caption">{{ $item->title }}</div>
                </div>
            @empty
                <div class="archive-empty">
                    <i class="fas fa-photo-video"></i>
                    Belum ada foto atau video untuk kategori ini.
                </div>
            @endforelse
        </div>

        <div class="pub-pagination archive-pagination">{{ $galleries->appends(request()->query())->links() }}</div>
    </div>
</div>

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
@endsection

@push('scripts')
<script src="{{ asset('assets/js/public-home.js') }}"></script>
@endpush
