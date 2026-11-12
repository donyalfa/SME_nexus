@extends('admin.layouts.intelligence')

@section('title', $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('header', $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('subheader', 'Akun pengguna dan hak akses modul Intelligence')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">{{ $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h1>
            <p class="ba-muted mb-0 small">Isi data akun dengan benar. Email digunakan untuk masuk.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.users.index') }}">Kembali</a>
    </div>

    <form method="POST" action="{{ $user->exists ? route('admin.intelligence.users.update', $user) : route('admin.intelligence.users.store') }}">
        @csrf
        @if ($user->exists)
            @method('PUT')
        @endif

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="ba-card">
                    <div class="ba-card-body">
                        <h2 class="h6 fw-bold mb-3">Informasi Akun</h2>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="full_name">Nama Lengkap <span class="text-danger">*</span></label>
                                <input class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}" required maxlength="100">
                                @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="email">Email <span class="text-danger">*</span></label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="100">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="role">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    @foreach ($roles as $value => $label)
                                        <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="password">Password {{ $user->exists ? '(opsional)' : '' }} <span class="text-danger">{{ $user->exists ? '' : '*' }}</span></label>
                                <input class="form-control @error('password') is-invalid @enderror" id="password" type="password" name="password" {{ $user->exists ? '' : 'required' }} autocomplete="new-password">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if ($user->exists)
                                    <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="password_confirmation">Konfirmasi Password</label>
                                <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password">
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_verified" value="0">
                                    <input class="form-check-input" id="is_verified" type="checkbox" role="switch" name="is_verified" value="1" @checked(old('is_verified', $user->is_verified ?? true))>
                                    <label class="form-check-label" for="is_verified">Email sudah terverifikasi</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="ba-card ba-card-body ba-form-summary">
                    <h2 class="h6 fw-bold mb-2">Ringkasan</h2>
                    <p class="small ba-muted mb-3">Pastikan role sesuai tanggung jawab pengguna.</p>
                    <dl class="row small mb-3">
                        <dt class="col-5">Role</dt>
                        <dd class="col-7" id="summary-role">{{ $roles[old('role', $user->role)] ?? '-' }}</dd>
                        <dt class="col-5">Status</dt>
                        <dd class="col-7">{{ $user->exists ? 'Akun terdaftar' : 'Akun baru' }}</dd>
                    </dl>
                    <button class="btn ba-btn-primary text-white w-100" type="submit">{{ $user->exists ? 'Simpan Perubahan' : 'Tambah Pengguna' }}</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const roleSelect = document.getElementById('role');
        const roleSummary = document.getElementById('summary-role');
        if (roleSelect && roleSummary) {
            roleSelect.addEventListener('change', () => {
                roleSummary.textContent = roleSelect.options[roleSelect.selectedIndex]?.text ?? '-';
            });
        }
    </script>
@endpush
