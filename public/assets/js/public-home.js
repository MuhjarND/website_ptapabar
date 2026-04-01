(function () {
    var currentSlide = 0;
    var slides = [];
    var dots = [];
    var sliderTimer = null;

    function refreshSliderElements() {
        slides = Array.prototype.slice.call(document.querySelectorAll('.hero-slider .slide'));
        dots = Array.prototype.slice.call(document.querySelectorAll('.slider-dots .dot'));
    }

    function setBodyScrollLocked(locked) {
        document.body.style.overflow = locked ? 'hidden' : '';
    }

    function goToSlide(index) {
        if (!slides.length) {
            return;
        }

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

            window.setTimeout(function () {
                counters.forEach(function (el) {
                    startCounter(el);
                });
            }, 1400);

            return;
        }

        counters.forEach(function (el) {
            startCounter(el);
        });
    }

    function initAnnouncementFilters() {
        var filterRoot = document.querySelector('[data-announcement-filter]');
        if (!filterRoot) {
            return;
        }

        var buttons = Array.prototype.slice.call(filterRoot.querySelectorAll('.announcement-filter-button'));
        var items = Array.prototype.slice.call(document.querySelectorAll('.announcement-entry'));
        var emptyState = document.querySelector('[data-empty-message]');
        var paginationRoot = document.querySelector('[data-announcement-pagination]');
        var nav = document.querySelector('[data-announcement-pagination-nav]');
        var navButtons = nav ? Array.prototype.slice.call(nav.querySelectorAll('.news-home-pagination-button')) : [];
        var perPage = paginationRoot ? parseInt(paginationRoot.getAttribute('data-announcement-per-page') || '5', 10) : 5;

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

            if (paginationRoot) {
                paginationRoot.classList.add('is-ready');
            }
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
        if (!filterRoot) {
            return;
        }

        var buttons = Array.prototype.slice.call(filterRoot.querySelectorAll('.article-filter-button'));
        var items = Array.prototype.slice.call(document.querySelectorAll('.announcement-article-item[data-article-category]'));
        var emptyState = document.querySelector('[data-article-empty-message]');
        var paginationRoot = document.querySelector('[data-article-pagination]');
        var nav = document.querySelector('[data-article-pagination-nav]');
        var navButtons = nav ? Array.prototype.slice.call(nav.querySelectorAll('.news-home-pagination-button')) : [];
        var perPage = paginationRoot ? parseInt(paginationRoot.getAttribute('data-article-per-page') || '5', 10) : 5;

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

            if (paginationRoot) {
                paginationRoot.classList.add('is-ready');
            }
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
            var perPage = parseInt(root.getAttribute('data-news-per-page') || '3', 10);
            var maxPages = parseInt(root.getAttribute('data-news-max-pages') || '3', 10);
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

                root.classList.add('is-ready');
            }

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    applyPage(parseInt(button.getAttribute('data-page'), 10));
                });
            });

            applyPage(1);
        });
    }

    function initGalleryFilters() {
        var filterRoot = document.querySelector('[data-gallery-filter]');
        if (!filterRoot) {
            return;
        }

        var buttons = Array.prototype.slice.call(filterRoot.querySelectorAll('.gallery-filter-button'));
        var items = Array.prototype.slice.call(document.querySelectorAll('.fv-card[data-gallery-category]'));
        var emptyState = document.querySelector('[data-gallery-empty-message]');

        if (!buttons.length || !items.length) {
            return;
        }

        function applyFilter(filterValue) {
            var visibleCount = 0;

            buttons.forEach(function (button) {
                button.classList.toggle('is-active', button.getAttribute('data-filter') === filterValue);
            });

            items.forEach(function (item) {
                var matches = !filterValue || item.getAttribute('data-gallery-category') === filterValue;
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
                var filterValue = button.getAttribute('data-filter');
                var isActive = button.classList.contains('is-active');

                applyFilter(isActive ? null : filterValue);
            });
        });

        applyFilter(null);
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
        refreshSliderElements();

        if (slides.length > 1) {
            sliderTimer = window.setInterval(nextSlide, 5000);
        }

        initSurveyCounters();
        initNewsFilters();
        initNewsPagination();
        initAnnouncementFilters();
        initArticleFilters();
        initGalleryFilters();

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
