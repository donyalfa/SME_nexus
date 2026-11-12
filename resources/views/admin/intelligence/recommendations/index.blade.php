@extends('admin.layouts.intelligence')

@section('title', 'Rekomendasi')
@section('header', 'Rekomendasi')
@section('subheader', 'Tindak lanjut untuk setiap master diagnosis')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">Rekomendasi</h1>
            <p class="ba-muted mb-0 small">Total {{ $recommendations->total() }} rekomendasi. Satu rekomendasi terhubung ke satu diagnosis.</p>
        </div>
        <a class="btn ba-btn-primary text-white" href="{{ route('admin.intelligence.recommendations.create') }}">Tambah Rekomendasi</a>
    </div>

    <div class="ba-card mb-3">
        <div class="ba-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold" for="search">Cari</label>
                    <input class="form-control" id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Kode atau judul">
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
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="is_active">Status</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="">Semua</option>
                        <option value="1" @selected(($filters['is_active'] ?? '') === '1')>Aktif</option>
                        <option value="0" @selected(($filters['is_active'] ?? '') === '0')>Nonaktif</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button class="btn btn-outline-secondary flex-fill" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.recommendations.index') }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="ba-card">
        <div class="table-responsive">
            <table class="table ba-table ba-table-mobile align-middle mb-0">
                <thead>
                    <tr>
                        <th>Rekomendasi</th>
                        <th>Diagnosis</th>
                        <th>Prioritas</th>
                        <th>Langkah</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recommendations as $recommendation)
                        <tr>
                            <td data-label="Rekomendasi">
                                <div class="fw-semibold">{{ $recommendation->title }}</div>
                                <div class="small ba-muted">{{ $recommendation->code }}</div>
                            </td>
                            <td data-label="Diagnosis">{{ $recommendation->diagnosis?->name ?? '-' }}</td>
                            <td data-label="Prioritas">{{ $recommendation->priority }}</td>
                            <td data-label="Langkah">{{ count($recommendation->action_steps ?? []) }} langkah</td>
                            <td data-label="Status"><span class="badge {{ $recommendation->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $recommendation->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td data-label="Aksi" class="text-end">
                                <div class="d-flex justify-content-end flex-wrap gap-2">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.intelligence.recommendations.edit', $recommendation) }}">Edit</a>
                                    @if ($recommendation->is_active)
                                        <form method="POST" action="{{ route('admin.intelligence.recommendations.deactivate', $recommendation) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Nonaktifkan</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.intelligence.recommendations.activate', $recommendation) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success" type="submit">Aktifkan</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"><div class="ba-empty">Belum ada rekomendasi.</div></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($recommendations->hasPages())
            <div class="ba-card-body border-top">{{ $recommendations->links() }}</div>
        @endif
    </div>
@endsection
