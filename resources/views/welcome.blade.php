<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SME Nexus membandingkan data dua periode usaha Anda, mendeteksi gejala penurunan omzet, pelanggan, dan penjualan, lalu menyusun langkah pemulihan yang bisa langsung dikerjakan.">
    <title>{{ config('app.name', 'SME Nexus') }} - Cari tahu kenapa omzet turun</title>

    <script>
        document.documentElement.classList.add('js');
        try { document.documentElement.setAttribute('data-bs-theme', localStorage.getItem('darkMode') === 'true' ? 'dark' : 'light'); } catch (e) {}
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <script>
        // Prefix "tw-" + preflight mati: sama dengan layout admin, aman berdampingan dengan Bootstrap.
        tailwind.config = {
            prefix: 'tw-',
            corePlugins: { preflight: false },
            darkMode: ['selector', '[data-bs-theme="dark"]'],
            theme: {
                extend: {
                    colors: {
                        ink: 'var(--lp-text)',
                        muted: 'var(--lp-muted)',
                        surface: 'var(--lp-surface)',
                        field: 'var(--lp-surface-2)',
                    },
                },
            },
        };
    </script>

    <style>
        /* ============ Token warna (mengikuti app.css + halaman Autopsy) ============ */
        :root {
            --lp-ink: #171A44;
            --lp-brand: #4B4FE0;
            --lp-brand-2: #6A5CFF;
            --lp-peri: #7C8CFF;
            --lp-mint: #4FE3C1;
            --lp-sun: #FFB36B;

            --lp-bg: #EEF0FF;
            --lp-glow: #FFFFFF;
            --lp-surface: #FFFFFF;
            --lp-surface-2: #F6F7FF;
            --lp-text: #171A44;
            --lp-muted: #666B92;
            --lp-line: #DADDF4;
            --lp-link: #4B4FE0;
            --lp-mon: #171A44;
            --lp-mon-line: rgba(255, 255, 255, .08);

            /* tingkat keparahan: sehat > ringan > sedang > signifikan > anjlok */
            --sev-ok: #4FE3C1;
            --sev-low: #FFD166;
            --sev-mid: #FFB36B;
            --sev-high: #FF7A5A;
            --sev-crit: #F0506A;

            --lp-display: 'Bricolage Grotesque', 'Segoe UI', sans-serif;
            --lp-body: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
            --lp-ease: cubic-bezier(.2, .8, .2, 1);
        }
        [data-bs-theme="dark"] {
            --lp-bg: #0E1030;
            --lp-glow: rgba(75, 79, 224, .28);
            --lp-surface: #171A44;
            --lp-surface-2: #1F2358;
            --lp-text: #F1F2FF;
            --lp-muted: #A3A8D6;
            --lp-line: #2B3070;
            --lp-link: #8C93FF;
            --lp-mon: #1A1E52;
            --lp-mon-line: rgba(255, 255, 255, .14);
        }

        /* ============ Dasar ============ */
        html { scroll-behavior: smooth; scroll-padding-top: 84px; }
        .lp-body {
            margin: 0;
            font-family: var(--lp-body);
            color: var(--lp-text);
            background: radial-gradient(900px 520px at 6% -4%, var(--lp-glow), transparent 62%), var(--lp-bg);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }
        .lp-body:focus { outline: none; }
        .lp-body :focus-visible { outline: 3px solid var(--lp-peri); outline-offset: 3px; }
        .lp-skip:focus { position: fixed; top: 12px; left: 12px; z-index: 2000; padding: .6rem 1rem; border-radius: .7rem; background: var(--lp-surface); color: var(--lp-text); box-shadow: 0 10px 30px rgba(0, 0, 0, .25); }

        .lp-h1 { font: 800 clamp(2.5rem, 5vw, 4.1rem)/1.04 var(--lp-display); letter-spacing: -.035em; margin: 0 0 1.25rem; text-wrap: balance; }
        .lp-h2 { font: 800 clamp(1.85rem, 3.4vw, 2.75rem)/1.08 var(--lp-display); letter-spacing: -.03em; margin: 0 0 1rem; text-wrap: balance; }
        .lp-lead { margin: 0; font-size: 1.12rem; color: var(--lp-muted); }
        .lp-section { padding: clamp(3.5rem, 7vw, 6rem) 0; }

        /* ============ Tombol (Bootstrap .btn + variabel --bs-btn-*) ============ */
        .lp-btn {
            --bs-btn-padding-x: 1.5rem; --bs-btn-padding-y: .78rem;
            --bs-btn-font-weight: 700; --bs-btn-font-size: 1rem; --bs-btn-border-radius: .9rem;
            font-family: var(--lp-body);
            transition: transform .2s var(--lp-ease), box-shadow .25s, filter .2s, color .2s, border-color .2s, background-color .2s;
        }
        .lp-btn-sm { --bs-btn-padding-x: 1.05rem; --bs-btn-padding-y: .5rem; --bs-btn-font-size: .9rem; --bs-btn-border-radius: .75rem; }
        .lp-btn-primary {
            --bs-btn-color: #fff; --bs-btn-hover-color: #fff; --bs-btn-active-color: #fff;
            --bs-btn-bg: #4B4FE0; --bs-btn-hover-bg: #4B4FE0; --bs-btn-active-bg: #3F43D0;
            --bs-btn-border-color: transparent; --bs-btn-hover-border-color: transparent; --bs-btn-active-border-color: transparent;
            background-image: linear-gradient(100deg, #4B4FE0, #6A5CFF);
            box-shadow: 0 14px 28px -14px rgba(75, 79, 224, .9);
        }
        .lp-btn-primary:hover { filter: brightness(1.07); transform: translateY(-2px); box-shadow: 0 20px 32px -14px rgba(75, 79, 224, .95); }
        .lp-btn-primary:active { transform: translateY(0) scale(.985); }
        .lp-btn-ghost {
            --bs-btn-color: var(--lp-text); --bs-btn-hover-color: var(--lp-link); --bs-btn-active-color: var(--lp-link);
            --bs-btn-bg: transparent; --bs-btn-hover-bg: transparent; --bs-btn-active-bg: transparent;
            --bs-btn-border-color: var(--lp-line); --bs-btn-hover-border-color: var(--lp-brand); --bs-btn-active-border-color: var(--lp-brand);
        }
        .lp-btn-quiet {
            --bs-btn-color: var(--lp-muted); --bs-btn-hover-color: var(--lp-text); --bs-btn-active-color: var(--lp-text);
            --bs-btn-bg: transparent; --bs-btn-hover-bg: transparent; --bs-btn-active-bg: transparent;
            --bs-btn-border-color: transparent; --bs-btn-hover-border-color: transparent; --bs-btn-active-border-color: transparent;
        }
        .lp-btn-light {
            --bs-btn-color: #3F43D0; --bs-btn-hover-color: #3F43D0; --bs-btn-active-color: #3F43D0;
            --bs-btn-bg: #fff; --bs-btn-hover-bg: #fff; --bs-btn-active-bg: #EEF0FF;
            --bs-btn-border-color: transparent; --bs-btn-hover-border-color: transparent; --bs-btn-active-border-color: transparent;
            box-shadow: 0 16px 30px -16px rgba(0, 0, 0, .6);
        }
        .lp-btn-light:hover { transform: translateY(-2px); }

        /* ============ Hero ============ */
        .lp-hero { padding: clamp(2rem, 5vw, 4.5rem) 0 clamp(3rem, 6vw, 5.5rem); }
        .lp-checks { list-style: none; padding: 0; margin: 0; }
        .lp-checks li { display: inline-flex; align-items: center; gap: .5rem; font-weight: 600; }
        .lp-checks i { width: 9px; height: 9px; border-radius: 50%; background: var(--c); }

        /* ============ Monitor / konsol simulasi ============ */
        .lp-console {
            --sev: var(--sev-high); --spd: 1.4s;
            position: relative; isolation: isolate; overflow: hidden;
            padding: 1.5rem; border-radius: 1.6rem; color: #fff;
            background: var(--lp-mon); border: 1px solid var(--lp-mon-line);
            box-shadow: 0 40px 80px -40px rgba(23, 26, 68, .65);
        }
        .lp-console[data-level="ok"]   { --sev: var(--sev-ok);   --spd: 3.2s; }
        .lp-console[data-level="low"]  { --sev: var(--sev-low);  --spd: 2.4s; }
        .lp-console[data-level="mid"]  { --sev: var(--sev-mid);  --spd: 1.9s; }
        .lp-console[data-level="high"] { --sev: var(--sev-high); --spd: 1.4s; }
        .lp-console[data-level="crit"] { --sev: var(--sev-crit); --spd: .9s; }
        .lp-console::before, .lp-console::after { content: ""; position: absolute; z-index: -1; border-radius: 50%; pointer-events: none; filter: blur(60px); }
        .lp-console::before { right: -90px; top: -90px; width: 300px; height: 300px; background: rgba(106, 92, 255, .45); }
        .lp-console::after { left: -100px; bottom: -120px; width: 280px; height: 280px; background: var(--sev); opacity: .16; transition: background .5s; }

        .js .lp-console-inner { opacity: 0; transition: opacity .5s ease; animation: lpReveal 0s 2.5s forwards; }
        .js .lp-console.is-ready .lp-console-inner { opacity: 1; }
        @keyframes lpReveal { to { opacity: 1; } }

        .lp-console-head { display: flex; align-items: flex-start; justify-content: space-between; gap: .75rem; }
        .lp-console-title { margin: 0; font: 700 1.25rem/1.25 var(--lp-display); letter-spacing: -.01em; }
        .lp-console-sub { margin: .25rem 0 0; font-size: .85rem; color: rgba(255, 255, 255, .62); }
        .lp-pill {
            display: inline-flex; align-items: center; gap: .5rem; flex: none;
            padding: .35rem .85rem; border-radius: 99px; font-size: .78rem; font-weight: 700; white-space: nowrap;
            background: rgba(255, 255, 255, .1); box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .15);
            color: var(--sev); transition: color .5s;
        }
        .lp-dot { width: 8px; height: 8px; border-radius: 50%; background: currentColor; animation: lpPing var(--spd) ease-out infinite; }
        @keyframes lpPing { 0% { box-shadow: 0 0 0 0 currentColor; } 70%, 100% { box-shadow: 0 0 0 9px transparent; } }

        .lp-console-body { display: grid; gap: 1.25rem; margin-top: 1.25rem; }
        @media (min-width: 768px) and (max-width: 991.98px), (min-width: 1200px) {
            .lp-console-body { grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr); gap: 1.5rem; }
        }

        /* pilihan indikator: Bootstrap btn-group + btn-check */
        .lp-seg { display: flex; width: 100%; padding: 4px; border-radius: .95rem; background: rgba(255, 255, 255, .08); }
        .lp-seg .btn {
            --bs-btn-color: rgba(255, 255, 255, .72); --bs-btn-hover-color: #fff;
            --bs-btn-bg: transparent; --bs-btn-hover-bg: rgba(255, 255, 255, .08);
            --bs-btn-border-color: transparent; --bs-btn-hover-border-color: transparent;
            --bs-btn-active-color: #171A44; --bs-btn-active-bg: #fff; --bs-btn-active-border-color: #fff;
            flex: 1; padding: .5rem .25rem; font-size: .88rem; font-weight: 700; border-radius: .7rem !important;
        }
        .lp-seg .btn-check:focus-visible + .btn { outline: 3px solid var(--lp-peri); outline-offset: 2px; }

        .lp-ecg { margin-top: 1.1rem; color: var(--sev); transition: color .5s; }
        .lp-ecg svg { display: block; width: 100%; height: auto; }
        .lp-ecg path { fill: none; stroke: currentColor; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }
        .lp-ecg .base { stroke: rgba(255, 255, 255, .14); }
        .lp-ecg .beat { stroke-dasharray: 22 78; animation: lpBeat var(--spd) linear infinite; filter: drop-shadow(0 0 6px currentColor); }
        @keyframes lpBeat { from { stroke-dashoffset: 100; } to { stroke-dashoffset: 0; } }

        .lp-change { display: flex; align-items: baseline; flex-wrap: wrap; gap: .25rem .75rem; margin-top: .75rem; }
        .lp-change strong { font: 800 clamp(2.4rem, 4vw, 3rem)/1 var(--lp-display); letter-spacing: -.03em; font-variant-numeric: tabular-nums; color: var(--sev); transition: color .5s; }
        .lp-change span { font-size: .85rem; color: rgba(255, 255, 255, .62); }

        .lp-figures { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; margin: 1.1rem 0 0; overflow: hidden; border-radius: 1rem; background: rgba(255, 255, 255, .1); box-shadow: 0 0 0 1px rgba(255, 255, 255, .1); }
        .lp-figures div { padding: .8rem .9rem; background: var(--lp-mon); }
        .lp-figures dt { margin: 0; font-size: .74rem; font-weight: 500; color: rgba(255, 255, 255, .58); }
        .lp-figures dd { margin: .1rem 0 0; font: 700 1.02rem var(--lp-body); font-variant-numeric: tabular-nums; white-space: nowrap; }

        .lp-slider { margin-top: 1.1rem; }
        .lp-slider-top { display: flex; justify-content: space-between; gap: .5rem; font-size: .8rem; color: rgba(255, 255, 255, .72); margin-bottom: .35rem; }
        .lp-slider-top label { font-weight: 600; color: #fff; }
        .lp-range {
            --lp-track: linear-gradient(90deg,
                var(--sev-crit) 0 calc(11px + (100% - 22px) * .30),
                var(--sev-high) 0 calc(11px + (100% - 22px) * .60),
                var(--sev-mid)  0 calc(11px + (100% - 22px) * .70),
                var(--sev-low)  0 calc(11px + (100% - 22px) * .78),
                var(--sev-ok)   0);
            -webkit-appearance: none; appearance: none; display: block; width: 100%; height: 28px; margin: 0; background: transparent; cursor: pointer;
        }
        .lp-range::-webkit-slider-runnable-track { height: 10px; border-radius: 99px; background: var(--lp-track); }
        .lp-range::-moz-range-track { height: 10px; border-radius: 99px; background: var(--lp-track); }
        .lp-range::-webkit-slider-thumb { -webkit-appearance: none; width: 22px; height: 22px; margin-top: -6px; border-radius: 50%; background: #fff; border: 4px solid var(--sev); box-shadow: 0 4px 12px rgba(0, 0, 0, .4); transition: border-color .4s, transform .2s; }
        .lp-range::-moz-range-thumb { box-sizing: border-box; width: 22px; height: 22px; border-radius: 50%; background: #fff; border: 4px solid var(--sev); box-shadow: 0 4px 12px rgba(0, 0, 0, .4); }
        .lp-range:active::-webkit-slider-thumb { transform: scale(1.12); }
        .lp-scale { position: relative; height: 1.1rem; }
        .lp-scale span { position: absolute; top: 0; left: calc(11px + (100% - 22px) * var(--p)); transform: translateX(-50%); font-size: .68rem; color: rgba(255, 255, 255, .55); font-variant-numeric: tabular-nums; }

        .lp-dx { display: flex; flex-direction: column; padding: 1.1rem 1.2rem; border-radius: 1.1rem; background: rgba(255, 255, 255, .07); box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .1); }
        .lp-dx-label { margin: 0; font-size: .8rem; font-weight: 600; color: rgba(255, 255, 255, .58); }
        .lp-dx-name { margin: .2rem 0 0; font: 700 1.35rem/1.2 var(--lp-display); letter-spacing: -.015em; }
        .lp-dx-sev { margin: .45rem 0 0; font-size: .86rem; color: rgba(255, 255, 255, .72); }
        .lp-dx-sev b { color: var(--sev); transition: color .5s; }
        .lp-dx-head { margin: 1.1rem 0 .5rem; font-size: .8rem; font-weight: 600; color: rgba(255, 255, 255, .58); }
        .lp-dx-steps { list-style: none; padding: 0; margin: 0; display: grid; gap: .65rem; }
        .lp-dx-steps li { display: grid; grid-template-columns: 18px 1fr; gap: .6rem; font-size: .88rem; line-height: 1.45; color: rgba(255, 255, 255, .9); }
        .lp-dx-steps svg { width: 18px; height: 18px; margin-top: 1px; color: var(--lp-mint); }
        .lp-dx-more { margin: auto 0 0; padding-top: .9rem; font-size: .8rem; color: rgba(255, 255, 255, .58); }
        .lp-dx-note { margin: 1rem 0 0; font-size: .88rem; line-height: 1.5; color: rgba(255, 255, 255, .82); }

        .lp-console-foot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .9rem 1.25rem; margin-top: 1.4rem; padding-top: 1.2rem; border-top: 1px solid rgba(255, 255, 255, .12); }
        .lp-console-foot p { margin: 0; max-width: 20rem; font-size: .8rem; color: rgba(255, 255, 255, .62); }

        /* ============ Cara kerja + rencana pemulihan ============ */
        .lp-flow { list-style: none; padding: 0; margin: 2rem 0 0; display: grid; gap: 0; counter-reset: step; }
        .lp-flow li { position: relative; display: grid; grid-template-columns: 40px 1fr; gap: 1rem; padding-bottom: 1.6rem; }
        .lp-flow li:last-child { padding-bottom: 0; }
        .lp-flow li:not(:last-child)::before { content: ""; position: absolute; left: 19px; top: 42px; bottom: 2px; width: 2px; background: var(--lp-line); }
        .lp-flow-n { display: grid; place-items: center; width: 40px; height: 40px; border-radius: 50%; font: 800 1rem var(--lp-display); color: #fff; background: linear-gradient(135deg, var(--lp-brand), var(--lp-brand-2)); box-shadow: 0 10px 20px -10px rgba(75, 79, 224, .9); }
        .lp-flow h3 { margin: .35rem 0 .2rem; font: 700 1.1rem/1.25 var(--lp-display); letter-spacing: -.01em; }
        .lp-flow p { margin: 0; font-size: .95rem; color: var(--lp-muted); max-width: 30rem; }

        .lp-plan { padding: 1.6rem; border-radius: 1.5rem; background: var(--lp-surface); border: 1px solid var(--lp-line); box-shadow: 0 40px 70px -48px rgba(23, 26, 68, .5); }
        .lp-plan-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; }
        .lp-plan-title { margin: 0; font: 700 1.3rem/1.2 var(--lp-display); letter-spacing: -.015em; }
        .lp-plan-sub { margin: .25rem 0 0; font-size: .88rem; color: var(--lp-muted); }
        .lp-sev { flex: none; padding: .3rem .8rem; border-radius: 99px; font-size: .76rem; font-weight: 700; color: #A8321F; background: rgba(255, 122, 90, .18); }
        [data-bs-theme="dark"] .lp-sev { color: #FFA48F; }
        .lp-prog { display: flex; align-items: center; gap: .9rem; margin: 1.3rem 0 .4rem; }
        .lp-prog .progress { flex: 1; height: .6rem; border-radius: 99px; background: var(--lp-line); --bs-progress-bar-bg: var(--lp-mint); --bs-progress-bar-transition: width .6s var(--lp-ease); }
        .lp-prog .progress-bar { background-image: linear-gradient(90deg, var(--lp-brand), var(--lp-mint)); }
        .lp-prog output { flex: none; font-size: .82rem; font-weight: 700; color: var(--lp-muted); font-variant-numeric: tabular-nums; }

        .lp-tasks { list-style: none; padding: 0; margin: 0; }
        .lp-task { display: grid; grid-template-columns: 28px minmax(0, 1fr) auto; gap: .5rem 1rem; align-items: start; padding: 1rem 0; border-top: 1px solid var(--lp-line); }
        .lp-task:first-child { margin-top: .75rem; }
        .lp-task-ic { display: grid; place-items: center; width: 28px; height: 28px; margin-top: 1px; border-radius: 50%; border: 2.5px solid var(--lp-line); color: #fff; transition: background .3s, border-color .3s; }
        .lp-task-ic svg { width: 14px; height: 14px; opacity: 0; transition: opacity .2s; }
        .lp-task-ic::after { content: ""; display: none; width: 8px; height: 8px; border-radius: 50%; background: var(--lp-peri); }
        .lp-task[data-state="progress"] .lp-task-ic { border-color: var(--lp-peri); }
        .lp-task[data-state="progress"] .lp-task-ic::after { display: block; }
        .lp-task[data-state="done"] .lp-task-ic { background: var(--lp-mint); border-color: var(--lp-mint); }
        .lp-task[data-state="done"] .lp-task-ic svg { opacity: 1; color: #0B5C4A; }
        .lp-task-title { margin: 0 0 .35rem; font-size: .96rem; font-weight: 600; line-height: 1.45; }
        .lp-task[data-state="done"] .lp-task-title { color: var(--lp-muted); text-decoration: line-through; text-decoration-color: color-mix(in srgb, var(--lp-muted) 55%, transparent); }
        .lp-chip { font-size: .72rem; font-weight: 700; }
        .lp-chip-todo { color: var(--lp-muted); background: var(--lp-surface-2); box-shadow: inset 0 0 0 1px var(--lp-line); }
        .lp-chip-progress { color: #3F4BD0; background: rgba(124, 140, 255, .2); }
        [data-bs-theme="dark"] .lp-chip-progress { color: #B6BEFF; }
        .lp-chip-done { color: #0B7A62; background: rgba(79, 227, 193, .24); }
        [data-bs-theme="dark"] .lp-chip-done { color: #6FF0D2; }
        .lp-task-btn {
            --bs-btn-padding-y: .42rem; --bs-btn-padding-x: .9rem; --bs-btn-font-size: .82rem; --bs-btn-font-weight: 700; --bs-btn-border-radius: .65rem;
            --bs-btn-color: var(--lp-link); --bs-btn-bg: transparent; --bs-btn-border-color: var(--lp-line);
            --bs-btn-hover-color: var(--lp-link); --bs-btn-hover-bg: transparent; --bs-btn-hover-border-color: var(--lp-brand);
            --bs-btn-active-color: var(--lp-link); --bs-btn-active-bg: transparent; --bs-btn-active-border-color: var(--lp-brand);
            white-space: nowrap;
        }
        .lp-task[data-state="progress"] .lp-task-btn { --bs-btn-color: #fff; --bs-btn-hover-color: #fff; --bs-btn-active-color: #fff; --bs-btn-bg: #0E9F82; --bs-btn-hover-bg: #0B8A71; --bs-btn-active-bg: #0B8A71; --bs-btn-border-color: transparent; --bs-btn-hover-border-color: transparent; --bs-btn-active-border-color: transparent; }
        .lp-task[data-state="done"] .lp-task-btn { --bs-btn-color: var(--lp-muted); --bs-btn-hover-color: var(--lp-text); --bs-btn-active-color: var(--lp-text); }
        @media (max-width: 575.98px) {
            .lp-task { grid-template-columns: 28px minmax(0, 1fr); }
            .lp-task-btn { grid-column: 2; justify-self: start; }
        }

        /* ============ Nexus Radar ============ */
        .lp-band { padding: clamp(3.5rem, 7vw, 6rem) 0; background: var(--lp-surface); border-block: 1px solid var(--lp-line); }
        .lp-deck { position: relative; max-width: 390px; margin-inline: auto; padding-bottom: 22px; }
        .lp-deck::before, .lp-deck::after { content: ""; position: absolute; left: 0; right: 0; top: 14px; bottom: 0; border-radius: 1.6rem; background: var(--lp-surface-2); border: 1px solid var(--lp-line); z-index: 0; }
        .lp-deck::before { transform: rotate(-3.2deg) translateX(-8px); }
        .lp-deck::after { transform: rotate(2.4deg) translateX(7px); background: color-mix(in srgb, var(--lp-brand) 8%, var(--lp-surface-2)); }
        .lp-profile { position: relative; z-index: 1; padding: 1.4rem; border-radius: 1.6rem; background: var(--lp-surface); border: 1px solid var(--lp-line); box-shadow: 0 34px 60px -38px rgba(23, 26, 68, .55); transition: transform .3s var(--lp-ease), opacity .3s; }
        .lp-profile.is-out-pass { transform: translateX(-70px) rotate(-7deg); opacity: 0; }
        .lp-profile.is-out-like { transform: translateX(70px) rotate(7deg); opacity: 0; }
        .lp-profile.is-in { animation: lpCardIn .35s var(--lp-ease) both; }
        @keyframes lpCardIn { from { opacity: 0; transform: translateY(14px) scale(.97); } to { opacity: 1; transform: none; } }
        .lp-prof-top { display: flex; align-items: center; gap: .9rem; }
        .lp-ava { display: grid; place-items: center; flex: none; width: 54px; height: 54px; border-radius: 16px; font: 800 1.05rem var(--lp-display); color: #fff; background: var(--c, var(--lp-brand)); }
        .lp-prof-name { margin: 0; font: 800 1.35rem/1.15 var(--lp-display); letter-spacing: -.02em; }
        .lp-tags { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .45rem; }
        .lp-tag { padding: .15rem .65rem; border-radius: 99px; font-size: .74rem; font-weight: 700; color: var(--lp-link); background: color-mix(in srgb, var(--lp-brand) 12%, transparent); }
        .lp-tag.alt { color: #0B7A62; background: rgba(79, 227, 193, .22); }
        [data-bs-theme="dark"] .lp-tag.alt { color: #6FF0D2; }
        .lp-prof-list { display: grid; gap: .9rem; margin: 1.2rem 0 0; }
        .lp-prof-list dt { margin: 0; font-size: .78rem; font-weight: 700; color: var(--lp-muted); }
        .lp-prof-list dd { margin: .1rem 0 0; font-size: .94rem; line-height: 1.5; }
        .lp-prof-act { display: flex; gap: .75rem; margin-top: 1.3rem; padding-top: 1.2rem; border-top: 1px solid var(--lp-line); }
        .lp-prof-act .btn { flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: .45rem; }
        .lp-prof-act svg { width: 17px; height: 17px; }
        .lp-radar-note { min-height: 3rem; margin: 1.5rem auto 0; max-width: 390px; font-size: .9rem; color: var(--lp-muted); text-align: center; }
        .lp-radar-note b { color: var(--lp-text); }
        .lp-empty { position: relative; z-index: 1; padding: 2.2rem 1.5rem; text-align: center; border-radius: 1.6rem; background: var(--lp-surface); border: 1px solid var(--lp-line); }
        .lp-empty h3 { margin: 0 0 .4rem; font: 800 1.3rem var(--lp-display); }
        .lp-empty p { margin: 0 0 1.2rem; font-size: .92rem; color: var(--lp-muted); }

        /* ============ Jenis usaha ============ */
        .lp-cats { display: grid; margin: 0; padding: 0; list-style: none; gap: 0 2.5rem; }
        .lp-cats li { padding: 1.1rem 0 1.4rem; border-top: 1px solid var(--lp-line); }
        .lp-cats h3 { margin: 0 0 .2rem; font: 700 1.15rem var(--lp-display); letter-spacing: -.01em; }
        .lp-cats p { margin: 0; font-size: .94rem; color: var(--lp-muted); }

        /* ============ FAQ (Bootstrap accordion) ============ */
        .lp-faq {
            --bs-accordion-bg: transparent; --bs-accordion-color: var(--lp-text);
            --bs-accordion-border-color: var(--lp-line); --bs-accordion-border-radius: 0; --bs-accordion-inner-border-radius: 0;
            --bs-accordion-btn-color: var(--lp-text); --bs-accordion-btn-bg: transparent;
            --bs-accordion-active-color: var(--lp-text); --bs-accordion-active-bg: transparent;
            --bs-accordion-btn-padding-y: 1.2rem; --bs-accordion-btn-padding-x: 0; --bs-accordion-body-padding-x: 0;
            --bs-accordion-btn-focus-box-shadow: none;
        }
        .lp-faq .accordion-item { border-width: 0 0 1px; }
        .lp-faq .accordion-item:first-child { border-top-width: 1px; }
        .lp-faq .accordion-button { font: 700 1.05rem/1.4 var(--lp-body); box-shadow: none; }
        .lp-faq .accordion-button:not(.collapsed) { box-shadow: none; color: var(--lp-link); }
        .lp-faq .accordion-body { padding-top: 0; padding-bottom: 1.3rem; color: var(--lp-muted); max-width: 40rem; }
        [data-bs-theme="dark"] .lp-faq .accordion-button::after { filter: invert(1) brightness(1.6); }

        /* ============ CTA penutup ============ */
        .lp-cta { position: relative; isolation: isolate; overflow: hidden; padding: clamp(3rem, 7vw, 5.5rem) clamp(1.5rem, 5vw, 4rem); border-radius: 2rem; color: #fff; background: linear-gradient(160deg, #2A2EB5 0%, #1B1E63 55%, #171A44 100%); }
        .lp-cta::before { content: ""; position: absolute; z-index: -1; right: -80px; top: -110px; width: 340px; height: 340px; border-radius: 50%; background: var(--lp-peri); opacity: .5; filter: blur(60px); }
        .lp-cta h2 { margin: 0 0 .9rem; max-width: 20ch; font: 800 clamp(2rem, 4.4vw, 3.3rem)/1.05 var(--lp-display); letter-spacing: -.03em; text-wrap: balance; }
        .lp-cta p { margin: 0 0 1.8rem; max-width: 32rem; font-size: 1.05rem; color: rgba(255, 255, 255, .8); }
        .lp-cta .lp-ecg { position: absolute; left: 0; right: 0; bottom: 0; margin: 0; opacity: .55; --sev: var(--lp-mint); --spd: 3.2s; z-index: -1; }
        .lp-cta .lp-ecg svg { width: 100%; height: 90px; }

        /* ============ Footer ============ */
        .lp-foot { padding: 2.2rem 0 2.6rem; font-size: .9rem; color: var(--lp-muted); }
        .lp-foot a:hover { color: var(--lp-link); }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .lp-body *, .lp-body *::before, .lp-body *::after { animation-duration: .001ms !important; animation-iteration-count: 1 !important; transition-duration: .001ms !important; }
            .lp-ecg .beat { stroke-dasharray: none; filter: none; }
        }
         .hx {
        --mx: 50%; --my: 38%;
        position: relative; isolation: isolate; overflow: hidden;
        display: flex; flex-direction: column;
        min-height: 100vh; min-height: calc(100svh - 64px);
        }
        .hx.is-paused *, .hx.is-paused *::before, .hx.is-paused *::after { animation-play-state: paused !important; }
    
        /* --- latar --- */
        .hx-bg { position: absolute; inset: 0; z-index: -1; pointer-events: none; }
        .hx-grid {
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, color-mix(in srgb, var(--lp-text) 22%, transparent) 1px, transparent 1.5px);
            background-size: 28px 28px;
            -webkit-mask-image: radial-gradient(ellipse 75% 65% at 50% 40%, #000 25%, transparent 78%);
                    mask-image: radial-gradient(ellipse 75% 65% at 50% 40%, #000 25%, transparent 78%);
            opacity: .55;
        }
        .hx-spot { position: absolute; inset: 0; background: radial-gradient(520px circle at var(--mx) var(--my), color-mix(in srgb, var(--lp-brand) 20%, transparent), transparent 62%); }
        .hx-blob { position: absolute; border-radius: 50%; filter: blur(70px); animation: hxDrift 18s ease-in-out infinite alternate; }
        .hx-blob.b1 { left: -8%; top: -12%; width: 420px; height: 420px; background: var(--lp-brand-2); opacity: .28; }
        .hx-blob.b2 { right: -7%; top: 18%; width: 380px; height: 380px; background: var(--lp-mint); opacity: .24; animation-duration: 22s; animation-delay: -6s; }
        .hx-blob.b3 { left: 32%; bottom: -20%; width: 360px; height: 360px; background: var(--lp-sun); opacity: .2; animation-duration: 26s; animation-delay: -12s; }
        @keyframes hxDrift { from { transform: translate(0, 0) scale(1); } to { transform: translate(46px, -34px) scale(1.14); } }
    
        .hx-main { flex: 1; display: flex; align-items: center; padding: clamp(2rem, 5vw, 4rem) 0 clamp(1.5rem, 3vw, 2.5rem); }
    
        /* --- salin --- */
        .hx-a { animation: hxUp .85s var(--lp-ease) both; animation-delay: var(--d, 0s); }
        @keyframes hxUp { from { opacity: 0; translate: 0 20px; filter: blur(6px); } to { opacity: 1; translate: 0 0; filter: blur(0); } }
    
        .hx-eyebrow {
            display: inline-flex; align-items: center; gap: .6rem; margin-bottom: 1.4rem;
            padding: .4rem 1rem .4rem .8rem; border-radius: 99px;
            font-size: .82rem; font-weight: 700; color: var(--lp-link);
            background: color-mix(in srgb, var(--lp-surface) 70%, transparent);
            border: 1px solid var(--lp-line); backdrop-filter: blur(10px);
        }
        .hx-dot { position: relative; width: 8px; height: 8px; border-radius: 50%; background: var(--lp-mint); }
        .hx-dot::after { content: ""; position: absolute; inset: 0; border-radius: 50%; background: var(--lp-mint); animation: hxPing 2s ease-out infinite; }
        @keyframes hxPing { 0% { transform: scale(1); opacity: .8; } 100% { transform: scale(3.4); opacity: 0; } }
    
        .hx-title { font: 800 clamp(2.6rem, 5.6vw, 4.8rem)/1.04 var(--lp-display); letter-spacing: -.04em; margin: 0 0 1.4rem; text-wrap: balance; }
        .hx-l2 { display: block; }
        .hx-mask { display: inline-block; vertical-align: top; overflow: hidden; padding: .04em .02em .16em; margin: -.04em -.02em -.16em; }
        .hx-w { display: inline-block; opacity: 0; transform: translateY(108%) rotate(5deg); transform-origin: 0 100%; filter: blur(8px); animation: hxWord .95s var(--lp-ease) forwards; animation-delay: calc(.3s + var(--i) * .09s); }
        @keyframes hxWord { to { opacity: 1; transform: none; filter: blur(0); } }
        .hx-grad {
            background: linear-gradient(100deg, var(--lp-brand) 0%, var(--lp-brand-2) 25%, #16B89A 50%, var(--lp-brand-2) 75%, var(--lp-brand) 100%);
            background-size: 300% 100%; -webkit-background-clip: text; background-clip: text; color: transparent; -webkit-text-fill-color: transparent;
            animation: hxWord .95s var(--lp-ease) forwards, hxShine 7s linear infinite;
            animation-delay: calc(.3s + var(--i) * .09s), 1.4s;
        }
        @keyframes hxShine { to { background-position: -300% 0; } }
    
        .hx-sub { max-width: 33rem; margin: 0; font-size: 1.12rem; line-height: 1.65; color: var(--lp-muted); }
    
        .hx-stats { display: flex; flex-wrap: wrap; gap: 1.2rem 2.2rem; margin: 2.4rem 0 0; padding: 1.3rem 0 0; list-style: none; border-top: 1px solid var(--lp-line); }
        .hx-stats li { min-width: 0; }
        .hx-stats b { display: block; font: 800 1.9rem/1 var(--lp-display); letter-spacing: -.03em; font-variant-numeric: tabular-nums; color: var(--lp-text); }
        .hx-stats span { display: block; margin-top: .3rem; font-size: .82rem; color: var(--lp-muted); }
    
        /* --- panggung orbit --- */
        .hx-stage { position: relative; width: min(100%, 540px); aspect-ratio: 1; margin-inline: auto; perspective: 1100px; }
        .hx-tilt { position: absolute; inset: 0; transform-style: preserve-3d; transform: rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg)); transition: transform .25s ease-out; }
    
        .hx-ring { position: absolute; left: 50%; top: 50%; width: var(--s); aspect-ratio: 1; border-radius: 50%; translate: -50% -50%; border: 1px dashed var(--lp-line); animation: hxRingIn 1.1s var(--lp-ease) both; animation-delay: var(--d); }
        .hx-ring.solid { border-style: solid; box-shadow: inset 0 0 60px color-mix(in srgb, var(--lp-brand) 8%, transparent), 0 0 60px color-mix(in srgb, var(--lp-brand) 7%, transparent); }
        .hx-ring.turn { animation: hxRingIn 1.1s var(--lp-ease) both, hxTurn 90s linear infinite; animation-delay: var(--d), 0s; }
        @keyframes hxRingIn { from { opacity: 0; scale: .55; } to { opacity: 1; scale: 1; } }
        @keyframes hxTurn { to { rotate: 360deg; } }
    
        .hx-orbit { position: absolute; inset: 0; animation: hxSpin 42s linear infinite; }
        .hx-spin-r { position: relative; width: 0; height: 0; animation: hxSpin 42s linear infinite reverse; }
        @keyframes hxSpin { to { transform: rotate(360deg); } }
    
        .hx-arm { position: absolute; inset: 0; transform: rotate(var(--a)); }
        .hx-link { position: absolute; left: 50%; bottom: 50%; width: 2px; height: 38%; margin-left: -1px; overflow: hidden; background: linear-gradient(to top, transparent, var(--lp-line) 22%); }
        .hx-link::after { content: ""; position: absolute; left: 0; top: 0; width: 100%; height: 30%; background: linear-gradient(to top, transparent, var(--c), transparent); animation: hxTravel 2.8s ease-in-out infinite; animation-delay: var(--d); }
        @keyframes hxTravel { 0% { translate: 0 330%; opacity: 0; } 15%, 80% { opacity: 1; } 100% { translate: 0 -10%; opacity: 0; } }
    
        .hx-nodewrap { position: absolute; left: 50%; bottom: 88%; width: 0; height: 0; transform: rotate(calc(-1 * var(--a))); }
        .hx-node {
            position: absolute; left: 0; top: 0; translate: -50% -50%;
            display: grid; place-items: center; width: 58px; height: 58px; border-radius: 19px;
            background: var(--lp-surface); border: 1px solid color-mix(in srgb, var(--c) 38%, var(--lp-line));
            box-shadow: 0 20px 34px -18px color-mix(in srgb, var(--c) 80%, transparent), 0 0 0 6px color-mix(in srgb, var(--c) 10%, transparent);
            animation: hxPop .8s var(--lp-ease) both; animation-delay: var(--d);
        }
        .hx-node svg { width: 26px; height: 26px; color: var(--c); }
        .hx-node-lbl { position: absolute; top: 100%; left: 50%; translate: -50% 0; margin-top: .55rem; padding: .18rem .7rem; border-radius: 99px; white-space: nowrap; font-size: .74rem; font-weight: 700; color: var(--lp-text); background: color-mix(in srgb, var(--lp-surface) 88%, transparent); border: 1px solid var(--lp-line); backdrop-filter: blur(8px); }
        @keyframes hxPop { from { opacity: 0; scale: 0; } to { opacity: 1; scale: 1; } }
    
        .hx-orbit-b { position: absolute; inset: 0; animation: hxSpin 70s linear infinite reverse; }
        .hx-orbit-b i { position: absolute; inset: 0; transform: rotate(var(--a)); }
        .hx-orbit-b i::before { content: ""; position: absolute; left: 50%; top: 2%; width: 8px; height: 8px; margin-left: -4px; border-radius: 50%; background: var(--c); box-shadow: 0 0 16px var(--c); }
    
        .hx-orb {
            position: absolute; left: 50%; top: 50%; translate: -50% -50%; width: 25%; aspect-ratio: 1; border-radius: 50%; display: grid; place-items: center;
            background: radial-gradient(circle at 32% 28%, #8C93FF, var(--lp-brand) 55%, #2A2EB5);
            box-shadow: 0 26px 60px -18px rgba(75, 79, 224, .85), inset 0 2px 0 rgba(255, 255, 255, .35);
            animation: hxPop 1s var(--lp-ease) .15s both, hxBreath 4.5s ease-in-out 1.2s infinite;
        }
        .hx-orb::before, .hx-orb::after { content: ""; position: absolute; inset: 0; border-radius: 50%; border: 2px solid var(--lp-peri); animation: hxRipple 3.2s ease-out infinite; }
        .hx-orb::after { animation-delay: 1.6s; }
        @keyframes hxRipple { 0% { transform: scale(1); opacity: .7; } 100% { transform: scale(2.3); opacity: 0; } }
        @keyframes hxBreath { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.06); } }
        .hx-orb svg { width: 52%; height: 52%; overflow: visible; }
        .hx-orb .x { fill: none; stroke: #fff; stroke-width: 5; stroke-linecap: round; stroke-dasharray: 40; stroke-dashoffset: 40; animation: hxDraw .9s var(--lp-ease) forwards; }
        .hx-orb .x.two { animation-delay: .25s; stroke: var(--lp-mint); }
        .hx-orb .core { fill: #fff; transform-origin: 24px 24px; animation: hxPop .6s var(--lp-ease) .9s both; }
        @keyframes hxDraw { to { stroke-dashoffset: 0; } }
    
        /* --- kartu melayang --- */
        .hx-card {
            position: absolute; z-index: 3; display: flex; align-items: center; gap: .75rem; padding: .7rem 1rem .7rem .85rem; border-radius: 1rem;
            background: color-mix(in srgb, var(--lp-surface) 84%, transparent); backdrop-filter: blur(14px);
            border: 1px solid var(--lp-line); box-shadow: 0 26px 46px -26px rgba(23, 26, 68, .55);
            animation: hxCardIn .9s var(--lp-ease) both, hxBob 6s ease-in-out infinite; animation-delay: var(--d), calc(var(--d) + .9s);
        }
        @keyframes hxCardIn { from { opacity: 0; scale: .7; } to { opacity: 1; scale: 1; } }
        @keyframes hxBob { 0%, 100% { translate: 0 0; } 50% { translate: 0 -12px; } }
        .hx-card-a { left: -3%; bottom: 7%; --sev: var(--sev-high); }
        .hx-card-a[data-level="ok"]   { --sev: var(--sev-ok); }
        .hx-card-a[data-level="low"]  { --sev: var(--sev-low); }
        .hx-card-a[data-level="mid"]  { --sev: var(--sev-mid); }
        .hx-card-a[data-level="high"] { --sev: var(--sev-high); }
        .hx-card-b { right: -2%; top: 9%; }
        .hx-card-dot { position: relative; flex: none; width: 10px; height: 10px; border-radius: 50%; background: var(--sev, var(--lp-mint)); transition: background .4s; }
        .hx-card-dot::after { content: ""; position: absolute; inset: 0; border-radius: 50%; background: inherit; animation: hxPing 1.8s ease-out infinite; }
        .hx-card-body { transition: opacity .22s, transform .22s; }
        .hx-card.is-out .hx-card-body { opacity: 0; transform: translateY(6px); }
        .hx-card-k { margin: 0; font-size: .72rem; font-weight: 600; color: var(--lp-muted); }
        .hx-card-v { margin: 0; font: 800 1.35rem/1.1 var(--lp-display); letter-spacing: -.02em; color: var(--sev, var(--lp-text)); transition: color .4s; font-variant-numeric: tabular-nums; }
        .hx-card-t { flex: none; padding: .2rem .6rem; border-radius: 99px; font-size: .7rem; font-weight: 700; white-space: nowrap; color: var(--lp-text); background: color-mix(in srgb, var(--sev, var(--lp-mint)) 22%, transparent); transition: opacity .22s, background .4s; }
        .hx-card.is-out .hx-card-t { opacity: 0; }
        .hx-card-ic { display: grid; place-items: center; flex: none; width: 34px; height: 34px; border-radius: 11px; color: #0B5C4A; background: rgba(79, 227, 193, .3); }
        .hx-card-ic svg { width: 18px; height: 18px; }
        [data-bs-theme="dark"] .hx-card-ic { color: #6FF0D2; }
    
        /* --- indikator scroll + ticker --- */
        .hx-cue { display: flex; justify-content: center; padding-bottom: 1.1rem; animation: hxUp .8s var(--lp-ease) 1.6s both; }
        .hx-cue a { display: inline-flex; flex-direction: column; align-items: center; gap: .45rem; font-size: .74rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--lp-muted); text-decoration: none; }
        .hx-cue a:hover { color: var(--lp-link); }
        .hx-mouse { position: relative; width: 24px; height: 38px; border: 2px solid currentColor; border-radius: 14px; }
        .hx-mouse::after { content: ""; position: absolute; left: 50%; top: 7px; width: 4px; height: 8px; margin-left: -2px; border-radius: 4px; background: currentColor; animation: hxWheel 1.8s ease-in-out infinite; }
        @keyframes hxWheel { 0% { translate: 0 0; opacity: 1; } 70% { translate: 0 11px; opacity: 0; } 100% { translate: 0 0; opacity: 0; } }
    
        .hx-ticker { overflow: hidden; border-top: 1px solid var(--lp-line); padding: .95rem 0; background: color-mix(in srgb, var(--lp-surface) 55%, transparent); backdrop-filter: blur(6px); -webkit-mask-image: linear-gradient(90deg, transparent, #000 9%, #000 91%, transparent); mask-image: linear-gradient(90deg, transparent, #000 9%, #000 91%, transparent); }
        .hx-track { display: flex; width: max-content; animation: hxMarquee 34s linear infinite; }
        .hx-ticker:hover .hx-track { animation-play-state: paused; }
        .hx-group { display: flex; flex: none; align-items: center; gap: 2.2rem; padding-right: 2.2rem; }
        .hx-group span { font: 700 1.02rem var(--lp-display); letter-spacing: .16em; text-transform: uppercase; color: var(--lp-muted); white-space: nowrap; }
        .hx-group svg { width: 14px; height: 14px; flex: none; color: var(--lp-brand-2); }
        @keyframes hxMarquee { to { transform: translateX(-50%); } }
    
        /* --- responsif --- */
        @media (max-width: 991.98px) {
            .hx-stage { width: min(100%, 440px); margin-top: .5rem; }
            .hx-title { font-size: clamp(2.4rem, 9vw, 3.6rem); }
        }
        @media (max-width: 575.98px) {
            .hx-node { width: 46px; height: 46px; border-radius: 15px; }
            .hx-node svg { width: 21px; height: 21px; }
            .hx-node-lbl { font-size: .68rem; padding: .14rem .55rem; }
            .hx-card { padding: .5rem .75rem .5rem .65rem; gap: .55rem; }
            .hx-card-v { font-size: 1.1rem; }
            .hx-card-a { left: -2%; bottom: 2%; }
            .hx-card-b { right: -2%; top: 2%; }
            .hx-stats b { font-size: 1.55rem; }
        }
    
        @media (prefers-reduced-motion: reduce) {
            .hx-w, .hx-a, .hx-cue, .hx-ring, .hx-node, .hx-card, .hx-orb, .hx-orb .core { opacity: 1 !important; transform: none; filter: none; }
            .hx-orb .x { stroke-dashoffset: 0; }
            .hx-track { animation: none !important; transform: none !important; flex-wrap: wrap; }
            .hx-orbit, .hx-spin-r, .hx-orbit-b { animation: none !important; }
            .hx-tilt { transform: none !important; }
        }
    </style>
</head>
<body class="lp-body" data-bs-spy="scroll" data-bs-target="#navLanding" data-bs-root-margin="0px 0px -70%" tabindex="0">

    <a class="visually-hidden-focusable lp-skip" href="#utama">Lewati ke konten utama</a>

    {{-- ===================== NAVBAR (kelas sama dengan layouts/navigation.blade.php) ===================== --}}
    <nav class="navbar navbar-expand-md nx-navbar sticky-top py-2" aria-label="Navigasi utama">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center py-0" href="/" aria-label="{{ config('app.name', 'SME Nexus') }}">
                <img src="{{ asset('images/logo_sme.png') }}" alt="SME Nexus" class="nx-logo-img">
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navLanding" aria-controls="navLanding" aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navLanding">
                <ul class="navbar-nav me-auto gap-1 mt-2 mt-md-0 ms-md-3">
                    <li class="nav-item"><a class="nav-link fw-semibold px-3" href="#cara-kerja">Cara kerja</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold px-3" href="#nexus-radar">Nexus Radar</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold px-3" href="#faq">Tanya jawab</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
                    <button type="button" class="btn nx-icon-btn" data-theme-toggle aria-label="Mode gelap/terang" title="Mode gelap/terang">
                        <svg class="i-sun" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <svg class="i-moon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn lp-btn lp-btn-primary lp-btn-sm">Buka dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn lp-btn lp-btn-quiet lp-btn-sm">Masuk</a>
                        <a href="{{ route('register') }}" class="btn lp-btn lp-btn-primary lp-btn-sm">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <section class="hx" id="intro" aria-labelledby="hxTitle">
        <div class="hx-bg" aria-hidden="true">
            <div class="hx-grid"></div>
            <div class="hx-spot"></div>
            <i class="hx-blob b1"></i><i class="hx-blob b2"></i><i class="hx-blob b3"></i>
        </div>
    
        <div class="hx-main">
            <div class="container">
                <div class="row g-5 align-items-center">
    
                    {{-- ---------- Salin ---------- --}}
                    <div class="col-lg-6">
                        <span class="hx-eyebrow hx-a" style="--d:.1s"><i class="hx-dot"></i>Diagnosis &amp; pemulihan untuk UMKM</span>
    
                        <h1 class="hx-title" id="hxTitle">
                            <span class="hx-mask"><span class="hx-w" style="--i:0">Setiap</span></span>
                            <span class="hx-mask"><span class="hx-w" style="--i:1">angka</span></span>
                            <span class="hx-mask"><span class="hx-w" style="--i:2">punya</span></span>
                            <span class="hx-mask"><span class="hx-w" style="--i:3">cerita.</span></span>
                            <span class="hx-l2">
                                <span class="hx-mask"><span class="hx-w" style="--i:5">Kami</span></span>
                                <span class="hx-mask"><span class="hx-w" style="--i:6">bantu</span></span>
                                <span class="hx-mask"><span class="hx-w hx-grad" style="--i:7">membacanya.</span></span>
                            </span>
                        </h1>
    
                        <p class="hx-sub hx-a" style="--d:1.15s">
                            SME Nexus mendeteksi masalah usaha, memahami penyebabnya, mensimulasikan langkah pemulihan, lalu menghubungkan Anda dengan UMKM lain.
                        </p>
    
                        <div class="d-flex flex-wrap gap-3 mt-4 hx-a" style="--d:1.3s">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn lp-btn lp-btn-primary">Buka dashboard</a>
                            @else
                                <a href="{{ route('register') }}" class="btn lp-btn lp-btn-primary">Mulai gratis</a>
                            @endauth
                            <a href="#beranda" class="btn lp-btn lp-btn-ghost">Coba simulasinya</a>
                        </div>
    
                        <ul class="hx-stats hx-a" style="--d:1.45s">
                            <li><b><span data-count="5">5</span></b><span>kelompok data diperiksa</span></li>
                            <li><b><span data-count="6">6</span></b><span>jenis usaha didukung</span></li>
                            <li><b><span data-count="3">3</span></b><span>modul: Autopsi, Action Plan, Radar</span></li>
                        </ul>
                    </div>
    
                    {{-- ---------- Panggung orbit "Nexus" ---------- --}}
                    <div class="col-lg-6">
                        <div class="hx-stage" aria-hidden="true">
                            <div class="hx-tilt">
                                <i class="hx-ring turn" style="--s:98%; --d:.2s"></i>
                                <i class="hx-ring solid" style="--s:76%; --d:.35s"></i>
                                <i class="hx-ring" style="--s:50%; --d:.5s"></i>
    
                                <div class="hx-orbit-b">
                                    <i style="--a:20deg; --c:#4B4FE0"></i>
                                    <i style="--a:140deg; --c:#16B89A"></i>
                                    <i style="--a:260deg; --c:#F59A3C"></i>
                                </div>
    
                                <div class="hx-orbit">
                           
                                    <div class="hx-arm" style="--a:0deg; --c:#4B4FE0">
                                        <i class="hx-link" style="--d:0s"></i>
                                        <div class="hx-nodewrap"><div class="hx-spin-r">
                                            <div class="hx-node" style="--d:.9s">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h4l2.2-6 4.2 12 2.2-6H21"/></svg>
                                                <span class="hx-node-lbl">Detection &amp; Understand</span>
                                            </div>
                                        </div></div>
                                    </div>

                                    <div class="hx-arm" style="--a:120deg; --c:#F59A3C">
                                        <i class="hx-link" style="--d:.9s"></i>
                                        <div class="hx-nodewrap"><div class="hx-spin-r">
                                            <div class="hx-node" style="--d:1.05s">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h9M17 7h3M4 17h3M11 17h9"/><circle cx="15" cy="7" r="2"/><circle cx="9" cy="17" r="2"/></svg>
                                                <span class="hx-node-lbl">Simulasion</span>
                                            </div>
                                        </div></div>
                                    </div>

                                    <div class="hx-arm" style="--a:240deg; --c:#16B89A">
                                        <i class="hx-link" style="--d:1.8s"></i>
                                        <div class="hx-nodewrap"><div class="hx-spin-r">
                                            <div class="hx-node" style="--d:1.2s">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6.5" cy="12" r="3"/><circle cx="17.5" cy="12" r="3"/><path d="M9.5 12h5"/></svg>
                                                <span class="hx-node-lbl">Collaboration</span>
                                            </div>
                                        </div></div>
                                    </div>
                                </div>
    
                                <div class="hx-orb">
                                    <svg viewBox="0 0 48 48"><path class="x" d="M10 10L38 38"/><path class="x two" d="M38 10L10 38"/><circle class="core" cx="24" cy="24" r="4"/></svg>
                                </div>
                            </div>
    
                            <div class="hx-card hx-card-a" id="hxLive" data-level="high" style="--d:1.5s">
                                <i class="hx-card-dot"></i>
                                <div class="hx-card-body">
                                    <p class="hx-card-k" id="hxK">Omzet</p>
                                    <p class="hx-card-v" id="hxV">&minus;20%</p>
                                </div>
                                <span class="hx-card-t" id="hxT">Gejala terdeteksi</span>
                            </div>
    
                            <div class="hx-card hx-card-b" style="--d:1.75s">
                                <span class="hx-card-ic"><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 10.5l3.5 3.5 7.5-8"/></svg></span>
                                <div>
                                    <p class="hx-card-k">Nexus Radar</p>
                                    <p class="hx-card-v" style="font-size:1.05rem">Mitra cocok!</p>
                                </div>
                            </div>
                        </div>
                    </div>
    
                </div>
            </div>
        </div>
    
        <div class="hx-cue d-none d-md-flex">
            <a href="#beranda" aria-label="Gulir ke simulasi autopsi"><span class="hx-mouse" aria-hidden="true"></span>Gulir</a>
        </div>
    
        <div class="hx-ticker" aria-hidden="true">
            <div class="hx-track">
                @for ($g = 0; $g < 2; $g++)
                    <div class="hx-group">
                        @for ($r = 0; $r < 2; $r++)
                            <span>Decision</span><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 1l2.4 6.6L19 10l-6.6 2.4L10 19l-2.4-6.6L1 10l6.6-2.4z"/></svg>
                            <span>Understand</span><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 1l2.4 6.6L19 10l-6.6 2.4L10 19l-2.4-6.6L1 10l6.6-2.4z"/></svg>
                            <span>Simulasion</span><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 1l2.4 6.6L19 10l-6.6 2.4L10 19l-2.4-6.6L1 10l6.6-2.4z"/></svg>
                            <span>Collaboration</span><svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 1l2.4 6.6L19 10l-6.6 2.4L10 19l-2.4-6.6L1 10l6.6-2.4z"/></svg>
                        @endfor
                    </div>
                @endfor
            </div>
        </div>
    </section>

    <main id="utama">

        {{-- ===================== HERO: simulasi autopsi (modul Intelligence) ===================== --}}
        <section class="lp-hero" id="beranda">
            <div class="container">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-5">
                        <h1 class="lp-h1">Omzet turun? Cari tahu penyebabnya, lalu pulihkan.</h1>
                        <p class="lp-lead tw-max-w-[33rem] tw-leading-relaxed">
                            SME Nexus membandingkan data dua periode usaha Anda, mendeteksi gejala penurunan omzet, pelanggan, dan penjualan, lalu menyusun langkah pemulihan yang bisa langsung dikerjakan.
                        </p>

                        <div class="d-flex flex-wrap gap-3 tw-mt-8">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn lp-btn lp-btn-primary">Buka dashboard</a>
                            @else
                                <a href="{{ route('register') }}" class="btn lp-btn lp-btn-primary">Mulai analisis usaha</a>
                            @endauth
                            <a href="#cara-kerja" class="btn lp-btn lp-btn-ghost">Lihat cara kerja</a>
                        </div>

                        <div class="tw-mt-10">
                            <p class="tw-mb-3 tw-text-sm tw-font-semibold tw-text-ink">Autopsi memeriksa lima kelompok data usaha Anda</p>
                            <ul class="lp-checks tw-flex tw-flex-wrap tw-gap-x-5 tw-gap-y-2 tw-text-sm tw-text-muted">
                                <li><i style="--c:#4B4FE0"></i>Omzet</li>
                                <li><i style="--c:#16B89A"></i>Pelanggan</li>
                                <li><i style="--c:#F59A3C"></i>Penjualan</li>
                                <li><i style="--c:#6A5CFF"></i>Produk</li>
                                <li><i style="--c:#F0506A"></i>Biaya</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="lp-console" id="monitor" data-level="high">
                            <div class="lp-console-inner">
                                <div class="lp-console-head">
                                    <div>
                                        <h2 class="lp-console-title">Autopsi usaha, versi singkat</h2>
                                        <p class="lp-console-sub">Geser untuk melihat bagaimana mesin diagnosis menilai perubahan.</p>
                                    </div>
                                    <span class="lp-pill"><i class="lp-dot"></i><span id="pillText">Signifikan</span></span>
                                </div>

                                <div class="lp-console-body">
                                    <div>
                                        <div class="btn-group lp-seg" role="group" aria-label="Pilih indikator">
                                            <input type="radio" class="btn-check" name="metric" id="m-omzet" value="omzet" autocomplete="off" checked>
                                            <label class="btn" for="m-omzet">Omzet</label>
                                            <input type="radio" class="btn-check" name="metric" id="m-pelanggan" value="pelanggan" autocomplete="off">
                                            <label class="btn" for="m-pelanggan">Pelanggan</label>
                                            <input type="radio" class="btn-check" name="metric" id="m-penjualan" value="penjualan" autocomplete="off">
                                            <label class="btn" for="m-penjualan">Penjualan</label>
                                        </div>

                                        <div class="lp-ecg" aria-hidden="true">
                                            <svg viewBox="0 0 300 64" preserveAspectRatio="xMidYMid meet">
                                                <path class="base" pathLength="100" d="M0 32 H60 L68 32 L74 14 L82 52 L90 6 L98 32 H140 L148 32 L154 22 L160 32 H210 L218 32 L224 14 L232 52 L240 6 L248 32 H300"/>
                                                <path class="beat" pathLength="100" d="M0 32 H60 L68 32 L74 14 L82 52 L90 6 L98 32 H140 L148 32 L154 22 L160 32 H210 L218 32 L224 14 L232 52 L240 6 L248 32 H300"/>
                                            </svg>
                                        </div>

                                        <div class="lp-change">
                                            <strong id="chg">−20%</strong>
                                            <span>dibanding periode sebelumnya</span>
                                        </div>

                                        <dl class="lp-figures">
                                            <div><dt>Periode sebelumnya</dt><dd id="prev">Rp 50.000.000</dd></div>
                                            <div><dt>Periode sekarang</dt><dd id="curr">Rp 40.000.000</dd></div>
                                        </dl>

                                        <div class="lp-slider">
                                            <div class="lp-slider-top">
                                                <label for="slider">Ubah periode sekarang</label>
                                            </div>
                                            <input type="range" class="lp-range" id="slider" min="-40" max="10" step="1" value="-20" aria-valuetext="Turun 20 persen, Signifikan">
                                            <div class="lp-scale" aria-hidden="true">
                                                <span style="--p:.30">−25%</span>
                                                <span style="--p:.60">−10%</span>
                                                <span style="--p:.70">−5%</span>
                                                <span style="--p:.78">−1%</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="lp-dx" id="dx">
                                        <p class="lp-dx-label">Diagnosis</p>
                                        <h3 class="lp-dx-name" id="dxName">Omzet Menurun Signifikan</h3>
                                        <p class="lp-dx-sev" id="dxSev">Tingkat keparahan: <b>Tinggi</b></p>
                                        <p class="lp-dx-head" id="dxHead">Langkah pemulihan pertama</p>
                                        <ol class="lp-dx-steps" id="dxSteps">
                                            <li><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="10" cy="10" r="8.2"/><path d="M6.4 10.3l2.5 2.5 4.7-5"/></svg><span>Analisis penyebab penurunan berdasarkan produk, pelanggan, dan kanal penjualan.</span></li>
                                            <li><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="10" cy="10" r="8.2"/><path d="M6.4 10.3l2.5 2.5 4.7-5"/></svg><span>Prioritaskan produk atau layanan dengan permintaan dan margin terbaik.</span></li>
                                        </ol>
                                        <p class="lp-dx-more" id="dxMore">+3 langkah lain di action plan</p>
                                    </div>
                                </div>

                                <div class="lp-console-foot">
                                    <p>Simulasi ini memakai ambang batas yang sama dengan mesin diagnosis SME Nexus.</p>
                                    @auth
                                        <a href="{{ route('intelligence.autopsy') }}" class="btn lp-btn lp-btn-light lp-btn-sm">Analisis data usaha saya</a>
                                    @else
                                        <a href="{{ route('register') }}" class="btn lp-btn lp-btn-light lp-btn-sm">Analisis data usaha saya</a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===================== CARA KERJA + ACTION PLAN (modul Decision) ===================== --}}
        <section class="lp-section" id="cara-kerja" style="padding-top: 1rem">
            <div class="container">
                <div class="row g-5 align-items-start">
                    <div class="col-lg-5">
                        <h2 class="lp-h2">Dari angka yang turun sampai tugas yang selesai</h2>
                        <p class="lp-lead tw-max-w-[30rem] tw-leading-relaxed">Hasil analisis tidak berhenti sebagai laporan. Setiap langkah rekomendasi bisa Anda simpan sebagai tugas dan dipantau sampai selesai.</p>

                        <ol class="lp-flow">
                            <li>
                                <span class="lp-flow-n">1</span>
                                <div>
                                    <h3>Isi data dua periode</h3>
                                    <p>Omzet, pelanggan, dan penjualan untuk periode sebelumnya dan sekarang, ditambah jumlah produk, harga rata-rata, dan biaya.</p>
                                </div>
                            </li>
                            <li>
                                <span class="lp-flow-n">2</span>
                                <div>
                                    <h3>Gejala terdeteksi</h3>
                                    <p>Setiap indikator dinilai sendiri: ringan, sedang, signifikan, atau anjlok.</p>
                                </div>
                            </li>
                            <li>
                                <span class="lp-flow-n">3</span>
                                <div>
                                    <h3>Diagnosis dan rekomendasi</h3>
                                    <p>Aturan merangkai gejala menjadi diagnosis beserta tingkat keparahannya, lalu menyusun langkah pemulihan bertahap.</p>
                                </div>
                            </li>
                            <li>
                                <span class="lp-flow-n">4</span>
                                <div>
                                    <h3>Jadikan action plan</h3>
                                    <p>Simpan langkah sebagai tugas, atur tenggat, dan pantau statusnya dari To-Do sampai Done.</p>
                                </div>
                            </li>
                        </ol>
                    </div>

                    <div class="col-lg-7">
                        <div class="lp-plan" id="plan">
                            <div class="lp-plan-head">
                                <div>
                                    <h3 class="lp-plan-title">Rencana pemulihan: Omzet Menurun Signifikan</h3>
                                    <p class="lp-plan-sub">Contoh action plan. Coba ubah status tiap langkah.</p>
                                </div>
                                <span class="lp-sev">Tinggi</span>
                            </div>

                            <div class="lp-prog">
                                <div class="progress" role="progressbar" aria-label="Kemajuan action plan" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar" id="planBar" style="width: 20%"></div>
                                </div>
                                <output id="planText">1 dari 5 selesai</output>
                            </div>

                            <ol class="lp-tasks" id="planList">
                                <li class="lp-task" data-state="done">
                                    <span class="lp-task-ic"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.2 6.4l2.6 2.6 5-5.2"/></svg></span>
                                    <div>
                                        <p class="lp-task-title">Analisis penyebab penurunan berdasarkan produk, pelanggan, dan kanal penjualan.</p>
                                        <span class="badge rounded-pill lp-chip lp-chip-done">Done</span>
                                    </div>
                                    <button type="button" class="btn lp-task-btn">Buka Kembali</button>
                                </li>
                                <li class="lp-task" data-state="progress">
                                    <span class="lp-task-ic"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.2 6.4l2.6 2.6 5-5.2"/></svg></span>
                                    <div>
                                        <p class="lp-task-title">Prioritaskan produk atau layanan dengan permintaan dan margin terbaik.</p>
                                        <span class="badge rounded-pill lp-chip lp-chip-progress">In Progress</span>
                                    </div>
                                    <button type="button" class="btn lp-task-btn">Tandai Selesai</button>
                                </li>
                                <li class="lp-task" data-state="todo">
                                    <span class="lp-task-ic"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.2 6.4l2.6 2.6 5-5.2"/></svg></span>
                                    <div>
                                        <p class="lp-task-title">Hubungi pelanggan lama dan jalankan program pemulihan pelanggan.</p>
                                        <span class="badge rounded-pill lp-chip lp-chip-todo">To-Do</span>
                                    </div>
                                    <button type="button" class="btn lp-task-btn">Mulai Kerjakan</button>
                                </li>
                                <li class="lp-task" data-state="todo">
                                    <span class="lp-task-ic"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.2 6.4l2.6 2.6 5-5.2"/></svg></span>
                                    <div>
                                        <p class="lp-task-title">Susun program promosi terukur dengan batas biaya yang jelas.</p>
                                        <span class="badge rounded-pill lp-chip lp-chip-todo">To-Do</span>
                                    </div>
                                    <button type="button" class="btn lp-task-btn">Mulai Kerjakan</button>
                                </li>
                                <li class="lp-task" data-state="todo">
                                    <span class="lp-task-ic"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.2 6.4l2.6 2.6 5-5.2"/></svg></span>
                                    <div>
                                        <p class="lp-task-title">Evaluasi pencapaian target setiap minggu.</p>
                                        <span class="badge rounded-pill lp-chip lp-chip-todo">To-Do</span>
                                    </div>
                                    <button type="button" class="btn lp-task-btn">Mulai Kerjakan</button>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===================== NEXUS RADAR (modul Collaboration) ===================== --}}
        <section class="lp-band" id="nexus-radar">
            <div class="container">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6 order-lg-2">
                        <h2 class="lp-h2">Pulih lebih cepat dengan bantuan UMKM lain</h2>
                        <p class="lp-lead tw-max-w-[32rem] tw-leading-relaxed">
                            Nexus Radar menampilkan UMKM lain yang bisa menjadi pemasok, pelanggan, atau mitra. Tiap profil memuat apa yang mereka tawarkan, apa yang mereka cari, dan di mana lokasinya.
                        </p>
                        <p class="lp-lead tw-mt-4 tw-max-w-[32rem] tw-leading-relaxed">
                            Pilih Connect jika tertarik, atau Pass untuk melewati. Kalau dua usaha saling memilih Connect, keduanya otomatis cocok.
                        </p>
                    </div>

                    <div class="col-lg-6 order-lg-1">
                        <div class="lp-deck" id="deck" role="group" aria-label="Contoh kartu Nexus Radar">
                            <article class="lp-profile" id="profile">
                                <div class="lp-prof-top">
                                    <span class="lp-ava" id="pAva" style="--c:#16B89A">KT</span>
                                    <div>
                                        <h3 class="lp-prof-name" id="pName">Kopi Tani Lestari</h3>
                                        <div class="lp-tags"><span class="lp-tag" id="pCat">Pertanian</span><span class="lp-tag alt" id="pStaff">12 karyawan</span></div>
                                    </div>
                                </div>
                                <dl class="lp-prof-list">
                                    <div><dt>Kami menawarkan</dt><dd id="pOffer">Biji kopi robusta dan arabika sangrai, kemasan curah maupun retail.</dd></div>
                                    <div><dt>Kami mencari</dt><dd id="pSeek">Kedai kopi atau toko oleh-oleh untuk pasokan tetap tiap bulan.</dd></div>
                                    <div><dt>Lokasi</dt><dd id="pLoc">Malang, Jawa Timur</dd></div>
                                </dl>
                                <div class="lp-prof-act">
                                    <button type="button" class="btn lp-btn lp-btn-ghost lp-btn-sm" data-swipe="pass">
                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M5 5l10 10M15 5L5 15"/></svg>Pass
                                    </button>
                                    <button type="button" class="btn lp-btn lp-btn-primary lp-btn-sm" data-swipe="like">
                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4.5 10.5l3.5 3.5 7.5-8"/></svg>Connect
                                    </button>
                                </div>
                            </article>
                        </div>
                        <p class="lp-radar-note" id="radarNote" role="status">Profil di atas hanya contoh. Coba pilih Connect atau Pass.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===================== JENIS USAHA ===================== --}}
        <section class="lp-section" id="usaha">
            <div class="container">
                <div class="row g-5">
                    <div class="col-lg-4">
                        <h2 class="lp-h2">Dirancang untuk enam jenis usaha</h2>
                        <p class="lp-lead tw-max-w-[26rem] tw-leading-relaxed">Pilih kategori usaha saat melengkapi profil, dan analisis disesuaikan dengan konteksnya.</p>
                    </div>
                    <div class="col-lg-8">
                        <ul class="lp-cats tw-grid tw-grid-cols-1 sm:tw-grid-cols-2">
                            <li><h3>Kuliner</h3><p>Usaha makanan dan minuman</p></li>
                            <li><h3>Retail</h3><p>Perdagangan barang eceran</p></li>
                            <li><h3>Jasa</h3><p>Usaha jasa dan konsultasi</p></li>
                            <li><h3>Manufaktur</h3><p>Produksi dan pengolahan barang</p></li>
                            <li><h3>Pertanian</h3><p>Pertanian, perkebunan, dan agribisnis</p></li>
                            <li><h3>Teknologi</h3><p>Produk digital dan teknologi</p></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===================== FAQ (Bootstrap accordion) ===================== --}}
        <section class="lp-section" id="faq" style="padding-top: 1rem">
            <div class="container">
                <div class="row g-5">
                    <div class="col-lg-4">
                        <h2 class="lp-h2">Pertanyaan yang sering muncul</h2>
                        <p class="lp-lead tw-max-w-[24rem] tw-leading-relaxed">Hal-hal yang biasanya ingin diketahui sebelum menjalankan analisis pertama.</p>
                    </div>
                    <div class="col-lg-8">
                        <div class="accordion lp-faq" id="faqList">
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">Data apa yang perlu saya siapkan?</button>
                                </h3>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqList">
                                    <div class="accordion-body">Omzet, jumlah pelanggan, dan jumlah penjualan untuk dua periode yang ingin dibandingkan, misalnya bulan lalu dan bulan ini. Lalu jumlah produk, harga rata-rata, biaya marketing, dan biaya operasional periode sekarang.</div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">Bagaimana tingkat penurunan ditentukan?</button>
                                </h3>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqList">
                                    <div class="accordion-body">Perubahan tiap indikator dihitung antar dua periode. Turun 1% atau lebih dinilai ringan, lebih dari 5% sedang, lebih dari 10% signifikan, dan lebih dari 25% anjlok. Aturan diagnosis lalu merangkai gejala itu menjadi diagnosis dengan tingkat keparahan rendah, sedang, tinggi, atau kritis.</div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">Apakah yang diperiksa hanya penurunan?</button>
                                </h3>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqList">
                                    <div class="accordion-body">Tidak. Selain penurunan omzet, pelanggan, dan penjualan, mesin juga memeriksa biaya operasional yang melebihi 70% omzet dan margin laba yang di bawah 10%.</div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">Apakah hasilnya bisa menggantikan akuntan atau konsultan?</button>
                                </h3>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqList">
                                    <div class="accordion-body">Tidak. Hasilnya adalah panduan awal berbasis aturan dari data yang Anda isi. Untuk keputusan besar seperti pinjaman atau restrukturisasi usaha, tetap libatkan akuntan atau konsultan.</div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">Apa yang terjadi setelah saya mendaftar?</button>
                                </h3>
                                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqList">
                                    <div class="accordion-body">Anda akan diminta memverifikasi email dan melengkapi profil usaha: nama usaha, kategori, kota, dan detail lainnya. Setelah itu analisis pertama bisa dijalankan dari dashboard.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===================== CTA PENUTUP ===================== --}}
        <section class="pb-5">
            <div class="container">
                <div class="lp-cta">
                    <div class="lp-ecg" aria-hidden="true">
                        <svg viewBox="0 0 300 64" preserveAspectRatio="none">
                            <path class="base" pathLength="100" d="M0 32 H60 L68 32 L74 14 L82 52 L90 6 L98 32 H140 L148 32 L154 22 L160 32 H210 L218 32 L224 14 L232 52 L240 6 L248 32 H300"/>
                            <path class="beat" pathLength="100" d="M0 32 H60 L68 32 L74 14 L82 52 L90 6 L98 32 H140 L148 32 L154 22 L160 32 H210 L218 32 L224 14 L232 52 L240 6 L248 32 H300"/>
                        </svg>
                    </div>
                    <h2>Mulai dari satu pertanyaan: kenapa omzet turun?</h2>
                    <p>Daftar, lengkapi profil usaha, lalu jalankan analisis pertama Anda.</p>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn lp-btn lp-btn-light">Buka dashboard</a>
                    @else
                        <a href="{{ route('register') }}" class="btn lp-btn lp-btn-light">Daftar sekarang</a>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    <footer class="lp-foot">
        <div class="container tw-flex tw-flex-wrap tw-items-center tw-justify-between tw-gap-4">
            <a href="/" aria-label="{{ config('app.name', 'SME Nexus') }}"><img src="{{ asset('images/logo_sme.png') }}" alt="SME Nexus" class="nx-logo-img" style="height:32px"></a>
            <p class="tw-m-0">&copy; {{ date('Y') }} {{ config('app.name', 'SME Nexus') }}. Semua hak dilindungi.</p>
            <nav class="tw-flex tw-gap-5 tw-font-semibold" aria-label="Tautan footer">
                @auth
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    <a href="{{ route('login') }}">Masuk</a>
                    <a href="{{ route('register') }}">Daftar</a>
                @endauth
            </nav>
        </div>
    </footer>

<script>
    (function () {
        var root = document.getElementById('intro');
        if (!root) return;
        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    
        /* 1. Hitung naik angka statistik */
        var nums = Array.prototype.slice.call(root.querySelectorAll('[data-count]'));
        if (!reduce) {
            nums.forEach(function (n) { n.textContent = '0'; });
            setTimeout(function () {
                var t0 = performance.now(), dur = 1400;
                (function tick(t) {
                    var p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 3);
                    nums.forEach(function (n) { n.textContent = Math.round(parseInt(n.getAttribute('data-count'), 10) * e); });
                    if (p < 1) requestAnimationFrame(tick);
                })(t0);
            }, 1450);
        }
    
        /* 2. Kartu status bergilir: Terdeteksi -> Diagnosis -> Action plan -> Pulih */
        var live = document.getElementById('hxLive');
        var K = document.getElementById('hxK'), V = document.getElementById('hxV'), T = document.getElementById('hxT');
        var STATES = [
            { l: 'high', k: 'Omzet',       v: '\u221220%',   t: 'Gejala terdeteksi' },
            { l: 'mid',  k: 'Diagnosis',   v: 'Tinggi',       t: 'Penyebab ditemukan' },
            { l: 'low',  k: 'Action plan', v: '5 langkah',    t: 'Siap dikerjakan' },
            { l: 'ok',   k: 'Omzet',       v: '+12%',         t: 'Pulih' }
        ];
        var si = 0, cycleTimer = 0;
        function nextState() {
            si = (si + 1) % STATES.length;
            live.classList.add('is-out');
            setTimeout(function () {
                var s = STATES[si];
                live.setAttribute('data-level', s.l);
                K.textContent = s.k; V.textContent = s.v; T.textContent = s.t;
                live.classList.remove('is-out');
            }, 240);
        }
        if (!reduce) cycleTimer = setInterval(nextState, 3200);
    
        /* 3. Tilt 3D + sorotan kursor (hanya perangkat dengan mouse) */
        var tilt = root.querySelector('.hx-tilt');
        if (!reduce && window.matchMedia('(pointer: fine)').matches) {
            var raf = 0, px = .5, py = .4;
            root.addEventListener('pointermove', function (e) {
                var r = root.getBoundingClientRect();
                px = (e.clientX - r.left) / r.width; py = (e.clientY - r.top) / r.height;
                if (raf) return;
                raf = requestAnimationFrame(function () {
                    raf = 0;
                    root.style.setProperty('--mx', (px * 100) + '%');
                    root.style.setProperty('--my', (py * 100) + '%');
                    tilt.style.setProperty('--ry', ((px - .5) * 12).toFixed(2) + 'deg');
                    tilt.style.setProperty('--rx', ((.5 - py) * 10).toFixed(2) + 'deg');
                });
            });
            root.addEventListener('pointerleave', function () {
                tilt.style.setProperty('--rx', '0deg'); tilt.style.setProperty('--ry', '0deg');
            });
        }
    
        /* 4. Jeda animasi saat hero di luar layar (hemat baterai/CPU) */
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (entries) {
                var on = entries[0].isIntersecting;
                root.classList.toggle('is-paused', !on);
                if (!reduce) { clearInterval(cycleTimer); if (on) cycleTimer = setInterval(nextState, 3200); }
            }, { threshold: 0.05 }).observe(root);
        }
    })();
(function () {
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    function $(s, c) { return (c || document).querySelector(s); }
    function $$(s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); }

    /* =============== 1. Monitor simulasi autopsi =============== */
    var consoleEl = $('#monitor');
    var slider = $('#slider');
    var fmtNum = function (n) { return new Intl.NumberFormat('id-ID').format(Math.round(n)); };

    var METRICS = {
        omzet:     { base: 50000000, money: true,  name: 'Omzet' },
        pelanggan: { base: 500,      money: false, name: 'Pelanggan' },
        penjualan: { base: 1000,     money: false, name: 'Volume penjualan' }
    };
    // Teks langkah dan ambang batas mengikuti IntelligenceMasterSeeder.
    var LEVELS = {
        ok:   { label: 'Sehat', sev: '', steps: [] },
        low:  { label: 'Ringan', sev: 'Rendah', steps: [
            'Bandingkan hasil usaha dengan periode sebelumnya dan catat sumber penurunan.',
            'Periksa produk atau layanan yang mengalami penurunan kontribusi.',
            'Buat target perbaikan untuk periode berikutnya.',
            'Evaluasi hasil perbaikan setiap minggu.'
        ] },
        mid:  { label: 'Sedang', sev: 'Sedang', steps: [
            'Identifikasi produk, layanan, atau kanal penjualan yang paling banyak mengalami penurunan.',
            'Hubungi pelanggan yang mulai jarang bertransaksi dan cari tahu penyebabnya.',
            'Uji penawaran bundling atau promosi dengan margin yang sehat.',
            'Tetapkan target pemulihan dan pantau hasilnya setiap minggu.'
        ] },
        high: { label: 'Signifikan', sev: 'Tinggi', steps: [
            'Analisis penyebab penurunan berdasarkan produk, pelanggan, dan kanal penjualan.',
            'Prioritaskan produk atau layanan dengan permintaan dan margin terbaik.',
            'Hubungi pelanggan lama dan jalankan program pemulihan pelanggan.',
            'Susun program promosi terukur dengan batas biaya yang jelas.',
            'Evaluasi pencapaian target setiap minggu.'
        ] },
        crit: { label: 'Anjlok', sev: 'Kritis', steps: [
            'Periksa penyebab penurunan dan dampaknya terhadap arus kas usaha.',
            'Identifikasi sumber pendapatan yang masih bertahan dan prioritaskan pemulihannya.',
            'Hubungi pelanggan utama dan pelanggan tidak aktif untuk mencari penyebab kehilangan transaksi.',
            'Hentikan sementara pengeluaran atau pemasaran yang tidak menghasilkan kontribusi.',
            'Buat rencana pemulihan 30 hari dengan target mingguan.',
            'Evaluasi hasil pemulihan setiap minggu dan sesuaikan strategi.'
        ] }
    };
    var CHECK = '<svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="10" cy="10" r="8.2"/><path d="M6.4 10.3l2.5 2.5 4.7-5"/></svg>';

    function levelOf(c) {
        if (c < -25) return 'crit';
        if (c < -10) return 'high';
        if (c < -5) return 'mid';
        if (c <= -1) return 'low';
        return 'ok';
    }
    function signed(c) { return (c > 0 ? '+' : c < 0 ? '\u2212' : '') + Math.abs(c) + '%'; }
    function metricValue(m, n) { return m.money ? 'Rp ' + fmtNum(n) : fmtNum(n); }

    var el = {
        pill: $('#pillText'), chg: $('#chg'), prev: $('#prev'), curr: $('#curr'),
        name: $('#dxName'), sev: $('#dxSev'), head: $('#dxHead'), steps: $('#dxSteps'), more: $('#dxMore')
    };
    var metric = 'omzet';

    function render(c) {
        var m = METRICS[metric], lvl = levelOf(c), L = LEVELS[lvl];
        consoleEl.setAttribute('data-level', lvl);
        el.pill.textContent = L.label;
        el.chg.textContent = signed(c);
        el.prev.textContent = metricValue(m, m.base);
        el.curr.textContent = metricValue(m, m.base * (1 + c / 100));
        slider.setAttribute('aria-valuetext', (c < 0 ? 'Turun ' + Math.abs(c) + ' persen' : c > 0 ? 'Naik ' + c + ' persen' : 'Tidak berubah') + ', ' + L.label);

        if (lvl === 'ok') {
            el.name.textContent = 'Belum ada gejala';
            el.sev.textContent = 'Perubahan masih di atas ambang \u22121%, jadi belum ada penurunan yang dicatat.';
            el.head.hidden = true;
            el.steps.innerHTML = '';
            el.more.textContent = 'Terus pantau tiap periode.';
            return;
        }
        el.name.textContent = m.name + ' Menurun ' + L.label;
        el.sev.innerHTML = 'Tingkat keparahan: <b>' + L.sev + '</b>';
        el.head.hidden = false;
        el.steps.innerHTML = L.steps.slice(0, 2).map(function (s) { return '<li>' + CHECK + '<span>' + s + '</span></li>'; }).join('');
        var rest = L.steps.length - 2;
        el.more.textContent = '+' + rest + ' langkah lain di action plan';
    }

    var introOn = false, introRaf = 0, introTimer = 0;
    function stopIntro() { introOn = false; cancelAnimationFrame(introRaf); clearTimeout(introTimer); }

    function userInput() {
        stopIntro();
        // Pengumuman ke pembaca layar hanya setelah pengguna mulai berinteraksi.
        $('#dx').setAttribute('aria-live', 'polite');
    }
    slider.addEventListener('input', function () { userInput(); render(parseInt(slider.value, 10)); });
    slider.addEventListener('pointerdown', stopIntro);
    $$('input[name="metric"]').forEach(function (r) {
        r.addEventListener('change', function () { userInput(); metric = r.value; render(parseInt(slider.value, 10)); });
    });

    // Satu momen pembuka: monitor mulai "sehat" lalu bergeser ke contoh -20%.
    function playIntro() {
        if (reduce) { render(-20); consoleEl.classList.add('is-ready'); return; }
        slider.value = 0; render(0);
        consoleEl.classList.add('is-ready');
        introOn = true;
        introTimer = setTimeout(function () {
            var t0 = performance.now(), dur = 1700;
            (function step(t) {
                if (!introOn) return;
                var p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 3);
                var v = Math.round(-20 * e);
                slider.value = v; render(v);
                if (p < 1) introRaf = requestAnimationFrame(step); else introOn = false;
            })(t0);
        }, 900);
    }
    playIntro();

    /* =============== 2. Action plan (meniru status To-Do / In Progress / Done di modul Decision) =============== */
    var NEXT = { todo: 'progress', progress: 'done', done: 'progress' };
    var BTN = { todo: 'Mulai Kerjakan', progress: 'Tandai Selesai', done: 'Buka Kembali' };
    var CHIP = { todo: 'To-Do', progress: 'In Progress', done: 'Done' };
    var rows = $$('#planList .lp-task');
    var bar = $('#planBar'), planText = $('#planText'), progressEl = $('#plan .progress');

    function paintPlan() {
        var done = 0;
        rows.forEach(function (row) {
            var st = row.getAttribute('data-state');
            if (st === 'done') done++;
            var chip = $('.badge', row);
            chip.className = 'badge rounded-pill lp-chip lp-chip-' + st;
            chip.textContent = CHIP[st];
            $('.lp-task-btn', row).textContent = BTN[st];
        });
        var pct = Math.round(done / rows.length * 100);
        bar.style.width = pct + '%';
        progressEl.setAttribute('aria-valuenow', pct);
        planText.textContent = done + ' dari ' + rows.length + ' selesai';
    }
    $('#planList').addEventListener('click', function (e) {
        var btn = e.target.closest('.lp-task-btn');
        if (!btn) return;
        var row = btn.closest('.lp-task');
        row.setAttribute('data-state', NEXT[row.getAttribute('data-state')]);
        paintPlan();
    });
    paintPlan();

    /* =============== 3. Nexus Radar (contoh kartu Connect / Pass) =============== */
    var PROFILES = [
        { name: 'Kopi Tani Lestari', ava: 'KT', color: '#16B89A', cat: 'Pertanian', staff: '12 karyawan',
          offer: 'Biji kopi robusta dan arabika sangrai, kemasan curah maupun retail.',
          seek: 'Kedai kopi atau toko oleh-oleh untuk pasokan tetap tiap bulan.', loc: 'Malang, Jawa Timur', likesYou: false },
        { name: 'Dapur Bu Ratna', ava: 'DR', color: '#F59A3C', cat: 'Kuliner', staff: '8 karyawan',
          offer: 'Katering harian dan kotak snack untuk acara kantor.',
          seek: 'Pemasok bahan baku segar dengan harga tetap.', loc: 'Sidoarjo, Jawa Timur', likesYou: true },
        { name: 'Studio Kemasan Nusa', ava: 'SK', color: '#6A5CFF', cat: 'Manufaktur', staff: '20 karyawan',
          offer: 'Cetak kemasan food grade dengan minimum order rendah untuk UMKM.',
          seek: 'Brand makanan dan minuman lokal sebagai pelanggan tetap.', loc: 'Bandung, Jawa Barat', likesYou: false }
    ];
    var idx = 0, busy = false;
    var deck = $('#deck'), card = $('#profile'), note = $('#radarNote');
    var cardHTML = card.outerHTML;

    function fillCard() {
        var p = PROFILES[idx];
        $('#pAva').textContent = p.ava; $('#pAva').style.setProperty('--c', p.color);
        $('#pName').textContent = p.name; $('#pCat').textContent = p.cat; $('#pStaff').textContent = p.staff;
        $('#pOffer').textContent = p.offer; $('#pSeek').textContent = p.seek; $('#pLoc').textContent = p.loc;
    }
    function showEmpty() {
        card.outerHTML = '<div class="lp-empty" id="profile"><h3>Radar kosong</h3><p>Tidak ada UMKM baru untuk ditampilkan. Di aplikasi, rekomendasi baru muncul seiring bertambahnya usaha yang bergabung.</p><button type="button" class="btn lp-btn lp-btn-ghost lp-btn-sm" data-swipe="reset">Ulangi contoh</button></div>';
        card = $('#profile');
    }
    function restoreCard() {
        card.outerHTML = cardHTML;
        card = $('#profile');
        idx = 0; fillCard();
        card.classList.add('is-in');
        note.innerHTML = 'Profil di atas hanya contoh. Coba pilih Connect atau Pass.';
    }
    deck.addEventListener('click', function (e) {
        var b = e.target.closest('[data-swipe]');
        if (!b || busy) return;
        var act = b.getAttribute('data-swipe');
        if (act === 'reset') { restoreCard(); return; }

        var p = PROFILES[idx], like = act === 'like';
        if (like) {
            note.innerHTML = p.likesYou
                ? '<b>Cocok!</b> ' + p.name + ' sudah lebih dulu memilih Connect untuk usaha Anda.'
                : 'Connect terkirim ke <b>' + p.name + '</b>. Kalian cocok jika mereka juga memilih Connect.';
        } else {
            note.innerHTML = 'Anda melewati <b>' + p.name + '</b>.';
        }

        var advance = function () {
            idx++;
            if (idx >= PROFILES.length) { showEmpty(); return; }
            fillCard();
            card.classList.remove('is-out-pass', 'is-out-like');
            card.classList.add('is-in');
        };
        if (reduce) { advance(); return; }
        busy = true;
        card.classList.remove('is-in');
        card.classList.add(like ? 'is-out-like' : 'is-out-pass');
        setTimeout(function () { advance(); busy = false; }, 300);
    });
})();
</script>
</body>
</html>