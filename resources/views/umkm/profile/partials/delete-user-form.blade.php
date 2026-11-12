<section class="d-grid gap-3">
    <div class="alert alert-danger small mb-0">
        {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Sebelum menghapus akun Anda, harap unduh data atau informasi apa pun yang ingin Anda simpan.') }}
    </div>

    <div>
        <button type="button" class="btn btn-danger d-inline-flex align-items-center gap-2 px-4"
                data-bs-toggle="modal" data-bs-target="#confirm-user-deletion">
            <svg class="pf-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            {{ __('Hapus Akun') }}
        </button>
    </div>

    {{-- Modal Bootstrap --}}
    <div class="modal fade" id="confirm-user-deletion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('delete')

                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title h5 fw-semibold">{{ __('Apakah Anda yakin ingin menghapus akun Anda?') }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <p class="small text-body-secondary">
                        {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Silakan masukkan password Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.') }}
                    </p>

                    <label for="delete_password" class="visually-hidden">{{ __('Password') }}</label>
                    <input id="delete_password" name="password" type="password"
                           class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif"
                           placeholder="{{ __('Password') }}">
                    @foreach ($errors->userDeletion->get('password') as $message)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @endforeach
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Hapus Akun') }}</button>
                </div>
            </form>
        </div>
    </div>

    @if ($errors->userDeletion->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('confirm-user-deletion')).show();
            });
        </script>
    @endif
</section>
