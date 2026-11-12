(function () {
'use strict';
const bootstrap = window.bootstrap;

// Skrip ini HANYA dimuat oleh layouts/intelligence-admin.blade.php.

const root = document.documentElement;
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const isDesktop = () => window.matchMedia('(min-width: 992px)').matches;

const store = {
    get(key) {
        try { return localStorage.getItem(key); } catch (_) { return null; }
    },
    set(key, value) {
        try { localStorage.setItem(key, value); } catch (_) { /* abaikan */ }
    },
};

/* --------------------------------------------------------------------------
 * 1. Reveal elemen konten saat masuk viewport (dengan stagger)
 * ------------------------------------------------------------------------ */
function initReveal() {
    const content = document.querySelector('.ba-content');
    if (!content || reduceMotion) {
        root.classList.add('ba-ready');
        return;
    }

    const targets = [];
    const collect = (container) => {
        Array.from(container.children).forEach((child) => {
            if (child.matches('script, style, template')) return;
            if (child.classList.contains('row')) {
                Array.from(child.children).forEach((col) => targets.push(col));
            } else {
                targets.push(child);
            }
        });
    };

    collect(content);
    content.querySelectorAll(':scope > form').forEach(collect);

    // Elemen <form> sendiri tidak dianimasikan, hanya isinya.
    const items = targets.filter((el) => el.tagName !== 'FORM');

    items.forEach((el, index) => {
        el.setAttribute('data-reveal', '');
        el.style.setProperty('--ba-d', `${Math.min(index, 9) * 70}ms`);
    });

    root.classList.add('ba-ready');

    if (!('IntersectionObserver' in window)) {
        items.forEach((el) => el.classList.add('is-in'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-in');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });

    items.forEach((el) => observer.observe(el));
}

/* --------------------------------------------------------------------------
 * 2. Count-up angka statistik: <span data-countup="128">128</span>
 * ------------------------------------------------------------------------ */
function initCountUp() {
    const els = document.querySelectorAll('[data-countup]');
    if (!els.length) return;

    const format = (value, decimals) =>
        value.toLocaleString('id-ID', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        });

    const run = (el) => {
        const target = parseFloat(el.getAttribute('data-countup'));
        if (Number.isNaN(target)) return;

        const decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
        const duration = 1100;
        let start = null;

        const step = (ts) => {
            if (start === null) start = ts;
            const p = Math.min((ts - start) / duration, 1);
            const eased = p === 1 ? 1 : 1 - Math.pow(2, -10 * p);
            el.textContent = format(target * eased, decimals);
            if (p < 1) requestAnimationFrame(step);
        };

        requestAnimationFrame(step);
    };

    if (reduceMotion || !('IntersectionObserver' in window)) return; // nilai akhir sudah ada di HTML

    els.forEach((el) => { el.textContent = '0'; });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            run(entry.target);
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.4 });

    els.forEach((el) => observer.observe(el));
}

/* --------------------------------------------------------------------------
 * 3. Sidebar: collapse (desktop) & drawer (mobile)
 * ------------------------------------------------------------------------ */
function initSidebar() {
    const body = document.body;
    const links = document.querySelectorAll('.ba-nav-link');

    // Stagger animasi masuk sidebar
    document.querySelectorAll('.ba-sidebar .ba-brand, .ba-sidebar .ba-nav-label, .ba-sidebar .ba-nav-link')
        .forEach((el, i) => el.style.setProperty('--ba-n', i));

    const syncTitles = () => {
        const collapsed = body.classList.contains('ba-collapsed') && isDesktop();
        links.forEach((link) => {
            const label = link.querySelector('.ba-nav-text')?.textContent.trim();
            if (collapsed && label) link.setAttribute('title', label);
            else link.removeAttribute('title');
        });
    };

    if (store.get('ba-sidebar-collapsed') === '1' && isDesktop()) {
        body.classList.add('ba-collapsed');
    }
    syncTitles();

    const closeDrawer = () => body.classList.remove('ba-nav-open');

    document.querySelectorAll('[data-sidebar-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (isDesktop()) {
                const collapsed = body.classList.toggle('ba-collapsed');
                store.set('ba-sidebar-collapsed', collapsed ? '1' : '0');
                syncTitles();
            } else {
                body.classList.toggle('ba-nav-open');
            }
        });
    });

    document.querySelector('.ba-backdrop')?.addEventListener('click', closeDrawer);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeDrawer(); });
    window.addEventListener('resize', () => {
        if (isDesktop()) closeDrawer();
        syncTitles();
    });

    // Menu "Segera" tidak boleh berpindah halaman
    links.forEach((link) => {
        if (link.getAttribute('aria-disabled') === 'true') {
            link.addEventListener('click', (e) => e.preventDefault());
        }
    });

    // Pastikan menu aktif terlihat saat sidebar bisa di-scroll
    document.querySelector('.ba-nav-link.active')?.scrollIntoView({ block: 'nearest' });
}

/* --------------------------------------------------------------------------
 * 4. Progress bar saat berpindah halaman / submit form
 * ------------------------------------------------------------------------ */
function initProgress() {
    const bar = document.querySelector('.ba-progress');
    if (!bar) return;

    const start = () => {
        bar.classList.add('is-active');
        document.body.classList.add('ba-leaving');
    };
    const reset = () => {
        bar.classList.remove('is-active');
        document.body.classList.remove('ba-leaving');
    };

    window.baProgressStart = start;

    document.addEventListener('click', (e) => {
        const a = e.target.closest('a[href]');
        if (!a || e.defaultPrevented) return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;
        if (a.target && a.target !== '_self') return;
        if (a.hasAttribute('download') || a.getAttribute('aria-disabled') === 'true') return;

        const href = a.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
        if (a.origin !== window.location.origin) return;
        if (a.pathname === window.location.pathname && a.search === window.location.search) return;

        start();
    });

    document.addEventListener('submit', (e) => {
        if (e.defaultPrevented) return;
        start();
    });

    // Saat kembali lewat tombol Back (bfcache)
    window.addEventListener('pageshow', reset);
}

/* --------------------------------------------------------------------------
 * 5. Efek ripple pada tombol
 * ------------------------------------------------------------------------ */
function initRipple() {
    if (reduceMotion) return;

    document.addEventListener('pointerdown', (e) => {
        const btn = e.target.closest('.btn');
        if (!btn || btn.disabled || btn.classList.contains('disabled')) return;

        const rect = btn.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height) * 2;
        const ripple = document.createElement('span');
        ripple.className = 'ba-ripple';
        ripple.style.width = ripple.style.height = `${size}px`;
        ripple.style.left = `${e.clientX - rect.left - size / 2}px`;
        ripple.style.top = `${e.clientY - rect.top - size / 2}px`;
        btn.appendChild(ripple);
        ripple.addEventListener('animationend', () => ripple.remove());
    });
}

/* --------------------------------------------------------------------------
 * 6. Modal konfirmasi menggantikan confirm() bawaan browser
 *    Pakai: <form data-confirm="Pesan" data-confirm-variant="primary"
 *                 data-confirm-ok="Publikasikan"> ... </form>
 * ------------------------------------------------------------------------ */
function initConfirm() {
    if (!document.querySelector('form[data-confirm]')) return;

    const el = document.createElement('div');
    el.className = 'modal fade ba-confirm';
    el.tabIndex = -1;
    el.setAttribute('aria-hidden', 'true');
    el.innerHTML = `
        <div class="modal-dialog modal-dialog-centered modal-sm-down" style="max-width: 400px;">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="ba-confirm-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                    </div>
                    <h2 class="h5 fw-bold mb-2" data-title>Konfirmasi tindakan</h2>
                    <p class="ba-muted mb-4" data-message></p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn px-4" data-ok></button>
                    </div>
                </div>
            </div>
        </div>`;
    document.body.appendChild(el);

    const modal = new bootstrap.Modal(el);
    const titleEl = el.querySelector('[data-title]');
    const messageEl = el.querySelector('[data-message]');
    const okBtn = el.querySelector('[data-ok]');
    let pendingForm = null;

    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm')) return;
        if (form.dataset.confirmed === '1') return;

        e.preventDefault();
        e.stopImmediatePropagation();
        pendingForm = form;

        const variant = form.getAttribute('data-confirm-variant') || 'danger';
        el.setAttribute('data-variant', variant);
        titleEl.textContent = form.getAttribute('data-confirm-title')
            || (variant === 'danger' ? 'Hapus data ini?' : 'Lanjutkan tindakan?');
        messageEl.textContent = form.getAttribute('data-confirm');
        okBtn.textContent = form.getAttribute('data-confirm-ok')
            || (variant === 'danger' ? 'Ya, hapus' : 'Ya, lanjutkan');
        okBtn.className = `btn px-4 ${variant === 'danger' ? 'btn-danger' : 'ba-btn-primary'}`;

        modal.show();
    }, true);

    okBtn.addEventListener('click', () => {
        if (!pendingForm) return;
        const form = pendingForm;
        pendingForm = null;
        form.dataset.confirmed = '1';
        modal.hide();
        window.baProgressStart?.();
        HTMLFormElement.prototype.submit.call(form);
    });

    el.addEventListener('hidden.bs.modal', () => { pendingForm = null; });
}

/* --------------------------------------------------------------------------
 * 7. Alert sukses menutup otomatis (selaras dengan bar timer di CSS: 6 detik)
 * ------------------------------------------------------------------------ */
function initAlerts() {
    document.querySelectorAll('.alert-success').forEach((alert) => {
        setTimeout(() => {
            if (document.body.contains(alert)) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        }, 6000);
    });
}

/* --------------------------------------------------------------------------
 * 8. Tooltip Bootstrap (kompatibel dengan versi lama)
 * ------------------------------------------------------------------------ */
function initTooltips() {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
}

document.addEventListener('DOMContentLoaded', () => {
    initReveal();
    initCountUp();
    initSidebar();
    initProgress();
    initRipple();
    initConfirm();
    initAlerts();
    initTooltips();
});

})();
