@extends('admin.layouts.intelligence')

@section('title', 'Gejala')
@section('header', 'Gejala')
@section('subheader', 'Gejala terdeteksi dari perubahan metrik bisnis')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">Gejala</h1>
            <p class="ba-muted mb-0 small">Total {{ $symptoms->total() }} gejala. Gejala yang dipakai rule tidak dihapus, tetapi dinonaktifkan.</p>
        </div>
        <a class="btn ba-btn-primary text-white" href="{{ route('admin.intelligence.symptoms.create') }}">Tambah Gejala</a>
    </div>

    <div class="ba-card mb-3">
        <div class="ba-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold" for="search">Cari</label>
                    <input class="form-control" id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Kode, nama, atau metrik">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="is_active">Status</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="">Semua</option>
                        <option value="1" @selected(($filters['is_active'] ?? '') === '1')>Aktif</option>
                        <option value="0" @selected(($filters['is_active'] ?? '') === '0')>Nonaktif</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 d-flex gap-2">
                    <button class="btn btn-outline-secondary flex-fill" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.symptoms.index') }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="ba-card">
        <div class="table-responsive">
            <table class="table ba-table ba-table-mobile align-middle mb-0">
                <thead>
                    <tr>
                        <th>Gejala</th>
                        <th>Kondisi</th>
                        <th>Severity</th>
                        <th>Dipakai</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($symptoms as $symptom)
                        <tr>
                            <td data-label="Gejala">
                                <div class="fw-semibold">{{ $symptom->name }}</div>
                                <div class="small ba-muted">{{ $symptom->code }}</div>
                            </td>
                            <td data-label="Kondisi"><code>{{ $symptom->conditionLabel() }}</code></td>
                            <td data-label="Severity"><span class="ba-badge {{ $symptom->severity->badgeClass() }}">{{ $symptom->severity->label() }}</span></td>
                            <td data-label="Dipakai">{{ $symptom->conditions_count }} kondisi</td>
                            <td data-label="Status">
                                <span class="badge {{ $symptom->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $symptom->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td data-label="Aksi" class="text-end">
                                <div class="d-flex justify-content-end flex-wrap gap-2">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.intelligence.symptoms.edit', $symptom) }}">Edit</a>
                                    @if ($symptom->is_active)
                                        <form method="POST" action="{{ route('admin.intelligence.symptoms.deactivate', $symptom) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Nonaktifkan</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.intelligence.symptoms.activate', $symptom) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success" type="submit">Aktifkan</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"><div class="ba-empty">Belum ada gejala yang cocok dengan filter.</div></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($symptoms->hasPages())
            <div class="ba-card-body border-top">{{ $symptoms->links() }}</div>
        @endif
    </div>
@endsection
