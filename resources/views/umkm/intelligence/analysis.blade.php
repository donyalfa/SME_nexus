<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
                <h2 class="h4 fw-bold mb-0">Hasil Diagnosis</h2>
                <p class="small text-body-secondary mb-0">Analisis dan rekomendasi untuk bisnis Anda</p>
            </div>
            <a href="{{ route('intelligence.autopsy') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    @php
        $statusConfigs = [
            'critical' => [
                'title' => 'Perlu Perhatian',
                'description' => 'Masalah signifikan terdeteksi pada performa bisnis Anda. Segera lakukan evaluasi penyebab dan tindakan perbaikan.',
                'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                'gradient' => 'linear-gradient(90deg,#D93A41,#FF7A90)',
            ],
            'warning' => [
                'title' => 'Perlu Evaluasi',
                'description' => 'Terdeteksi beberapa indikator yang perlu dipantau dan dievaluasi lebih lanjut.',
                'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'gradient' => 'linear-gradient(90deg,#FFB36B,#FF7A90)',
            ],
            'monitor' => [
                'title' => 'Perlu Dipantau',
                'description' => 'Terdeteksi penurunan ringan pada satu atau beberapa indikator. Pantau dan cegah agar tidak memburuk.',
                'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'gradient' => 'linear-gradient(90deg,#F5C518,#FFB36B)',
            ],
            'ok' => [
                'title' => 'Kondisi Bisnis Stabil',
                'description' => 'Tidak ada penurunan yang terdeteksi pada omzet, customer, maupun penjualan. Kondisi bisnis Anda masih tergolong baik.',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'gradient' => 'linear-gradient(90deg,#4FE3C1,#4B4FE0)',
            ],
        ];
        $statusConfig = $statusConfigs[$analysis->status] ?? $statusConfigs['monitor'];

        $metrics = [
            ['label' => 'Perubahan Omzet', 'change' => $analysis->omzet_change,
             'detail' => 'Rp ' . number_format($analysis->omzet_prev, 0, ',', '.') . ' → Rp ' . number_format($analysis->omzet_current, 0, ',', '.')],
            ['label' => 'Perubahan Customer', 'change' => $analysis->customer_change,
             'detail' => number_format($analysis->customer_prev, 0, ',', '.') . ' → ' . number_format($analysis->customer_current, 0, ',', '.') . ' customer'],
            ['label' => 'Perubahan Penjualan', 'change' => $analysis->sales_change,
             'detail' => number_format($analysis->sales_prev, 0, ',', '.') . ' → ' . number_format($analysis->sales_current, 0, ',', '.') . ' penjualan'],
        ];

        $severityMap = [
            'critical' => ['bg-danger text-white', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
            'high'   => ['bg-danger-subtle text-danger', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
            'medium' => ['bg-warning-subtle text-warning-emphasis', 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            'low'    => ['bg-success-subtle text-success', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];
        $priorityMap = [
            'high'   => ['bg-danger-subtle text-danger-emphasis', 'Prioritas Tinggi'],
            'medium' => ['bg-warning-subtle text-warning-emphasis', 'Prioritas Sedang'],
            'low'    => ['bg-success-subtle text-success-emphasis', 'Prioritas Rendah'],
        ];
    @endphp

    <style>
        .dx-icon { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; flex: none; }
        .dx-icon svg { width: 20px; height: 20px; }
        .dx-banner { border-radius: 1rem; color: #fff; box-shadow: 0 18px 40px -22px rgba(23, 26, 68, .5); }
        .dx-banner .dx-icon { width: 48px; height: 48px; background: rgba(255,255,255,.2); box-shadow: inset 0 0 0 1px rgba(255,255,255,.3); }
        .dx-banner .dx-icon svg { width: 24px; height: 24px; }
        .dx-card { border-radius: 1rem; border: 1px solid var(--nx-line); background: var(--nx-surface-solid); }
    </style>

    <div class="mx-auto" style="max-width: 80rem;">

        {{-- Status Banner --}}
        <div class="dx-banner nx-fade-up p-4 mb-4" style="background: {{ $statusConfig['gradient'] }}">
            <div class="d-flex align-items-start gap-3">
                <div class="dx-icon nx-scale-in">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusConfig['icon'] }}" />
                    </svg>
                </div>
                <div>
                    <p class="small fw-bold text-uppercase mb-1" style="letter-spacing:.16em; opacity:.7">Status Autopsy</p>
                    <h3 class="h5 fw-bold mb-2">{{ $statusConfig['title'] }}</h3>
                    <p class="small mb-0" style="opacity:.9">{{ $statusConfig['description'] }}</p>
                    @if ($analysis->periodLabel() || $analysis->created_at)
                        <p class="small mt-2 mb-0" style="opacity:.75">
                            @if ($analysis->periodLabel())
                                Periode: {{ $analysis->periodLabel() }}@if ($analysis->created_at) &middot; @endif
                            @endif
                            @if ($analysis->created_at)
                                Disimpan {{ $analysis->created_at->format('d M Y, H:i') }}
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="row g-3">
            @foreach ($metrics as $i => $m)
                <div class="col-12 col-md-4">
                    <div class="dx-card nx-fade-up p-4 h-100" style="--d: {{ 0.05 + $i * 0.05 }}s">
                        <p class="small fw-medium text-body-secondary mb-2">{{ $m['label'] }}</p>
                        <p class="display-6 fw-bold mb-1 {{ $m['change'] >= 0 ? 'text-success' : 'text-danger' }}">
                            <span data-countup="{{ round($m['change'], 1) }}" data-sign="{{ $m['change'] >= 0 ? '+' : '' }}">{{ $m['change'] >= 0 ? '+' : '' }}{{ round($m['change'], 1) }}</span>%
                        </p>
                        <p class="small text-body-secondary mb-0">{{ $m['detail'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Diagnosis --}}
        <div class="nx-fade-up mt-5" style="--d: 0.1s">
            <h3 class="h5 fw-bold mb-3">Diagnosis Masalah</h3>
            <div class="d-grid gap-3">
                @foreach ($analysis->diagnoses as $index => $diagnosis)
                    @php [$sevClass, $sevIcon] = $severityMap[$diagnosis['severity']] ?? $severityMap['low']; @endphp
                    <div class="dx-card nx-fade-up p-4" style="--d: {{ 0.15 + $index * 0.08 }}s">
                        <div class="d-flex align-items-start gap-3">
                            <div class="dx-icon {{ $sevClass }} {{ $diagnosis['severity'] === 'high' ? 'nx-pulse-soft' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sevIcon }}" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold mb-1">{{ $diagnosis['title'] }}</h4>
                                <p class="small text-body-secondary mb-0">{{ $diagnosis['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Rekomendasi --}}
        <div class="nx-fade-up mt-5" style="--d: 0.15s">
            <h3 class="h5 fw-bold mb-3">Rekomendasi Tindakan</h3>
            <div class="d-grid gap-3">
                @foreach ($analysis->recommendations as $recIndex => $rec)
                    @php [$priClass, $priLabel] = $priorityMap[$rec['priority']] ?? $priorityMap['low']; @endphp
                    <div class="dx-card nx-fade-up p-4" style="--d: {{ 0.15 + $recIndex * 0.08 }}s">
                        <div class="d-flex align-items-start gap-3">
                            <div class="dx-icon" style="background: rgba(75,79,224,.1); color: var(--nx-indigo)">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002 2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <h4 class="h6 fw-bold mb-0">{{ $rec['title'] }}</h4>
                                    <span class="badge rounded-pill {{ $priClass }}">{{ $priLabel }}</span>
                                </div>
                                <p class="small text-body-secondary mb-0">{{ $rec['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>