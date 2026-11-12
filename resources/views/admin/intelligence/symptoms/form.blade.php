@extends('admin.layouts.intelligence')

@section('title', $symptom->exists ? 'Edit Gejala' : 'Tambah Gejala')
@section('header', $symptom->exists ? 'Edit Gejala' : 'Tambah Gejala')
@section('subheader', 'Definisikan kondisi metrik yang memicu gejala')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">{{ $symptom->exists ? 'Edit Gejala' : 'Tambah Gejala' }}</h1>
            <p class="ba-muted mb-0 small">Gejala dievaluasi dari metrik bisnis sebelum rule diagnosis dijalankan.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.symptoms.index') }}">Kembali</a>
    </div>

    <form method="POST" action="{{ $symptom->exists ? route('admin.intelligence.symptoms.update', $symptom) : route('admin.intelligence.symptoms.store') }}">
        @csrf
        @if ($symptom->exists)
            @method('PUT')
        @endif

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="ba-card">
                    <div class="ba-card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-5">
                                <label class="form-label fw-semibold" for="code">Kode <span class="text-danger">*</span></label>
                                <input class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $symptom->code) }}" required maxlength="100" placeholder="revenue_declined">
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-7">
                                <label class="form-label fw-semibold" for="name">Nama Gejala <span class="text-danger">*</span></label>
                                <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $symptom->name) }}" required maxlength="150" placeholder="Penurunan omzet">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="description">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2" maxlength="2000">{{ old('description', $symptom->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="metric_key">Kunci Metrik <span class="text-danger">*</span></label>
                                <input class="form-control @error('metric_key') is-invalid @enderror" id="metric_key" name="metric_key" value="{{ old('metric_key', $symptom->metric_key) }}" required maxlength="100" list="metric-key-options" placeholder="revenue_change_percent">
                                <datalist id="metric-key-options">
                                    <option value="revenue_change_percent">Perubahan omzet/pendapatan (%)</option>
                                    <option value="customer_change_percent">Perubahan jumlah pelanggan (%)</option>
                                    <option value="operating_expense_ratio">Rasio biaya operasional (%)</option>
                                    <option value="profit_margin">Margin laba (%)</option>
                                </datalist>
                                @error('metric_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold" for="operator">Operator <span class="text-danger">*</span></label>
                                <select class="form-select @error('operator') is-invalid @enderror" id="operator" name="operator" required>
                                    @foreach ($operators as $operator)
                                        <option value="{{ $operator->value }}" @selected(old('operator', $symptom->operator?->value) === $operator->value)>{{ $operator->symbol() }} {{ $operator->label() }}</option>
                                    @endforeach
                                </select>
                                @error('operator')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold" for="threshold">Ambang <span class="text-danger">*</span></label>
                                <input class="form-control @error('threshold') is-invalid @enderror" id="threshold" type="number" step="0.0001" name="threshold" value="{{ old('threshold', $symptom->threshold) }}" required>
                                @error('threshold')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="severity">Severity <span class="text-danger">*</span></label>
                                <select class="form-select @error('severity') is-invalid @enderror" id="severity" name="severity" required>
                                    @foreach ($severities as $severity)
                                        <option value="{{ $severity->value }}" @selected(old('severity', $symptom->severity?->value) === $severity->value)>{{ $severity->label() }}</option>
                                    @endforeach
                                </select>
                                @error('severity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" id="is_active" type="checkbox" role="switch" name="is_active" value="1" @checked(old('is_active', $symptom->is_active ?? true))>
                                    <label class="form-check-label" for="is_active">Gejala aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="ba-card ba-card-body ba-form-summary">
                    <h2 class="h6 fw-bold mb-2">Pratinjau Kondisi</h2>
                    <p class="small ba-muted">Gejala terdeteksi jika nilai metrik memenuhi operator dan ambang berikut.</p>
                    <div class="alert alert-light border small mb-3" id="condition-preview">
                        {{ $symptom->metric_key ?: 'metric_key' }} {{ $symptom->operator?->symbol() ?? '?' }} {{ $symptom->threshold ?? 'threshold' }}
                    </div>
                    <button class="btn ba-btn-primary text-white w-100" type="submit">{{ $symptom->exists ? 'Simpan Perubahan' : 'Tambah Gejala' }}</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const metric = document.getElementById('metric_key');
        const operator = document.getElementById('operator');
        const threshold = document.getElementById('threshold');
        const preview = document.getElementById('condition-preview');

        const refreshPreview = () => {
            if (!preview) return;
            const symbol = operator?.selectedOptions?.[0]?.textContent?.split(' ')?.[0] ?? '?';
            preview.textContent = `${metric?.value || 'metric_key'} ${symbol} ${threshold?.value || 'threshold'}`;
        };

        [metric, operator, threshold].forEach((element) => element?.addEventListener('input', refreshPreview));
        [operator].forEach((element) => element?.addEventListener('change', refreshPreview));
    </script>
@endpush
