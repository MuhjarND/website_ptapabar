document.addEventListener('DOMContentLoaded', function () {
    var observer = 'IntersectionObserver' in window ? createObserver() : null;
    document.querySelectorAll('.fade-up').forEach(function (el) {
        if (observer) observer.observe(el);
        else el.classList.add('visible');
    });
    initTextToSpeech();
});

function createObserver() {
    return new IntersectionObserver(function (entries, observer) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
}

function initTextToSpeech() {
    if (!('speechSynthesis' in window)) {
        var controls = document.querySelector('#tts-panel .tts-controls');
        var status = document.getElementById('tts-status');
        if (controls) controls.hidden = true;
        if (status) status.textContent = 'Pembacaan suara tidak didukung oleh browser ini.';
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

    var isSpeaking = false, isPaused = false;
    function getPageText() {
        var main = document.getElementById('main-content');
        return main ? main.innerText.replace(/\s+/g, ' ').trim() : '';
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
    playBtn.addEventListener('click', function () {
        if (isPaused) { synth.resume(); isPaused = false; playBtn.style.display = 'none'; pauseBtn.style.display = 'flex'; toggleBtn.classList.add('speaking'); statusEl.textContent = 'Sedang membaca...'; return; }
        startSpeaking();
    });
    pauseBtn.addEventListener('click', function () { synth.pause(); isPaused = true; playBtn.style.display = 'flex'; pauseBtn.style.display = 'none'; toggleBtn.classList.remove('speaking'); statusEl.textContent = 'Dijeda.'; });
    stopBtn.addEventListener('click', function () { synth.cancel(); isSpeaking = false; isPaused = false; playBtn.style.display = 'flex'; pauseBtn.style.display = 'none'; toggleBtn.classList.remove('speaking'); statusEl.textContent = 'Dihentikan.'; });
    closeBtn.addEventListener('click', function () { stopBtn.click(); });
    rateInput.addEventListener('input', function () { rateVal.textContent = this.value + 'x'; if (isSpeaking && !isPaused) { synth.cancel(); startSpeaking(); } });
    if (synth.getVoices().length === 0) synth.addEventListener('voiceschanged', function () {});
}
