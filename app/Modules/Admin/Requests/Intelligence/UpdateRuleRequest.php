<?php

namespace App\Modules\Admin\Requests\Intelligence;

use App\Modules\Intelligence\Domain\Enums\Severity;
use App\Modules\Admin\Requests\Intelligence\Concerns\ValidatesRuleGroups;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRuleRequest extends FormRequest
{
    use ValidatesRuleGroups;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rule = $this->route('rule');

        return [
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('autopsy_rules', 'code')->ignore($rule?->getKey()),
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'id_diagnosis' => ['required', 'integer', Rule::exists('autopsy_diagnoses', 'id')],
            'severity' => ['required', Rule::in(array_column(Severity::cases(), 'value'))],
            'priority' => ['required', 'integer', 'min:0', 'max:65535'],
            'version_notes' => ['nullable', 'string', 'max:2000'],
            'groups' => ['required', 'array', 'min:1'],
            'groups.*.label' => ['nullable', 'string', 'max:100'],
            'groups.*.conditions' => ['required', 'array', 'min:1'],
            'groups.*.conditions.*.id_symptom' => ['required', 'integer', Rule::exists('autopsy_symptoms', 'id')],
            'groups.*.conditions.*.is_negated' => ['nullable', 'boolean'],
        ];
    }
}
