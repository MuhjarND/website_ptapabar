<div class="home-publications home-showcase">
    <div class="container home-publications-board">
        <section class="section home-publication-panel home-media-panel" aria-labelledby="home-media-heading">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title" id="home-media-heading">Video dan Campaign</h2>
                    <a href="{{ route('gallery.index') }}" class="section-link">Arsip Galeri <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                </div>
                @include('public.partials.home.showcase-carousel', [
                    'items' => $homeMedia,
                    'carousel' => 'media',
                    'headingId' => 'home-media-heading',
                    'delay' => 7000,
                    'emptyText' => 'Belum ada video atau banner.',
                ])
            </div>
        </section>

        <section class="section home-publication-panel home-greetings-panel" aria-labelledby="home-greetings-heading">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title" id="home-greetings-heading">Ucapan Selamat &amp; Duka</h2>
                </div>
                @include('public.partials.home.showcase-carousel', [
                    'items' => $homeGreetings,
                    'carousel' => 'greetings',
                    'headingId' => 'home-greetings-heading',
                    'delay' => 9000,
                    'emptyText' => 'Belum ada ucapan.',
                ])
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
