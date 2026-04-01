<div class="hero-slider" id="heroSlider">
    @forelse($sliders as $index => $slider)
        <div class="slide {{ $index === 0 ? 'active' : '' }}">
            <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->title }}">
            @if($slider->title)
                <div class="slide-overlay pos-{{ $slider->text_position ?? 'bottom-left' }}">
                    <h3>{{ $slider->title }}</h3>
                    @if($slider->description)
                        <p>{{ $slider->description }}</p>
                    @endif
                </div>
            @endif
        </div>
    @empty
        <div class="slide slide-fallback active">
            <div class="slide-fallback-content">
                <div class="slide-fallback-icon"><i class="fas fa-landmark"></i></div>
                <h3>Pengadilan Tinggi Agama Papua Barat</h3>
                <p>Mahkamah Agung Republik Indonesia</p>
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
