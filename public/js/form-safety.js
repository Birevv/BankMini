document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-safe-submit]').forEach((form) => {
        form.addEventListener('submit', () => {
            if (!form.checkValidity()) {
                return;
            }

            form.setAttribute('aria-busy', 'true');

            const submitButton = form.querySelector('[type="submit"]');

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Memproses…';
            }
        }, { once: true });
    });
});
