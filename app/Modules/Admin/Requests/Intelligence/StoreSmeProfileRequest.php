<?php

namespace App\Modules\Admin\Requests\Intelligence;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSmeProfileRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id_user' => ['required', 'integer', Rule::exists('users', 'id_user')],
            'id_category' => ['required', 'integer', Rule::exists('business_categories', 'id_category')],
            'business_name' => ['required', 'string', 'max:100'],
            'legal_type' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:2000'],
            'city' => ['required', 'string', 'max:100'],
            'established_year' => ['required', 'integer', 'min:1900', 'max:'.date('Y')],
        ];
    }
}
