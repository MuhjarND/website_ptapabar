document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-sinova-copy]');
    if (!button) return;

    const field = document.getElementById(button.getAttribute('aria-controls'));
    const status = button.closest('td').querySelector('[data-copy-status]');
    status.textContent = '';

    try {
        if (navigator.clipboard) {
            await navigator.clipboard.writeText(field.value);
            status.textContent = 'URL disalin.';
            return;
        }
    } catch {
        // Clipboard access can be denied even on HTTPS.
    }
    field.focus();
    field.select();
    status.textContent = 'URL dipilih. Gunakan Ctrl+C atau perintah Salin browser.';
});
