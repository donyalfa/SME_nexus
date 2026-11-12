<?php

namespace App\Modules\Admin\Requests\Intelligence;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBusinessCategoryRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'category_name' => ['required', 'string', 'max:100', Rule::unique('business_categories', 'category_name')],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
