<div class="custom-cursor" id="cursorDot"></div>
<div class="cursor-ring" id="cursorRing"></div>

<div id="tts-widget" class="tts-widget">
    <button id="tts-toggle" class="tts-toggle" title="Text-to-Speech">
        <i class="fas fa-volume-up" id="tts-icon"></i>
    </button>
    <div id="tts-panel" class="tts-panel" style="display:none;">
        <div class="tts-header">
            <span><i class="fas fa-headphones"></i> Baca Halaman</span>
            <button id="tts-close" class="tts-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="tts-controls">
            <button id="tts-play" class="tts-btn tts-btn-play" title="Play"><i class="fas fa-play"></i></button>
            <button id="tts-pause" class="tts-btn" title="Pause" style="display:none;"><i class="fas fa-pause"></i></button>
            <button id="tts-stop" class="tts-btn" title="Stop"><i class="fas fa-stop"></i></button>
            <div class="tts-speed">
                <label>Kecepatan</label>
                <input type="range" id="tts-rate" min="0.5" max="2" step="0.1" value="1">
                <span id="tts-rate-val">1x</span>
            </div>
        </div>
        <div id="tts-status" class="tts-status">Siap membaca...</div>
    </div>
</div>
