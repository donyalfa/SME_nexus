<section>
    <form method="post" action="{{ route('password.update') }}" class="d-grid gap-3">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="form-label small fw-medium">{{ __('Password Saat Ini') }}</label>
            <div class="input-group">
                <span class="input-group-text">
                    <svg class="pf-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </span>
                <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" placeholder="••••••••"
                       class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif">
                <button type="button" class="btn btn-outline-secondary" data-toggle-password="update_password_current_password" aria-label="Tampilkan password">
                    <svg class="pf-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
                @foreach ($errors->updatePassword->get('current_password') as $message)
                    <div class="invalid-feedback">{{ $message }}</div>
                @endforeach
            </div>
        </div>

        <div>
            <label for="update_password_password" class="form-label small fw-medium">{{ __('Password Baru') }}</label>
            <div class="input-group">
                <span class="input-group-text">
                    <svg class="pf-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </span>
                <input id="update_password_password" name="password" type="password" autocomplete="new-password" placeholder="••••••••"
                       class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif">
                <button type="button" class="btn btn-outline-secondary" data-toggle-password="update_password_password" aria-label="Tampilkan password">
                    <svg class="pf-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
                @foreach ($errors->updatePassword->get('password') as $message)
                    <div class="invalid-feedback">{{ $message }}</div>
                @endforeach
            </div>
        </div>

        <div>
            <label for="update_password_password_confirmation" class="form-label small fw-medium">{{ __('Konfirmasi Password') }}</label>
            <div class="input-group">
                <span class="input-group-text">
                    <svg class="pf-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </span>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="••••••••"
                       class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif">
                <button type="button" class="btn btn-outline-secondary" data-toggle-password="update_password_password_confirmation" aria-label="Tampilkan password">
                    <svg class="pf-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
                @foreach ($errors->updatePassword->get('password_confirmation') as $message)
                    <div class="invalid-feedback">{{ $message }}</div>
                @endforeach
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 pt-2">
            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4">
                <svg class="pf-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('Simpan') }}
            </button>

            @if (session('status') === 'password-updated')
                <span class="small text-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)">
                    {{ __('Tersimpan.') }}
                </span>
            @endif
        </div>
    </form>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.togglePassword);
                input.type = input.type === 'password' ? 'text' : 'password';
            });
        });
    </script>
</section>
