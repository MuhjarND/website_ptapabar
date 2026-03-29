@extends('layouts.public')
@section('title', $post->title)
@section('content')
<div class="container">
    <div style="padding:40px 0; max-width:800px; margin:0 auto;">
        <div class="page-content">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Beranda</a>
                &nbsp;/&nbsp;
                @if($post->category == 'berita')
                    <a href="{{ route('berita.index') }}">Berita</a>
                @else
                    <a href="{{ route('pengumuman.index') }}">Pengumuman</a>
                @endif
                &nbsp;/&nbsp; {{ Str::limit($post->title, 40) }}
            </div>
            <span style="display:inline-block;background:var(--gold);color:var(--green-dark);padding:4px 14px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;margin-bottom:12px;">
                {{ ucfirst($post->category) }}
            </span>
            <h1>{{ $post->title }}</h1>
            <div style="font-size:13px;color:var(--text-light);margin:8px 0 24px;display:flex;align-items:center;gap:12px;">
                <span><i class="far fa-calendar"></i> {{ $post->created_at->format('d F Y') }}</span>
                @if($post->user) <span><i class="far fa-user"></i> {{ $post->user->name }}</span> @endif
            </div>
            @if($post->image)
                <img src="{{ asset('storage/'.$post->image) }}" alt="{{ $post->title }}" style="width:100%;border-radius:10px;margin-bottom:24px;">
            @endif
            <div class="content-body">
                {!! $post->content !!}
            </div>
        </div>

        {{-- Dokumentasi Foto & PDF --}}
        @if($post->photos->count() > 0)
        @php
            $imageFiles = $post->photos->filter(function($p) { return !Str::endsWith(strtolower($p->image), '.pdf'); });
            $pdfFiles = $post->photos->filter(function($p) { return Str::endsWith(strtolower($p->image), '.pdf'); });
        @endphp
        <div class="page-content" style="margin-top:24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <h2 style="font-size:20px;font-weight:700;color:var(--green-dark);display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-folder-open" style="color:var(--gold);"></i> Dokumentasi
                    <span style="background:var(--gold);color:var(--green-dark);padding:2px 10px;border-radius:20px;font-size:12px;font-weight:700;">{{ $post->photos->count() }}</span>
                </h2>
                <a href="javascript:void(0)" onclick="downloadAllFiles()" style="font-size:13px;color:var(--green-dark);font-weight:600;display:flex;align-items:center;gap:6px;padding:8px 16px;border:1px solid var(--border);border-radius:8px;transition:0.2s;" onmouseover="this.style.background='var(--green-dark)';this.style.color='#fff'" onmouseout="this.style.background='';this.style.color='var(--green-dark)'">
                    <i class="fas fa-download"></i> Unduh Semua
                </a>
            </div>

            {{-- Image Gallery --}}
            @if($imageFiles->count() > 0)
            <div class="tiled-gallery">
                @foreach($imageFiles->values() as $index => $photo)
                <div class="tile-item tile-{{ ($index % 6) }}" onclick="openLightbox({{ $index }})">
                    <img src="{{ asset('storage/'.$photo->image) }}" alt="{{ $photo->caption ?? 'Foto '.($index+1) }}" loading="lazy">
                    <div class="tile-overlay">
                        <div class="tile-actions">
                            @if($photo->caption)
                            <span class="tile-caption">{{ $photo->caption }}</span>
                            @endif
                            <a href="{{ asset('storage/'.$photo->image) }}" download class="tile-dl file-dl" onclick="event.stopPropagation();" title="Unduh foto">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- PDF List --}}
            @if($pdfFiles->count() > 0)
            <div style="margin-top:{{ $imageFiles->count() > 0 ? '20px' : '0' }};display:flex;flex-direction:column;gap:10px;">
                @foreach($pdfFiles as $pdf)
                <a href="{{ asset('storage/'.$pdf->image) }}" target="_blank" class="pdf-card file-dl">
                    <div class="pdf-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="pdf-info">
                        <span class="pdf-name">{{ $pdf->caption ?? basename($pdf->image) }}</span>
                        <span class="pdf-meta">Dokumen PDF — Klik untuk membuka</span>
                    </div>
                    <div class="pdf-action" title="Unduh PDF">
                        <i class="fas fa-download"></i>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Lightbox (images only) --}}
        @if($imageFiles->count() > 0)
        <div id="lightbox" style="display:none;">
            <div class="lb-backdrop" onclick="closeLightbox()"></div>
            <button class="lb-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
            <button class="lb-nav lb-prev" onclick="event.stopPropagation();navigateLB(-1)"><i class="fas fa-chevron-left"></i></button>
            <button class="lb-nav lb-next" onclick="event.stopPropagation();navigateLB(1)"><i class="fas fa-chevron-right"></i></button>
            <div class="lb-content" onclick="event.stopPropagation()">
                <img id="lb-img" src="">
                <div class="lb-footer">
                    <span id="lb-caption"></span>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <span id="lb-counter" style="font-size:13px;color:rgba(255,255,255,0.5);"></span>
                        <a id="lb-download" href="" download class="lb-dl-btn"><i class="fas fa-download"></i> Unduh</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif

        @if($related->count() > 0)
        <div style="margin-top:40px;">
            <h3 style="font-size:18px;font-weight:700;color:var(--green-dark);margin-bottom:20px;">Artikel Terkait</h3>
            <div class="posts-grid" style="grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;">
                @foreach($related as $r)
                <a href="{{ route('post.detail', $r->slug) }}" class="post-card">
                    <div class="card-img" style="height:120px;">
                        @if($r->image)
                            <img src="{{ asset('storage/'.$r->image) }}" alt="{{ $r->title }}">
                        @else
                            <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--green),var(--green-light));display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-newspaper" style="font-size:20px;color:rgba(255,255,255,0.3);"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body" style="padding:12px;">
                        <h3 class="card-title" style="font-size:13px;">{{ $r->title }}</h3>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<style>
/* ===== TILED GALLERY ===== */
.tiled-gallery {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-auto-rows: 180px;
    gap: 6px;
    border-radius: 12px;
    overflow: hidden;
}
.tile-item {
    position: relative;
    overflow: hidden;
    cursor: pointer;
    background: var(--green-dark);
}
.tile-item.tile-0 { grid-row: span 2; }
.tile-item.tile-3 { grid-column: span 2; }
.tile-item.tile-5 { grid-row: span 2; }

.tile-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease, filter 0.3s;
}
.tile-item:hover img {
    transform: scale(1.08);
    filter: brightness(0.75);
}
.tile-overlay {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    padding: 12px;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    opacity: 0;
    transition: opacity 0.3s;
    display: flex;
    align-items: flex-end;
}
.tile-item:hover .tile-overlay { opacity: 1; }
.tile-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}
.tile-caption {
    color: #fff;
    font-size: 12px;
    font-weight: 500;
    flex: 1;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    margin-right: 10px;
}
.tile-dl {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(4px);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
    transition: 0.2s;
    flex-shrink: 0;
}
.tile-dl:hover {
    background: var(--gold);
    color: var(--green-dark);
    transform: scale(1.1);
}

/* ===== PDF CARDS ===== */
.pdf-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 10px;
    transition: 0.2s;
    text-decoration: none;
}
.pdf-card:hover {
    border-color: #dc3545;
    box-shadow: 0 4px 16px rgba(220,53,69,0.1);
    transform: translateY(-2px);
}
.pdf-icon {
    width: 44px; height: 44px;
    border-radius: 10px;
    background: linear-gradient(135deg, #dc3545, #c82333);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.pdf-icon i { font-size: 20px; color: #fff; }
.pdf-info { flex: 1; overflow: hidden; }
.pdf-name {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.pdf-meta { font-size: 12px; color: var(--text-light); }
.pdf-action {
    width: 36px; height: 36px;
    border-radius: 8px;
    background: #f8f9fa;
    display: flex; align-items: center; justify-content: center;
    color: var(--text-light);
    font-size: 14px;
    flex-shrink: 0;
    transition: 0.2s;
}
.pdf-card:hover .pdf-action {
    background: var(--green);
    color: #fff;
}

@media (max-width: 600px) {
    .tiled-gallery { grid-template-columns: repeat(2, 1fr); grid-auto-rows: 140px; }
    .tile-item.tile-0, .tile-item.tile-5 { grid-row: span 1; }
    .tile-item.tile-3 { grid-column: span 1; }
}

/* ===== LIGHTBOX ===== */
#lightbox {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}
.lb-backdrop {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.92);
    backdrop-filter: blur(8px);
}
.lb-close {
    position: absolute;
    top: 20px; right: 20px;
    background: none; border: none;
    color: rgba(255,255,255,0.7);
    font-size: 24px; cursor: pointer;
    z-index: 10; transition: 0.2s;
    width: 44px; height: 44px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
}
.lb-close:hover { color: #fff; background: rgba(255,255,255,0.1); }
.lb-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(4px);
    border: none; color: #fff;
    width: 48px; height: 48px;
    border-radius: 50%;
    font-size: 18px; cursor: pointer;
    z-index: 10; transition: 0.2s;
    display: flex; align-items: center; justify-content: center;
}
.lb-nav:hover { background: var(--gold); color: var(--green-dark); }
.lb-prev { left: 20px; }
.lb-next { right: 20px; }
.lb-content {
    position: relative; z-index: 5;
    max-width: 90vw; max-height: 85vh;
    display: flex; flex-direction: column;
    align-items: center;
}
#lb-img {
    max-width: 90vw; max-height: 75vh;
    border-radius: 8px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    object-fit: contain;
    transition: opacity 0.25s;
}
.lb-footer {
    margin-top: 16px;
    display: flex; align-items: center;
    justify-content: space-between;
    width: 100%; max-width: 600px;
}
#lb-caption { color: rgba(255,255,255,0.8); font-size: 14px; flex: 1; }
.lb-dl-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px;
    background: var(--gold); color: var(--green-dark);
    border-radius: 8px; font-size: 13px; font-weight: 600;
    transition: 0.2s;
}
.lb-dl-btn:hover { transform: scale(1.05); box-shadow: 0 4px 16px rgba(200,169,81,0.4); }
</style>

<script>
var photos = @json($imageFiles->values()->map(function($p) { return ['url' => asset('storage/'.$p->image), 'caption' => $p->caption]; }));
var currentIndex = 0;

function openLightbox(index) {
    currentIndex = index;
    updateLightbox();
    document.getElementById('lightbox').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
    document.body.style.overflow = '';
}
function navigateLB(dir) {
    currentIndex = (currentIndex + dir + photos.length) % photos.length;
    updateLightbox();
}
function updateLightbox() {
    if (!photos.length) return;
    var p = photos[currentIndex];
    document.getElementById('lb-img').src = p.url;
    document.getElementById('lb-caption').textContent = p.caption || '';
    document.getElementById('lb-download').href = p.url;
    document.getElementById('lb-counter').textContent = (currentIndex+1) + ' / ' + photos.length;
}
document.addEventListener('keydown', function(e) {
    var lb = document.getElementById('lightbox');
    if (lb && lb.style.display === 'flex') {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') navigateLB(-1);
        if (e.key === 'ArrowRight') navigateLB(1);
    }
});

function downloadAllFiles() {
    document.querySelectorAll('.file-dl').forEach(function(link, i) {
        setTimeout(function() { link.click(); }, i * 400);
    });
}
</script>
@endsection
