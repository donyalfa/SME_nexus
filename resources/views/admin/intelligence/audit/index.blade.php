@extends('admin.layouts.intelligence')

@section('title', 'Audit Log')
@section('header', 'Audit Log')
@section('subheader', 'Jejak perubahan master data dan rule Intelligence')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">Audit Log</h1>
            <p class="ba-muted mb-0 small">Total {{ $logs->total() }} catatan audit.</p>
        </div>
    </div>

    <div class="ba-card mb-3">
        <div class="ba-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label small fw-semibold" for="search">Cari</label>
                    <input class="form-control" id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Aksi atau tipe entitas">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label small fw-semibold" for="entity_type">Tipe Entitas</label>
                    <select class="form-select" id="entity_type" name="entity_type">
                        <option value="">Semua</option>
                        @foreach (['user', 'sme_profile', 'business_category', 'autopsy_symptom', 'autopsy_diagnosis', 'autopsy_rule', 'autopsy_recommendation'] as $entityType)
                            <option value="{{ $entityType }}" @selected(($filters['entity_type'] ?? '') === $entityType)>{{ $entityType }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3 d-flex gap-2">
                    <button class="btn btn-outline-secondary flex-fill" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.audit.index') }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="ba-card">
        <div class="table-responsive">
            <table class="table ba-table ba-table-mobile align-middle mb-0">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pengguna</th>
                        <th>Aksi</th>
                        <th>Entitas</th>
                        <th>IP</th>
                        <th class="text-end">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td data-label="Waktu">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                            <td data-label="Pengguna">{{ $log->user?->full_name ?? 'Sistem' }}</td>
                            <td data-label="Aksi"><code>{{ $log->action }}</code></td>
                            <td data-label="Entitas">{{ $log->entity_type }} @if ($log->entity_id) #{{ $log->entity_id }} @endif</td>
                            <td data-label="IP">{{ $log->ip_address ?: '-' }}</td>
                            <td data-label="Detail" class="text-end">
                                @if ($log->old_values || $log->new_values)
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#audit-detail-{{ $log->id }}">Lihat</button>
                                @else
                                    <span class="small ba-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @if ($log->old_values || $log->new_values)
                            <tr>
                                <td colspan="6" class="p-0 border-0">
                                    <div class="collapse" id="audit-detail-{{ $log->id }}">
                                        <div class="p-3 small bg-light">
                                            @if ($log->old_values)
                                                <div class="fw-semibold mb-1">Sebelum</div>
                                                <pre class="mb-2 small">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            @endif
                                            @if ($log->new_values)
                                                <div class="fw-semibold mb-1">Sesudah</div>
                                                <pre class="mb-0 small">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6"><div class="ba-empty">Belum ada catatan audit.</div></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($logs->hasPages())
            <div class="ba-card-body border-top">{{ $logs->links() }}</div>
        @endif
    </div>
@endsection
