<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 fw-bold mb-0">{{ __('Profile') }}</h2>
            <p class="small text-body-secondary mb-0">Kelola informasi akun Anda</p>
        </div>
    </x-slot>

    <style>
        .pf-chip { width: 48px; height: 48px; border-radius: 12px; display: grid; place-items: center; flex: none; color: #fff; font-weight: 700; font-size: 1.1rem; }
        .pf-chip svg { width: 24px; height: 24px; }
        .pf-card { border-radius: 1rem; }
        .pf-form { max-width: 36rem; }
        .pf-form-wide { max-width: 100%; }
        .pf-icon { width: 18px; height: 18px; }
    </style>

    <div class="mx-auto d-grid gap-4" style="max-width: 64rem;">

        {{-- Informasi Profil --}}
        <div class="card pf-card shadow-sm">
            <div class="card-body p-4 p-sm-5">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="pf-chip" style="background: linear-gradient(135deg,#6366f1,#a855f7)">
                        {{ strtoupper(substr(Auth::user()->full_name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="h5 fw-semibold mb-0">Informasi Profil</h3>
                        <p class="small text-body-secondary mb-0">Perbarui informasi profil Anda</p>
                    </div>
                </div>
                <div class="pf-form">
                    @include('umkm.profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        {{-- Profil Perusahaan --}}
        <div class="card pf-card shadow-sm">
            <div class="card-body p-4 p-sm-5">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="pf-chip" style="background: linear-gradient(135deg,#0ea5e9,#2563eb)">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="h5 fw-semibold mb-0">Profil Perusahaan</h3>
                        <p class="small text-body-secondary mb-0">Kelola informasi identitas dan detail bisnis Anda</p>
                    </div>
                </div>
                <div class="pf-form-wide">
                    @include('umkm.profile.partials.update-business-profile-form')
                </div>
            </div>
        </div>

        {{-- Ubah Password --}}
        <div class="card pf-card shadow-sm">
            <div class="card-body p-4 p-sm-5">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="pf-chip" style="background: linear-gradient(135deg,#10b981,#14b8a6)">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="h5 fw-semibold mb-0">Ubah Password</h3>
                        <p class="small text-body-secondary mb-0">Pastikan password Anda kuat dan aman</p>
                    </div>
                </div>
                <div class="pf-form">
                    @include('umkm.profile.partials.update-password-form')
                </div>
            </div>
        </div>

        {{-- Hapus Akun --}}
        <div class="card pf-card shadow-sm border-danger-subtle">
            <div class="card-body p-4 p-sm-5">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="pf-chip" style="background: linear-gradient(135deg,#ef4444,#f43f5e)">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="h5 fw-semibold mb-0">Hapus Akun</h3>
                        <p class="small text-body-secondary mb-0">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
                <div class="pf-form">
                    @include('umkm.profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
