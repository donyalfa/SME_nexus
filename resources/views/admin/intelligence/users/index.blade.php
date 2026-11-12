@extends('admin.layouts.intelligence')

@section('title', 'Pengguna')
@section('header', 'Pengguna')
@section('subheader', 'Kelola akun admin, pemilik UMKM, mentor, dan investor')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">Pengguna</h1>
            <p class="ba-muted mb-0 small">Total {{ $users->total() }} akun terdaftar.</p>
        </div>
        <a class="btn ba-btn-primary text-white" href="{{ route('admin.intelligence.users.create') }}">Tambah Pengguna</a>
    </div>

    <div class="ba-card mb-3">
        <div class="ba-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label small fw-semibold" for="search">Cari</label>
                    <input class="form-control" id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nama atau email">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold" for="role">Role</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">Semua role</option>
                        @foreach ($roles as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['role'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold" for="is_verified">Verifikasi</label>
                    <select class="form-select" id="is_verified" name="is_verified">
                        <option value="">Semua</option>
                        <option value="1" @selected(($filters['is_verified'] ?? '') === '1')>Terverifikasi</option>
                        <option value="0" @selected(($filters['is_verified'] ?? '') === '0')>Belum</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button class="btn btn-outline-secondary flex-fill" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.users.index') }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="ba-card">
        <div class="table-responsive">
            <table class="table ba-table ba-table-mobile align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td data-label="Nama">
                                <div class="fw-semibold">{{ $user->full_name }}</div>
                                <div class="small ba-muted">ID {{ $user->id_user }}</div>
                            </td>
                            <td data-label="Email">{{ $user->email }}</td>
                            <td data-label="Role">{{ $roles[$user->role] ?? $user->role }}</td>
                            <td data-label="Status">
                                @if ($user->is_verified)
                                    <span class="badge text-bg-success">Terverifikasi</span>
                                @else
                                    <span class="badge text-bg-secondary">Belum verifikasi</span>
                                @endif
                            </td>
                            <td data-label="Aksi" class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.intelligence.users.edit', $user) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.intelligence.users.destroy', $user) }}" data-confirm="Pengguna ini beserta data terkait akan ikut terhapus dan tidak dapat dikembalikan." data-confirm-title="Hapus pengguna ini?" data-confirm-ok="Ya, hapus">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="ba-empty">Belum ada pengguna yang cocok dengan filter.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="ba-card-body border-top">{{ $users->links() }}</div>
        @endif
    </div>
@endsection
