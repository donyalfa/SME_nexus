<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
                <h2 class="h4 fw-bold mb-1">Lengkapi Profil Usaha</h2>
                <p class="small text-body-secondary mb-0">Satu langkah lagi — ceritakan usaha Anda agar fitur sistem bekerja lebih akurat.</p>
            </div>
        </div>
    </x-slot>

    <style>
        .ob-card { border: 1px solid var(--nx-line, #dbe0ea); border-radius: 1rem; background: var(--nx-surface-solid, #fff); }
        .ob-section-title { display: flex; align-items: center; gap: .5rem; margin-bottom: 1rem; }
        .ob-section-ico { width: 34px; height: 34px; border-radius: .55rem; display: grid; place-items: center; color: #fff; flex: none; }
        .ob-section-ico svg { width: 18px; height: 18px; }
    </style>

    <div class="mx-auto" style="max-width: 56rem;">

        @if (session('csrf_expired'))
            <div class="alert alert-warning border-0 d-flex flex-wrap align-items-start gap-2 mb-4" role="status">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="flex-none mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="small">{{ session('csrf_expired') }}</span>
            </div>
        @endif

        @unless ($user->hasVerifiedEmail())
            <div class="alert alert-info border-0 d-flex flex-wrap align-items-start gap-2 mb-4" role="status">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="flex-none mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="small">
                    <strong>Email Anda belum diverifikasi.</strong>
                    Setelah profil usaha tersimpan, Anda akan diarahkan ke halaman verifikasi email.
                </span>
            </div>
        @endunless

        <form method="POST" action="{{ route('onboarding.business-profile.store') }}" novalidate onsubmit="let b = document.getElementById('submit-btn'); setTimeout(() => { b.disabled = true; b.innerHTML = 'Menyimpan...'; }, 10);">
            @csrf

            <div class="row g-4">

                {{-- Seksi: Identitas Usaha --}}
                <div class="col-12 col-lg-6">
                    <div class="ob-card p-4 h-100">
                        <div class="ob-section-title">
                            <span class="ob-section-ico" style="background: linear-gradient(135deg, #4B4FE0, #6A5CFF);">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </span>
                            <h3 class="h6 fw-bold mb-0">Identitas Usaha</h3>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="business_name">Nama Usaha <span class="text-danger">*</span></label>
                            <input class="form-control @error('business_name') is-invalid @enderror" id="business_name" name="business_name" value="{{ old('business_name') }}" required maxlength="100" placeholder="Contoh: Kopi Kita Store">
                            @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="id_category">Kategori Usaha <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_category') is-invalid @enderror" id="id_category" name="id_category" required>
                                <option value="">Pilih kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id_category }}" @selected((string) old('id_category') === (string) $category->id_category)>{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                            @error('id_category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if ($categories->isEmpty())
                                <div class="form-text text-danger">Belum ada kategori usaha. Jalankan seeder master data terlebih dahulu.</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="legal_type">Badan Hukum</label>
                            <input class="form-control" list="legal-types" id="legal_type" name="legal_type" value="{{ old('legal_type') }}" maxlength="100" placeholder="Contoh: Perorangan, CV, PT">
                            <datalist id="legal-types">
                                <option value="Perorangan"></option>
                                <option value="CV"></option>
                                <option value="PT"></option>
                                <option value="UD"></option>
                                <option value="Koperasi"></option>
                            </datalist>
                            <div class="form-text">Bisa dikosongkan bila usaha belum berbadan hukum.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="established_year">Tahun Berdiri <span class="text-danger">*</span></label>
                            <input class="form-control @error('established_year') is-invalid @enderror" id="established_year" type="number" name="established_year" value="{{ old('established_year', date('Y')) }}" required min="1900" max="{{ date('Y') }}">
                            @error('established_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold" for="description">Deskripsi Usaha</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" maxlength="1000" placeholder="Ceritakan ringkas apa yang dijual/layanan yang diberikan">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Seksi: Detail & Kontak --}}
                <div class="col-12 col-lg-6">
                    <div class="ob-card p-4 h-100">
                        <div class="ob-section-title">
                            <span class="ob-section-ico" style="background: linear-gradient(135deg, #0B9C7D, #4FE3C1);">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <h3 class="h6 fw-bold mb-0">Detail & Kontak</h3>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-semibold" for="city">Kota <span class="text-danger">*</span></label>
                                <input class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}" required maxlength="100" placeholder="Contoh: Bandung">
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-semibold" for="province">Provinsi</label>
                                <input class="form-control @error('province') is-invalid @enderror" id="province" name="province" value="{{ old('province') }}" maxlength="100" placeholder="Contoh: Jawa Barat">
                                @error('province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-semibold" for="postal_code">Kode Pos</label>
                                <input class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" maxlength="10">
                                @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-sm-8">
                                <label class="form-label fw-semibold" for="phone">No. Telepon / WA</label>
                                <input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" maxlength="20" placeholder="Contoh: +6281234567890">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="address">Alamat Lengkap</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" maxlength="2000" placeholder="Alamat tempat usaha">{{ old('address') }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-semibold" for="business_email">Email Usaha</label>
                                <input class="form-control @error('business_email') is-invalid @enderror" id="business_email" type="email" name="business_email" value="{{ old('business_email') }}" maxlength="100" placeholder="kontak@usahaanda.com">
                                @error('business_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-semibold" for="website">Website / Media Sosial</label>
                                <input class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website') }}" maxlength="255" placeholder="Contoh: instagram.com/usahakita">
                                @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-semibold" for="nib">NIB</label>
                                <input class="form-control @error('nib') is-invalid @enderror" id="nib" name="nib" value="{{ old('nib') }}" maxlength="50" placeholder="Nomor Induk Berusaha (opsional)">
                                @error('nib')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-semibold" for="npwp">NPWP</label>
                                <input class="form-control @error('npwp') is-invalid @enderror" id="npwp" name="npwp" value="{{ old('npwp') }}" maxlength="50" placeholder="NPWP usaha (opsional)">
                                @error('npwp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="employee_count">Jumlah Karyawan</label>
                                <input class="form-control @error('employee_count') is-invalid @enderror" id="employee_count" type="number" name="employee_count" value="{{ old('employee_count') }}" min="0" placeholder="Contoh: 5">
                                @error('employee_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Aksi --}}
            <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
                <button type="submit" id="submit-btn" class="btn btn-primary px-4">
                    Simpan & Lanjut
                </button>
            </div>
        </form>
    </div>

    <script>
        // Halaman yang dipulihkan dari cache browser (bfcache) membawa token CSRF
        // yang sudah kedaluwarsa. Setelah di muat ulang otomatis, user selalu
        // memakai token yang masih berlaku — 419 tidak akan muncul.
        window.addEventListener('pageshow', function (event) {
            var nav = performance.getEntriesByType('navigation')[0];
            if (event.persisted || (nav && nav.type === 'back_forward')) {
                window.location.reload();
            }
        });
    </script>
</x-app-layout>
