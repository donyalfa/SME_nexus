<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#080C20">
    <title>SME NEXUS - Radar</title>

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap (modal & tooltip) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind (layout & utilitas) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['"Bricolage Grotesque"', 'sans-serif'],
                        body: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        night: '#080C20',
                        teal: '#19C3B1',
                        violet: '#7C5CFF',
                        amber: '#FFB547',
                        coral: '#FF5C7A',
                    },
                },
            },
        };
    </script>

    <style>
        @@property --angle { syntax: '<angle>'; inherits: false; initial-value: 0deg; }

        :root {
            --night: #080C20;
            --teal: #19C3B1;
            --violet: #7C5CFF;
            --amber: #FFB547;
            --coral: #FF5C7A;
            --text: #F2F5FF;
            --muted: rgba(226,232,255,.66);
            --mood-o: 0;
            --mood-c: transparent;
        }

        html, body { min-height: 100%; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text);
            background: var(--night);
            overflow-x: hidden;
        }

        /* ================= LATAR ================= */
        .aurora { position: fixed; inset: 0; z-index: 0; overflow: hidden; pointer-events: none; }
        .aurora i {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: .5;
            will-change: transform;
        }
        .aurora i:nth-child(1) { width: 60vmax; height: 60vmax; left: -20vmax; top: -22vmax; background: var(--violet); animation: float-a 22s ease-in-out infinite alternate; }
        .aurora i:nth-child(2) { width: 55vmax; height: 55vmax; right: -22vmax; top: 10vmax; background: var(--teal); opacity: .38; animation: float-b 26s ease-in-out infinite alternate; }
        .aurora i:nth-child(3) { width: 45vmax; height: 45vmax; left: 10vmax; bottom: -24vmax; background: var(--amber); opacity: .22; animation: float-c 30s ease-in-out infinite alternate; }
        @@keyframes float-a { to { transform: translate(18vmax, 12vmax) scale(1.15); } }
        @@keyframes float-b { to { transform: translate(-16vmax, 14vmax) scale(.9); } }
        @@keyframes float-c { to { transform: translate(14vmax, -12vmax) scale(1.2); } }

        #stars { position: fixed; inset: 0; z-index: 0; pointer-events: none; }

        .mood {
            position: fixed; inset: 0; z-index: 1; pointer-events: none;
            opacity: var(--mood-o);
            background: radial-gradient(700px circle at 50% 55%, var(--mood-c), transparent 70%);
        }

        .radar-bg { position: fixed; inset: 0; display: grid; place-items: center; z-index: 0; pointer-events: none; }
        .radar-disc {
            position: relative;
            width: min(130vmax, 1400px);
            aspect-ratio: 1;
            border-radius: 50%;
            background: repeating-radial-gradient(circle at center, transparent 0 89px, rgba(25,195,177,.16) 89px 90px);
            -webkit-mask-image: radial-gradient(circle, #000 0%, #000 20%, transparent 60%);
            mask-image: radial-gradient(circle, #000 0%, #000 20%, transparent 60%);
        }
        .radar-disc::before, .radar-disc::after { content: ''; position: absolute; background: rgba(25,195,177,.14); }
        .radar-disc::before { left: 50%; top: 0; width: 1px; height: 100%; }
        .radar-disc::after  { top: 50%; left: 0; height: 1px; width: 100%; }
        .radar-sweep {
            position: absolute; inset: 0; border-radius: 50%;
            background: conic-gradient(from 0deg, rgba(25,195,177,0) 0deg, rgba(25,195,177,.34) 55deg, rgba(25,195,177,0) 58deg);
            animation: sweep 8s linear infinite;
        }
        @@keyframes sweep { to { transform: rotate(360deg); } }
        .blip {
            position: absolute; width: 10px; height: 10px; border-radius: 50%;
            background: var(--teal); box-shadow: 0 0 18px 4px rgba(25,195,177,.7);
            opacity: 0; animation: blip 8s ease-out infinite;
        }
        @@keyframes blip {
            0%, 8% { opacity: 0; transform: scale(.4); }
            12% { opacity: 1; transform: scale(1.3); }
            48% { opacity: 0; transform: scale(2.4); }
            100% { opacity: 0; }
        }

        /* ================= HEADER ================= */
        .logo-mark {
            position: relative; width: 44px; height: 44px; border-radius: 14px;
            display: grid; place-items: center; overflow: hidden;
            background: linear-gradient(145deg, rgba(255,255,255,.14), rgba(255,255,255,.04));
            border: 1px solid rgba(255,255,255,.16);
            color: var(--amber);
            box-shadow: 0 10px 30px -10px rgba(25,195,177,.6);
        }
        .logo-mark::after {
            content: ''; position: absolute; inset: 0;
            background: conic-gradient(from 0deg, transparent 0 300deg, rgba(25,195,177,.55) 360deg);
            animation: sweep 3s linear infinite;
        }
        .logo-mark svg { position: relative; z-index: 1; }
        .brand {
            font-family: 'Bricolage Grotesque', sans-serif; font-weight: 800; letter-spacing: -.02em;
            background: linear-gradient(100deg, #fff 20%, #8ff3e8 45%, #fff 70%);
            background-size: 220% 100%;
            -webkit-background-clip: text; background-clip: text; color: transparent;
            animation: shine 5s ease-in-out infinite;
        }
        @@keyframes shine { 0%, 100% { background-position: 100% 0; } 50% { background-position: 0 0; } }
        .chip-live {
            display: flex; align-items: center; gap: 8px; padding: 7px 13px; border-radius: 999px;
            font-size: .75rem; font-weight: 600; color: var(--text);
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.14);
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
        }
        .live-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--teal); animation: live 1.6s ease-out infinite; }
        @@keyframes live { 0% { box-shadow: 0 0 0 0 rgba(25,195,177,.7); } 100% { box-shadow: 0 0 0 10px rgba(25,195,177,0); } }

        /* Tombol kembali */
        .back-btn {
            position: relative; overflow: hidden; flex: none; width: 42px; height: 42px; border-radius: 14px;
            display: grid; place-items: center; text-decoration: none; color: var(--text);
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.16);
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            transition: transform .25s cubic-bezier(.3,1.7,.5,1), background .25s, border-color .25s, color .25s;
        }
        .back-btn svg { transition: transform .3s cubic-bezier(.3,1.7,.5,1); }
        .back-btn:hover { color: #fff; background: rgba(255,255,255,.16); border-color: rgba(255,255,255,.3); transform: translateX(-2px); }
        .back-btn:hover svg { transform: translateX(-3px); }
        .back-btn:active { transform: scale(.9); }
        .back-btn:focus-visible { outline: 3px solid var(--amber); outline-offset: 3px; }
        @@media (max-width: 420px) { .logo-mark { display: none; } }

        /* ================= ENTRANCE ================= */
        body.is-loading .reveal,
        body.is-loading .card-enter,
        body.is-loading .ping-ring { animation-play-state: paused; }

        .ping-ring {
            position: absolute; left: 50%; top: 42%;
            width: 120px; height: 120px; margin: -60px 0 0 -60px; border-radius: 50%;
            border: 2px solid var(--teal); opacity: 0; pointer-events: none;
            animation: ping-out 1.6s cubic-bezier(.2,.7,.2,1) .1s 1 forwards;
        }
        .ping-ring.r2 { animation-delay: .35s; border-color: var(--violet); }
        .ping-ring.r3 { animation-delay: .6s; border-color: var(--amber); }
        @@keyframes ping-out { 0% { opacity: .8; transform: scale(.3); } 100% { opacity: 0; transform: scale(8); } }

        .card-enter { animation: card-in 1s cubic-bezier(.2,.9,.25,1.15) .3s both; }
        @@keyframes card-in {
            0% { opacity: 0; transform: translateY(60px) scale(.88) rotateX(18deg); filter: blur(10px); }
            100% { opacity: 1; transform: none; filter: blur(0); }
        }
        .reveal { animation: reveal-up .7s cubic-bezier(.2,.8,.2,1) both; animation-delay: calc(.75s + var(--d, 0s)); }
        @@keyframes reveal-up { 0% { opacity: 0; transform: translateY(16px); filter: blur(6px); } 100% { opacity: 1; transform: none; filter: blur(0); } }

        /* ================= KARTU ================= */
        .stack { position: relative; perspective: 1200px; }
        .ghost {
            position: absolute; inset: 0; border-radius: 32px;
            background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        }
        .ghost.g1 { transform: translateY(14px) scale(.95); opacity: .7; }
        .ghost.g2 { transform: translateY(28px) scale(.9); opacity: .4; }

        .swipe-card {
            position: relative; border-radius: 32px;
            touch-action: pan-y; user-select: none; -webkit-user-select: none; cursor: grab;
            transition: transform .45s cubic-bezier(.2,.9,.25,1.15);
            will-change: transform;
        }
        .swipe-card.dragging { transition: none; cursor: grabbing; }

        .tilt {
            transform: perspective(1000px) rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg));
            transition: transform .25s ease-out;
            transform-style: preserve-3d;
        }
        .glass {
            position: relative; overflow: hidden; border-radius: 32px;
            background: linear-gradient(160deg, rgba(255,255,255,.13) 0%, rgba(255,255,255,.05) 100%);
            backdrop-filter: blur(24px) saturate(150%); -webkit-backdrop-filter: blur(24px) saturate(150%);
            box-shadow: 0 40px 80px -30px rgba(0,0,0,.7), inset 0 1px 0 rgba(255,255,255,.18);
        }
        .glass::before {
            content: ''; position: absolute; inset: 0; border-radius: inherit; padding: 1.5px; pointer-events: none; z-index: 4;
            background: conic-gradient(from var(--angle), rgba(255,255,255,.06) 0 55%, var(--teal), var(--violet), var(--amber), rgba(255,255,255,.06));
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude;
            animation: spin-angle 7s linear infinite;
        }
        @@keyframes spin-angle { to { --angle: 360deg; } }
        .spot {
            position: absolute; inset: 0; z-index: 3; pointer-events: none; opacity: 0; transition: opacity .3s;
            background: radial-gradient(380px circle at var(--mx, 50%) var(--my, 30%), rgba(255,255,255,.16), transparent 45%);
        }
        .swipe-card:hover .spot { opacity: 1; }

        /* Sampul */
        .cover {
            position: relative; height: 210px; overflow: hidden; display: grid; place-items: center;
            background:
                radial-gradient(circle at 20% 25%, rgba(124,92,255,.95), transparent 55%),
                radial-gradient(circle at 80% 30%, rgba(25,195,177,.9), transparent 55%),
                radial-gradient(circle at 55% 95%, rgba(255,181,71,.85), transparent 60%),
                #151a45;
            background-size: 140% 140%;
            animation: mesh 12s ease-in-out infinite alternate;
        }
        @@keyframes mesh { 0% { background-position: 0% 0%; } 100% { background-position: 100% 100%; } }
        .cover::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(to bottom, transparent 50%, rgba(8,12,32,.55));
        }
        .orbit {
            position: absolute; left: 50%; top: 50%; border-radius: 50%;
            border: 1.5px dashed rgba(255,255,255,.35);
        }
        .orbit.o1 { width: 150px; height: 150px; margin: -75px 0 0 -75px; animation: spin 14s linear infinite; }
        .orbit.o2 { width: 210px; height: 210px; margin: -105px 0 0 -105px; border-style: solid; border-color: rgba(255,255,255,.14); animation: spin 22s linear infinite reverse; }
        .orbit::after {
            content: ''; position: absolute; top: -5px; left: 50%; width: 10px; height: 10px; margin-left: -5px;
            border-radius: 50%; background: #fff; box-shadow: 0 0 14px 3px rgba(255,255,255,.85);
        }
        .orbit.o2::after { background: var(--amber); box-shadow: 0 0 14px 3px rgba(255,181,71,.9); }
        @@keyframes spin { to { transform: rotate(360deg); } }
        .monogram {
            position: relative; z-index: 2; width: 96px; height: 96px; border-radius: 28px;
            display: grid; place-items: center;
            font-family: 'Bricolage Grotesque', sans-serif; font-weight: 800; font-size: 2.5rem; letter-spacing: -.03em;
            color: #1b1330;
            background: linear-gradient(145deg, #ffe3a3, var(--amber) 55%, #ff9f43);
            box-shadow: 0 20px 40px -12px rgba(255,181,71,.65), inset 0 2px 0 rgba(255,255,255,.7);
            animation: bob 5s ease-in-out infinite;
        }
        @@keyframes bob { 0%, 100% { transform: translateY(0) rotate(-6deg); } 50% { transform: translateY(-8px) rotate(4deg); } }

        /* Konten kartu */
        .name {
            font-family: 'Bricolage Grotesque', sans-serif; font-weight: 800; font-size: 2rem; line-height: 1.1; letter-spacing: -.03em;
            background: linear-gradient(120deg, #fff 30%, #b9a8ff);
            -webkit-background-clip: text; background-clip: text; color: transparent;
        }
        .tag {
            padding: 6px 13px; border-radius: 999px; font-size: .75rem; font-weight: 600; border: 1px solid;
        }
        .tag.t1 { color: #7ff0e3; background: rgba(25,195,177,.14); border-color: rgba(25,195,177,.35); }
        .tag.t2 { color: #ffd592; background: rgba(255,181,71,.14); border-color: rgba(255,181,71,.35); }

        .info-row { display: flex; gap: 14px; }
        .info-icon {
            flex: none; width: 42px; height: 42px; border-radius: 14px; display: grid; place-items: center;
            border: 1px solid rgba(255,255,255,.14);
        }
        .info-icon.i1 { color: #7ff0e3; background: linear-gradient(145deg, rgba(25,195,177,.3), rgba(25,195,177,.08)); }
        .info-icon.i2 { color: #ffd592; background: linear-gradient(145deg, rgba(255,181,71,.3), rgba(255,181,71,.08)); }
        .info-icon.i3 { color: #c9bcff; background: linear-gradient(145deg, rgba(124,92,255,.35), rgba(124,92,255,.1)); }
        .info-row h3 { margin: 0 0 3px; font-size: .8rem; font-weight: 600; color: var(--muted); }
        .info-row p { margin: 0; font-size: .95rem; line-height: 1.5; color: var(--text); }

        /* Stempel */
        .stamp {
            position: absolute; top: 24px; z-index: 6; padding: 7px 16px; border: 3px solid; border-radius: 14px;
            font-family: 'Bricolage Grotesque', sans-serif; font-weight: 800; font-size: 1.2rem;
            background: rgba(8,12,32,.55); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
            opacity: 0; pointer-events: none;
        }
        .stamp.yes { right: 22px; color: #5df2df; border-color: #5df2df; transform: rotate(10deg); box-shadow: 0 0 30px rgba(25,195,177,.6); }
        .stamp.no  { left: 22px; color: #ff8aa0; border-color: #ff8aa0; transform: rotate(-10deg); box-shadow: 0 0 30px rgba(255,92,122,.6); }

        /* ================= TOMBOL ================= */
        .act {
            position: relative; overflow: hidden; border: 0; display: grid; place-items: center; border-radius: 50%;
            width: 70px; height: 70px; color: var(--text);
            background: rgba(255,255,255,.09); border: 1px solid rgba(255,255,255,.18);
            backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 18px 36px -14px rgba(0,0,0,.7);
            transition: transform .25s cubic-bezier(.3,1.7,.5,1), background .25s, box-shadow .25s, color .25s;
        }
        .act:hover { transform: translateY(-6px) scale(1.1); }
        .act:active { transform: scale(.9); }
        .act:focus-visible { outline: 3px solid var(--amber); outline-offset: 4px; }
        .act.pass { color: #ff8aa0; }
        .act.pass:hover { background: var(--coral); color: #fff; box-shadow: 0 18px 40px -8px rgba(255,92,122,.75); }
        .act.connect { width: 82px; height: 82px; color: #fff; background: linear-gradient(145deg, #25e0cb, #129c91); border-color: rgba(255,255,255,.35); box-shadow: 0 18px 44px -10px rgba(25,195,177,.8); }
        .act.connect:hover { box-shadow: 0 22px 56px -6px rgba(25,195,177,1); }
        .halo { position: relative; }
        .halo::before {
            content: ''; position: absolute; left: 50%; top: 41px; width: 82px; height: 82px; margin: -41px 0 0 -41px;
            border-radius: 50%; border: 2px solid var(--teal); animation: halo 2.4s ease-out infinite; pointer-events: none;
        }
        @@keyframes halo { 0% { transform: scale(1); opacity: .8; } 100% { transform: scale(1.7); opacity: 0; } }
        .ripple {
            position: absolute; border-radius: 50%; background: rgba(255,255,255,.55); transform: scale(0);
            animation: ripple .6s ease-out forwards; pointer-events: none;
        }
        @@keyframes ripple { to { transform: scale(4); opacity: 0; } }
        .act-label { margin-top: 10px; display: block; font-size: .75rem; font-weight: 600; color: var(--muted); }
        kbd {
            display: inline-grid; place-items: center; min-width: 24px; height: 24px; padding: 0 6px; border-radius: 8px;
            font-family: inherit; font-size: .72rem; font-weight: 700; color: var(--text);
            background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.18); border-bottom-width: 2px;
        }

        /* ================= EMPTY ================= */
        .empty-radar {
            position: relative; width: 170px; height: 170px; margin: 0 auto 26px; border-radius: 50%; overflow: hidden;
            border: 2px solid rgba(25,195,177,.5);
            background: repeating-radial-gradient(circle, transparent 0 27px, rgba(25,195,177,.25) 27px 28px);
            box-shadow: 0 0 60px rgba(25,195,177,.3);
        }
        .empty-radar::after {
            content: ''; position: absolute; inset: 0;
            background: conic-gradient(from 0deg, rgba(25,195,177,0) 0deg, rgba(25,195,177,.65) 70deg, rgba(25,195,177,0) 72deg);
            animation: sweep 3s linear infinite;
        }
        .btn-scan {
            margin-top: 24px; padding: 13px 26px; border: 0; border-radius: 999px; font-size: .9rem; font-weight: 700; color: #08202a;
            background: linear-gradient(120deg, #5df2df, var(--amber)); box-shadow: 0 16px 36px -12px rgba(25,195,177,.8);
            transition: transform .25s cubic-bezier(.3,1.7,.5,1), box-shadow .25s;
        }
        .btn-scan:hover { transform: translateY(-3px) scale(1.04); box-shadow: 0 20px 44px -10px rgba(25,195,177,1); }

        /* ================= MATCH MODAL ================= */
        .match-modal .modal-content {
            border: 0; border-radius: 34px; overflow: hidden; text-align: center; color: var(--text);
            background: linear-gradient(160deg, #1a1650, #0c1230 70%);
            box-shadow: 0 40px 100px -20px rgba(124,92,255,.7), inset 0 1px 0 rgba(255,255,255,.2);
        }
        .match-modal .modal-body { position: relative; padding: 52px 28px 34px; overflow: hidden; }
        .match-modal .modal-body::before {
            content: ''; position: absolute; left: 50%; top: 40px; width: 420px; height: 420px; margin-left: -210px;
            background: conic-gradient(from 0deg, transparent, rgba(255,181,71,.25), transparent 25%, rgba(25,195,177,.25), transparent 55%, rgba(124,92,255,.3), transparent 80%);
            border-radius: 50%; filter: blur(20px); animation: spin 9s linear infinite; pointer-events: none;
        }
        .match-rings { position: relative; width: 130px; height: 130px; margin: 0 auto 24px; }
        .match-rings i { position: absolute; inset: 0; border-radius: 50%; border: 2px solid var(--amber); animation: ring 2.2s ease-out infinite; }
        .match-rings i:nth-child(2) { animation-delay: .7s; }
        .match-rings i:nth-child(3) { animation-delay: 1.4s; }
        @@keyframes ring { 0% { transform: scale(.4); opacity: .9; } 100% { transform: scale(1.7); opacity: 0; } }
        .match-core {
            position: absolute; inset: 26px; border-radius: 50%; display: grid; place-items: center; color: #1b1330;
            background: linear-gradient(145deg, #ffe3a3, var(--amber));
            box-shadow: 0 0 40px rgba(255,181,71,.8);
            animation: pop .8s cubic-bezier(.2,1.8,.4,1) both;
        }
        @@keyframes pop { 0% { transform: scale(0) rotate(-90deg); } 100% { transform: scale(1) rotate(0); } }
        .match-title {
            position: relative; font-family: 'Bricolage Grotesque', sans-serif; font-weight: 800; font-size: 1.6rem; line-height: 1.2;
            background: linear-gradient(110deg, #fff, #ffd592, #7ff0e3); -webkit-background-clip: text; background-clip: text; color: transparent;
        }
        .confetti { position: absolute; top: -14px; width: 8px; height: 14px; border-radius: 2px; animation: fall linear forwards; z-index: 3; }
        @@keyframes fall { to { transform: translateY(560px) rotate(620deg); opacity: 0; } }

        /* ================= LOADER ================= */
        #radar-loader {
            position: fixed; inset: 0; z-index: 9999; display: grid; place-items: center; align-content: center; gap: 30px;
            background:
                radial-gradient(900px circle at 50% 40%, rgba(124,92,255,.35), transparent 60%),
                radial-gradient(700px circle at 20% 90%, rgba(25,195,177,.25), transparent 60%),
                var(--night);
            clip-path: circle(150% at 50% 50%);
            transition: clip-path .85s cubic-bezier(.7,0,.2,1), opacity .5s ease .35s, visibility 0s linear .85s;
            animation: loader-failsafe .5s ease 6s forwards;
        }
        @@keyframes loader-failsafe { to { opacity: 0; visibility: hidden; } }
        #radar-loader.is-hidden { clip-path: circle(0% at 50% 50%); opacity: 0; visibility: hidden; pointer-events: none; animation: none; }
        #radar-loader.is-leaving {
            clip-path: circle(150% at 50% 50%); opacity: 1; visibility: visible; animation: none;
            transition: clip-path .55s cubic-bezier(.2,.7,.2,1), opacity .2s ease, visibility 0s;
        }
        .ld-radar {
            position: relative; width: 230px; height: 230px; border-radius: 50%; overflow: hidden;
            border: 2px solid rgba(25,195,177,.6);
            background: repeating-radial-gradient(circle, transparent 0 28px, rgba(25,195,177,.26) 28px 29px),
                        radial-gradient(circle, rgba(25,195,177,.22), transparent 70%);
            box-shadow: 0 0 80px rgba(25,195,177,.45), inset 0 0 40px rgba(25,195,177,.2);
        }
        .ld-radar::before, .ld-radar::after { content: ''; position: absolute; background: rgba(25,195,177,.3); }
        .ld-radar::before { left: 50%; top: 0; width: 1px; height: 100%; }
        .ld-radar::after { top: 50%; left: 0; height: 1px; width: 100%; }
        .ld-sweep {
            position: absolute; inset: 0; border-radius: 50%;
            background: conic-gradient(from 0deg, rgba(93,242,223,0) 0deg, rgba(93,242,223,.75) 75deg, rgba(93,242,223,0) 77deg);
            animation: sweep 1.8s linear infinite;
        }
        .ld-core {
            position: absolute; left: 50%; top: 50%; width: 12px; height: 12px; margin: -6px 0 0 -6px; border-radius: 50%; z-index: 2;
            background: var(--amber); animation: core-pulse 1.8s ease-out infinite;
        }
        @@keyframes core-pulse { 0% { box-shadow: 0 0 0 0 rgba(255,181,71,.7); } 100% { box-shadow: 0 0 0 30px rgba(255,181,71,0); } }
        .ld-blip {
            position: absolute; width: 9px; height: 9px; border-radius: 50%; background: var(--amber);
            box-shadow: 0 0 14px 3px rgba(255,181,71,.8); opacity: 0; animation: ld-blip 1.8s ease-out infinite;
        }
        @@keyframes ld-blip { 0%, 20% { opacity: 0; transform: scale(.3); } 28% { opacity: 1; transform: scale(1.4); } 80% { opacity: 0; } 100% { opacity: 0; } }
        .ld-text { font-family: 'Bricolage Grotesque', sans-serif; font-weight: 700; font-size: 1.1rem; text-align: center; color: #fff; }
        .ld-text small { display: block; margin-top: 5px; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 500; font-size: .8rem; color: var(--muted); }
        .ld-dots span { animation: dot-blink 1.2s infinite both; }
        .ld-dots span:nth-child(2) { animation-delay: .2s; }
        .ld-dots span:nth-child(3) { animation-delay: .4s; }
        @@keyframes dot-blink { 0%, 80%, 100% { opacity: .15; } 40% { opacity: 1; } }

        @@media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .001ms !important; animation-iteration-count: 1 !important; transition-duration: .001ms !important; }
        }
    </style>
</head>
<body class="is-loading min-h-screen font-body antialiased">

    <!-- Loader radar: tampil saat halaman dibuka, di-refresh, atau berpindah halaman -->
    <div id="radar-loader" role="status" aria-live="polite">
        <div class="ld-radar" aria-hidden="true">
            <div class="ld-sweep"></div>
            <span class="ld-blip" style="left:66%; top:30%; animation-delay:.15s"></span>
            <span class="ld-blip" style="left:28%; top:62%; animation-delay:.75s"></span>
            <span class="ld-blip" style="left:58%; top:74%; animation-delay:1.2s"></span>
            <span class="ld-blip" style="left:30%; top:28%; animation-delay:1.55s"></span>
            <span class="ld-core"></span>
        </div>
        <div class="ld-text">
            Memindai UMKM terdekat<span class="ld-dots"><span>.</span><span>.</span><span>.</span></span>
            <small>Nexus Radar</small>
        </div>
    </div>
    <script>
        (function () {
            var loader = document.getElementById('radar-loader');
            var shownAt = Date.now();
            var MIN_SHOW = 1200; // ms, supaya sapuan radar terlihat

            function hide(minWait) {
                var wait = Math.max(0, minWait - (Date.now() - shownAt));
                setTimeout(function () {
                    loader.classList.remove('is-leaving');
                    loader.classList.add('is-hidden');
                    document.body.classList.remove('is-loading');
                }, wait);
            }
            function show() {
                shownAt = Date.now();
                loader.classList.remove('is-hidden');
                loader.classList.add('is-leaving');
            }

            if (document.readyState === 'complete') { hide(MIN_SHOW); }
            else { window.addEventListener('load', function () { hide(MIN_SHOW); }); }

            window.addEventListener('beforeunload', show);
            window.addEventListener('pageshow', function (e) { if (e.persisted) hide(0); });
        })();
    </script>

    <!-- Latar -->
    <div class="aurora" aria-hidden="true"><i></i><i></i><i></i></div>
    <canvas id="stars" aria-hidden="true"></canvas>
    <div class="radar-bg" aria-hidden="true">
        <div class="radar-disc">
            <div class="radar-sweep"></div>
            <span class="blip" style="left:62%; top:34%; animation-delay:.8s"></span>
            <span class="blip" style="left:36%; top:58%; animation-delay:2.6s"></span>
            <span class="blip" style="left:55%; top:70%; animation-delay:4.4s"></span>
            <span class="blip" style="left:28%; top:30%; animation-delay:6s"></span>
        </div>
    </div>
    <div class="mood" aria-hidden="true"></div>

    <main class="relative z-10 mx-auto flex min-h-screen w-full max-w-md flex-col px-4 pb-8 pt-5">

        <!-- Header -->
        <header class="reveal mb-6 flex items-center justify-between" style="--d:-.4s">
            <div class="flex items-center gap-3">
                <!-- Tombol kembali ke halaman sebelumnya -->
                <a href="{{ route('dashboard') }}" id="back-btn" class="back-btn" aria-label="Kembali ke dashboard" data-bs-toggle="tooltip" data-bs-title="Kembali ke dashboard">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                </a>
                <div class="logo-mark">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4.5"/><path d="M12 12 18.5 5.5"/></svg>
                </div>
                <h1 class="brand text-xl">Nexus Radar</h1>
            </div>
            <div class="chip-live"><span class="live-dot"></span>Memindai UMKM</div>
        </header>

        @if($recommendation)
            <div class="relative flex flex-1 flex-col justify-center">
                <span class="ping-ring"></span>
                <span class="ping-ring r2"></span>
                <span class="ping-ring r3"></span>

                <!-- Tumpukan kartu -->
                <div class="stack card-enter">
                    <div class="ghost g2"></div>
                    <div class="ghost g1"></div>

                    <!-- Card Konten -->
                    <article id="swipe-card" class="swipe-card" aria-label="Rekomendasi UMKM: {{ $recommendation->name }}">
                        <div class="stamp yes" id="stamp-yes">Hubungkan</div>
                        <div class="stamp no" id="stamp-no">Lewati</div>

                        <div class="tilt" id="tilt">
                            <div class="glass">
                                <div class="spot"></div>

                                <!-- Foto Placeholder -->
                                <div class="cover" role="img" aria-label="Gambar/Logo: {{ $recommendation->name }}">
                                    <span class="orbit o2"></span>
                                    <span class="orbit o1"></span>
                                    <div class="monogram">{{ mb_strtoupper(mb_substr($recommendation->name, 0, 2)) }}</div>
                                </div>

                                <div class="px-6 pb-7 pt-5">
                                    <h2 class="name reveal" style="--d:0s">{{ $recommendation->name }}</h2>

                                    <div class="reveal mt-3 flex flex-wrap gap-2" style="--d:.1s">
                                        <span class="tag t1">{{ $recommendation->businessProfile?->category?->category_name ?? $recommendation->industry_sector ?? 'Belum ditentukan' }}</span>
                                        <span class="tag t2">{{ $recommendation->businessProfile?->employee_count !== null ? $recommendation->businessProfile->employee_count . ' karyawan' : 'Jumlah karyawan belum tersedia' }}</span>
                                    </div>

                                    <div class="mt-6 space-y-5">
                                        <div class="info-row reveal" style="--d:.2s">
                                            <div class="info-icon i1">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12v8H4v-8"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 1 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 1 0 0-5C13 2 12 7 12 7z"/></svg>
                                            </div>
                                            <div>
                                                <h3>Kami menawarkan</h3>
                                                <p>{{ $recommendation->offering_description }}</p>
                                            </div>
                                        </div>
                                        <div class="info-row reveal" style="--d:.3s">
                                            <div class="info-icon i2">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                                            </div>
                                            <div>
                                                <h3>Kami mencari</h3>
                                                <p>{{ $recommendation->seeking_description }}</p>
                                            </div>
                                        </div>
                                        <div class="info-row reveal" style="--d:.4s">
                                            <div class="info-icon i3">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                            </div>
                                            <div>
                                                <h3>Lokasi</h3>
                                                <p>{{ $recommendation->location }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Action Buttons -->
                <div class="reveal mt-14 flex items-start justify-center gap-10" style="--d:.55s">
                    <!-- Tombol Pass (Kiri) -->
                    <form action="{{ route('collaboration.swipe') }}" method="POST" id="form-pass" class="mt-2 text-center">
                        @csrf
                        <input type="hidden" name="swiped_id" value="{{ $recommendation->id }}">
                        <input type="hidden" name="is_interested" value="0">
                        <button type="submit" class="act pass mx-auto" aria-label="Lewati UMKM ini" data-bs-toggle="tooltip" data-bs-title="Lewati (←)">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </button>
                        <span class="act-label">Lewati</span>
                    </form>

                    <!-- Tombol Connect (Kanan) -->
                    <form action="{{ route('collaboration.swipe') }}" method="POST" id="form-connect" class="halo text-center">
                        @csrf
                        <input type="hidden" name="swiped_id" value="{{ $recommendation->id }}">
                        <input type="hidden" name="is_interested" value="1">
                        <button type="submit" class="act connect mx-auto" aria-label="Hubungkan dengan UMKM ini" data-bs-toggle="tooltip" data-bs-title="Hubungkan (→)">
                            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
                        </button>
                        <span class="act-label">Hubungkan</span>
                    </form>
                </div>

                <p class="reveal mt-7 hidden items-center justify-center gap-2 text-xs sm:flex" style="--d:.65s; color: var(--muted)">
                    <kbd>←</kbd> lewati <span class="mx-1 opacity-40">|</span> hubungkan <kbd>→</kbd>
                </p>
                <p class="reveal mt-7 text-center text-xs sm:hidden" style="--d:.65s; color: var(--muted)">Geser kartu ke kanan untuk terhubung, ke kiri untuk melewati.</p>
            </div>
        @else
            <!-- Jika tidak ada rekomendasi tersisa -->
            <div class="flex flex-1 items-center">
                <div class="card-enter glass w-full p-10 text-center">
                    <div class="empty-radar" aria-hidden="true"></div>
                    <h2 class="name reveal" style="--d:0s">Radar Kosong</h2>
                    <p class="reveal mx-auto mt-3 max-w-xs" style="--d:.15s; color: var(--muted)">Tidak ada UMKM baru di sekitar Anda. Coba lagi nanti atau perluas pencarian Anda.</p>
                    <button type="button" onclick="location.reload()" class="btn-scan reveal" style="--d:.3s">Pindai ulang</button>
                </div>
            </div>
        @endif
    </main>

    <!-- Notifikasi Match -->
    @if(session('match_success'))
        <div class="modal fade match-modal" id="matchModal" tabindex="-1" aria-labelledby="matchTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                <div class="modal-content">
                    <div class="modal-body" id="matchBody">
                        <div class="match-rings">
                            <i></i><i></i><i></i>
                            <div class="match-core">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M8 12h8"/><path d="M10 7H8a5 5 0 0 0 0 10h2"/><path d="M14 7h2a5 5 0 0 1 0 10h-2"/></svg>
                            </div>
                        </div>
                        <h2 id="matchTitle" class="match-title">{{ session('match_success') }}</h2>
                        <button type="button" class="btn-scan" style="position:relative" data-bs-dismiss="modal">Lanjut menjelajah</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            /* ---------- Tooltip Bootstrap ---------- */
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                new bootstrap.Tooltip(el, { trigger: 'hover' });
            });

            /* ---------- Tombol kembali ---------- */
            var backBtn = document.getElementById('back-btn');
            if (backBtn) {
                backBtn.addEventListener('click', function (e) {
                    // Jika ada riwayat browser, kembali persis ke halaman sebelumnya.
                    // Jika tidak (halaman dibuka langsung), pakai link href sebagai cadangan.
                    if (window.history.length > 1) {
                        e.preventDefault();
                        window.history.back();
                    }
                });
            }

            /* ---------- Partikel bintang & garis penghubung ---------- */
            (function () {
                var c = document.getElementById('stars');
                if (!c || reduceMotion) return;
                var ctx = c.getContext('2d');
                var dpr = Math.min(window.devicePixelRatio || 1, 2);
                var w, h, pts = [];

                function size() {
                    w = c.width = innerWidth * dpr;
                    h = c.height = innerHeight * dpr;
                    c.style.width = innerWidth + 'px';
                    c.style.height = innerHeight + 'px';
                    var n = Math.min(Math.max(Math.round(innerWidth * innerHeight / 16000), 30), 90);
                    pts = [];
                    for (var i = 0; i < n; i++) {
                        pts.push({
                            x: Math.random() * w, y: Math.random() * h,
                            vx: (Math.random() - .5) * .28 * dpr, vy: (Math.random() - .5) * .28 * dpr,
                            r: (Math.random() * 1.4 + .4) * dpr
                        });
                    }
                }
                function frame() {
                    ctx.clearRect(0, 0, w, h);
                    var max = 130 * dpr, i, j, p, q, d;
                    for (i = 0; i < pts.length; i++) {
                        p = pts[i];
                        p.x += p.vx; p.y += p.vy;
                        if (p.x < 0) p.x = w; if (p.x > w) p.x = 0;
                        if (p.y < 0) p.y = h; if (p.y > h) p.y = 0;
                        ctx.beginPath();
                        ctx.arc(p.x, p.y, p.r, 0, 6.283);
                        ctx.fillStyle = 'rgba(255,255,255,.6)';
                        ctx.fill();
                        for (j = i + 1; j < pts.length; j++) {
                            q = pts[j];
                            d = Math.hypot(p.x - q.x, p.y - q.y);
                            if (d < max) {
                                ctx.strokeStyle = 'rgba(25,195,177,' + ((1 - d / max) * .2) + ')';
                                ctx.lineWidth = dpr * .8;
                                ctx.beginPath();
                                ctx.moveTo(p.x, p.y);
                                ctx.lineTo(q.x, q.y);
                                ctx.stroke();
                            }
                        }
                    }
                    requestAnimationFrame(frame);
                }
                size();
                window.addEventListener('resize', size);
                frame();
            })();

            /* ---------- Ripple pada tombol ---------- */
            document.querySelectorAll('.act, .btn-scan, .back-btn').forEach(function (btn) {
                btn.addEventListener('pointerdown', function (e) {
                    var r = btn.getBoundingClientRect();
                    var s = Math.max(r.width, r.height);
                    var span = document.createElement('span');
                    span.className = 'ripple';
                    span.style.width = span.style.height = s + 'px';
                    span.style.left = (e.clientX - r.left - s / 2) + 'px';
                    span.style.top = (e.clientY - r.top - s / 2) + 'px';
                    btn.appendChild(span);
                    setTimeout(function () { span.remove(); }, 650);
                });
            });

            /* ---------- Modal Match + confetti ---------- */
            var matchEl = document.getElementById('matchModal');
            if (matchEl) {
                var colors = ['#FFB547', '#19C3B1', '#7C5CFF', '#FF5C7A', '#ffffff'];
                matchEl.addEventListener('shown.bs.modal', function () {
                    var body = document.getElementById('matchBody');
                    for (var i = 0; i < 44; i++) {
                        var c = document.createElement('span');
                        c.className = 'confetti';
                        c.style.left = Math.random() * 100 + '%';
                        c.style.background = colors[i % colors.length];
                        c.style.animationDuration = (1.6 + Math.random() * 1.8) + 's';
                        c.style.animationDelay = (Math.random() * .6) + 's';
                        body.appendChild(c);
                        setTimeout(function (n) { n.remove(); }, 4200, c);
                    }
                });
                // Tampilkan setelah loader radar selesai membuka layar
                setTimeout(function () { new bootstrap.Modal(matchEl).show(); }, 1900);
            }

            /* ---------- Kartu: tilt 3D, spotlight, swipe ---------- */
            var card = document.getElementById('swipe-card');
            if (!card) return;

            var tilt = document.getElementById('tilt');
            var formPass = document.getElementById('form-pass');
            var formConnect = document.getElementById('form-connect');
            var stampYes = document.getElementById('stamp-yes');
            var stampNo = document.getElementById('stamp-no');
            var root = document.documentElement;
            var THRESHOLD = 110;
            var startX = 0, dx = 0, dragging = false, locked = false;

            // Tilt + spotlight mengikuti kursor (mouse saja)
            card.addEventListener('pointermove', function (e) {
                if (e.pointerType !== 'mouse' || dragging || locked || reduceMotion) return;
                var r = card.getBoundingClientRect();
                var px = (e.clientX - r.left) / r.width;
                var py = (e.clientY - r.top) / r.height;
                tilt.style.setProperty('--ry', ((px - .5) * 12) + 'deg');
                tilt.style.setProperty('--rx', ((.5 - py) * 10) + 'deg');
                tilt.style.setProperty('--mx', (px * 100) + '%');
                tilt.style.setProperty('--my', (py * 100) + '%');
            });
            card.addEventListener('pointerleave', function () {
                tilt.style.setProperty('--rx', '0deg');
                tilt.style.setProperty('--ry', '0deg');
            });

            function mood(x) {
                var p = Math.min(Math.abs(x) / THRESHOLD, 1);
                root.style.setProperty('--mood-c', x > 0 ? 'rgba(25,195,177,.55)' : 'rgba(255,92,122,.55)');
                root.style.setProperty('--mood-o', (p * .9).toFixed(3));
            }
            function paint(x) {
                card.style.transform = 'translateX(' + x + 'px) rotate(' + (x / 18) + 'deg)';
                var p = Math.min(Math.abs(x) / THRESHOLD, 1);
                stampYes.style.opacity = x > 0 ? p : 0;
                stampNo.style.opacity = x < 0 ? p : 0;
                mood(x);
            }

            // Semburan partikel saat keputusan diambil
            function burst(direction) {
                if (reduceMotion) return;
                var r = card.getBoundingClientRect();
                var cx = r.left + r.width / 2, cy = r.top + r.height / 2;
                var pal = direction === 'connect'
                    ? ['#19C3B1', '#5df2df', '#FFB547', '#ffffff']
                    : ['#FF5C7A', '#ff8aa0', '#7C5CFF', '#ffffff'];
                for (var i = 0; i < 34; i++) {
                    var el = document.createElement('span');
                    var size = 5 + Math.random() * 7;
                    var ang = Math.random() * Math.PI * 2;
                    var dist = 90 + Math.random() * 220;
                    el.style.cssText = 'position:fixed;z-index:9000;pointer-events:none;border-radius:50%;' +
                        'left:' + cx + 'px;top:' + cy + 'px;width:' + size + 'px;height:' + size + 'px;' +
                        'background:' + pal[i % pal.length] + ';box-shadow:0 0 12px ' + pal[i % pal.length];
                    document.body.appendChild(el);
                    var anim = el.animate([
                        { transform: 'translate(-50%,-50%) scale(1)', opacity: 1 },
                        { transform: 'translate(' + (Math.cos(ang) * dist) + 'px,' + (Math.sin(ang) * dist) + 'px) scale(0)', opacity: 0 }
                    ], { duration: 650 + Math.random() * 550, easing: 'cubic-bezier(.1,.8,.3,1)' });
                    anim.onfinish = (function (n) { return function () { n.remove(); }; })(el);
                }
            }

            function decide(direction) {
                if (locked) return;
                locked = true;
                var sign = direction === 'connect' ? 1 : -1;
                card.classList.remove('dragging');
                tilt.style.setProperty('--rx', '0deg');
                tilt.style.setProperty('--ry', '0deg');
                mood(sign);
                root.style.setProperty('--mood-o', '.9');
                burst(direction);
                card.style.transition = 'transform .6s cubic-bezier(.5,0,.75,0), opacity .6s ease';
                card.style.transform = 'translateX(' + (sign * window.innerWidth) + 'px) rotate(' + (sign * 28) + 'deg)';
                card.style.opacity = '0';
                (sign > 0 ? stampYes : stampNo).style.opacity = 1;
                setTimeout(function () {
                    (direction === 'connect' ? formConnect : formPass).submit();
                }, 520);
            }

            // Tombol: animasikan dulu, baru kirim form
            [[formPass, 'pass'], [formConnect, 'connect']].forEach(function (pair) {
                pair[0].addEventListener('submit', function (e) {
                    if (!locked) { e.preventDefault(); decide(pair[1]); }
                });
            });

            // Gestur drag / sentuh
            card.addEventListener('pointerdown', function (e) {
                if (locked) return;
                dragging = true;
                startX = e.clientX;
                dx = 0;
                card.classList.add('dragging');
                tilt.style.setProperty('--rx', '0deg');
                tilt.style.setProperty('--ry', '0deg');
                card.setPointerCapture(e.pointerId);
            });
            card.addEventListener('pointermove', function (e) {
                if (!dragging) return;
                dx = e.clientX - startX;
                paint(dx);
            });
            function release() {
                if (!dragging) return;
                dragging = false;
                card.classList.remove('dragging');
                if (Math.abs(dx) > THRESHOLD) {
                    decide(dx > 0 ? 'connect' : 'pass');
                } else {
                    card.style.transform = '';
                    stampYes.style.opacity = 0;
                    stampNo.style.opacity = 0;
                    root.style.setProperty('--mood-o', '0');
                }
            }
            card.addEventListener('pointerup', release);
            card.addEventListener('pointercancel', release);

            // Keyboard
            document.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowRight') decide('connect');
                if (e.key === 'ArrowLeft') decide('pass');
            });
        });
    </script>
</body>
</html>