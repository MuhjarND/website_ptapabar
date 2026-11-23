
@if(($publicFloatingIcons ?? collect())->isNotEmpty())
    @foreach($publicFloatingIcons->groupBy(function ($icon) { return $icon->position ?: \App\FloatingIcon::POSITION_BOTTOM_RIGHT; }) as $position => $floatingIconGroup)
        <div class="floating-icon-stack floating-icon-stack-{{ str_replace('_', '-', $position) }}">
            @foreach($floatingIconGroup as $floatingIcon)
                <a
                    href="{{ $floatingIcon->url ?: '#' }}"
                    class="floating-icon-link {{ in_array($position, [\App\FloatingIcon::POSITION_BOTTOM_LEFT, \App\FloatingIcon::POSITION_TOP_LEFT]) ? 'floating-icon-link-left' : '' }}"
                    aria-label="{{ $floatingIcon->title }}"
                    @if($floatingIcon->text) aria-describedby="floating-icon-note-{{ $floatingIcon->id }}" @endif
                    title="{{ $floatingIcon->title }}"
                    @if($floatingIcon->url) target="_blank" rel="noopener noreferrer" @endif
                >
                    @if($floatingIcon->text)
                        <span id="floating-icon-note-{{ $floatingIcon->id }}" class="floating-icon-text {{ in_array($position, [\App\FloatingIcon::POSITION_BOTTOM_LEFT, \App\FloatingIcon::POSITION_TOP_LEFT]) ? 'floating-icon-text-right' : '' }}">{{ $floatingIcon->text }}</span>
                    @endif
                    <span class="floating-icon-shell">
                        <span class="floating-icon-orb" aria-hidden="true"></span>
                        <span class="floating-icon-button">
                            @if($floatingIcon->icon)
                                <img src="{{ asset('storage/' . $floatingIcon->icon) }}" alt="{{ $floatingIcon->title }}">
                            @else
                                <i class="fas fa-book-open"></i>
                            @endif
                        </span>
                    </span>
                </a>
            @endforeach
        </div>
    @endforeach
@endif

<div id="tts-widget" class="tts-widget">
    <button type="button" id="tts-toggle" class="tts-toggle" title="Aksesibilitas" aria-label="Buka pengaturan aksesibilitas" aria-controls="tts-panel" aria-expanded="false">
        <i class="fas fa-volume-up" id="tts-icon"></i>
    </button>
    <div id="tts-panel" class="tts-panel" style="display:none;">
        <div class="tts-header">
            <span><i class="fas fa-headphones" aria-hidden="true"></i> Aksesibilitas</span>
            <button type="button" id="tts-close" class="tts-close" aria-label="Tutup pengaturan aksesibilitas"><i class="fas fa-times" aria-hidden="true"></i></button>
        </div>
        <div class="accessibility-tools">
            <button type="button" data-contrast aria-pressed="false">Kontras tinggi</button>
            <label for="accessibility-text-size">Ukuran teks</label>
            <select id="accessibility-text-size">
                <option value="100">100% (normal)</option>
                <option value="125">125%</option>
                <option value="150">150%</option>
            </select>
        </div>
        <div class="tts-controls">
            <button id="tts-play" class="tts-btn tts-btn-play" aria-label="Baca halaman"><i class="fas fa-play" aria-hidden="true"></i></button>
            <button id="tts-pause" class="tts-btn" aria-label="Jeda pembacaan" style="display:none;"><i class="fas fa-pause" aria-hidden="true"></i></button>
            <button id="tts-stop" class="tts-btn" aria-label="Hentikan pembacaan"><i class="fas fa-stop" aria-hidden="true"></i></button>
            <div class="tts-speed">
                <label for="tts-rate">Kecepatan</label>
                <input type="range" id="tts-rate" min="0.5" max="2" step="0.1" value="1">
                <span id="tts-rate-val">1x</span>
            </div>
        </div>
        <div id="tts-status" class="tts-status" role="status">Siap membaca...</div>
    </div>
</div>
