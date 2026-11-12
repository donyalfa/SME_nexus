<section>
    <header class="mb-4">
        <h3 class="h5 fw-semibold mb-1">Profil Perusahaan</h3>
        <p class="small text-body-secondary mb-0">Perbarui informasi identitas, kategori, dan detail kontak bisnis Anda.</p>
    </header>

    @php
        $bProfile = $user->businessProfiles()->first();
        $categories = \App\Modules\Intelligence\Models\BusinessCategory::orderBy('category_name')->get();
    @endphp

    <form method="post" action="{{ route('profile.business.update') }}">
        @csrf
        @method('patch')

        <div class="row g-3">
            {{-- Kolom Kiri --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="business_name" class="form-label small fw-semibold">Nama Usaha</label>
                    <input type="text" class="form-control @error('business_name') is-invalid @enderror" id="business_name" name="business_name" value="{{ old('business_name', $bProfile?->business_name) }}" required>
                    @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="id_category" class="form-label small fw-semibold">Kategori Usaha</label>
                    <select class="form-select @error('id_category') is-invalid @enderror" id="id_category" name="id_category" required>
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id_category }}" @selected((string) old('id_category', $bProfile?->id_category) === (string) $category->id_category)>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="legal_type" class="form-label small fw-semibold">Badan Hukum (Opsional)</label>
                    <input type="text" class="form-control" id="legal_type" name="legal_type" value="{{ old('legal_type', $bProfile?->legal_type) }}" placeholder="Contoh: PT, CV, Perorangan">
                </div>

                <div class="mb-3">
                    <label for="city" class="form-label small fw-semibold">Kota Domisili</label>
                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $bProfile?->city) }}" required>
                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Kolom Kanan --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="established_year" class="form-label small fw-semibold">Tahun Berdiri</label>
                    <input type="number" class="form-control @error('established_year') is-invalid @enderror" id="established_year" name="established_year" value="{{ old('established_year', $bProfile?->established_year ?? date('Y')) }}" required>
                    @error('established_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label small fw-semibold">Nomor WhatsApp / Telp</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $bProfile?->phone) }}">
                </div>

                <div class="mb-3">
                    <label for="employee_count" class="form-label small fw-semibold">Jumlah Karyawan</label>
                    <input type="number" class="form-control" id="employee_count" name="employee_count" value="{{ old('employee_count', $bProfile?->employee_count) }}" min="0">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label small fw-semibold">Alamat Lengkap</label>
                    <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $bProfile?->address) }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 mt-4">
            <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>

            @if (session('status') === 'business-profile-updated')
                <span class="text-success small fw-semibold">Profil Usaha berhasil diperbarui.</span>
            @endif
        </div>
    </form>
</section>
