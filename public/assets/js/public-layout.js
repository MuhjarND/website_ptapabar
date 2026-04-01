document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.fade-up').forEach(function (el) {
        createObserver().observe(el);
    });
    initCustomCursor();
    initTextToSpeech();
});

function createObserver() {
    return new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });
}

function initCustomCursor() {
    if (window.innerWidth <= 768 || 'ontouchstart' in window) return;

    var dot = document.getElementById('cursorDot');
    var ring = document.getElementById('cursorRing');
    if (!dot || !ring) return;

    var mouseX = 0, mouseY = 0, ringX = 0, ringY = 0, trailCounter = 0;
    document.addEventListener('mousemove', function (e) {
        mouseX = e.clientX;
        mouseY = e.clientY;
        dot.style.left = mouseX + 'px';
        dot.style.top = mouseY + 'px';
        trailCounter++;
        if (trailCounter % 3 === 0) {
            var trail = document.createElement('div');
            trail.className = 'cursor-trail';
            trail.style.left = mouseX + 'px';
            trail.style.top = mouseY + 'px';
            document.body.appendChild(trail);
            setTimeout(function () { trail.remove(); }, 400);
        }
    });
    (function animateRing() {
        ringX += (mouseX - ringX) * 0.15;
        ringY += (mouseY - ringY) * 0.15;
        ring.style.left = ringX + 'px';
        ring.style.top = ringY + 'px';
        requestAnimationFrame(animateRing);
    })();
    document.addEventListener('mouseover', function (e) {
        var t = e.target;
        if (t.matches('a, button, input, textarea, select, [onclick], .quick-link-card, .post-card, .survey-card, .nav-search-btn, .slider-dots .dot')) {
            dot.classList.add('hover');
            ring.classList.add('hover');
        }
    });
    document.addEventListener('mouseout', function (e) {
        var t = e.target;
        if (t.matches('a, button, input, textarea, select, [onclick], .quick-link-card, .post-card, .survey-card, .nav-search-btn, .slider-dots .dot')) {
            dot.classList.remove('hover');
            ring.classList.remove('hover');
        }
    });
    document.addEventListener('mousedown', function (e) {
        dot.classList.add('click');
        ring.classList.add('click');
        for (var i = 0; i < 6; i++) {
            var spark = document.createElement('div');
            spark.className = 'cursor-sparkle';
            var angle = (Math.PI * 2 / 6) * i;
            var dist = 18 + Math.random() * 14;
            spark.style.left = (e.clientX + Math.cos(angle) * dist) + 'px';
            spark.style.top = (e.clientY + Math.sin(angle) * dist) + 'px';
            spark.style.width = (3 + Math.random() * 4) + 'px';
            spark.style.height = spark.style.width;
            document.body.appendChild(spark);
            setTimeout(function () { spark.remove(); }, 600);
        }
    });
    document.addEventListener('mouseup', function () {
        dot.classList.remove('click');
        ring.classList.remove('click');
    });
    document.addEventListener('mouseleave', function () {
        dot.style.opacity = '0';
        ring.style.opacity = '0';
    });
    document.addEventListener('mouseenter', function () {
        dot.style.opacity = '1';
        ring.style.opacity = '1';
    });
}

function initTextToSpeech() {
    if (!('speechSynthesis' in window)) {
        var widget = document.getElementById('tts-widget');
        if (widget) widget.style.display = 'none';
        return;
    }

    var synth = window.speechSynthesis;
    var toggleBtn = document.getElementById('tts-toggle');
    var panel = document.getElementById('tts-panel');
    var playBtn = document.getElementById('tts-play');
    var pauseBtn = document.getElementById('tts-pause');
    var stopBtn = document.getElementById('tts-stop');
    var closeBtn = document.getElementById('tts-close');
    var rateInput = document.getElementById('tts-rate');
    var rateVal = document.getElementById('tts-rate-val');
    var statusEl = document.getElementById('tts-status');
    var ttsIcon = document.getElementById('tts-icon');
    if (!toggleBtn || !panel || !playBtn || !pauseBtn || !stopBtn || !closeBtn || !rateInput || !rateVal || !statusEl || !ttsIcon) return;

    var panelOpen = false, isSpeaking = false, isPaused = false;
    function getPageText() {
        var text = '';
        ['.content-body', '.page-content', '.hero-content', '.posts-grid'].forEach(function (selector) {
            document.querySelectorAll(selector).forEach(function (el) { text += el.innerText + ' '; });
        });
        return text.replace(/\s+/g, ' ').trim();
    }
    function startSpeaking() {
        synth.cancel();
        var text = getPageText();
        if (!text) {
            statusEl.textContent = 'Tidak ada teks untuk dibaca.';
            return;
        }
        var utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'id-ID';
        utterance.rate = parseFloat(rateInput.value);
        synth.getVoices().forEach(function (voice) { if (!utterance.voice && voice.lang.indexOf('id') === 0) utterance.voice = voice; });
        utterance.onstart = function () { isSpeaking = true; isPaused = false; playBtn.style.display = 'none'; pauseBtn.style.display = 'flex'; toggleBtn.classList.add('speaking'); ttsIcon.className = 'fas fa-volume-up'; statusEl.textContent = 'Sedang membaca...'; };
        utterance.onend = function () { isSpeaking = false; isPaused = false; playBtn.style.display = 'flex'; pauseBtn.style.display = 'none'; toggleBtn.classList.remove('speaking'); statusEl.textContent = 'Selesai.'; };
        utterance.onerror = function () { isSpeaking = false; playBtn.style.display = 'flex'; pauseBtn.style.display = 'none'; toggleBtn.classList.remove('speaking'); statusEl.textContent = 'Terjadi kesalahan.'; };
        synth.speak(utterance);
    }
    function togglePanel() { panelOpen = !panelOpen; panel.style.display = panelOpen ? 'block' : 'none'; }
    toggleBtn.addEventListener('click', function () {
        if (!panelOpen) { togglePanel(); startSpeaking(); return; }
        if (isSpeaking && !isPaused) { synth.pause(); isPaused = true; playBtn.style.display = 'flex'; pauseBtn.style.display = 'none'; toggleBtn.classList.remove('speaking'); statusEl.textContent = 'Dijeda.'; return; }
        if (isPaused) { synth.resume(); isPaused = false; playBtn.style.display = 'none'; pauseBtn.style.display = 'flex'; toggleBtn.classList.add('speaking'); statusEl.textContent = 'Sedang membaca...'; return; }
        startSpeaking();
    });
    playBtn.addEventListener('click', function () {
        if (isPaused) { synth.resume(); isPaused = false; playBtn.style.display = 'none'; pauseBtn.style.display = 'flex'; toggleBtn.classList.add('speaking'); statusEl.textContent = 'Sedang membaca...'; return; }
        startSpeaking();
    });
    pauseBtn.addEventListener('click', function () { synth.pause(); isPaused = true; playBtn.style.display = 'flex'; pauseBtn.style.display = 'none'; toggleBtn.classList.remove('speaking'); statusEl.textContent = 'Dijeda.'; });
    stopBtn.addEventListener('click', function () { synth.cancel(); isSpeaking = false; isPaused = false; playBtn.style.display = 'flex'; pauseBtn.style.display = 'none'; toggleBtn.classList.remove('speaking'); statusEl.textContent = 'Dihentikan.'; });
    closeBtn.addEventListener('click', function () { synth.cancel(); isSpeaking = false; isPaused = false; playBtn.style.display = 'flex'; pauseBtn.style.display = 'none'; toggleBtn.classList.remove('speaking'); panelOpen = false; panel.style.display = 'none'; statusEl.textContent = 'Siap membaca...'; });
    rateInput.addEventListener('input', function () { rateVal.textContent = this.value + 'x'; if (isSpeaking && !isPaused) { synth.cancel(); startSpeaking(); } });
    if (synth.getVoices().length === 0) synth.addEventListener('voiceschanged', function () {});
}
