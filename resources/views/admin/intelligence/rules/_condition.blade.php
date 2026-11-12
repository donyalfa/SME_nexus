@php
    $conditionId = $condition['id_symptom'] ?? '';
    $negated = (bool) ($condition['is_negated'] ?? false);
    $checkboxId = 'negated-'.$groupIndex.'-'.$conditionIndex;
@endphp

<div class="row g-2 align-items-center mb-2" data-condition>
    <div class="col-12 col-md-7">
        <select class="form-select form-select-sm" name="groups[{{ $groupIndex }}][conditions][{{ $conditionIndex }}][id_symptom]" required>
            <option value="">Pilih gejala</option>
            @foreach ($symptoms as $symptom)
                <option value="{{ $symptom->id }}" @selected((string) $conditionId === (string) $symptom->id)>{{ $symptom->name }} ({{ $symptom->code }})@if (! $symptom->is_active) &mdash; nonaktif @endif</option>
            @endforeach
        </select>
    </div>
    <div class="col-7 col-md-3">
        <div class="form-check">
            <input class="form-check-input" id="{{ $checkboxId }}" type="checkbox" name="groups[{{ $groupIndex }}][conditions][{{ $conditionIndex }}][is_negated]" value="1" @checked($negated)>
            <label class="form-check-label small" for="{{ $checkboxId }}">Negasi (TIDAK)</label>
        </div>
    </div>
    <div class="col-5 col-md-2 text-end">
        <button class="btn btn-sm btn-outline-danger" type="button" data-remove-condition>Hapus</button>
    </div>
</div>
