@extends('admin.layouts.intelligence')

@section('title', $recommendation->exists ? 'Edit Rekomendasi' : 'Tambah Rekomendasi')
@section('header', $recommendation->exists ? 'Edit Rekomendasi' : 'Tambah Rekomendasi')
@section('subheader', 'Rekomendasi tindakan untuk diagnosis tertentu')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">{{ $recommendation->exists ? 'Edit Rekomendasi' : 'Tambah Rekomendasi' }}</h1>
            <p class="ba-muted mb-0 small">Gunakan satu baris untuk setiap langkah action plan.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.recommendations.index') }}">Kembali</a>
    </div>

    <form method="POST" action="{{ $recommendation->exists ? route('admin.intelligence.recommendations.update', $recommendation) : route('admin.intelligence.recommendations.store') }}">
        @csrf
        @if ($recommendation->exists)
            @method('PUT')
        @endif

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="ba-card">
                    <div class="ba-card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="id_diagnosis">Diagnosis <span class="text-danger">*</span></label>
                                <select class="form-select @error('id_diagnosis') is-invalid @enderror" id="id_diagnosis" name="id_diagnosis" required>
                                    <option value="">Pilih diagnosis</option>
                                    @foreach ($diagnoses as $diagnosis)
                                        <option value="{{ $diagnosis->id }}" @selected((string) old('id_diagnosis', $recommendation->id_diagnosis) === (string) $diagnosis->id)>{{ $diagnosis->name }}@if (! $diagnosis->is_active) — nonaktif @endif</option>
                                    @endforeach
                                </select>
                                @error('id_diagnosis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-5">
                                <label class="form-label fw-semibold" for="code">Kode <span class="text-danger">*</span></label>
                                <input class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $recommendation->code) }}" required maxlength="100" placeholder="increase_marketing">
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-7">
                                <label class="form-label fw-semibold" for="title">Judul <span class="text-danger">*</span></label>
                                <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $recommendation->title) }}" required maxlength="150">
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="description">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" maxlength="2000">{{ old('description', $recommendation->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="action_steps_text">Langkah Action Plan</label>
                                <textarea class="form-control @error('action_steps_text') is-invalid @enderror" id="action_steps_text" name="action_steps_text" rows="5" maxlength="5000" placeholder="Contoh:&#10;Audit biaya operasional bulanan&#10;Negosiasi ulang harga supplier">{{ old('action_steps_text', implode("\n", $recommendation->action_steps ?? [])) }}</textarea>
                                <div class="form-text">Satu baris menjadi satu langkah. Baris kosong diabaikan.</div>
                                @error('action_steps_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="priority">Prioritas <span class="text-danger">*</span></label>
                                <input class="form-control @error('priority') is-invalid @enderror" id="priority" type="number" name="priority" value="{{ old('priority', $recommendation->priority ?? 100) }}" required min="0" max="65535">
                                <div class="form-text">Angka lebih kecil ditampilkan lebih dahulu.</div>
                                @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" id="is_active" type="checkbox" role="switch" name="is_active" value="1" @checked(old('is_active', $recommendation->is_active ?? true))>
                                    <label class="form-check-label" for="is_active">Rekomendasi aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="ba-card ba-card-body ba-form-summary">
                    <h2 class="h6 fw-bold mb-2">Ringkasan</h2>
                    <p class="small ba-muted mb-3">Rekomendasi muncul pada hasil diagnosis dan menjadi dasar action plan UMKM.</p>
                    <dl class="row small mb-3">
                        <dt class="col-5">Diagnosis</dt>
                        <dd class="col-7">{{ $recommendation->diagnosis?->name ?? '-' }}</dd>
                        <dt class="col-5">Langkah</dt>
                        <dd class="col-7" id="summary-steps">{{ count($recommendation->action_steps ?? []) }} langkah</dd>
                    </dl>
                    <button class="btn ba-btn-primary text-white w-100" type="submit">{{ $recommendation->exists ? 'Simpan Perubahan' : 'Tambah Rekomendasi' }}</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const steps = document.getElementById('action_steps_text');
        const summarySteps = document.getElementById('summary-steps');
        if (steps && summarySteps) {
            steps.addEventListener('input', () => {
                const count = steps.value.split(/\r?\n/).filter((line) => line.trim() !== '').length;
                summarySteps.textContent = `${count} langkah`;
            });
        }
    </script>
@endpush
