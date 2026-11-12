@extends('admin.layouts.intelligence')

@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')
@section('header', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')
@section('subheader', 'Kategori usaha untuk profil UMKM')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">{{ $category->exists ? 'Edit Kategori' : 'Tambah Kategori' }}</h1>
            <p class="ba-muted mb-0 small">Kategori yang masih digunakan oleh profil tidak dapat dihapus.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.categories.index') }}">Kembali</a>
    </div>

    <form method="POST" action="{{ $category->exists ? route('admin.intelligence.categories.update', $category) : route('admin.intelligence.categories.store') }}">
        @csrf
        @if ($category->exists)
            @method('PUT')
        @endif

        <div class="row g-3">
            <div class="col-12 col-lg-7">
                <div class="ba-card">
                    <div class="ba-card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="category_name">Nama Kategori <span class="text-danger">*</span></label>
                            <input class="form-control @error('category_name') is-invalid @enderror" id="category_name" name="category_name" value="{{ old('category_name', $category->category_name) }}" required maxlength="100">
                            @error('category_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold" for="deskripsi">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="4" maxlength="2000">{{ old('deskripsi', $category->deskripsi) }}</textarea>
                            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="ba-card ba-card-body ba-form-summary">
                    <h2 class="h6 fw-bold mb-2">Ringkasan</h2>
                    <p class="small ba-muted mb-3">Kategori membantu mengelompokkan profil UMKM.</p>
                    <button class="btn ba-btn-primary text-white w-100" type="submit">{{ $category->exists ? 'Simpan Perubahan' : 'Tambah Kategori' }}</button>
                </div>
            </div>
        </div>
    </form>
@endsection
