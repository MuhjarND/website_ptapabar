(function () {
    var currentSlide = 0;
    var requestedSlide = 0;
    var slides = [];
    var dots = [];

    function refreshSliderElements() {
        slides = Array.prototype.slice.call(document.querySelectorAll('.hero-slider .slide'));
        dots = Array.prototype.slice.call(document.querySelectorAll('.slider-dots .dot'));
    }

    function setBodyScrollLocked(locked) {
        document.body.style.overflow = locked ? 'hidden' : '';
    }

    function goToSlide(index) {
        if (!slides[index]) {
            return;
        }

        requestedSlide = index;
        var image = slides[index].querySelector('img');
        if (image && image.dataset.slideSrc) {
            var source = image.previousElementSibling;
            if (source) {
                source.srcset = source.dataset.srcset;
                delete source.dataset.srcset;
            }
            image.src = image.dataset.slideSrc;
            delete image.dataset.slideSrc;
        }

        if (image && !image.complete && typeof image.decode === 'function') {
            image.decode().catch(function () {}).then(function () {
                if (requestedSlide === index) {
                    activateSlide(index);
                }
            });
        } else {
            activateSlide(index);
        }
    }

    function activateSlide(index) {
        slides[currentSlide].classList.remove('active');
        if (dots[currentSlide]) {
            dots[currentSlide].classList.remove('active');
        }

        currentSlide = index;
        slides[currentSlide].classList.add('active');
        if (dots[currentSlide]) {
            dots[currentSlide].classList.add('active');
        }
    }

    function nextSlide() {
        if (!slides.length) {
            return;
        }

        goToSlide((currentSlide + 1) % slides.length);
    }

    function prevSlide() {
        if (!slides.length) {
            return;
        }

        goToSlide((currentSlide - 1 + slides.length) % slides.length);
    }

    function countUp(el) {
        var target = parseFloat(el.getAttribute('data-target'));
        var decimals = (target % 1 !== 0) ? (target.toString().split('.')[1] || '').length : 0;
        var duration = 1800;
        var startTime = null;
        var startValue = 0;

        function formatValue(value) {
            var formatted = value.toFixed(decimals);
            if (decimals === 0) {
                return formatted;
            }

            return formatted
                .replace(/(\.\d*?[1-9])0+$/, '$1')
                .replace(/\.0+$/, '')
                .replace(/\.00$/, '');
        }

        function ease(t) {
            return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
        }

        function step(timestamp) {
            if (!startTime) {
                startTime = timestamp;
            }

            var progress = Math.min((timestamp - startTime) / duration, 1);
            var current = startValue + (ease(progress) * (target - startValue));
            el.textContent = formatValue(current);

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = formatValue(target);
            }
        }

        requestAnimationFrame(step);
    }

    function initSurveyCounters() {
        var counters = Array.prototype.slice.call(document.querySelectorAll('.survey-value[data-target]'));
        if (!counters.length) {
            return;
        }

        function startCounter(el) {
            if (el.classList.contains('counted')) {
                return;
            }

            el.classList.add('counted');
            countUp(el);
        }

        counters.forEach(function (el) {
            el.textContent = '0';
        });

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        startCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.3 });

            counters.forEach(function (el) {
                observer.observe(el);
            });

            return;
        }

        counters.forEach(function (el) {
            startCounter(el);
        });
    }

    function initMobilePublications() {
        var board = document.querySelector('.home-publications');
        if (!board) {
            return;
        }

        var panels = Array.from(board.querySelectorAll('[data-mobile-publication]'));
        if (panels.length < 2) {
            return;
        }

        var switcher = document.createElement('div');
        switcher.className = 'home-mobile-publication-switcher';
        switcher.setAttribute('role', 'group');
        switcher.setAttribute('aria-label', 'Pilih publikasi pengadilan');

        var buttons = panels.map(function (panel, i) {
            var button = document.createElement('button');
            panel.id = 'home-publication-' + (i + 1);
            panel.classList.add('home-mobile-publication-pane');
            button.type = 'button';
            button.textContent = panel.dataset.mobilePublication;
            button.setAttribute('aria-controls', panel.id);
            button.addEventListener('click', function () { show(i); });
            switcher.appendChild(button);
            return button;
        });

        function show(index) {
            panels.forEach(function (panel, i) {
                panel.classList.toggle('is-mobile-publication-active', i === index);
                buttons[i].setAttribute('aria-pressed', String(i === index));
            });
        }

        board.prepend(switcher);
        board.classList.add('has-mobile-publications');
        var anchor = document.getElementById(window.location.hash.slice(1));
        show(Math.max(0, panels.findIndex(function (panel) { return anchor && panel.contains(anchor); })));
    }

    function initAnnouncementFilters() {
        var filterRoot = document.querySelector('[data-announcement-filter]');
        var paginationRoot = document.querySelector('[data-announcement-pagination]');
        if (!filterRoot || !paginationRoot) {
            return;
        }

        var buttons = Array.prototype.slice.call(filterRoot.querySelectorAll('.announcement-filter-button'));
        var items = Array.prototype.slice.call(document.querySelectorAll('.announcement-entry'));
        var emptyState = document.querySelector('[data-empty-message]');
        var nav = document.querySelector('[data-announcement-pagination-nav]');
        var navButtons = nav ? Array.prototype.slice.call(nav.querySelectorAll('.news-home-pagination-button')) : [];
        var perPage = Number(paginationRoot.dataset.announcementPerPage);

        if (!buttons.length || !items.length) {
            return;
        }

        function applyFilter(filterValue, page) {
            var filteredItems = items.filter(function (item) {
                return filterValue === 'all' || item.getAttribute('data-announcement-category') === filterValue;
            });
            var totalPages = Math.max(1, Math.ceil(filteredItems.length / perPage));
            var currentPage = Math.min(Math.max(page || 1, 1), totalPages);
            var start = (currentPage - 1) * perPage;
            var end = start + perPage;

            buttons.forEach(function (button) {
                button.classList.toggle('is-active', button.getAttribute('data-filter') === filterValue);
            });

            items.forEach(function (item) {
                item.hidden = true;
            });

            filteredItems.forEach(function (item, index) {
                item.hidden = index < start || index >= end;
            });

            if (emptyState) {
                emptyState.hidden = filteredItems.length !== 0;
            }

            if (nav) {
                nav.hidden = filteredItems.length === 0 || totalPages <= 1;
            }

            navButtons.forEach(function (button) {
                var pageNumber = parseInt(button.getAttribute('data-page'), 10);
                button.hidden = pageNumber > totalPages;
                button.classList.toggle('is-active', pageNumber === currentPage);
            });

        }

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                applyFilter(button.getAttribute('data-filter'), 1);
            });
        });

        navButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var activeFilterButton = filterRoot.querySelector('.announcement-filter-button.is-active');
                var activeFilter = activeFilterButton ? activeFilterButton.getAttribute('data-filter') : 'all';
                applyFilter(activeFilter, parseInt(button.getAttribute('data-page'), 10));
            });
        });

        applyFilter('all', 1);
    }

    function initArticleFilters() {
        var filterRoot = document.querySelector('[data-article-filter]');
        var paginationRoot = document.querySelector('[data-article-pagination]');
        if (!filterRoot || !paginationRoot) {
            return;
        }

        var buttons = Array.prototype.slice.call(filterRoot.querySelectorAll('.article-filter-button'));
        var items = Array.prototype.slice.call(paginationRoot.querySelectorAll('[data-article-category]'));
        var emptyState = document.querySelector('[data-article-empty-message]');
        var nav = document.querySelector('[data-article-pagination-nav]');
        var navButtons = nav ? Array.prototype.slice.call(nav.querySelectorAll('.news-home-pagination-button')) : [];
        var perPage = Number(paginationRoot.dataset.articlePerPage);

        if (!buttons.length || !items.length) {
            return;
        }

        function applyFilter(filterValue, page) {
            var filteredItems = items.filter(function (item) {
                return filterValue === 'all' || item.getAttribute('data-article-category') === filterValue;
            });
            var totalPages = Math.max(1, Math.ceil(filteredItems.length / perPage));
            var currentPage = Math.min(Math.max(page || 1, 1), totalPages);
            var start = (currentPage - 1) * perPage;
            var end = start + perPage;

            buttons.forEach(function (button) {
                button.classList.toggle('is-active', button.getAttribute('data-filter') === filterValue);
            });

            items.forEach(function (item) {
                item.hidden = true;
            });

            filteredItems.forEach(function (item, index) {
                item.hidden = index < start || index >= end;
            });

            if (emptyState) {
                emptyState.hidden = filteredItems.length !== 0;
            }

            if (nav) {
                nav.hidden = filteredItems.length === 0 || totalPages <= 1;
            }

            navButtons.forEach(function (button) {
                var pageNumber = parseInt(button.getAttribute('data-page'), 10);
                button.hidden = pageNumber > totalPages;
                button.classList.toggle('is-active', pageNumber === currentPage);
            });

        }

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                applyFilter(button.getAttribute('data-filter'), 1);
            });
        });

        navButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var activeFilterButton = filterRoot.querySelector('.article-filter-button.is-active');
                var activeFilter = activeFilterButton ? activeFilterButton.getAttribute('data-filter') : 'all';
                applyFilter(activeFilter, parseInt(button.getAttribute('data-page'), 10));
            });
        });

        applyFilter('all', 1);
    }

    function initNewsFilters() {
        var filterRoots = Array.prototype.slice.call(document.querySelectorAll('[data-news-filter]'));
        if (!filterRoots.length) {
            return;
        }

        filterRoots.forEach(function (filterRoot) {
            var filterKey = filterRoot.getAttribute('data-news-filter');
            var buttons = Array.prototype.slice.call(filterRoot.querySelectorAll('.news-filter-button'));
            var items = Array.prototype.slice.call(document.querySelectorAll('[data-news-filter-item="' + filterKey + '"]'));
            var emptyState = document.querySelector('[data-news-empty-message="' + filterKey + '"]');

            if (!buttons.length || !items.length) {
                return;
            }

            function applyFilter(filterValue) {
                var visibleCount = 0;

                buttons.forEach(function (button) {
                    button.classList.toggle('is-active', button.getAttribute('data-filter') === filterValue);
                });

                items.forEach(function (item) {
                    var matches = filterValue === 'all' || item.getAttribute('data-news-category') === filterValue;
                    item.hidden = !matches;
                    if (matches) {
                        visibleCount += 1;
                    }
                });

                if (emptyState) {
                    emptyState.hidden = visibleCount !== 0;
                }
            }

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    applyFilter(button.getAttribute('data-filter'));
                });
            });

            applyFilter('all');
        });
    }

    function initNewsPagination() {
        var paginationRoots = Array.prototype.slice.call(document.querySelectorAll('[data-news-pagination]'));
        if (!paginationRoots.length) {
            return;
        }

        paginationRoots.forEach(function (root) {
            var key = root.getAttribute('data-news-pagination');
            var perPage = parseInt(root.getAttribute('data-news-per-page'), 10);
            var maxPages = parseInt(root.getAttribute('data-news-max-pages'), 10);
            var items = Array.prototype.slice.call(root.querySelectorAll('[data-news-page-item="' + key + '"]'));
            var nav = document.querySelector('[data-news-pagination-nav="' + key + '"]');
            var buttons = nav ? Array.prototype.slice.call(nav.querySelectorAll('.news-home-pagination-button')) : [];
            var totalPages = Math.min(maxPages, Math.ceil(items.length / perPage));

            if (!items.length || totalPages <= 1) {
                return;
            }

            function applyPage(page) {
                var currentPage = Math.min(Math.max(page, 1), totalPages);
                var start = (currentPage - 1) * perPage;
                var end = start + perPage;

                items.forEach(function (item, index) {
                    item.hidden = index < start || index >= end;
                });

                buttons.forEach(function (button) {
                    button.classList.toggle('is-active', parseInt(button.getAttribute('data-page'), 10) === currentPage);
                });
            }

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    applyPage(parseInt(button.getAttribute('data-page'), 10));
                });
            });

            applyPage(1);
        });
    }

    function initHomeShowcase() {
        var showcase = document.querySelector('.home-showcase');
        if (!showcase) {
            return;
        }

        showcase.querySelectorAll('[data-showcase-carousel]').forEach(initShowcaseCarousel);
        showcase.addEventListener('click', function (event) {
            var item = event.target.closest('[data-showcase-video], [data-showcase-poster]');
            if (!item || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
                return;
            }

            event.preventDefault();
            if (item.dataset.showcaseVideo) {
                openVideoPopup(item.dataset.showcaseVideo);
            } else {
                openPosterPopup(item);
            }
        });
    }

    function initShowcaseCarousel(root) {
        var navigation = root.querySelector('[data-showcase-navigation]');
        if (!navigation) {
            return;
        }

        var slides = root.querySelectorAll('[data-showcase-slide]');
        var options = navigation.querySelectorAll('[data-showcase-select]');
        var rail = navigation.querySelector('.home-showcase-titles');
        var position = navigation.querySelector('[data-showcase-position]');
        var toggle = navigation.querySelector('[data-showcase-toggle]');
        var active = 0;
        var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        var paused = reducedMotion.matches;
        var canHover = window.matchMedia('(hover: hover)');
        var animations = [];
        var timer;

        function show(index) {
            var next = (index + slides.length) % slides.length;
            if (next === active) {
                return;
            }

            var option = options[next].getBoundingClientRect();
            var visible = rail.getBoundingClientRect();
            if (option.left < visible.left) {
                rail.scrollBy(option.left - visible.left - 4, 0);
            } else if (option.right > visible.right) {
                rail.scrollBy(option.right - visible.right + 4, 0);
            }

            var previous = slides[active];
            var incoming = slides[next];
            var direction = index < active ? -1 : 1;
            var animate = !reducedMotion.matches && typeof incoming.animate === 'function';
            animations.forEach(function (animation) { animation.cancel(); });
            animations = [];

            slides.forEach(function (slide) {
                slide.hidden = slide !== incoming && (!animate || slide !== previous);
                slide.inert = slide !== incoming;
            });

            options[active].setAttribute('aria-pressed', 'false');
            active = next;
            options[active].setAttribute('aria-pressed', 'true');
            position.textContent = (active + 1) + ' / ' + slides.length;

            if (animate) {
                var timing = { duration: 320, easing: 'cubic-bezier(.22,.61,.36,1)', fill: 'both' };
                var outgoingAnimation = previous.animate([
                    { transform: 'translateX(0)' }, { transform: 'translateX(' + (-direction * 100) + '%)' }
                ], timing);
                var incomingAnimation = incoming.animate([
                    { transform: 'translateX(' + (direction * 100) + '%)' }, { transform: 'translateX(0)' }
                ], timing);
                outgoingAnimation.onfinish = function () {
                    if (slides[active] !== previous) {
                        previous.hidden = true;
                    }
                };
                animations = [outgoingAnimation, incomingAnimation];
            }
        }

        function schedule() {
            window.clearInterval(timer);
            toggle.textContent = paused ? 'Putar otomatis' : 'Jeda otomatis';
            if (paused) {
                return;
            }

            timer = window.setInterval(function () {
                var bounds = root.getBoundingClientRect();
                if (!document.hidden && bounds.bottom > 0 && bounds.top < window.innerHeight
                    && !(canHover.matches && root.matches(':hover')) && !root.contains(document.activeElement)
                    && document.body.style.overflow !== 'hidden' && !document.querySelector('dialog[open]')) {
                    show(active + 1);
                }
            }, Number(root.dataset.showcaseDelay));
        }

        navigation.addEventListener('click', function (event) {
            var button = event.target.closest('button');
            if (!button) {
                return;
            }

            if (button === toggle) {
                paused = !paused;
            } else if (button.hasAttribute('data-showcase-select')) {
                show(Number(button.dataset.showcaseSelect));
            } else if (button.hasAttribute('data-showcase-step')) {
                show(active + Number(button.dataset.showcaseStep));
            }
            schedule();
        });
        navigation.hidden = false;
        schedule();
    }

    function openVideoPopup(youtubeId) {
        var frame = document.getElementById('videoFrame');
        var popup = document.getElementById('videoPopup');
        if (!frame || !popup) {
            return;
        }

        frame.src = 'https://www.youtube.com/embed/' + youtubeId + '?autoplay=1';
        popup.style.display = 'flex';
        setBodyScrollLocked(true);
    }

    function closeVideoPopup() {
        var frame = document.getElementById('videoFrame');
        var popup = document.getElementById('videoPopup');
        if (!frame || !popup) {
            return;
        }

        frame.src = '';
        popup.style.display = 'none';
        setBodyScrollLocked(false);
    }

    function openPosterPopup(el) {
        var poster = document.getElementById('posterImg');
        var popup = document.getElementById('posterPopup');
        var image = el ? el.querySelector('img') : null;
        if (!poster || !popup || !image) {
            return;
        }

        poster.src = image.src;
        poster.alt = image.alt || el.querySelector('.card-title')?.textContent.trim() || 'Poster galeri';
        popup.style.display = 'flex';
        setBodyScrollLocked(true);
    }

    function closePosterPopup() {
        var popup = document.getElementById('posterPopup');
        if (!popup) {
            return;
        }

        popup.style.display = 'none';
        setBodyScrollLocked(false);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var homePopup = document.getElementById('homeImagePopup');
        if (homePopup && typeof homePopup.showModal === 'function') {
            homePopup.showModal();
        }

        refreshSliderElements();

        if (slides.length > 1) {
            var sliderTimer;
            function startSlider() {
                sliderTimer = window.setInterval(function () {
                    if (!document.hidden && !homePopup?.open) {
                        nextSlide();
                    }
                }, 5000);
            }
            if ('IntersectionObserver' in window) {
                var slider = document.getElementById('heroSlider');
                new IntersectionObserver(function (entries) {
                    window.clearInterval(sliderTimer);
                    if (entries[0].isIntersecting) {
                        startSlider();
                    }
                }).observe(slider);
            } else {
                startSlider();
            }
        }

        initSurveyCounters();
        initMobilePublications();
        initNewsFilters();
        initNewsPagination();
        initAnnouncementFilters();
        initArticleFilters();
        initHomeShowcase();

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') {
                return;
            }

            var videoPopup = document.getElementById('videoPopup');
            var posterPopup = document.getElementById('posterPopup');
            if (videoPopup && videoPopup.style.display === 'flex') {
                closeVideoPopup();
            }
            if (posterPopup && posterPopup.style.display === 'flex') {
                closePosterPopup();
            }
        });
    });

    window.goToSlide = goToSlide;
    window.nextSlide = nextSlide;
    window.prevSlide = prevSlide;
    window.openVideoPopup = openVideoPopup;
    window.closeVideoPopup = closeVideoPopup;
    window.openPosterPopup = openPosterPopup;
    window.closePosterPopup = closePosterPopup;
})();
