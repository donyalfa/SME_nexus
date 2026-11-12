@extends('admin.layouts.intelligence')

@section('title', $diagnosis->exists ? 'Edit Diagnosis' : 'Tambah Diagnosis')
@section('header', $diagnosis->exists ? 'Edit Diagnosis' : 'Tambah Diagnosis')
@section('subheader', 'Master penyebab utama hasil diagnosis')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">{{ $diagnosis->exists ? 'Edit Diagnosis' : 'Tambah Diagnosis' }}</h1>
            <p class="ba-muted mb-0 small">Severity dan prioritas menentukan pemenang saat beberapa rule cocok.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.diagnoses.index') }}">Kembali</a>
    </div>

    <form method="POST" action="{{ $diagnosis->exists ? route('admin.intelligence.diagnoses.update', $diagnosis) : route('admin.intelligence.diagnoses.store') }}">
        @csrf
        @if ($diagnosis->exists)
            @method('PUT')
        @endif

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="ba-card">
                    <div class="ba-card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-5">
                                <label class="form-label fw-semibold" for="code">Kode <span class="text-danger">*</span></label>
                                <input class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $diagnosis->code) }}" required maxlength="100" placeholder="low_sales">
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-7">
                                <label class="form-label fw-semibold" for="name">Nama Diagnosis <span class="text-danger">*</span></label>
                                <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $diagnosis->name) }}" required maxlength="150" placeholder="Penurunan penjualan">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="description">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" maxlength="2000">{{ old('description', $diagnosis->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="severity">Severity <span class="text-danger">*</span></label>
                                <select class="form-select @error('severity') is-invalid @enderror" id="severity" name="severity" required>
                                    @foreach ($severities as $severity)
                                        <option value="{{ $severity->value }}" @selected(old('severity', $diagnosis->severity?->value) === $severity->value)>{{ $severity->label() }}</option>
                                    @endforeach
                                </select>
                                @error('severity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="priority">Prioritas <span class="text-danger">*</span></label>
                                <input class="form-control @error('priority') is-invalid @enderror" id="priority" type="number" name="priority" value="{{ old('priority', $diagnosis->priority ?? 100) }}" required min="0" max="65535">
                                <div class="form-text">Angka lebih kecil diprioritaskan lebih tinggi.</div>
                                @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" id="is_active" type="checkbox" role="switch" name="is_active" value="1" @checked(old('is_active', $diagnosis->is_active ?? true))>
                                    <label class="form-check-label" for="is_active">Diagnosis aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="ba-card ba-card-body ba-form-summary">
                    <h2 class="h6 fw-bold mb-2">Ringkasan</h2>
                    <p class="small ba-muted mb-3">Diagnosis yang dipakai rule harus tetap aktif agar dapat menghasilkan hasil baru.</p>
                    <dl class="row small mb-3">
                        <dt class="col-5">Severity</dt>
                        <dd class="col-7">{{ $diagnosis->severity?->label() ?? '-' }}</dd>
                        <dt class="col-5">Prioritas</dt>
                        <dd class="col-7">{{ old('priority', $diagnosis->priority ?? 100) }}</dd>
                    </dl>
                    <button class="btn ba-btn-primary text-white w-100" type="submit">{{ $diagnosis->exists ? 'Simpan Perubahan' : 'Tambah Diagnosis' }}</button>
                </div>
            </div>
        </div>
    </form>
@endsection
