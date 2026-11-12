<?php

namespace App\Modules\Admin\Requests\Intelligence;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRecommendationRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $recommendation = $this->route('recommendation');

        return [
            'id_diagnosis' => ['required', 'integer', Rule::exists('autopsy_diagnoses', 'id')],
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('autopsy_recommendations', 'code')->ignore($recommendation?->getKey()),
            ],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'action_steps_text' => ['nullable', 'string', 'max:5000'],
            'priority' => ['required', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
