(function () {
    var photos = [];
    var currentIndex = 0;

    function getLightbox() {
        return document.getElementById('lightbox');
    }

    function setBodyScrollLocked(locked) {
        document.body.style.overflow = locked ? 'hidden' : '';
    }

    function updateLightbox() {
        if (!photos.length) {
            return;
        }

        var photo = photos[currentIndex];
        var image = document.getElementById('lb-img');
        var caption = document.getElementById('lb-caption');
        var download = document.getElementById('lb-download');
        var counter = document.getElementById('lb-counter');

        if (!image || !caption || !download || !counter) {
            return;
        }

        image.src = photo.url;
        caption.textContent = photo.caption || '';
        download.href = photo.url;
        counter.textContent = (currentIndex + 1) + ' / ' + photos.length;
    }

    function openLightbox(index) {
        var lightbox = getLightbox();
        if (!lightbox || !photos.length) {
            return;
        }

        currentIndex = index;
        updateLightbox();
        lightbox.style.display = 'flex';
        setBodyScrollLocked(true);
    }

    function closeLightbox() {
        var lightbox = getLightbox();
        if (!lightbox) {
            return;
        }

        lightbox.style.display = 'none';
        setBodyScrollLocked(false);
    }

    function navigateLB(direction) {
        if (!photos.length) {
            return;
        }

        currentIndex = (currentIndex + direction + photos.length) % photos.length;
        updateLightbox();
    }

    function downloadAllFiles() {
        document.querySelectorAll('.file-dl').forEach(function (link, index) {
            window.setTimeout(function () {
                link.click();
            }, index * 400);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var jsonNode = document.getElementById('postDetailPhotos');
        if (jsonNode && jsonNode.textContent) {
            photos = JSON.parse(jsonNode.textContent);
        }

        document.addEventListener('keydown', function (e) {
            var lightbox = getLightbox();
            if (!lightbox || lightbox.style.display !== 'flex') {
                return;
            }

            if (e.key === 'Escape') {
                closeLightbox();
            }
            if (e.key === 'ArrowLeft') {
                navigateLB(-1);
            }
            if (e.key === 'ArrowRight') {
                navigateLB(1);
            }
        });
    });

    window.openLightbox = openLightbox;
    window.closeLightbox = closeLightbox;
    window.navigateLB = navigateLB;
    window.downloadAllFiles = downloadAllFiles;
})();
