@extends('admin.layouts.intelligence')

@section('title', $profile->exists ? 'Edit Profil UMKM' : 'Tambah Profil UMKM')
@section('header', $profile->exists ? 'Edit Profil UMKM' : 'Tambah Profil UMKM')
@section('subheader', 'Data identitas usaha yang digunakan untuk analisis')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">{{ $profile->exists ? 'Edit Profil UMKM' : 'Tambah Profil UMKM' }}</h1>
            <p class="ba-muted mb-0 small">Satu akun pemilik dapat memiliki lebih dari satu profil usaha.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.profiles.index') }}">Kembali</a>
    </div>

    <form method="POST" action="{{ $profile->exists ? route('admin.intelligence.profiles.update', $profile) : route('admin.intelligence.profiles.store') }}">
        @csrf
        @if ($profile->exists)
            @method('PUT')
        @endif

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="ba-card">
                    <div class="ba-card-body">
                        <h2 class="h6 fw-bold mb-3">Identitas Usaha</h2>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="id_user">Pemilik <span class="text-danger">*</span></label>
                                <select class="form-select @error('id_user') is-invalid @enderror" id="id_user" name="id_user" required>
                                    <option value="">Pilih pemilik</option>
                                    @foreach ($owners as $owner)
                                        <option value="{{ $owner->id_user }}" @selected((string) old('id_user', $profile->id_user) === (string) $owner->id_user)>{{ $owner->full_name }} ({{ $owner->email }})@if ($owner->role !== 'sme_owner') — nonaktif @endif</option>
                                    @endforeach
                                </select>
                                @error('id_user')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if ($owners->isEmpty())
                                    <div class="form-text text-danger">Belum ada pengguna dengan role Pemilik UMKM. Tambahkan pengguna terlebih dahulu.</div>
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="id_category">Kategori Usaha <span class="text-danger">*</span></label>
                                <select class="form-select @error('id_category') is-invalid @enderror" id="id_category" name="id_category" required>
                                    <option value="">Pilih kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id_category }}" @selected((string) old('id_category', $profile->id_category) === (string) $category->id_category)>{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                                @error('id_category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="business_name">Nama Usaha <span class="text-danger">*</span></label>
                                <input class="form-control @error('business_name') is-invalid @enderror" id="business_name" name="business_name" value="{{ old('business_name', $profile->business_name) }}" required maxlength="100">
                                @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="legal_type">Badan Hukum</label>
                                <input class="form-control @error('legal_type') is-invalid @enderror" id="legal_type" name="legal_type" value="{{ old('legal_type', $profile->legal_type) }}" maxlength="100" placeholder="Contoh: Perorangan, CV, PT">
                                @error('legal_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="city">Kota <span class="text-danger">*</span></label>
                                <input class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $profile->city) }}" required maxlength="100">
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="established_year">Tahun Berdiri <span class="text-danger">*</span></label>
                                <input class="form-control @error('established_year') is-invalid @enderror" id="established_year" type="number" name="established_year" value="{{ old('established_year', $profile->established_year) }}" required min="1900" max="{{ date('Y') }}">
                                @error('established_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="address">Alamat</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" maxlength="2000">{{ old('address', $profile->address) }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="ba-card ba-card-body ba-form-summary">
                    <h2 class="h6 fw-bold mb-2">Ringkasan</h2>
                    <p class="small ba-muted mb-3">Profil ini menjadi acuan data bisnis dan diagnosis.</p>
                    <dl class="row small mb-3">
                        <dt class="col-5">Nama usaha</dt>
                        <dd class="col-7">{{ old('business_name', $profile->business_name) ?: '-' }}</dd>
                        <dt class="col-5">Kota</dt>
                        <dd class="col-7">{{ old('city', $profile->city) ?: '-' }}</dd>
                        <dt class="col-5">Status</dt>
                        <dd class="col-7">{{ $profile->exists ? 'Profil terdaftar' : 'Profil baru' }}</dd>
                    </dl>
                    <button class="btn ba-btn-primary text-white w-100" type="submit">{{ $profile->exists ? 'Simpan Perubahan' : 'Tambah Profil' }}</button>
                </div>
            </div>
        </div>
    </form>
@endsection
