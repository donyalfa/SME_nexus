@php
    $adminName = trim(auth()->user()->full_name ?? '') ?: 'Admin';
    $initials = collect(preg_split('/\s+/', $adminName))->filter()->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') ?: 'A';

    $nav = [
        'Ringkasan' => [
            ['Dashboard', 'dashboard', 'admin.intelligence.dashboard', ['admin.intelligence.dashboard']],
        ],
        'Master Data' => [
            ['Pengguna', 'users', 'admin.intelligence.users.index', ['admin.intelligence.users.*']],
            ['Profil UMKM', 'building', 'admin.intelligence.profiles.index', ['admin.intelligence.profiles.*']],
            ['Kategori Bisnis', 'tag', 'admin.intelligence.categories.index', ['admin.intelligence.categories.*']],
            ['Gejala', 'activity', 'admin.intelligence.symptoms.index', ['admin.intelligence.symptoms.*']],
            ['Master Diagnosis', 'stethoscope', 'admin.intelligence.diagnoses.index', ['admin.intelligence.diagnoses.*']],
        ],
        'Mesin Diagnosis' => [
            ['Business Rules', 'workflow', 'admin.intelligence.rules.index', ['admin.intelligence.rules.index', 'admin.intelligence.rules.create', 'admin.intelligence.rules.show', 'admin.intelligence.rules.edit']],
            ['Rule Tester', 'flask', 'admin.intelligence.rules.tester', ['admin.intelligence.rules.tester*']],
            ['Rekomendasi', 'lightbulb', 'admin.intelligence.recommendations.index', ['admin.intelligence.recommendations.*']],
            ['Audit', 'audit', 'admin.intelligence.audit.index', ['admin.intelligence.audit.*']],
        ],
    ];

    $soon = [['Laporan', 'chart'], ['Pengaturan', 'settings']];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Intelligence') &mdash; UMKM Nexus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>document.documentElement.classList.add('ba-anim');</script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('admin/admin.css') }}?v={{ @filemtime(public_path('admin/admin.css')) }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <script>
        // Prefix "tw-" + preflight mati agar tidak bentrok dengan Bootstrap (.collapse, .container, reset, dll).
        tailwind.config = {
            prefix: 'tw-',
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    colors: {
                        brand: { DEFAULT: '#0b5d6b', dark: '#073f49', soft: '#e6f2f4' },
                        accent: '#f59a3c',
                        ink: '#10282d',
                    },
                },
            },
        };
    </script>
</head>
<body class="ba-app">
    <div class="ba-progress" aria-hidden="true"></div>

    <div class="ba-shell">
        <aside class="ba-sidebar" id="ba-sidebar" aria-label="Navigasi admin">
            <a class="ba-brand" href="{{ route('admin.intelligence.dashboard') }}">
                <span class="ba-brand-mark">BA</span>
                <span class="ba-brand-text">
                    <span class="d-block">UMKM Nexus</span>
                    <small class="fw-normal" style="color: rgba(255,255,255,.62)">Admin Intelligence</small>
                </span>
            </a>

            <nav class="ba-nav">
                @foreach ($nav as $group => $items)
                    <div class="ba-nav-label">{{ $group }}</div>
                    @foreach ($items as [$label, $icon, $route, $patterns])
                        <a class="ba-nav-link {{ request()->routeIs(...$patterns) ? 'active' : '' }}"
                           href="{{ route($route) }}"
                           @if (request()->routeIs(...$patterns)) aria-current="page" @endif>
                            <span class="ba-nav-icon"><x-admin::admin-icon :name="$icon" :size="20" /></span>
                            <span class="ba-nav-text">{{ $label }}</span>
                        </a>
                    @endforeach
                @endforeach

                <div class="ba-nav-label">Segera hadir</div>
                @foreach ($soon as [$label, $icon])
                    <a class="ba-nav-link" href="#" aria-disabled="true">
                        <span class="ba-nav-icon"><x-admin::admin-icon :name="$icon" :size="20" /></span>
                        <span class="ba-nav-text">{{ $label }}</span>
                        <span class="ba-nav-badge">Segera</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="ba-backdrop" aria-hidden="true"></div>

        <div class="ba-main">
            <header class="ba-topbar">
                <div class="tw-flex tw-items-center tw-gap-3 tw-min-w-0">
                    <button type="button" class="ba-icon-btn" data-sidebar-toggle aria-controls="ba-sidebar" aria-label="Buka atau tutup menu">
                        <x-admin::admin-icon name="menu" :size="20" />
                    </button>
                    <div class="min-w-0">
                        <div class="ba-topbar-title">@yield('header', 'Admin Intelligence')</div>
                        <div class="tw-hidden sm:tw-block tw-text-sm ba-muted">@yield('subheader', 'Kelola master data dan aturan diagnosis')</div>
                    </div>
                </div>

                <div class="tw-flex tw-items-center tw-gap-3">
                    <div class="tw-flex tw-items-center tw-gap-2">
                        <span class="ba-avatar" aria-hidden="true">{{ $initials }}</span>
                        <span class="tw-hidden md:tw-inline tw-text-sm tw-font-semibold tw-text-ink">{{ $adminName }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="ba-icon-btn" title="Keluar" aria-label="Keluar">
                            <x-admin::admin-icon name="logout" :size="19" />
                        </button>
                    </form>
                </div>
            </header>

            <main class="ba-content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <div class="fw-semibold mb-1">Periksa kembali isian berikut:</div>
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('admin/admin.js') }}?v={{ @filemtime(public_path('admin/admin.js')) }}"></script>
    @stack('scripts')
</body>
</html>