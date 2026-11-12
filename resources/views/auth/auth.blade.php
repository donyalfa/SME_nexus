{{--
    resources/views/auth/auth.blade.php
    Login + Register dalam satu halaman, dengan panel geser dan animasi.
    Tanpa library tambahan: hanya HTML, CSS, dan JavaScript biasa.
--}}
@php
    // Mode awal: dari old input (setelah gagal validasi), lalu dari route, lalu default login.
    $mode = old('form', $mode ?? (request()->routeIs('register') ? 'register' : 'login'));
    $loginUrl    = Route::has('login') ? route('login') : '#';
    $registerUrl = Route::has('register') ? route('register') : '#';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $mode === 'register' ? 'Daftar' : 'Masuk' }} - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink: #171A44;
            --indigo: #4B4FE0;
            --peri: #7C8CFF;
            --mint: #4FE3C1;
            --sun: #FFB36B;
            --paper: #EEF0FF;
            --field: #F6F7FF;
            --line: #DADDF4;
            --muted: #666B92;
            --danger: #D93A41;
            --ease: cubic-bezier(.77, 0, .18, 1);
            --font-display: 'Bricolage Grotesque', 'Segoe UI', sans-serif;
            --font-body: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; }

        body {
            min-height: 100vh;
            min-height: 100dvh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: var(--font-body);
            color: var(--ink);
            background:
                radial-gradient(900px 500px at 10% 0%, #fff 0%, transparent 60%),
                var(--paper);
        }

        /* ---------- Kartu utama ---------- */
        .auth {
            position: relative;
            width: min(920px, 100%);
            min-height: 640px;
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 70px -30px rgba(23, 26, 68, .35);
        }

        /* ---------- Panel form ---------- */
        .pane {
            position: absolute;
            top: 0; left: 0;
            width: 50%; height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 36px 44px;
            overflow-y: auto;
            background: #fff;
            transition: transform .8s var(--ease), opacity .45s ease, visibility 0s linear .8s;
        }
        .pane.register { opacity: 0; visibility: hidden; pointer-events: none; }

        .auth.is-register .pane.login {
            transform: translateX(100%);
            opacity: 0; visibility: hidden; pointer-events: none;
        }
        .auth.is-register .pane.register {
            transform: translateX(100%);
            opacity: 1; visibility: visible; pointer-events: auto;
            transition: transform .8s var(--ease), opacity .6s ease .25s, visibility 0s;
        }

        .brand-m { display: none; }

        .pane h1 {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 2.1rem;
            letter-spacing: -.02em;
            line-height: 1.1;
        }
        .pane .sub {
            margin: 10px 0 26px;
            color: var(--muted);
            font-size: .95rem;
            line-height: 1.55;
        }

        /* ---------- Input dengan label mengambang ---------- */
        .field { position: relative; margin-bottom: 14px; }
        .field input[type="text"],
        .field input[type="email"],
        .field input[type="password"] {
            width: 100%;
            height: 56px;
            padding: 22px 48px 8px 16px;
            font: 500 .98rem var(--font-body);
            color: var(--ink);
            background: var(--field);
            border: 1.5px solid var(--line);
            border-radius: 14px;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .field label {
            position: absolute;
            left: 17px; top: 50%;
            transform: translateY(-50%);
            font-size: .95rem;
            color: var(--muted);
            pointer-events: none;
            transition: top .2s, font-size .2s, color .2s, transform .2s;
        }
        .field input:focus,
        .field input:not(:placeholder-shown) { background: #fff; }
        .field input:focus {
            border-color: var(--indigo);
            box-shadow: 0 0 0 4px rgba(75, 79, 224, .14);
        }
        .field input:focus + label,
        .field input:not(:placeholder-shown) + label {
            top: 17px;
            transform: none;
            font-size: .72rem;
            font-weight: 600;
        }
        .field input:focus + label { color: var(--indigo); }

        .eye {
            position: absolute;
            right: 8px; top: 8px;
            width: 40px; height: 40px;
            display: grid; place-items: center;
            border: 0; border-radius: 10px;
            background: transparent;
            color: var(--muted);
            cursor: pointer;
            transition: background .2s, color .2s;
        }
        .eye:hover { background: rgba(75, 79, 224, .08); color: var(--indigo); }
        .eye:focus-visible { outline: 2px solid var(--indigo); outline-offset: 1px; }
        .eye svg { width: 20px; height: 20px; }
        .eye .off { display: none; }
        .eye[aria-pressed="true"] .on { display: none; }
        .eye[aria-pressed="true"] .off { display: block; }

        .field.shake { animation: shake .45s; }
        .field.shake input { border-color: var(--danger); }

        /* ---------- Indikator kekuatan kata sandi ---------- */
        .meter { margin: -4px 0 14px; display: none; }
        .meter.show { display: block; }
        .bars { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; }
        .bars i {
            height: 5px; border-radius: 99px;
            background: var(--line);
            transition: background .3s, transform .3s;
            transform-origin: left;
        }
        .meter[data-score="1"] .bars i:nth-child(-n+1) { background: var(--danger); }
        .meter[data-score="2"] .bars i:nth-child(-n+2) { background: var(--sun); }
        .meter[data-score="3"] .bars i:nth-child(-n+3) { background: var(--peri); }
        .meter[data-score="4"] .bars i:nth-child(-n+4) { background: var(--mint); }
        .meter p { margin-top: 6px; font-size: .78rem; color: var(--muted); }

        /* ---------- Checkbox ---------- */
        .row {
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px; margin: 4px 0 20px;
            font-size: .88rem;
        }
        .check { display: flex; align-items: center; gap: 10px; cursor: pointer; color: var(--muted); line-height: 1.4; }
        .check input { position: absolute; opacity: 0; width: 1px; height: 1px; }
        .check .box {
            flex: none;
            width: 20px; height: 20px;
            display: grid; place-items: center;
            border: 1.5px solid var(--line);
            border-radius: 6px;
            background: #fff;
            transition: background .2s, border-color .2s, transform .2s;
        }
        .check .box svg { width: 12px; height: 12px; stroke: #fff; stroke-width: 3; fill: none; stroke-linecap: round; stroke-linejoin: round; stroke-dasharray: 20; stroke-dashoffset: 20; transition: stroke-dashoffset .3s ease .05s; }
        .check input:checked + .box { background: var(--indigo); border-color: var(--indigo); transform: scale(1.06); }
        .check input:checked + .box svg { stroke-dashoffset: 0; }
        .check input:focus-visible + .box { outline: 2px solid var(--indigo); outline-offset: 2px; }

        a { color: var(--indigo); font-weight: 600; text-decoration: none; }
        a:hover { text-decoration: underline; }
        a:focus-visible, .linklike:focus-visible { outline: 2px solid var(--indigo); outline-offset: 3px; border-radius: 4px; }

        /* ---------- Tombol utama ---------- */
        .btn {
            position: relative;
            width: 100%; height: 54px;
            border: 0; border-radius: 14px;
            font: 600 1rem var(--font-body);
            color: #fff;
            background: linear-gradient(120deg, var(--indigo), #6A5CFF 55%, var(--indigo));
            background-size: 200% 100%;
            cursor: pointer;
            overflow: hidden;
            transition: background-position .6s ease, transform .15s, box-shadow .25s;
            box-shadow: 0 14px 28px -14px rgba(75, 79, 224, .75);
        }
        .btn:hover { background-position: 100% 0; box-shadow: 0 18px 32px -14px rgba(75, 79, 224, .9); }
        .btn:active { transform: scale(.985); }
        .btn:focus-visible { outline: 3px solid var(--mint); outline-offset: 3px; }
        .btn .label { transition: opacity .2s, transform .3s; display: inline-block; }
        .btn .spin {
            position: absolute; inset: 0; margin: auto;
            width: 22px; height: 22px;
            border: 3px solid rgba(255, 255, 255, .35);
            border-top-color: #fff;
            border-radius: 50%;
            opacity: 0;
            animation: spin .8s linear infinite;
        }
        .btn.is-loading { pointer-events: none; }
        .btn.is-loading .label { opacity: 0; transform: translateY(-8px); }
        .btn.is-loading .spin { opacity: 1; }

        .alert {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: .88rem;
            line-height: 1.45;
            color: #8E1F25;
            background: #FDECEE;
            border: 1px solid #F7C7CB;
            animation: shake .45s;
        }
        .alert.ok { color: #0F6B55; background: #E5FAF4; border-color: #B6EEDF; animation: none; }

        .swap-m { display: none; margin-top: 22px; text-align: center; font-size: .9rem; color: var(--muted); }
        .linklike { border: 0; background: none; padding: 0; font: 600 inherit var(--font-body); color: var(--indigo); cursor: pointer; }

        /* ---------- Panel geser (momen utama animasi) ---------- */
        .overlay {
            position: absolute;
            top: 0; left: 50%;
            width: 50%; height: 100%;
            z-index: 5;
            overflow: hidden;
            color: #fff;
            background: linear-gradient(160deg, #2A2EB5 0%, #1B1E63 55%, var(--ink) 100%);
            transition: transform .8s var(--ease);
        }
        .auth.is-register .overlay { transform: translateX(-100%); }

        .orbs {
            position: absolute; inset: 0;
            transform: translate(calc(var(--mx, 0) * 22px), calc(var(--my, 0) * 22px));
            transition: transform .5s ease-out;
        }
        .orb {
            position: absolute;
            filter: blur(38px);
            will-change: transform, border-radius;
            animation: morph 13s ease-in-out infinite alternate, drift 17s ease-in-out infinite alternate;
        }
        .orb.a { width: 320px; height: 320px; top: -80px; right: -90px; background: var(--peri); opacity: .75; }
        .orb.b { width: 280px; height: 280px; bottom: -70px; left: -80px; background: var(--mint); opacity: .55; animation-duration: 16s, 21s; animation-delay: -4s; }
        .orb.c { width: 190px; height: 190px; top: 42%; left: 38%; background: var(--sun); opacity: .5; animation-duration: 11s, 14s; animation-delay: -7s; }

        .ring {
            position: absolute;
            left: 50%; top: 50%;
            width: 120px; height: 120px;
            margin: -60px 0 0 -60px;
            border: 1.5px solid rgba(255, 255, 255, .28);
            border-radius: 50%;
            animation: ring 7s ease-out infinite;
        }
        .ring:nth-child(2) { animation-delay: 2.3s; }
        .ring:nth-child(3) { animation-delay: 4.6s; }

        .brand {
            position: absolute;
            top: 36px; left: 48px;
            z-index: 2;
            display: flex; align-items: center; gap: 10px;
            font: 800 1.15rem var(--font-display);
            letter-spacing: -.01em;
        }
        .brand svg { width: 30px; height: 30px; }

        .copy {
            position: absolute; inset: 0;
            z-index: 2;
            display: flex; flex-direction: column; justify-content: center;
            padding: 56px 52px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .35s ease;
        }
        .auth:not(.is-register) .copy.go-register,
        .auth.is-register .copy.go-login { opacity: 1; pointer-events: auto; transition-delay: .3s; }

        .copy h2 {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 2.6rem;
            line-height: 1.04;
            letter-spacing: -.03em;
            margin-bottom: 16px;
        }
        .copy p { max-width: 30ch; color: rgba(255, 255, 255, .78); line-height: 1.6; margin-bottom: 30px; }

        .w { display: inline-block; overflow: hidden; vertical-align: top; padding-bottom: .14em; margin-bottom: -.14em; }
        .c { display: inline-block; transform: translateY(115%); opacity: 0; }
        .copy.play .c {
            animation: rise .75s cubic-bezier(.2, .8, .2, 1) forwards;
            animation-delay: calc(var(--i) * 24ms + 380ms);
        }
        .copy .ghost, .copy p { opacity: 0; transform: translateY(12px); }
        .copy.play p { animation: fadeUp .6s ease forwards .7s; }
        .copy.play .ghost { animation: fadeUp .6s ease forwards .85s; }

        .ghost {
            align-self: flex-start;
            height: 50px; padding: 0 30px;
            font: 600 .98rem var(--font-body);
            color: #fff;
            background: rgba(255, 255, 255, .08);
            border: 1.5px solid rgba(255, 255, 255, .7);
            border-radius: 99px;
            backdrop-filter: blur(6px);
            cursor: pointer;
            transition: background .25s, color .25s, border-color .25s;
        }
        .ghost:hover { background: #fff; color: var(--ink); border-color: #fff; }
        .ghost:focus-visible { outline: 3px solid var(--mint); outline-offset: 3px; }

        /* ---------- Keyframes ---------- */
        @keyframes morph {
            0%   { border-radius: 42% 58% 63% 37% / 45% 41% 59% 55%; }
            50%  { border-radius: 62% 38% 35% 65% / 58% 62% 38% 42%; }
            100% { border-radius: 38% 62% 55% 45% / 36% 55% 45% 64%; }
        }
        @keyframes drift {
            from { transform: translate(0, 0) rotate(0) scale(1); }
            to   { transform: translate(46px, -52px) rotate(50deg) scale(1.18); }
        }
        @keyframes ring {
            0%   { transform: scale(.6); opacity: .9; }
            100% { transform: scale(4.6); opacity: 0; }
        }
        @keyframes rise   { to { transform: translateY(0); opacity: 1; } }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
        @keyframes spin   { to { transform: rotate(360deg); } }
        @keyframes shake {
            10%, 90% { transform: translateX(-2px); }
            20%, 80% { transform: translateX(4px); }
            30%, 50%, 70% { transform: translateX(-7px); }
            40%, 60% { transform: translateX(7px); }
        }

        /* ---------- Mobile: panel geser diganti pergantian form ---------- */
        @media (max-width: 860px) {
            body { 
                padding: 16px 12px; 
                align-items: center;
                min-height: 100vh;
                min-height: 100dvh;
            }
            .auth { 
                min-height: auto; 
                border-radius: 20px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                background: #fff;
            }
            .overlay { display: none; }
            .swap-m { display: block; }

            .pane,
            .pane.register,
            .auth.is-register .pane.login,
            .auth.is-register .pane.register {
                position: relative;
                width: 100%; 
                min-height: 100vh;
                min-height: 100dvh;
                transform: none;
                padding: 32px 20px;
                overflow: visible;
                transition: none;
                display: flex;
                flex-direction: column;
                justify-content: center;
                background: #fff;
            }
            .pane { display: none; opacity: 1; visibility: visible; pointer-events: auto; }
            .auth:not(.is-register) .pane.login,
            .auth.is-register .pane.register { 
                display: flex; 
                animation: fadeUp .5s ease both; 
            }
            .pane { --from: 14px; }
            @keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }

            .brand-m { 
                display: flex; 
                align-items: center; 
                gap: 8px; 
                margin-bottom: 16px; 
                font: 800 1rem var(--font-display); 
                color: var(--indigo); 
            }
            .brand-m svg { width: 24px; height: 24px; }
            .pane h1 { font-size: 1.6rem; }
            .pane .sub { margin: 6px 0 20px; font-size: .9rem; }
            .field input[type="text"],
            .field input[type="email"],
            .field input[type="password"] {
                height: 52px;
                font-size: .95rem;
            }
            .btn { height: 50px; font-size: .95rem; }
        }

        /* ---------- Hormati preferensi kurangi gerakan ---------- */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .001ms !important; animation-iteration-count: 1 !important; transition-duration: .001ms !important; transition-delay: 0s !important; }
            .c { transform: none; opacity: 1; }
            .copy p, .copy .ghost { opacity: 1; transform: none; }
            .orbs { transform: none; }
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            margin-bottom: 24px;
            color: var(--muted);
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            transition: color .2s, transform .2s;
        }

        .back-home:hover {
            color: var(--indigo);
            text-decoration: none;
            transform: translateX(-3px);
        }

        .back-home svg {
            width: 17px;
            height: 17px;
            stroke: currentColor;
        }
    </style>
</head>
<body>

<main class="auth {{ $mode === 'register' ? 'is-register' : '' }}" id="auth"
      data-login-url="{{ $loginUrl }}" data-register-url="{{ $registerUrl }}">

    {{-- ========== FORM LOGIN ========== --}}
    <section class="pane login" id="pane-login" aria-labelledby="t-login" @if($mode === 'register') aria-hidden="true" @endif>
        <div class="brand-m">
            <svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><circle cx="16" cy="16" r="14" stroke="currentColor" stroke-width="2.5"/><path d="M7 19c4-8 14-8 18 0" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
            {{ config('app.name') }}
        </div>

        <a href="{{ url('/') }}" class="back-home">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5"/>
                <path d="M12 19l-7-7 7-7"/>
            </svg>
            Kembali ke Beranda
        </a>

        <h1 id="t-login">Masuk ke akun Anda</h1>
        <p class="sub">Lanjutkan dari terakhir kali Anda berhenti.</p>

        @if (session('status'))
            <div class="alert ok" role="status">{{ session('status') }}</div>
        @endif
        @if (session('csrf_expired'))
            <div class="alert ok" role="status">{{ session('csrf_expired') }}</div>
        @endif
        @if ($errors->any() && old('form') === 'login')
            <div class="alert" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ $loginUrl }}" data-auth-form>
            @csrf
            <input type="hidden" name="form" value="login">

            <div class="field">
                <input type="email" id="login-email" name="email" placeholder=" " autocomplete="email" maxlength="100" required
                       value="{{ old('form') === 'login' ? old('email') : '' }}">
                <label for="login-email">Email</label>
            </div>

            <div class="field">
                <input type="password" id="login-password" name="password" placeholder=" " autocomplete="current-password" required>
                <label for="login-password">Kata sandi</label>
                <button type="button" class="eye" aria-label="Tampilkan kata sandi" aria-pressed="false" data-eye>
                    <svg class="on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4.2M6.6 6.6A17 17 0 0 0 2 12s3.6 7 10 7c1.7 0 3.2-.4 4.5-1"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                </button>
            </div>

            <div class="row">
                <label class="check">
                    <input type="checkbox" name="remember" value="1">
                    <span class="box"><svg viewBox="0 0 12 12"><path d="M2 6.5l2.8 2.8L10 3.5"/></svg></span>
                    Ingat saya
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Lupa kata sandi?</a>
                @endif
            </div>

            <button type="submit" class="btn"><span class="label">Masuk</span><span class="spin" aria-hidden="true"></span></button>
        </form>

        <p class="swap-m">Belum punya akun? <button type="button" class="linklike" data-switch="register">Daftar</button></p>
    </section>

    {{-- ========== FORM REGISTER ========== --}}
    <section class="pane register" id="pane-register" aria-labelledby="t-register" @if($mode !== 'register') aria-hidden="true" @endif>
        <div class="brand-m">
            <svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><circle cx="16" cy="16" r="14" stroke="currentColor" stroke-width="2.5"/><path d="M7 19c4-8 14-8 18 0" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
            {{ config('app.name') }}
        </div>

        <a href="{{ url('/') }}" class="back-home">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5"/>
                <path d="M12 19l-7-7 7-7"/>
            </svg>
            Kembali ke Beranda
        </a>
        
        <h1 id="t-register">Buat akun baru</h1>
        <p class="sub">Cukup satu menit, lalu Anda bisa langsung mulai.</p>

        @if ($errors->any() && old('form') === 'register')
            <div class="alert" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ $registerUrl }}" data-auth-form>
            @csrf
            <input type="hidden" name="form" value="register">

            <div class="field">
                <input type="text" id="reg-name" name="full_name" placeholder=" " autocomplete="name" maxlength="100" required
                       value="{{ old('form') === 'register' ? old('full_name') : '' }}">
                <label for="reg-name">Nama lengkap</label>
            </div>

            <div class="field">
                <input type="email" id="reg-email" name="email" placeholder=" " autocomplete="email" maxlength="100" required
                       value="{{ old('form') === 'register' ? old('email') : '' }}">
                <label for="reg-email">Email</label>
            </div>

            <div class="field">
                <input type="password" id="reg-password" name="password" placeholder=" " autocomplete="new-password" minlength="8" required>
                <label for="reg-password">Kata sandi (min. 8 karakter)</label>
                <button type="button" class="eye" aria-label="Tampilkan kata sandi" aria-pressed="false" data-eye>
                    <svg class="on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4.2M6.6 6.6A17 17 0 0 0 2 12s3.6 7 10 7c1.7 0 3.2-.4 4.5-1"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                </button>
            </div>

            <div class="meter" id="meter" data-score="0" aria-live="polite">
                <div class="bars"><i></i><i></i><i></i><i></i></div>
                <p id="meter-text">Gunakan huruf besar, angka, dan simbol agar lebih kuat.</p>
            </div>

            <div class="field">
                <input type="password" id="reg-password2" name="password_confirmation" placeholder=" " autocomplete="new-password" required>
                <label for="reg-password2">Ulangi kata sandi</label>
            </div>

            <div class="row" style="margin-top:2px">
                <label class="check">
                    <input type="checkbox" name="terms" value="1" required>
                    <span class="box"><svg viewBox="0 0 12 12"><path d="M2 6.5l2.8 2.8L10 3.5"/></svg></span>
                    <span>Saya setuju dengan syarat dan ketentuan.</span>
                </label>
            </div>

            <button type="submit" class="btn"><span class="label">Buat akun</span><span class="spin" aria-hidden="true"></span></button>
        </form>

        <p class="swap-m">Sudah punya akun? <button type="button" class="linklike" data-switch="login">Masuk</button></p>
    </section>

    {{-- ========== PANEL GESER ========== --}}
    <aside class="overlay" aria-label="Pilihan masuk atau daftar">
        <div class="orbs" aria-hidden="true">
            <span class="orb a"></span><span class="orb b"></span><span class="orb c"></span>
            <span class="ring"></span><span class="ring"></span><span class="ring"></span>
        </div>

        <div class="brand">
            <svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><circle cx="16" cy="16" r="14" stroke="#fff" stroke-width="2.5"/><path d="M7 19c4-8 14-8 18 0" stroke="#4FE3C1" stroke-width="2.5" stroke-linecap="round"/></svg>
            {{ config('app.name') }}
        </div>

        <div class="copy go-register {{ $mode === 'register' ? '' : 'play' }}">
            <h2 data-split>Belum punya akun?</h2>
            <p>Daftar gratis dan mulai gunakan {{ config('app.name') }} hari ini.</p>
            <button type="button" class="ghost" data-switch="register">Daftar</button>
        </div>

        <div class="copy go-login {{ $mode === 'register' ? 'play' : '' }}">
            <h2 data-split>Sudah punya akun?</h2>
            <p>Masuk untuk melanjutkan pekerjaan Anda.</p>
            <button type="button" class="ghost" data-switch="login">Masuk</button>
        </div>
    </aside>
</main>

<script>
(function () {
    var auth = document.getElementById('auth');
    var paneLogin = document.getElementById('pane-login');
    var paneRegister = document.getElementById('pane-register');
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* 1. Pecah judul panel menjadi huruf agar bisa muncul satu per satu */
    document.querySelectorAll('[data-split]').forEach(function (el) {
        var text = el.textContent.trim(), i = 0;
        el.textContent = '';
        el.setAttribute('aria-label', text);
        text.split(' ').forEach(function (word, wi, arr) {
            var w = document.createElement('span');
            w.className = 'w';
            w.setAttribute('aria-hidden', 'true');
            Array.from(word).forEach(function (ch) {
                var c = document.createElement('span');
                c.className = 'c';
                c.textContent = ch;
                c.style.setProperty('--i', i++);
                w.appendChild(c);
            });
            el.appendChild(w);
            if (wi < arr.length - 1) el.appendChild(document.createTextNode(' '));
        });
    });

    /* 2. Pindah antara login dan register */
    function setMode(mode, firstLoad) {
        var toRegister = mode === 'register';
        auth.classList.toggle('is-register', toRegister);
        paneLogin.setAttribute('aria-hidden', toRegister ? 'true' : 'false');
        paneRegister.setAttribute('aria-hidden', toRegister ? 'false' : 'true');
        paneLogin.inert = toRegister;
        paneRegister.inert = !toRegister;

        var incoming = auth.querySelector(toRegister ? '.copy.go-login' : '.copy.go-register');
        var outgoing = auth.querySelector(toRegister ? '.copy.go-register' : '.copy.go-login');
        outgoing.classList.remove('play');
        incoming.classList.remove('play');
        void incoming.offsetWidth; // paksa reflow supaya animasi huruf diputar ulang
        incoming.classList.add('play');

        var url = auth.dataset[toRegister ? 'registerUrl' : 'loginUrl'];
        document.title = (toRegister ? 'Daftar' : 'Masuk') + ' - ' + document.title.split(' - ').pop();
        if (!firstLoad && url && url !== '#') history.replaceState(null, '', url);

        if (!firstLoad) {
            var target = (toRegister ? paneRegister : paneLogin).querySelector('input:not([type=hidden])');
            setTimeout(function () { target && target.focus({ preventScroll: true }); }, reduce ? 0 : 650);
        }
    }
    document.querySelectorAll('[data-switch]').forEach(function (b) {
        b.addEventListener('click', function () { setMode(b.dataset.switch); });
    });
    setMode(auth.classList.contains('is-register') ? 'register' : 'login', true);

    /* 3. Lihat / sembunyikan kata sandi */
    document.querySelectorAll('[data-eye]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = btn.parentElement.querySelector('input');
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.setAttribute('aria-pressed', show);
            btn.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    });

    /* 4. Kekuatan kata sandi */
    var pass = document.getElementById('reg-password');
    var pass2 = document.getElementById('reg-password2');
    var meter = document.getElementById('meter');
    var meterText = document.getElementById('meter-text');
    var labels = ['', 'Lemah', 'Cukup', 'Kuat', 'Sangat kuat'];

    function score(v) {
        var s = 0;
        if (v.length >= 8) s++;
        if (/[A-Z]/.test(v) && /[a-z]/.test(v)) s++;
        if (/\d/.test(v)) s++;
        if (/[^A-Za-z0-9]/.test(v) || v.length >= 14) s++;
        return v ? Math.max(s, 1) : 0;
    }
    pass.addEventListener('input', function () {
        var s = score(pass.value);
        meter.classList.toggle('show', s > 0);
        meter.dataset.score = s;
        meterText.textContent = s ? 'Kekuatan kata sandi: ' + labels[s] : '';
        checkMatch();
    });
    function checkMatch() {
        pass2.setCustomValidity(pass2.value && pass2.value !== pass.value ? 'Kata sandi tidak sama.' : '');
    }
    pass2.addEventListener('input', checkMatch);

    /* 5. Goyang kolom yang salah + status loading saat kirim */
    document.querySelectorAll('[data-auth-form]').forEach(function (form) {
        var sending = false;

        form.addEventListener('invalid', function (e) {
            var f = e.target.closest('.field');
            if (!f) return;
            f.classList.remove('shake');
            void f.offsetWidth;
            f.classList.add('shake');
            f.addEventListener('animationend', function () { f.classList.remove('shake'); }, { once: true });
        }, true);

        form.addEventListener('submit', function (e) {
            if (sending) { e.preventDefault(); return; }
            sending = true;
            var btn = form.querySelector('.btn');
            btn.classList.add('is-loading');
            btn.setAttribute('aria-busy', 'true');
        });
    });

    /* Kembalikan tombol jika halaman dibuka lagi lewat tombol "kembali" browser */
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) location.reload();
    });

    /* 6. Gerakan halus pada bola warna mengikuti kursor (hanya desktop) */
    if (!reduce && window.matchMedia('(min-width: 861px)').matches) {
        auth.addEventListener('pointermove', function (e) {
            var r = auth.getBoundingClientRect();
            auth.style.setProperty('--mx', ((e.clientX - r.left) / r.width - .5).toFixed(3));
            auth.style.setProperty('--my', ((e.clientY - r.top) / r.height - .5).toFixed(3));
        });
    }
})();
</script>
</body>
</html>
