@extends('admin.layouts.intelligence')

@section('title', 'Dashboard Admin')
@section('header', 'Dashboard Admin')
@section('subheader', 'Pantau pengguna, master data, aturan, dan hasil diagnosis')

@section('content')
    @php
        $firstName = trim(explode(' ', trim(auth()->user()->full_name ?? 'Admin'))[0]) ?: 'Admin';
    @endphp

    <div class="ba-hero mb-4">
        <span class="ba-hero-blob b1"></span>
        <span class="ba-hero-blob b2"></span>

        <div class="position-relative">
            <span class="ba-hero-date mb-3">
                <x-admin::admin-icon name="sparkle" :size="14" />
                {{ now()->locale('id')->translatedFormat('l, d F Y') }}
            </span>
            <h1 class="h2 mb-2">Halo, {{ $firstName }}!</h1>
            <p class="mb-0">Pantau pengguna, master data, aturan, dan aktivitas diagnosis UMKM Nexus dalam satu tempat.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="ba-card ba-card-body ba-stat ba-lift h-100">
                <div class="ba-stat-icon"><x-admin::admin-icon name="users" :size="22" /></div>
                <span class="ba-stat-label">Pengguna</span>
                <span class="ba-stat-value" data-countup="{{ $summary['users'] }}">{{ $summary['users'] }}</span>
                <span class="small ba-muted">{{ $summary['owners'] }} pemilik UMKM</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ba-card ba-card-body ba-stat ba-stat--amber ba-lift h-100">
                <div class="ba-stat-icon"><x-admin::admin-icon name="building" :size="22" /></div>
                <span class="ba-stat-label">Profil UMKM</span>
                <span class="ba-stat-value" data-countup="{{ $summary['profiles'] }}">{{ $summary['profiles'] }}</span>
                <span class="small ba-muted">Profil usaha terdaftar</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ba-card ba-card-body ba-stat ba-stat--violet ba-lift h-100">
                <div class="ba-stat-icon"><x-admin::admin-icon name="workflow" :size="22" /></div>
                <span class="ba-stat-label">Rule aktif</span>
                <span class="ba-stat-value" data-countup="{{ $summary['rules'] }}">{{ $summary['rules'] }}</span>
                <span class="small ba-muted">Aturan siap dievaluasi</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ba-card ba-card-body ba-stat ba-stat--rose ba-lift h-100">
                <div class="ba-stat-icon"><x-admin::admin-icon name="lightbulb" :size="22" /></div>
                <span class="ba-stat-label">Rekomendasi aktif</span>
                <span class="ba-stat-value" data-countup="{{ $summary['recommendations'] }}">{{ $summary['recommendations'] }}</span>
                <span class="small ba-muted">Tindak lanjut diagnosis</span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="ba-card h-100">
                <div class="ba-card-body">
                    <h2 class="ba-page-title h5 mb-1">Fondasi master data</h2>
                    <p class="ba-muted mb-3 small">Lengkapi gejala, diagnosis, rule, dan rekomendasi sebelum UMKM menjalankan analisis.</p>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="ba-stat ba-stat--amber border rounded-4 p-3 h-100">
                                <div class="small ba-muted fw-semibold">Gejala aktif</div>
                                <div class="ba-stat-value fs-3" data-countup="{{ $summary['symptoms'] }}">{{ $summary['symptoms'] }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="ba-stat ba-stat--violet border rounded-4 p-3 h-100">
                                <div class="small ba-muted fw-semibold">Diagnosis aktif</div>
                                <div class="ba-stat-value fs-3" data-countup="{{ $summary['diagnoses'] }}">{{ $summary['diagnoses'] }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-12 col-sm-6">
                            <a class="ba-quick" href="{{ route('admin.intelligence.symptoms.index') }}">
                                <span class="ba-quick-icon"><x-admin::admin-icon name="activity" :size="19" /></span>
                                Kelola Gejala
                                <x-admin::admin-icon class="ba-quick-arrow" name="arrow" :size="17" />
                            </a>
                        </div>
                        <div class="col-12 col-sm-6">
                            <a class="ba-quick" href="{{ route('admin.intelligence.diagnoses.index') }}">
                                <span class="ba-quick-icon"><x-admin::admin-icon name="stethoscope" :size="19" /></span>
                                Kelola Diagnosis
                                <x-admin::admin-icon class="ba-quick-arrow" name="arrow" :size="17" />
                            </a>
                        </div>
                        <div class="col-12 col-sm-6">
                            <a class="ba-quick" href="{{ route('admin.intelligence.rules.index') }}">
                                <span class="ba-quick-icon"><x-admin::admin-icon name="workflow" :size="19" /></span>
                                Kelola Rule
                                <x-admin::admin-icon class="ba-quick-arrow" name="arrow" :size="17" />
                            </a>
                        </div>
                        <div class="col-12 col-sm-6">
                            <a class="ba-quick" href="{{ route('admin.intelligence.recommendations.index') }}">
                                <span class="ba-quick-icon"><x-admin::admin-icon name="lightbulb" :size="19" /></span>
                                Kelola Rekomendasi
                                <x-admin::admin-icon class="ba-quick-arrow" name="arrow" :size="17" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="ba-card h-100">
                <div class="ba-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="ba-page-title h5 mb-0">Aktivitas terbaru</h2>
                        <a class="small fw-semibold text-decoration-none" href="{{ route('admin.intelligence.audit.index') }}">Lihat semua</a>
                    </div>

                    @if ($summary['recent_logs']->isEmpty())
                        <div class="ba-empty">Belum ada aktivitas audit.</div>
                    @else
                        <ul class="ba-timeline">
                            @foreach ($summary['recent_logs'] as $log)
                                <li class="ba-timeline-item" style="--ba-n: {{ $loop->index }}">
                                    <div class="min-w-0">
                                        <div class="small fw-semibold">{{ $log->action }}</div>
                                        <div class="small ba-muted">{{ $log->user?->full_name ?? 'Sistem' }} &middot; {{ $log->entity_type }}</div>
                                    </div>
                                    <div class="small ba-muted text-nowrap">{{ $log->created_at?->format('d/m H:i') }}</div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
