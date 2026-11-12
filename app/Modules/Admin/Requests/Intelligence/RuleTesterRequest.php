<?php

namespace App\Modules\Admin\Requests\Intelligence;

use Illuminate\Foundation\Http\FormRequest;

class RuleTesterRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'metrics' => ['nullable', 'array'],
            'metrics.*' => ['nullable', 'numeric'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $metrics = $this->input('metrics');

        if (is_array($metrics)) {
            $this->merge([
                'metrics' => collect($metrics)
                    ->map(fn ($value) => $value === '' ? null : $value)
                    ->all(),
            ]);
        }
    }
}
