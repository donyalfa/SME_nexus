<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="d-grid gap-3">
        @csrf
        @method('patch')

        {{-- Nama --}}
        <div>
            <label for="full_name" class="form-label small fw-medium">{{ __('Nama Lengkap') }}</label>
            <div class="input-group">
                <span class="input-group-text">
                    <svg class="pf-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </span>
                <input id="full_name" name="full_name" type="text" maxlength="100" required autofocus autocomplete="name"
                       class="form-control @error('full_name') is-invalid @enderror"
                       value="{{ old('full_name', $user->full_name) }}" placeholder="Nama lengkap Anda">
                @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="form-label small fw-medium">{{ __('Email') }}</label>
            <div class="input-group">
                <span class="input-group-text">
                    <svg class="pf-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </span>
                <input id="email" name="email" type="email" maxlength="100" required autocomplete="username"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}" placeholder="nama@email.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning small mt-3 mb-0">
                    <p class="mb-2">{{ __('Alamat email Anda belum diverifikasi.') }}</p>
                    <button form="send-verification" class="btn btn-link btn-sm p-0 fw-medium">
                        {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                    </button>
                    @if (session('status') === 'verification-link-sent')
                        <p class="text-success mt-2 mb-0">{{ __('Link verifikasi baru telah dikirim ke alamat email Anda.') }}</p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Simpan --}}
        <div class="d-flex align-items-center gap-3 pt-2">
            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4">
                <svg class="pf-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('Simpan') }}
            </button>

            @if (session('status') === 'profile-updated')
                <span class="small text-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)">
                    {{ __('Tersimpan.') }}
                </span>
            @endif
        </div>
    </form>
</section>
