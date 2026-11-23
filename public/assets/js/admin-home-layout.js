(() => {
    const get = id => document.getElementById('home-editor-' + id);
    const { blocks, catalog, custom, rows, limit } = JSON.parse(get('data').textContent);
    const grid = get('grid');
    const select = get('type');
    const dialog = get('dialog');
    const detail = get('detail');
    const save = get('save');
    const geometry = ['x', 'y', 'w', 'h'];
    const contentFields = {
        text: ['title', 'content'],
        search: ['title'],
        banner: ['title', 'image', 'link'],
    };
    let editing = -1;
    let dirty = false;

    function status(message, error = false) {
        get('status').textContent = message;
        get('status').classList.toggle('is-error', error);
    }

    function position(tile, { x, y, w, h }) {
        tile.style.gridColumn = x + ' / span ' + w;
        tile.style.gridRow = y + ' / span ' + h;
        tile.querySelector('.home-editor-tile-size').textContent = w + ' × ' + h;
        tile.classList.toggle('is-slim', w === 1);
    }

    function placementError({ x, y, w, h }, index = -1, swap = -1) {
        if (x < 1 || y < 1 || w < 1 || h < 1 || x + w - 1 > 12 || y + h - 1 > rows) {
            return 'Blok melewati batas grid.';
        }
        if (blocks.some((block, i) => i !== index && i !== swap &&
            x < block.x + block.w && x + w > block.x &&
            y < block.y + block.h && y + h > block.y)) {
            return 'Ruang ini sudah ditempati blok lain.';
        }
        return '';
    }

    function attachDrag(handle, tile, index, resize) {
        let start;
        let candidate;
        let swap = -1;
        const clamp = (value, max) => Math.min(Math.max(value, 1), max);
        handle.addEventListener('pointerdown', event => {
            if (!event.isPrimary || event.button !== 0) return;
            const rect = grid.getBoundingClientRect();
            start = { x: event.clientX - rect.left, y: event.clientY - rect.top };
            candidate = blocks[index];
            swap = -1;
            handle.setPointerCapture(event.pointerId);
            tile.classList.add('is-moving');
        });
        handle.addEventListener('pointermove', event => {
            if (!start || !handle.hasPointerCapture(event.pointerId)) return;
            const rect = grid.getBoundingClientRect();
            const rowHeight = parseFloat(getComputedStyle(grid).getPropertyValue('--editor-row'));
            const dx = Math.round((event.clientX - rect.left - start.x) / (rect.width / 12));
            const dy = Math.round((event.clientY - rect.top - start.y) / rowHeight);
            const { x, y, w, h } = blocks[index];
            candidate = resize
                ? { ...blocks[index], w: clamp(w + dx, 13 - x), h: clamp(h + dy, rows + 1 - y) }
                : { ...blocks[index], x: clamp(x + dx, 13 - w), y: clamp(y + dy, rows + 1 - h) };
            swap = resize ? -1 : blocks.findIndex((block, i) =>
                i !== index && geometry.every(key => block[key] === candidate[key]));
            tile.classList.toggle('is-invalid', !!placementError(candidate, index, swap));
            position(tile, candidate);
        });
        handle.addEventListener('pointerup', event => {
            if (!start || !handle.hasPointerCapture(event.pointerId)) return;
            const error = placementError(candidate, index, swap);
            const changed = geometry.some(key => candidate[key] !== blocks[index][key]);
            start = null;
            tile.classList.remove('is-moving', 'is-invalid');
            if (error) {
                position(tile, blocks[index]);
                status(error, true);
            } else if (changed) {
                if (swap !== -1) {
                    const { x, y } = blocks[index];
                    blocks[swap] = { ...blocks[swap], x, y };
                }
                blocks[index] = candidate;
                markChanged();
            }
        });
        handle.addEventListener('lostpointercapture', () => {
            if (!start) return;
            start = null;
            tile.classList.remove('is-moving', 'is-invalid');
            position(tile, blocks[index]);
        });
    }

    function render(focusIndex = -1) {
        const selected = select.value;
        select.replaceChildren(...Object.entries(catalog)
            .filter(([type]) => custom.includes(type) || !blocks.some(block => block.type === type))
            .map(([type, [label]]) => new Option(label, type)));
        if (Array.from(select.options).some(option => option.value === selected)) select.value = selected;
        get('add').disabled = blocks.length >= limit;
        grid.style.gridTemplateRows = 'repeat(' + Math.min(rows, Math.max(3,
            ...blocks.map(block => block.y + block.h + 1))) + ', var(--editor-row))';
        grid.replaceChildren();
        blocks.forEach((block, i) => {
            const [label, icon] = catalog[block.type];
            const name = block.title || label;
            const tile = get('tile').content.firstElementChild.cloneNode(true);
            tile.title = name;
            position(tile, block);
            tile.querySelector('.home-editor-tile-icon').classList.add('fa-' + icon);
            tile.querySelector('.home-editor-tile-title').textContent = name;
            tile.querySelector('.home-editor-tile-kind').textContent = label === name
                ? (custom.includes(block.type) ? 'Blok tambahan' : 'Modul beranda')
                : label;
            const edit = tile.querySelector('.home-editor-edit');
            edit.setAttribute('aria-label', 'Edit ' + name);
            edit.addEventListener('click', () => openDetail(block, i));
            attachDrag(tile.querySelector('.home-editor-move'), tile, i, false);
            attachDrag(tile.querySelector('.home-editor-resize'), tile, i, true);
            grid.append(tile);
            if (i === focusIndex) edit.focus({ preventScroll: true });
        });
        get('value').value = JSON.stringify(blocks);
    }

    function markChanged(focusIndex) {
        dirty = true;
        save.querySelector('button').disabled = false;
        render(focusIndex);
        status('Ada perubahan yang belum disimpan.');
    }

    function openDetail(block, index) {
        editing = index;
        detail.reset();
        detail.dataset.type = block.type;
        get('dialog-title').textContent = (index < 0 ? 'Tambah ' : 'Edit ') + catalog[block.type][0];
        get('title-label').textContent = block.type === 'banner' ? 'Deskripsi gambar' : 'Judul';
        get('error').textContent = '';
        get('remove').hidden = index < 0;
        geometry.forEach(key => detail.elements[key].value = block[key]);
        detail.querySelectorAll('[data-field]').forEach(field => {
            const name = field.dataset.field;
            const input = detail.elements[name];
            const visible = (contentFields[block.type] || []).includes(name);
            field.hidden = input.disabled = !visible;
            input.required = visible && (name === 'image' || name === 'content' || block.type === 'banner' && name === 'title');
            input.value = block[name] ?? '';
        });
        dialog.showModal();
    }

    get('add').addEventListener('click', () => {
        const type = select.value;
        const w = catalog[type][2];
        for (let y = 1; y <= rows; y++) {
            for (let x = 1; x <= 13 - w; x++) {
                const block = { type, x, y, w, h: 1 };
                if (!placementError(block)) {
                    openDetail(block, -1);
                    return;
                }
            }
        }
        status('Tidak ada ruang untuk blok ini. Pindahkan atau perkecil blok lain terlebih dahulu.', true);
    });

    detail.addEventListener('submit', event => {
        event.preventDefault();
        const type = detail.dataset.type;
        const block = { type };
        geometry.forEach(key => block[key] = detail.elements[key].valueAsNumber);
        for (const key of contentFields[type] || []) {
            block[key] = detail.elements[key].value.trim();
        }
        let error = placementError(block, editing);
        if (type === 'text' && !block.content || type === 'banner' && (!block.title || !block.image)) {
            error = 'Isi kolom yang wajib diisi.';
        }
        if ([block.image, block.link].some(url => url && !/^https?:\/\//i.test(url))) {
            error = 'URL harus diawali http:// atau https://.';
        }
        if (error) {
            get('error').textContent = error;
            return;
        }
        const index = editing < 0 ? blocks.length : editing;
        blocks[index] = block;
        dialog.close();
        markChanged(index);
        if (editing < 0) grid.children[index].scrollIntoView({ block: 'nearest', inline: 'nearest' });
    });

    get('cancel').addEventListener('click', () => dialog.close());
    get('remove').addEventListener('click', () => {
        blocks.splice(editing, 1);
        dialog.close();
        markChanged(Math.min(editing, blocks.length - 1));
        if (!blocks.length) get('add').focus();
    });
    get('reset')?.addEventListener('submit', event => {
        if (window.confirm('Pulihkan tata letak bawaan? Tata letak khusus dan blok tambahan akan dihapus.')) {
            dirty = false;
        } else {
            event.preventDefault();
        }
    });
    save.addEventListener('submit', event => {
        if (!blocks.length && !window.confirm('Terbitkan beranda tanpa blok konten?')) {
            event.preventDefault();
        } else {
            dirty = false;
        }
    });
    window.addEventListener('beforeunload', event => {
        if (dirty) {
            event.preventDefault();
            event.returnValue = '';
        }
    });

    render();
    status('Tata letak sesuai dengan pengaturan yang sedang digunakan.');
})();
