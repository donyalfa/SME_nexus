{{-- resources/views/auth/verify-email.blade.php --}}
{{--
    Halaman Verifikasi Email — desain mengikuti bahasa desain Intelligence (Autopsy / Analysis).
    Perubahan disasar pada styling saja; logika form, route, dan validasi tetap sama.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Email - {{ config('app.name') }}</title>

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

        .alert { margin-bottom: 16px; padding: 12px 14px; border-radius: 12px; font-size: .88rem; line-height: 1.45; color: #8E1F25; background: #FDECEE; border: 1px solid #F7C7CB; animation: shake .45s; }
        .alert.ok { color: #0F6B55; background: #E5FAF4; border-color: #B6EEDF; animation: none; }
        .alert.info { color: var(--ink); background: var(--field); border-color: var(--line); animation: none; }

        .actions { display: flex; flex-direction: column; gap: 12px; margin-top: 8px; }
        .btn {
            position: relative; width: 100%; height: 50px;
            border: 0; border-radius: 14px;
            font: 600 .95rem var(--font-body); color: #fff;
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
        .btn .spin { position: absolute; inset: 0; margin: auto; width: 18px; height: 18px; border: 3px solid rgba(255, 255, 255, .35); border-top-color: #fff; border-radius: 50%; opacity: 0; animation: spin .8s linear infinite; }
        .btn.is-loading { pointer-events: none; }
        .btn.is-loading .label { opacity: 0; transform: translateY(-8px); }
        .btn.is-loading .spin { opacity: 1; }

        .linklike {
            border: 0; background: none; padding: 0; font: 600 .95rem var(--font-body);
            color: var(--indigo); cursor: pointer;
        }
        .linklike:hover { text-decoration: underline; }

        @keyframes shake { 10%, 90% { transform: translateX(-2px); } 20%, 80% { transform: translateX(4px); } 30%, 50%, 70% { transform: translateX(-7px); } 40%, 60% { transform: translateX(7px); } }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 560px) {
            body { padding: 16px 12px; }
            .pane { padding: 32px 20px; }
            .pane h1 { font-size: 1.8rem; }
            .pane .sub { margin: 6px 0 20px; font-size: .9rem; }
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
        <h1>Verifikasi Email</h1>
        <p class="sub">Cek inbox email Anda</p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert ok" role="status">{{ __('Link verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.') }}</div>
        @endif

        @if (session('success'))
            <div class="alert ok" role="status">{{ session('success') }}</div>
        @endif

        @if (session('csrf_expired'))
            <div class="alert info" role="status">{{ session('csrf_expired') }}</div>
        @endif

        <div class="mb-6 p-4 rounded-xl" style="background: var(--field); border: 1px solid var(--line);">
            <p class="text-sm" style="color: var(--ink);">
                {{ __('Terima kasih telah mendaftarkan! Sebelum memulai, dapatkah Anda memverifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan kepada Anda? Jika Anda tidak menerima email tersebut, kami dengan senang hati akan mengirimkan yang lain.') }}
            </p>
        </div>

        <div class="actions">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn"><span class="label">{{ __('Kirim Ulang Email') }}</span><span class="spin" aria-hidden="true"></span></button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn" style="background: transparent; color: var(--muted); border: 1.5px solid var(--line); box-shadow: none; height: 44px;">
                    <span class="label">{{ __('Keluar') }}</span>
                </button>
            </form>
        </div>
    </section>
</main>

</body>
</html>
