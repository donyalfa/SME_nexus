@extends('admin.layouts.intelligence')

@section('title', 'Detail Rule')
@section('header', 'Detail Rule')
@section('subheader', 'Tinjau versi, kondisi, dan publikasi rule')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">{{ $rule->name }}</h1>
            <p class="ba-muted mb-0 small">{{ $rule->code }} &middot; {{ $rule->diagnosis?->name ?? 'Tanpa diagnosis' }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.rules.index') }}">Kembali</a>
            <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.rules.edit', $rule) }}">Edit / Draf Baru</a>
        </div>
    </div>

    @if ($rule->diagnosis && ! $rule->diagnosis->is_active)
        <div class="alert alert-warning small">
            Diagnosis tujuan rule ini sedang nonaktif. Rule tidak akan dievaluasi pada analisis baru sampai diagnosis diaktifkan kembali.
        </div>
    @endif

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="ba-card mb-3">
                <div class="ba-card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                        <div>
                            <h2 class="h6 fw-bold mb-1">Informasi Rule</h2>
                            <div class="small ba-muted">Kondisi dalam grup AND, antar grup OR.</div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="ba-badge {{ $rule->severity->badgeClass() }}">{{ $rule->severity->label() }}</span>
                            <span class="badge {{ $rule->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $rule->is_active ? 'Rule aktif' : 'Rule nonaktif' }}</span>
                        </div>
                    </div>

                    <dl class="row small mb-0">
                        <dt class="col-5 col-md-3">Diagnosis</dt>
                        <dd class="col-7 col-md-9">{{ $rule->diagnosis?->name ?? '-' }}</dd>
                        <dt class="col-5 col-md-3">Prioritas</dt>
                        <dd class="col-7 col-md-9">{{ $rule->priority }}</dd>
                        <dt class="col-5 col-md-3">Deskripsi</dt>
                        <dd class="col-7 col-md-9">{{ $rule->description ?: '-' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="ba-card">
                <div class="ba-card-body">
                    <h2 class="h6 fw-bold mb-3">Kondisi Versi Terbaru</h2>

                    @if (! $version)
                        <div class="ba-empty">Belum ada versi rule.</div>
                    @else
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div class="small ba-muted">
                                Versi <strong>v{{ $version->version }}</strong>
                                <span class="badge {{ $version->status->badgeClass() }} ms-1">{{ $version->status->label() }}</span>
                                &middot; {{ $version->groups->sum(fn ($group) => $group->conditions->count()) }} kondisi
                            </div>
                            @if ($version->status->value !== 'published')
                                <form method="POST" action="{{ route('admin.intelligence.rules.publish', $rule) }}" data-confirm="Versi yang sudah dipublikasikan tidak akan berubah lagi." data-confirm-variant="primary" data-confirm-title="Publikasikan versi terbaru?" data-confirm-ok="Publikasikan">
                                    @csrf
                                    <button class="btn btn-sm ba-btn-primary text-white" type="submit">Publikasikan Versi Terbaru</button>
                                </form>
                            @elseif ($rule->is_active)
                                <form method="POST" action="{{ route('admin.intelligence.rules.deactivate', $rule) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Nonaktifkan Rule</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.intelligence.rules.activate', $rule) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success" type="submit">Aktifkan Rule</button>
                                </form>
                            @endif
                        </div>

                        @php
                            $inactiveSymptomConditions = $version->groups
                                ->flatMap(fn ($group) => $group->conditions)
                                ->filter(fn ($condition) => ! $condition->symptom || ! $condition->symptom->is_active);
                        @endphp

                        @if ($inactiveSymptomConditions->isNotEmpty())
                            <div class="alert alert-warning small">
                                Versi ini memakai gejala nonaktif. Rule ini tidak akan dievaluasi pada analisis baru sampai gejala diaktifkan kembali atau versi baru dipublikasikan.
                            </div>
                        @endif

                        @forelse ($version->groups as $group)
                            <div class="ba-rule-group">
                                <div class="fw-semibold mb-2">{{ $group->label ?: 'Grup '.$group->group_order }}</div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($group->conditions as $condition)
                                        <span class="badge text-bg-light border">
                                            {{ $condition->is_negated ? 'TIDAK ' : '' }}{{ $condition->symptom?->name ?? 'Gejala tidak ditemukan' }}
                                            @if (! $condition->symptom?->is_active)
                                                <span class="badge text-bg-warning text-dark ms-1">nonaktif</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="ba-empty">Versi ini belum memiliki grup kondisi.</div>
                        @endforelse

                        @if ($version->notes)
                            <div class="small ba-muted mt-3"><strong>Catatan versi:</strong> {{ $version->notes }}</div>
                        @endif
                    @endif
                </div>
            </div>

            <div class="ba-card mt-3">
                <div class="ba-card-body">
                    <h2 class="h6 fw-bold mb-3">Riwayat Versi</h2>
                    <div class="table-responsive">
                        <table class="table ba-table ba-table-mobile align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Versi</th>
                                    <th>Status</th>
                                    <th>Kondisi</th>
                                    <th>Catatan</th>
                                    <th>Dipublikasikan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rule->versions->sortByDesc('version') as $historyVersion)
                                    <tr>
                                        <td data-label="Versi">v{{ $historyVersion->version }}</td>
                                        <td data-label="Status"><span class="badge {{ $historyVersion->status->badgeClass() }}">{{ $historyVersion->status->label() }}</span></td>
                                        <td data-label="Kondisi">{{ $historyVersion->groups->sum(fn ($group) => $group->conditions->count()) }}</td>
                                        <td data-label="Catatan">{{ $historyVersion->notes ?: '-' }}</td>
                                        <td data-label="Dipublikasikan">{{ $historyVersion->published_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5"><div class="ba-empty">Belum ada riwayat versi.</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="ba-card ba-card-body ba-form-summary">
                <h2 class="h6 fw-bold mb-2">Aksi Rule</h2>
                <p class="small ba-muted">Versi yang sudah dipublikasikan tetap tersimpan untuk analisis lama.</p>

                <div class="d-grid gap-2">
                    @if ($version && ($version->status->value !== 'published' || ! $rule->is_active))
                        <form method="POST" action="{{ route('admin.intelligence.rules.publish', $rule) }}" data-confirm="Versi yang sudah dipublikasikan tidak akan berubah lagi." data-confirm-variant="primary" data-confirm-title="Publikasikan versi terbaru?" data-confirm-ok="Publikasikan">
                            @csrf
                            <button class="btn ba-btn-primary text-white w-100" type="submit">Publikasikan</button>
                        </form>
                    @endif

                    @if ($rule->is_active)
                        <form method="POST" action="{{ route('admin.intelligence.rules.deactivate', $rule) }}">
                            @csrf
                            <button class="btn btn-outline-danger w-100" type="submit">Nonaktifkan Rule</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.intelligence.rules.activate', $rule) }}">
                            @csrf
                            <button class="btn btn-outline-success w-100" type="submit">Aktifkan Rule</button>
                        </form>
                    @endif

                    <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.rules.edit', $rule) }}">Buat Perubahan</a>
                </div>

                <hr>
                <div class="small ba-muted">
                    <div class="d-flex justify-content-between mb-1"><span>Versi terbaru</span><strong>{{ $version ? 'v'.$version->version : '-' }}</strong></div>
                    <div class="d-flex justify-content-between mb-1"><span>Spesifisitas</span><strong>{{ $version ? $version->groups->sum(fn ($group) => $group->conditions->count()) : 0 }}</strong></div>
                    <div class="d-flex justify-content-between"><span>Dibuat</span><strong>{{ $rule->created_at?->format('d/m/Y') ?? '-' }}</strong></div>
                </div>
            </div>
        </div>
    </div>
@endsection
