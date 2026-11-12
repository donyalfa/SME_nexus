<?php

namespace App\Modules\Admin\Requests\Intelligence;

use App\Modules\Intelligence\Domain\Enums\Severity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDiagnosisRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $diagnosis = $this->route('diagnosis');

        return [
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('autopsy_diagnoses', 'code')->ignore($diagnosis?->getKey()),
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'severity' => ['required', Rule::in(array_column(Severity::cases(), 'value'))],
            'priority' => ['required', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
