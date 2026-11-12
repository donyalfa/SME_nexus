@extends('admin.layouts.intelligence')

@section('title', 'Business Rules')
@section('header', 'Business Rules')
@section('subheader', 'Aturan diagnosis dengan versi yang tidak berubah setelah dipublikasikan')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">Business Rules</h1>
            <p class="ba-muted mb-0 small">Total {{ $rules->total() }} rule. Kondisi dalam grup digabung dengan AND, antar grup dengan OR.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.rules.tester') }}">Rule Tester</a>
            <a class="btn ba-btn-primary text-white" href="{{ route('admin.intelligence.rules.create') }}">Tambah Rule</a>
        </div>
    </div>

    <div class="ba-card mb-3">
        <div class="ba-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold" for="search">Cari</label>
                    <input class="form-control" id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Kode atau nama rule">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="id_diagnosis">Diagnosis</label>
                    <select class="form-select" id="id_diagnosis" name="id_diagnosis">
                        <option value="">Semua diagnosis</option>
                        @foreach ($diagnoses as $diagnosis)
                            <option value="{{ $diagnosis->id }}" @selected((string) ($filters['id_diagnosis'] ?? '') === (string) $diagnosis->id)>{{ $diagnosis->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold" for="is_active">Status</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="">Semua</option>
                        <option value="1" @selected(($filters['is_active'] ?? '') === '1')>Aktif</option>
                        <option value="0" @selected(($filters['is_active'] ?? '') === '0')>Nonaktif</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button class="btn btn-outline-secondary flex-fill" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.rules.index') }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="ba-card">
        <div class="table-responsive">
            <table class="table ba-table ba-table-mobile align-middle mb-0">
                <thead>
                    <tr>
                        <th>Rule</th>
                        <th>Diagnosis</th>
                        <th>Severity</th>
                        <th>Prioritas</th>
                        <th>Versi Aktif</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rules as $rule)
                        <tr>
                            <td data-label="Rule">
                                <div class="fw-semibold">{{ $rule->name }}</div>
                                <div class="small ba-muted">{{ $rule->code }} &middot; {{ $rule->versions_count }} versi</div>
                            </td>
                            <td data-label="Diagnosis">{{ $rule->diagnosis?->name ?? '-' }}</td>
                            <td data-label="Severity"><span class="ba-badge {{ $rule->severity->badgeClass() }}">{{ $rule->severity->label() }}</span></td>
                            <td data-label="Prioritas">{{ $rule->priority }}</td>
                            <td data-label="Versi Aktif">
                                @if ($rule->activeVersion)
                                    <span class="badge text-bg-success">v{{ $rule->activeVersion->version }}</span>
                                @else
                                    <span class="badge text-bg-secondary">Belum ada</span>
                                @endif
                            </td>
                            <td data-label="Status"><span class="badge {{ $rule->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td data-label="Aksi" class="text-end">
                                <div class="d-flex justify-content-end flex-wrap gap-2">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.intelligence.rules.show', $rule) }}">Lihat</a>
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.intelligence.rules.edit', $rule) }}">Edit</a>
                                    @if ($rule->is_active)
                                        <form method="POST" action="{{ route('admin.intelligence.rules.deactivate', $rule) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Nonaktifkan</button>
                                        </form>
                                    @elseif ($rule->has_published_version)
                                        <form method="POST" action="{{ route('admin.intelligence.rules.activate', $rule) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success" type="submit">Aktifkan</button>
                                        </form>
                                    @else
                                        <a class="btn btn-sm ba-btn-primary text-white" href="{{ route('admin.intelligence.rules.show', $rule) }}">Publikasikan</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7"><div class="ba-empty">Belum ada rule. Tambahkan rule pertama untuk mulai diagnosis.</div></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($rules->hasPages())
            <div class="ba-card-body border-top">{{ $rules->links() }}</div>
        @endif
    </div>
@endsection
