<div class="hero-slider" id="heroSlider">
    @forelse($sliders as $index => $slider)
        @php
            $imageUrl = \App\Support\HomeImage::url($slider->image, 1600, $slider->image_url);
            $mobileImageUrl = $slider->image
                ? \App\Support\HomeImage::url($slider->image, 640)
                : \App\Support\HomeImage::wordpressPreview($imageUrl, 640);
        @endphp
        <div class="slide {{ $index === 0 ? 'active' : '' }}">
            <picture>
                @if($mobileImageUrl && $mobileImageUrl !== $imageUrl)
                    <source media="(max-width: 768px)" @if($index === 0) srcset="{{ $mobileImageUrl }}" @else data-srcset="{{ $mobileImageUrl }}" @endif>
                @endif
                <img
                    @if($index === 0)
                        src="{{ $imageUrl }}" fetchpriority="high"
                    @else
                        data-slide-src="{{ $imageUrl }}"
                    @endif
                    @if(!$slider->image && $mobileImageUrl) onerror="this.onerror = null; this.previousElementSibling.remove();" @endif
                    alt="{{ $slider->title }}">
            </picture>
            @if($slider->title && !$slider->wordpress_id)
                <div class="slide-overlay pos-{{ $slider->text_position ?? 'bottom-left' }}">
                    <span class="hero-kicker">Informasi Publik</span>
                    <h3>{{ $slider->title }}</h3>
                    @if($slider->description)
                        <p>{{ $slider->description }}</p>
                    @endif
                    <a href="{{ route('berita.index') }}" class="hero-more-link">Selengkapnya <i class="fas fa-arrow-right"></i></a>
                </div>
            @endif
        </div>
    @empty
        <div class="slide slide-fallback active">
            <div class="slide-fallback-content">
                <div class="slide-fallback-icon"><i class="fas fa-landmark"></i></div>
                <span class="hero-kicker">Website Resmi</span>
                <h3>Pengadilan Tinggi Agama Papua Barat</h3>
                <p>Mahkamah Agung Republik Indonesia</p>
                <a href="{{ route('berita.index') }}" class="hero-more-link">Lihat Informasi <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    @endforelse

    @if($sliders->count() > 1)
        <div class="slider-dots">
            @foreach($sliders as $index => $slider)
                <button
                    type="button"
                    class="dot {{ $index === 0 ? 'active' : '' }}"
                    onclick="goToSlide({{ $index }})"
                    aria-label="Pindah ke slide {{ $index + 1 }}"
                ></button>
            @endforeach
        </div>
        <div class="slider-nav">
            <button type="button" onclick="prevSlide()" aria-label="Slide sebelumnya"><i class="fas fa-chevron-left"></i></button>
            <button type="button" onclick="nextSlide()" aria-label="Slide berikutnya"><i class="fas fa-chevron-right"></i></button>
        </div>
    @endif
</div>
