<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Intelligence') &mdash; UMKM Nexus</title>
    @vite(['resources/css/intelligence.css', 'resources/js/intelligence.js'])
</head>
<body class="ba-app">
    <div class="ba-shell">
        <aside class="ba-sidebar">
            <a class="ba-brand" href="{{ route('intelligence.dashboard') }}">
                <span class="ba-brand-mark">BA</span>
                <span>
                    <span class="d-block">UMKM Nexus</span>
                    <small class="fw-normal text-white-50">Intelligence</small>
                </span>
            </a>

            <div class="ba-nav">
                <div class="ba-nav-label">Menu UMKM</div>
                <a class="ba-nav-link {{ request()->routeIs('intelligence.dashboard') ? 'active' : '' }}" href="{{ route('intelligence.dashboard') }}">Dashboard</a>
                <a class="ba-nav-link" href="#" aria-disabled="true">Profil Usaha <span class="badge text-bg-light ms-auto">Segera</span></a>
                <a class="ba-nav-link" href="#" aria-disabled="true">Data Bisnis <span class="badge text-bg-light ms-auto">Segera</span></a>
                <a class="ba-nav-link" href="#" aria-disabled="true">Analisis &amp; Diagnosis <span class="badge text-bg-light ms-auto">Segera</span></a>
                <a class="ba-nav-link" href="#" aria-disabled="true">Action Plan <span class="badge text-bg-light ms-auto">Segera</span></a>
                <a class="ba-nav-link" href="#" aria-disabled="true">Riwayat <span class="badge text-bg-light ms-auto">Segera</span></a>
            </div>
        </aside>

        <div class="ba-main">
            <header class="ba-topbar d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <div class="fw-semibold">@yield('header', 'Intelligence')</div>
                    <div class="small ba-muted">@yield('subheader', 'Diagnosis bisnis berbasis aturan')</div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="small ba-muted">{{ auth()->user()->full_name ?? '' }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Keluar</button>
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

    @stack('scripts')
</body>
</html>
