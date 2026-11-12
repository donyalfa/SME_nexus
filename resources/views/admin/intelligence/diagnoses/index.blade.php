@extends('admin.layouts.intelligence')

@section('title', 'Master Diagnosis')
@section('header', 'Master Diagnosis')
@section('subheader', 'Daftar penyebab utama yang dapat dihasilkan oleh rule')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">Master Diagnosis</h1>
            <p class="ba-muted mb-0 small">Total {{ $diagnoses->total() }} diagnosis. Diagnosis yang dipakai rule tidak dihapus, tetapi dinonaktifkan.</p>
        </div>
        <a class="btn ba-btn-primary text-white" href="{{ route('admin.intelligence.diagnoses.create') }}">Tambah Diagnosis</a>
    </div>

    <div class="ba-card mb-3">
        <div class="ba-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold" for="search">Cari</label>
                    <input class="form-control" id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Kode atau nama diagnosis">
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
                    <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.diagnoses.index') }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="ba-card">
        <div class="table-responsive">
            <table class="table ba-table ba-table-mobile align-middle mb-0">
                <thead>
                    <tr>
                        <th>Diagnosis</th>
                        <th>Severity</th>
                        <th>Prioritas</th>
                        <th>Rule</th>
                        <th>Rekomendasi</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($diagnoses as $diagnosis)
                        <tr>
                            <td data-label="Diagnosis">
                                <div class="fw-semibold">{{ $diagnosis->name }}</div>
                                <div class="small ba-muted">{{ $diagnosis->code }}</div>
                            </td>
                            <td data-label="Severity"><span class="ba-badge {{ $diagnosis->severity->badgeClass() }}">{{ $diagnosis->severity->label() }}</span></td>
                            <td data-label="Prioritas">{{ $diagnosis->priority }}</td>
                            <td data-label="Rule">{{ $diagnosis->rules_count }} rule</td>
                            <td data-label="Rekomendasi">{{ $diagnosis->recommendations_count }} rekomendasi</td>
                            <td data-label="Status"><span class="badge {{ $diagnosis->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $diagnosis->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td data-label="Aksi" class="text-end">
                                <div class="d-flex justify-content-end flex-wrap gap-2">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.intelligence.diagnoses.edit', $diagnosis) }}">Edit</a>
                                    @if ($diagnosis->is_active)
                                        <form method="POST" action="{{ route('admin.intelligence.diagnoses.deactivate', $diagnosis) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Nonaktifkan</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.intelligence.diagnoses.activate', $diagnosis) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success" type="submit">Aktifkan</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7"><div class="ba-empty">Belum ada master diagnosis.</div></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($diagnoses->hasPages())
            <div class="ba-card-body border-top">{{ $diagnoses->links() }}</div>
        @endif
    </div>
@endsection
