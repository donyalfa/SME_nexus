@php
    $modules = [
        [
            'name'  => 'Intelligence',
            'desc'  => 'Analisis data bisnis dan prediksi tren pasar untuk pengambilan keputusan yang lebih baik.',
            'items' => ['Analisis Tren Pasar', 'Prediksi Penjualan', 'Laporan Otomatis'],
            'from'  => '#4B4FE0', 'to' => '#6A5CFF', 'text' => '#4B4FE0',
            'href'  => '#',
            'icon'  => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
        ],
        [
            'name'  => 'Collaboration',
            'desc'  => 'Kolaborasi tim dan manajemen proyek untuk meningkatkan produktivitas bisnis.',
            'items' => ['Manajemen Tim', 'Tracking Proyek', 'Komunikasi Real-time'],
            'from'  => '#4FE3C1', 'to' => '#0B7A62', 'text' => '#0B9C7D',
            'href'  => '#',
            'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        ],
        [
            'name'  => 'Intelligence',
            'desc'  => 'Pengambilan keputusan berbasis data dan analisis bisnis yang komprehensif.',
            'items' => ['Intelligence', 'Analisis Kompetitor', 'Strategi Bisnis'],
            'from'  => '#FFB36B', 'to' => '#FF7A90', 'text' => '#E8873A',
            'href'  => route('intelligence.autopsy'),
            'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        ],
    ];

    $stats = [
        ['label' => 'Total Modul',      'value' => '3 Modul', 'color' => '#4B4FE0',
         'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ['label' => 'Modul Aktif',      'value' => '1 Modul', 'color' => '#0B9C7D',
         'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Update Terakhir',  'value' => 'Hari Ini', 'color' => '#E8873A',
         'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
                <h2 class="h4 fw-bold mb-0">Dashboard Modul</h2>
                <p class="small text-body-secondary mb-0">Pilih modul untuk mengelola bisnis Anda</p>
            </div>
            <a href="{{ route('intelligence.autopsy') }}" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Buka Autopsy
            </a>
        </div>
    </x-slot>

    <style>
        .md-card { position: relative; overflow: hidden; border-radius: 1rem; background: var(--nx-surface-solid); border: 1px solid var(--nx-line); transition: transform .25s, box-shadow .25s; }
        .md-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -24px rgba(75,79,224,.5); }
        .md-glow { position: absolute; top: -30px; right: -30px; width: 130px; height: 130px; border-radius: 50%; filter: blur(32px); opacity: .25; transition: transform .4s; pointer-events: none; }
        .md-card:hover .md-glow { transform: scale(1.5); }
        .md-icon { width: 56px; height: 56px; border-radius: 1rem; display: grid; place-items: center; color: #fff; }
        .md-icon svg { width: 28px; height: 28px; }
        .md-check { width: 16px; height: 16px; flex: none; }
        .md-link { font-weight: 600; font-size: .9rem; text-decoration: none; display: inline-flex; align-items: center; gap: .5rem; transition: gap .2s; }
        .md-link:hover { gap: .8rem; }
        .md-link svg { width: 16px; height: 16px; }
        .md-stat { border-radius: .85rem; background: var(--nx-surface-solid); border: 1px solid var(--nx-line); }
        .md-stat-ico { width: 40px; height: 40px; border-radius: .6rem; display: grid; place-items: center; flex: none; }
        .md-stat-ico svg { width: 20px; height: 20px; }
    </style>

    <div class="mx-auto" style="max-width: 80rem;">

        @php $noBusinessProfile = auth()->user()->businessProfiles()->doesntExist(); @endphp

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4" role="status">{{ session('success') }}</div>
        @endif

        @if ($noBusinessProfile)
            <div class="alert alert-primary border-0 shadow-sm mb-4" role="status">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="flex-grow-1">
                        <strong class="d-block">Lengkapi Profil Usaha Anda</strong>
                        <span class="small text-body-secondary">Data usaha Anda membantu sistem memberi analisis dan rekomendasi yang lebih akurat.</span>
                    </div>
                    <a href="{{ route('onboarding.business-profile.show') }}" class="btn btn-primary btn-sm">Isi Profil Usaha</a>
                </div>
            </div>
        @endif

        {{-- Kartu modul --}}
        <div class="row g-4">
            @foreach ($modules as $m)
                <div class="col-12 col-md-4">
                    <div class="md-card p-4 h-100">
                        <div class="md-glow" style="background: linear-gradient(135deg, {{ $m['from'] }}, {{ $m['to'] }})"></div>
                        <div class="position-relative d-flex flex-column h-100">
                            <div class="md-icon mb-3" style="background: linear-gradient(135deg, {{ $m['from'] }}, {{ $m['to'] }}); box-shadow: 0 10px 20px -10px {{ $m['from'] }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $m['icon'] }}" />
                                </svg>
                            </div>
                            <h3 class="h5 fw-bold">{{ $m['name'] }}</h3>
                            <p class="small text-body-secondary mt-1">{{ $m['desc'] }}</p>

                            <ul class="list-unstyled small mt-2 mb-4 d-grid gap-2">
                                @foreach ($m['items'] as $item)
                                    <li class="d-flex align-items-center gap-2">
                                        <svg class="md-check" style="color: {{ $m['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ $item }}
                                    </li>
                                @endforeach
                            </ul>

                            <a href="{{ $m['href'] }}" class="md-link mt-auto" style="color: {{ $m['text'] }}">
                                Buka Modul
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Statistik ringkas --}}
        <div class="row g-3 mt-3">
            @foreach ($stats as $s)
                <div class="col-12 col-sm-4">
                    <div class="md-stat p-3 d-flex align-items-center gap-3">
                        <div class="md-stat-ico" style="background: {{ $s['color'] }}1A; color: {{ $s['color'] }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}" />
                            </svg>
                        </div>
                        <div>
                            <p class="small text-body-secondary mb-0">{{ $s['label'] }}</p>
                            <p class="h5 fw-bold mb-0">{{ $s['value'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>