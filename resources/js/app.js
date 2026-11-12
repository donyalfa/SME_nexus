import 'bootstrap/dist/css/bootstrap.min.css';
import '../css/app.css';
import * as bootstrap from 'bootstrap';
import './bootstrap'; // file axios bawaan Laravel (bukan paket Bootstrap)

import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

window.bootstrap = bootstrap;

// Toggle mode gelap/terang
const root = document.documentElement;

// Count-up animasi angka pada kartu ring summary (halaman Intelligence / Autopsy)
function runCountUps(root = document) {
    root.querySelectorAll('[data-countup]').forEach((el) => {
        if (el.dataset.counted) return;
        el.dataset.counted = 'true';

        const target = parseFloat(el.getAttribute('data-countup'));
        const duration = 900;
        let startTime = null;

        const step = (timestamp) => {
            if (startTime === null) startTime = timestamp;
            const progress = Math.min((timestamp - startTime) / duration, 1);
            const eased = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
            el.textContent = (el.getAttribute('data-sign') || '') + (target * eased).toFixed(1);
            if (progress < 1) requestAnimationFrame(step);
        };

        requestAnimationFrame(step);
    });
}
document.addEventListener('DOMContentLoaded', () => runCountUps());

document.addEventListener('click', (e) => {
    if (!e.target.closest('[data-theme-toggle]')) return;
    const dark = root.getAttribute('data-bs-theme') !== 'dark';
    root.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
    try { localStorage.setItem('darkMode', dark); } catch (_) {}
});