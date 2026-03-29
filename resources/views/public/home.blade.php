@extends('layouts.public')
@section('title', 'Beranda')
@section('content')

<!-- Hero Slider -->
<div class="hero-slider" id="heroSlider">
    @forelse($sliders as $index => $slider)
    <div class="slide {{ $index === 0 ? 'active' : '' }}">
        <img src="{{ asset('storage/'.$slider->image) }}" alt="{{ $slider->title }}">
        @if($slider->title)
        <div class="slide-overlay pos-{{ $slider->text_position ?? 'bottom-left' }}">
            <h3>{{ $slider->title }}</h3>
            @if($slider->description) <p>{{ $slider->description }}</p> @endif
        </div>
        @endif
    </div>
    @empty
    <div class="slide active" style="background:linear-gradient(135deg, var(--green-dark), var(--green));display:flex;align-items:center;justify-content:center;">
        <div style="text-align:center;color:#fff;">
            <div style="font-size:48px;margin-bottom:16px;"><i class="fas fa-landmark"></i></div>
            <h3 style="font-size:28px;font-weight:700;">Pengadilan Tinggi Agama Papua Barat</h3>
            <p style="margin-top:8px;color:rgba(255,255,255,0.7);">Mahkamah Agung Republik Indonesia</p>
        </div>
    </div>
    @endforelse
    @if($sliders->count() > 1)
    <div class="slider-dots">
        @foreach($sliders as $i => $s)
        <div class="dot {{ $i === 0 ? 'active' : '' }}" onclick="goToSlide({{ $i }})"></div>
        @endforeach
    </div>
    <div class="slider-nav">
        <button onclick="prevSlide()"><i class="fas fa-chevron-left"></i></button>
        <button onclick="nextSlide()"><i class="fas fa-chevron-right"></i></button>
    </div>
    @endif
</div>

<!-- Welcome Strip -->
<div class="welcome-strip">
    <div class="container">
        <h3><i class="fas fa-landmark" style="margin-right:8px;"></i> Selamat Datang di Website Resmi PTA Papua Barat</h3>
        <p>Melayani Masyarakat dengan Integritas, Profesionalitas dan Akuntabilitas</p>
    </div>
</div>

<!-- Quick Links / Layanan -->
@if($quickLinks->count() > 0)
<section class="quick-links">
    <div class="container">
        <div class="quick-links-grid">
            @foreach($quickLinks as $ql)
            <a href="{{ $ql->url ?: '#' }}" class="quick-link-card fade-up" {{ $ql->url ? 'target=_blank' : '' }}>
                <div class="quick-link-icon">
                    @if($ql->icon)
                        <img src="{{ asset('storage/'.$ql->icon) }}" alt="{{ $ql->title }}">
                    @else
                        <i class="fas fa-link"></i>
                    @endif
                </div>
                <div class="quick-link-info">
                    <h4>{{ $ql->title }}</h4>
                    @if($ql->description)<p>{{ $ql->description }}</p>@endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Berita Section -->
<section class="section">
    <div class="container">
        <div class="section-header fade-up">
            <h2 class="section-title">Berita Terkini</h2>
            <a href="{{ route('berita.index') }}" class="section-link">Lihat Semua <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="posts-grid">
            @forelse($berita as $post)
            <a href="{{ route('post.detail', $post->slug) }}" class="post-card fade-up">
                <div class="card-img">
                    @if($post->image)
                        <img src="{{ asset('storage/'.$post->image) }}" alt="{{ $post->title }}">
                    @else
                        <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--green),var(--green-light));display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-newspaper" style="font-size:36px;color:rgba(255,255,255,0.3);"></i>
                        </div>
                    @endif
                    <span class="category-badge">Berita</span>
                </div>
                <div class="card-body">
                    <div class="card-date"><i class="far fa-calendar"></i> {{ $post->created_at->format('d M Y') }}</div>
                    <h3 class="card-title">{{ $post->title }}</h3>
                    <p class="card-excerpt">{{ $post->excerpt }}</p>
                </div>
            </a>
            @empty
            <div style="grid-column:1/-1;text-align:center;padding:48px;color:#999;">
                <i class="fas fa-newspaper" style="font-size:36px;margin-bottom:12px;display:block;"></i>
                Belum ada berita.
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Pengumuman Section -->
<section class="section" style="background: var(--white);">
    <div class="container">
        <div class="section-header fade-up">
            <h2 class="section-title">Pengumuman</h2>
            <a href="{{ route('pengumuman.index') }}" class="section-link">Lihat Semua <i class="fas fa-arrow-right"></i></a>
        </div>
        <div style="max-width:700px;">
            <ul class="pengumuman-list">
                @forelse($pengumuman as $item)
                <li class="fade-up">
                    <a href="{{ route('pengumuman.detail', $item->slug) }}" class="pengumuman-item">
                        <div class="date-box">
                            <span class="day">{{ $item->created_at->format('d') }}</span>
                            <span class="month">{{ $item->created_at->format('M') }}</span>
                        </div>
                        <div class="item-content">
                            <h4>{{ $item->title }}</h4>
                            <p>{{ Str::limit($item->excerpt, 100) }}</p>
                        </div>
                    </a>
                </li>
                @empty
                <li style="text-align:center;padding:32px;color:#999;">Belum ada pengumuman.</li>
                @endforelse
            </ul>
        </div>
    </div>
</section>

<!-- Foto & Video -->
@if($galleries->count() > 0)
<section class="section" style="background:var(--white);">
    <div class="container">
        <div class="section-header fade-up">
            <h2 class="section-title">Foto & Video</h2>
        </div>
        <div class="fv-scroll">
            @foreach($galleries as $item)
            <div class="fv-card fade-up">
                @if($item->type === 'video' && $item->youtube_id)
                <div class="fv-media fv-video" onclick="openVideoPopup('{{ $item->youtube_id }}')">
                    <img src="https://img.youtube.com/vi/{{ $item->youtube_id }}/hqdefault.jpg" alt="{{ $item->title }}">
                    <div class="fv-play"><i class="fas fa-play"></i></div>
                </div>
                @elseif($item->file)
                <div class="fv-media fv-poster" onclick="openPosterPopup(this)">
                    <img src="{{ asset('storage/'.$item->file) }}" alt="{{ $item->title }}">
                </div>
                @endif
                <div class="fv-caption">{{ $item->title }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Video Popup -->
<div id="videoPopup" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.92);" onclick="closeVideoPopup()">
    <button onclick="closeVideoPopup()" style="position:absolute;top:20px;right:20px;background:none;border:none;color:#fff;font-size:28px;cursor:pointer;z-index:10;"><i class="fas fa-times"></i></button>
    <div onclick="event.stopPropagation()" style="width:90%;max-width:800px;aspect-ratio:16/9;">
        <iframe id="videoFrame" src="" style="width:100%;height:100%;border:none;border-radius:10px;" allowfullscreen></iframe>
    </div>
</div>

<!-- Poster Popup -->
<div id="posterPopup" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.92);" onclick="closePosterPopup()">
    <button onclick="closePosterPopup()" style="position:absolute;top:20px;right:20px;background:none;border:none;color:#fff;font-size:28px;cursor:pointer;z-index:10;"><i class="fas fa-times"></i></button>
    <img id="posterImg" src="" style="max-width:90vw;max-height:88vh;border-radius:10px;box-shadow:0 20px 60px rgba(0,0,0,0.5);object-fit:contain;" onclick="event.stopPropagation()">
</div>
@endif

<!-- Survey & Indeks Pelayanan Publik -->
@if($surveys->count() > 0)
<section class="survey-section">
    <div class="container">
        <div class="survey-header fade-up">
            <h2>Survei & Indeks Pelayanan Publik</h2>
            <p>Lihat hasil survei terkini dan berikan suara Anda untuk membantu kami terus meningkatkan kualitas layanan.</p>
        </div>
        <div class="survey-grid">
            @foreach($surveys as $survey)
            <div class="survey-card fade-up">
                <div class="survey-icon"><i class="{{ $survey->icon }}"></i></div>
                <div class="survey-title">{{ $survey->title }}</div>
                <div class="survey-value" data-target="{{ $survey->value }}">0</div>
                <span class="survey-label">{{ $survey->label }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection

@section('scripts')
<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slider .slide');
const dots = document.querySelectorAll('.slider-dots .dot');

function goToSlide(index) {
    slides[currentSlide].classList.remove('active');
    if(dots[currentSlide]) dots[currentSlide].classList.remove('active');
    currentSlide = index;
    slides[currentSlide].classList.add('active');
    if(dots[currentSlide]) dots[currentSlide].classList.add('active');
}
function nextSlide() { goToSlide((currentSlide + 1) % slides.length); }
function prevSlide() { goToSlide((currentSlide - 1 + slides.length) % slides.length); }
if(slides.length > 1) setInterval(nextSlide, 5000);

// Count-up animation
function countUp(el) {
    var target = parseFloat(el.getAttribute('data-target'));
    var decimals = (target % 1 !== 0) ? (target.toString().split('.')[1] || '').length : 0;
    var duration = 1800;
    var startTime = null;
    function ease(t) { return t < 0.5 ? 4*t*t*t : 1 - Math.pow(-2*t+2, 3)/2; }
    function step(timestamp) {
        if (!startTime) startTime = timestamp;
        var progress = Math.min((timestamp - startTime) / duration, 1);
        var current = ease(progress) * target;
        el.textContent = current.toFixed(decimals);
        if (progress < 1) requestAnimationFrame(step);
        else el.textContent = target.toFixed(decimals).replace(/\.00$/, '');
    }
    requestAnimationFrame(step);
}
var countObserver = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
        if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
            entry.target.classList.add('counted');
            countUp(entry.target);
        }
    });
}, { threshold: 0.5 });
document.querySelectorAll('.survey-value[data-target]').forEach(function(el) { countObserver.observe(el); });

// Video popup
function openVideoPopup(youtubeId) {
    document.getElementById('videoFrame').src = 'https://www.youtube.com/embed/' + youtubeId + '?autoplay=1';
    document.getElementById('videoPopup').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeVideoPopup() {
    document.getElementById('videoFrame').src = '';
    document.getElementById('videoPopup').style.display = 'none';
    document.body.style.overflow = '';
}

// Poster popup
function openPosterPopup(el) {
    document.getElementById('posterImg').src = el.querySelector('img').src;
    document.getElementById('posterPopup').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closePosterPopup() {
    document.getElementById('posterPopup').style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var vp = document.getElementById('videoPopup');
        var pp = document.getElementById('posterPopup');
        if (vp && vp.style.display === 'flex') closeVideoPopup();
        if (pp && pp.style.display === 'flex') closePosterPopup();
    }
});
</script>

<style>
/* Foto & Video Showcase */
.fv-scroll {
    display: flex;
    gap: 18px;
    overflow-x: auto;
    padding-bottom: 12px;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
}
.fv-scroll::-webkit-scrollbar { height: 6px; }
.fv-scroll::-webkit-scrollbar-track { background: #f0f0f0; border-radius: 3px; }
.fv-scroll::-webkit-scrollbar-thumb { background: var(--green); border-radius: 3px; }
.fv-card {
    flex: 0 0 220px;
    scroll-snap-align: start;
    transition: 0.3s;
}
.fv-card:hover { transform: translateY(-4px); }
.fv-media {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    border: 1px solid var(--border);
    transition: 0.3s;
}
.fv-card:hover .fv-media {
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}
.fv-poster { aspect-ratio: 3/4; }
.fv-video { aspect-ratio: 16/9; }
.fv-media img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.fv-card:hover .fv-media img { transform: scale(1.05); }
.fv-play {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    background: rgba(0,0,0,0.3);
    transition: 0.3s;
}
.fv-play i {
    width: 48px; height: 48px;
    background: rgba(255,255,255,0.95);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; color: #dc3545;
    padding-left: 3px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    transition: 0.3s;
}
.fv-card:hover .fv-play i { transform: scale(1.15); }
.fv-caption {
    margin-top: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text);
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 0 4px;
}
@media (max-width: 600px) { .fv-card { flex: 0 0 160px; } }
</style>
@endsection
