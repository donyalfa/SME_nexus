@extends('admin.layouts.intelligence')

@php
    $initialGroups = old('groups');

    if (! is_array($initialGroups) || $initialGroups === []) {
        $initialGroups = $version
            ? $version->groups->map(fn ($group) => [
                'label' => $group->label,
                'conditions' => $group->conditions->map(fn ($condition) => [
                    'id_symptom' => $condition->id_symptom,
                    'is_negated' => $condition->is_negated,
                ])->all(),
            ])->all()
            : [];
    }

    if ($initialGroups === []) {
        $initialGroups = [[
            'label' => 'Grup 1',
            'conditions' => [['id_symptom' => '', 'is_negated' => false]],
        ]];
    }
@endphp

@section('title', $rule->exists ? 'Edit Rule' : 'Tambah Rule')
@section('header', $rule->exists ? 'Edit Rule' : 'Tambah Rule')
@section('subheader', 'Kondisi dalam grup AND, antar grup OR, lalu dipilih satu diagnosis utama')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="ba-page-title h4">{{ $rule->exists ? 'Edit Rule' : 'Tambah Rule' }}</h1>
            <p class="ba-muted mb-0 small">Rule yang sudah dipublikasikan tidak diubah. Perubahan membuat versi draf baru.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.intelligence.rules.index') }}">Kembali</a>
    </div>

    <form method="POST" action="{{ $rule->exists ? route('admin.intelligence.rules.update', $rule) : route('admin.intelligence.rules.store') }}">
        @csrf
        @if ($rule->exists)
            @method('PUT')
        @endif

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="ba-card mb-3">
                    <div class="ba-card-body">
                        <h2 class="h6 fw-bold mb-3">Identitas Rule</h2>

                        <div class="row g-3">
                            <div class="col-12 col-md-5">
                                <label class="form-label fw-semibold" for="code">Kode <span class="text-danger">*</span></label>
                                <input class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $rule->code) }}" required maxlength="100" placeholder="revenue_decline_rule">
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-7">
                                <label class="form-label fw-semibold" for="name">Nama Rule <span class="text-danger">*</span></label>
                                <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $rule->name) }}" required maxlength="150" placeholder="Penurunan omzet dan pelanggan">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="description">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2" maxlength="2000">{{ old('description', $rule->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="id_diagnosis">Diagnosis <span class="text-danger">*</span></label>
                                <select class="form-select @error('id_diagnosis') is-invalid @enderror" id="id_diagnosis" name="id_diagnosis" required>
                                    <option value="">Pilih diagnosis</option>
                                    @foreach ($diagnoses as $diagnosis)
                                        <option value="{{ $diagnosis->id }}" @selected((string) old('id_diagnosis', $rule->id_diagnosis) === (string) $diagnosis->id)>{{ $diagnosis->name }}@if (! $diagnosis->is_active) — nonaktif @endif</option>
                                    @endforeach
                                </select>
                                @error('id_diagnosis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold" for="severity">Severity <span class="text-danger">*</span></label>
                                <select class="form-select @error('severity') is-invalid @enderror" id="severity" name="severity" required>
                                    @foreach (\App\Modules\Intelligence\Domain\Enums\Severity::cases() as $severity)
                                        <option value="{{ $severity->value }}" @selected(old('severity', $rule->severity?->value) === $severity->value)>{{ $severity->label() }}</option>
                                    @endforeach
                                </select>
                                @error('severity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold" for="priority">Prioritas <span class="text-danger">*</span></label>
                                <input class="form-control @error('priority') is-invalid @enderror" id="priority" type="number" name="priority" value="{{ old('priority', $rule->priority ?? 100) }}" required min="0" max="65535">
                                <div class="form-text">Angka lebih kecil diprioritaskan.</div>
                                @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <div class="alert alert-light border small mb-0">
                                    Status aktif dikelola dari halaman detail rule. Rule baru disimpan sebagai draf dan aktif setelah versi pertama dipublikasikan.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ba-card">
                    <div class="ba-card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div>
                                <h2 class="h6 fw-bold mb-1">Kondisi Rule</h2>
                                <p class="small ba-muted mb-0">Satu grup = AND. Antar grup = OR. Minimal satu gejala per grup.</p>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" id="add-group" type="button">+ Tambah Grup</button>
                        </div>

                        <div id="rule-groups">
                            @foreach ($initialGroups as $groupIndex => $group)
                                @include('admin.intelligence.rules._group', [
                                    'groupIndex' => $groupIndex,
                                    'group' => $group,
                                    'symptoms' => $symptoms,
                                ])
                            @endforeach
                        </div>

                        <div class="ba-rule-or">Antar grup digabung dengan OR</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="ba-card ba-card-body ba-form-summary">
                    <h2 class="h6 fw-bold mb-2">Pratinjau Rule</h2>
                    <p class="small ba-muted">Nilai metrik akan dideteksi menjadi gejala, lalu dicocokkan dengan kondisi berikut.</p>
                    <div class="alert alert-light border small mb-3" id="rule-preview">(belum ada kondisi)</div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="version_notes">Catatan Versi</label>
                        <textarea class="form-control form-control-sm" id="version_notes" name="version_notes" rows="2" maxlength="2000" placeholder="Alasan perubahan versi">{{ old('version_notes', $version?->notes) }}</textarea>
                    </div>

                    @if ($version)
                        <div class="small ba-muted mb-3">
                            Versi terbaru: <strong>v{{ $version->version }}</strong>
                            <span class="badge {{ $version->status->badgeClass() }} ms-1">{{ $version->status->label() }}</span>
                        </div>
                    @endif

                    <button class="btn ba-btn-primary text-white w-100" type="submit">{{ $rule->exists ? 'Simpan Draf Baru' : 'Simpan sebagai Draf' }}</button>
                    <div class="small ba-muted text-center mt-2">Publikasikan dari halaman detail setelah draf ditinjau.</div>
                </div>
            </div>
        </div>
    </form>

    <template id="group-template">
        <div class="ba-rule-group" data-group data-group-index="__GROUP__">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                <div class="flex-grow-1">
                    <label class="form-label small fw-semibold mb-1" for="group-label-__GROUP__">Label Grup (opsional)</label>
                    <input class="form-control form-control-sm" id="group-label-__GROUP__" name="groups[__GROUP__][label]" maxlength="100" placeholder="Contoh: Penjualan menurun">
                </div>
                <button class="btn btn-sm btn-outline-danger" type="button" data-remove-group>Hapus Grup</button>
            </div>
            <div class="small fw-semibold ba-muted mb-2">Kondisi dalam grup ini digabung dengan AND.</div>
            <div data-conditions></div>
            <button class="btn btn-sm btn-outline-secondary mt-2" type="button" data-add-condition>+ Tambah Kondisi</button>
        </div>
    </template>

    <template id="condition-template">
        @include('admin.intelligence.rules._condition', [
            'groupIndex' => '__GROUP__',
            'conditionIndex' => '__CONDITION__',
            'condition' => ['id_symptom' => '', 'is_negated' => false],
            'symptoms' => $symptoms,
        ])
    </template>
@endsection

@push('scripts')
    <script>
        const groupsContainer = document.getElementById('rule-groups');
        const groupTemplate = document.getElementById('group-template');
        const conditionTemplate = document.getElementById('condition-template');
        const preview = document.getElementById('rule-preview');
        let groupCounter = 1000;
        let conditionCounter = 1000;

        const addCondition = (group) => {
            if (!group || !conditionTemplate) return;

            const groupId = group.dataset.groupIndex;
            const conditionId = `c${conditionCounter++}`;
            const html = conditionTemplate.innerHTML
                .replaceAll('__GROUP__', groupId)
                .replaceAll('__CONDITION__', conditionId);

            group.querySelector('[data-conditions]')?.insertAdjacentHTML('beforeend', html);
            refreshPreview();
        };

        const addGroup = () => {
            if (!groupTemplate || !groupsContainer) return;

            const groupId = `g${groupCounter++}`;
            const html = groupTemplate.innerHTML.replaceAll('__GROUP__', groupId);
            groupsContainer.insertAdjacentHTML('beforeend', html);

            const group = groupsContainer.lastElementChild;
            addCondition(group);
            group?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        };

        const refreshPreview = () => {
            if (!groupsContainer || !preview) return;

            const groups = [...groupsContainer.querySelectorAll('[data-group]')];
            const groupTexts = groups.map((group) => {
                const label = group.querySelector('input[name$="[label]"]')?.value?.trim();
                const conditions = [...group.querySelectorAll('[data-condition]')]
                    .map((condition) => {
                        const select = condition.querySelector('select');
                        if (!select || !select.value) return null;
                        const negated = condition.querySelector('input[type="checkbox"]')?.checked;
                        const text = select.selectedOptions[0]?.textContent?.trim() ?? 'gejala';
                        return `${negated ? 'TIDAK ' : ''}${text}`;
                    })
                    .filter(Boolean);

                const body = conditions.length ? conditions.join(' DAN ') : '(belum ada kondisi)';
                return `${label ? label + ': ' : ''}(${body})`;
            });

            preview.textContent = groupTexts.length ? groupTexts.join(' ATAU ') : '(belum ada kondisi)';
        };

        groupsContainer?.addEventListener('click', (event) => {
            const addConditionButton = event.target.closest('[data-add-condition]');
            if (addConditionButton) {
                addCondition(addConditionButton.closest('[data-group]'));
                return;
            }

            const removeConditionButton = event.target.closest('[data-remove-condition]');
            if (removeConditionButton) {
                const condition = removeConditionButton.closest('[data-condition]');
                const group = condition?.closest('[data-group]');
                if (group && group.querySelectorAll('[data-condition]').length <= 1) {
                    window.alert('Minimal satu kondisi per grup.');
                    return;
                }
                condition?.remove();
                refreshPreview();
                return;
            }

            const removeGroupButton = event.target.closest('[data-remove-group]');
            if (removeGroupButton) {
                if (groupsContainer.querySelectorAll('[data-group]').length <= 1) {
                    window.alert('Minimal satu grup kondisi.');
                    return;
                }
                removeGroupButton.closest('[data-group]')?.remove();
                refreshPreview();
            }
        });

        groupsContainer?.addEventListener('input', refreshPreview);
        groupsContainer?.addEventListener('change', refreshPreview);
        document.getElementById('add-group')?.addEventListener('click', addGroup);

        refreshPreview();
    </script>
@endpush
