<?php

namespace App\Modules\Admin\Requests\Intelligence;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessCategoryRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'category_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('business_categories', 'category_name')->ignore($category?->getKey(), 'id_category'),
            ],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
