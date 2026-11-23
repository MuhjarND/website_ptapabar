(function () {
    var nav = document.getElementById('site-navigation');
    var menu = document.querySelector('[data-mobile-nav]');
    var groups = Array.from(document.querySelectorAll('.site-nav-group'));
    var toggle = document.getElementById('tts-toggle');
    var panel = document.getElementById('tts-panel');
    var close = document.getElementById('tts-close');
    var contrast = document.querySelector('[data-contrast]');
    var textSize = document.getElementById('accessibility-text-size');

    function closeGroups(except) {
        groups.forEach(function (group) {
            if (group !== except) group.open = false;
        });
    }

    menu.addEventListener('click', function () {
        var open = nav.classList.toggle('open');
        menu.setAttribute('aria-expanded', String(open));
        if (!open) closeGroups();
    });
    groups.forEach(function (group) {
        group.addEventListener('toggle', function () {
            if (group.open) closeGroups(group);
        });
    });
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.navbar')) closeGroups();
    });

    function setPanel(open) {
        panel.style.display = open ? 'block' : 'none';
        toggle.setAttribute('aria-expanded', String(open));
        (open ? close : toggle).focus();
    }
    toggle.addEventListener('click', function () {
        setPanel(toggle.getAttribute('aria-expanded') !== 'true');
    });
    close.addEventListener('click', function () { setPanel(false); });
    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        if (panel.contains(document.activeElement)) {
            close.click();
            return;
        }
        var expanded = document.activeElement.closest('.site-nav-group[open]');
        if (expanded) {
            expanded.open = false;
            expanded.querySelector('summary').focus();
        } else if (nav.classList.contains('open') && nav.contains(document.activeElement)) {
            menu.click();
            menu.focus();
        }
    });

    function preferences(name, value) {
        try {
            if (value === undefined) return localStorage.getItem(name);
            localStorage.setItem(name, value);
        } catch (error) {
            // The controls still work if the browser disables storage.
        }
    }
    function setContrast(enabled) {
        document.body.classList.toggle('public-high-contrast', enabled);
        contrast.setAttribute('aria-pressed', String(enabled));
    }
    function setTextSize(size) {
        if (['100', '125', '150'].indexOf(size) === -1) size = '100';
        textSize.value = size;
        document.body.dataset.textSize = size;
    }
    contrast.addEventListener('click', function () {
        var enabled = contrast.getAttribute('aria-pressed') !== 'true';
        setContrast(enabled);
        preferences('pta-contrast', String(enabled));
    });
    textSize.addEventListener('change', function () {
        setTextSize(textSize.value);
        preferences('pta-text-size', textSize.value);
    });
    setContrast(preferences('pta-contrast') === 'true');
    setTextSize(preferences('pta-text-size'));
}());
