{{-- resources/views/auth/reset-password.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - {{ config('app.name') }}</title>

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
            min-height: 100vh; min-height: 100dvh;
            display: grid; place-items: center; padding: 24px;
            font-family: var(--font-body);
            color: var(--ink);
            background:
                radial-gradient(900px 500px at 10% 0%, #fff 0%, transparent 60%),
                var(--paper);
        }

        .auth { position: relative; width: min(460px, 100%); background: #fff; border-radius: 24px; overflow: hidden; box-shadow: 0 30px 70px -30px rgba(23, 26, 68, .35); }
        .pane { position: relative; width: 100%; display: flex; flex-direction: column; justify-content: center; padding: 40px; }
        .pane h1 { font-family: var(--font-display); font-weight: 800; font-size: 2.1rem; letter-spacing: -.02em; line-height: 1.1; }
        .pane .sub { margin: 10px 0 26px; color: var(--muted); font-size: .95rem; line-height: 1.55; }

        .field { position: relative; margin-bottom: 14px; }
        .field input[type="email"], .field input[type="password"] {
            width: 100%; height: 56px; padding: 22px 48px 8px 16px;
            font: 500 .98rem var(--font-body); color: var(--ink);
            background: var(--field); border: 1.5px solid var(--line); border-radius: 14px;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .field input[type="email"]:focus, .field input[type="password"]:focus,
        .field input[type="email"]:not(:placeholder-shown), .field input[type="password"]:not(:placeholder-shown) { background: #fff; }
        .field input[type="email"]:focus, .field input[type="password"]:focus { border-color: var(--indigo); box-shadow: 0 0 0 4px rgba(75, 79, 224, .14); }
        .field label {
            position: absolute; left: 17px; top: 50%; transform: translateY(-50%);
            font-size: .95rem; color: var(--muted); pointer-events: none;
            transition: top .2s, font-size .2s, color .2s, transform .2s;
        }
        .field input:focus + label, .field input:not(:placeholder-shown) + label { top: 17px; transform: none; font-size: .72rem; font-weight: 600; }
        .field input:focus + label { color: var(--indigo); }
        .eye {
            position: absolute; right: 8px; top: 8px; width: 40px; height: 40px;
            display: grid; place-items: center; border: 0; border-radius: 10px;
            background: transparent; color: var(--muted); cursor: pointer;
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

        .btn {
            position: relative; width: 100%; height: 54px;
            border: 0; border-radius: 14px;
            font: 600 1rem var(--font-body); color: #fff;
            background: linear-gradient(120deg, var(--indigo), #6A5CFF 55%, var(--indigo));
            background-size: 200% 100%;
            cursor: pointer; overflow: hidden;
            transition: background-position .6s ease, transform .15s, box-shadow .25s;
            box-shadow: 0 14px 28px -14px rgba(75, 79, 224, .75);
        }
        .btn:hover { background-position: 100% 0; box-shadow: 0 18px 32px -14px rgba(75, 79, 224, .9); }
        .btn:active { transform: scale(.985); }
        .btn:focus-visible { outline: 3px solid var(--mint); outline-offset: 3px; }
        .btn .label { transition: opacity .2s, transform .3s; display: inline-block; }
        .btn .spin { position: absolute; inset: 0; margin: auto; width: 22px; height: 22px; border: 3px solid rgba(255, 255, 255, .35); border-top-color: #fff; border-radius: 50%; opacity: 0; animation: spin .8s linear infinite; }
        .btn.is-loading { pointer-events: none; }
        .btn.is-loading .label { opacity: 0; transform: translateY(-8px); }
        .btn.is-loading .spin { opacity: 1; }

        .alert { margin-bottom: 16px; padding: 12px 14px; border-radius: 12px; font-size: .88rem; line-height: 1.45; color: #8E1F25; background: #FDECEE; border: 1px solid #F7C7CB; animation: shake .45s; }
        .alert.ok { color: #0F6B55; background: #E5FAF4; border-color: #B6EEDF; animation: none; }
        .alert.info { color: var(--ink); background: var(--field); border-color: var(--line); animation: none; }

        .back-home { display: inline-flex; align-items: center; gap: 8px; width: fit-content; margin-bottom: 24px; color: var(--muted); font-size: .85rem; font-weight: 600; text-decoration: none; transition: color .2s, transform .2s; }
        .back-home:hover { color: var(--indigo); text-decoration: none; transform: translateX(-3px); }
        .back-home svg { width: 17px; height: 17px; stroke: currentColor; }

        a { color: var(--indigo); font-weight: 600; text-decoration: none; }
        a:hover { text-decoration: underline; }

        @keyframes shake { 10%, 90% { transform: translateX(-2px); } 20%, 80% { transform: translateX(4px); } 30%, 50%, 70% { transform: translateX(-7px); } 40%, 60% { transform: translateX(7px); } }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 560px) {
            body { padding: 16px 12px; }
            .pane { padding: 32px 20px; }
            .pane h1 { font-size: 1.8rem; }
            .pane .sub { margin: 6px 0 20px; font-size: .9rem; }
            .field input[type="email"], .field input[type="password"] { height: 52px; font-size: .95rem; }
            .btn { height: 50px; font-size: .95rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .001ms !important; animation-iteration-count: 1 !important; transition-duration: .001ms !important; transition-delay: 0s !important; }
        }
    </style>
</head>
<body>

<main class="auth">
    <section class="pane">
        <a href="{{ url('/') }}" class="back-home">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5" />
                <path d="M12 19l-7-7 7-7" />
            </svg>
            Kembali ke Beranda
        </a>

        <h1>Reset Password</h1>
        <p class="sub">Masukkan password baru Anda</p>

        <div class="mb-6 p-4 rounded-xl" style="background: var(--field); border: 1px solid var(--line);">
            <p class="text-sm" style="color: var(--ink);">
                {{ __('Pastikan password baru Anda kuat dan mudah diingat untuk keamanan akun Anda.') }}
            </p>
        </div>

        @if ($errors->any())
            <div class="alert" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.store') }}" data-auth-form>
            @csrf
            @method('put')
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="field">
                <input type="email" id="email" name="email" placeholder=" " value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" />
                <label for="email">Email</label>
            </div>

            <div class="field">
                <input type="password" id="password" name="password" placeholder=" " required autocomplete="new-password" />
                <label for="password">Password Baru</label>
                <button type="button" class="eye" aria-label="Tampilkan kata sandi" aria-pressed="false" data-eye>
                    <svg class="on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4.2M6.6 6.6A17 17 0 0 0 2 12s3.6 7 10 7c1.7 0 3.2-.4 4.5-1"/></svg>
                </button>
            </div>

            <div class="field">
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder=" " required autocomplete="new-password" />
                <label for="password_confirmation">Konfirmasi Password</label>
                <button type="button" class="eye" aria-label="Tampilkan kata sandi" aria-pressed="false" data-eye>
                    <svg class="on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4.2M6.6 6.6A17 17 0 0 0 2 12s3.6 7 10 7c1.7 0 3.2-.4 4.5-1"/></svg>
                </button>
            </div>

            <button type="submit" class="btn"><span class="label">Reset Password</span><span class="spin" aria-hidden="true"></span></button>
        </form>

        <p class="mt-6 text-center text-sm" style="color: var(--muted);">
            <a href="{{ route('login') }}">Kembali ke halaman login</a>
        </p>
    </section>
</main>

<script>
    (function () {
        var form = document.querySelector('form');
        if (!form) return;
        var btn = form.querySelector('.btn');
        var sending = false;

        document.querySelectorAll('[data-eye]').forEach(function (b) {
            b.addEventListener('click', function () {
                var input = b.parentElement.querySelector('input');
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                b.setAttribute('aria-pressed', show);
                b.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            });
        });

        form.addEventListener('invalid', function (e) {
            var fld = e.target.closest('.field');
            if (!fld) return;
            fld.classList.remove('shake');
            void fld.offsetWidth;
            fld.classList.add('shake');
            fld.addEventListener('animationend', function () { fld.classList.remove('shake'); }, { once: true });
        }, true);

        form.addEventListener('submit', function (e) {
            if (sending) { e.preventDefault(); return; }
            sending = true;
            var b = form.querySelector('.btn');
            b.classList.add('is-loading');
            b.setAttribute('aria-busy', 'true');
        });
    })();
</script>
</body>
</html>
