@php
    $groupIndex = $groupIndex ?? 0;
    $conditions = $group['conditions'] ?? [];
@endphp

<div class="ba-rule-group" data-group data-group-index="{{ $groupIndex }}">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <div class="flex-grow-1">
            <label class="form-label small fw-semibold mb-1" for="group-label-{{ $groupIndex }}">Label Grup (opsional)</label>
            <input class="form-control form-control-sm" id="group-label-{{ $groupIndex }}" name="groups[{{ $groupIndex }}][label]" value="{{ $group['label'] ?? '' }}" maxlength="100" placeholder="Contoh: Penjualan menurun">
        </div>
        <button class="btn btn-sm btn-outline-danger" type="button" data-remove-group>Hapus Grup</button>
    </div>

    <div class="small fw-semibold ba-muted mb-2">Kondisi dalam grup ini digabung dengan AND.</div>

    <div data-conditions>
        @foreach ($conditions as $conditionIndex => $condition)
            @include('admin.intelligence.rules._condition', [
                'groupIndex' => $groupIndex,
                'conditionIndex' => $conditionIndex,
                'condition' => $condition,
                'symptoms' => $symptoms,
            ])
        @endforeach
    </div>

    <button class="btn btn-sm btn-outline-secondary mt-2" type="button" data-add-condition>+ Tambah Kondisi</button>
</div>
