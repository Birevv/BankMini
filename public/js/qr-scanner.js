document.addEventListener('DOMContentLoaded', () => {
    const scanner = document.querySelector('[data-qr-scanner]');

    if (!scanner) return;

    const button = scanner.querySelector('[data-qr-toggle]');
    const video = scanner.querySelector('[data-qr-video]');
    const status = scanner.querySelector('[data-qr-status]');
    const input = document.querySelector('[data-account-number-input]');
    let stream = null;
    let scanning = false;

    const stop = () => {
        scanning = false;
        stream?.getTracks().forEach((track) => track.stop());
        stream = null;
        video.classList.add('hidden');
        status.textContent = 'Kamera tidak aktif.';
        button.textContent = 'Aktifkan Kamera';
    };

    const scan = async (detector) => {
        if (!scanning) return;

        try {
            const codes = await detector.detect(video);
            const value = codes[0]?.rawValue?.trim();

            if (value && input) {
                input.value = value;
                input.dispatchEvent(new Event('input', { bubbles: true }));
                status.textContent = `QR terbaca: ${value}`;
                stop();
                return;
            }
        } catch (_) {
            status.textContent = 'QR belum terbaca. Arahkan kamera ke kode.';
        }

        requestAnimationFrame(() => scan(detector));
    };

    button?.addEventListener('click', async (event) => {
        event.preventDefault();

        if (stream) {
            stop();
            return;
        }

        if (!('BarcodeDetector' in window) || !navigator.mediaDevices?.getUserMedia) {
            status.textContent = 'Browser ini belum mendukung pemindai QR. Gunakan input manual.';
            return;
        }

        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            video.srcObject = stream;
            await video.play();
            video.classList.remove('hidden');
            status.textContent = 'Arahkan kamera ke QR Code rekening.';
            button.textContent = 'Matikan Kamera';
            scanning = true;
            scan(new BarcodeDetector({ formats: ['qr_code'] }));
        } catch (_) {
            status.textContent = 'Kamera tidak dapat diakses. Periksa izin browser.';
            stop();
        }
    });

    window.addEventListener('beforeunload', stop);
});
