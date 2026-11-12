@extends('admin.layouts.intelligence')

@section('title', 'Profil UMKM')
@section('header', 'Profil UMKM')
@section('subheader', 'Kelola profil usaha yang terhubung dengan akun pemilik')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">Profil UMKM</h1>
            <p class="ba-muted mb-0 small">Total {{ $profiles->total() }} profil usaha.</p>
        </div>
        <a class="btn ba-btn-primary text-white" href="{{ route('admin.intelligence.profiles.create') }}">Tambah Profil</a>
    </div>

    <div class="ba-card mb-3">
        <div class="ba-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold" for="search">Cari</label>
                    <input class="form-control" id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nama usaha atau kota">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="id_category">Kategori</label>
                    <select class="form-select" id="id_category" name="id_category">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id_category }}" @selected((string) ($filters['id_category'] ?? '') === (string) $category->id_category)>{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="id_user">Pemilik</label>
                    <select class="form-select" id="id_user" name="id_user">
                        <option value="">Semua pemilik</option>
                        @foreach ($owners as $owner)
                            <option value="{{ $owner->id_user }}" @selected((string) ($filters['id_user'] ?? '') === (string) $owner->id_user)>{{ $owner->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button class="btn btn-outline-secondary flex-fill" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.profiles.index') }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="ba-card">
        <div class="table-responsive">
            <table class="table ba-table ba-table-mobile align-middle mb-0">
                <thead>
                    <tr>
                        <th>Usaha</th>
                        <th>Pemilik</th>
                        <th>Kategori</th>
                        <th>Kota</th>
                        <th>Berdiri</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($profiles as $profile)
                        <tr>
                            <td data-label="Usaha">
                                <div class="fw-semibold">{{ $profile->business_name }}</div>
                                <div class="small ba-muted">{{ $profile->legal_type ?: 'Tanpa badan hukum' }}</div>
                            </td>
                            <td data-label="Pemilik">{{ $profile->user?->full_name ?? '-' }}</td>
                            <td data-label="Kategori">{{ $profile->category?->category_name ?? '-' }}</td>
                            <td data-label="Kota">{{ $profile->city }}</td>
                            <td data-label="Berdiri">{{ $profile->established_year }}</td>
                            <td data-label="Aksi" class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.intelligence.profiles.edit', $profile) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.intelligence.profiles.destroy', $profile) }}" data-confirm="Profil UMKM beserta data bisnis, riwayat diagnosis, alert, listing, dan metrik terkait akan dihapus permanen. Tindakan ini tidak dapat dibatalkan." data-confirm-title="Hapus profil ini?" data-confirm-ok="Ya, hapus">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"><div class="ba-empty">Belum ada profil UMKM yang cocok dengan filter.</div></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($profiles->hasPages())
            <div class="ba-card-body border-top">{{ $profiles->links() }}</div>
        @endif
    </div>
@endsection
