<?php

namespace App\Modules\UMKM\Controllers;


use App\Shared\Http\Controllers\Controller;
use App\Modules\Intelligence\Models\BusinessCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class BusinessProfileSettingsController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:100'],
            'id_category' => ['required', 'exists:business_categories,id_category'],
            'legal_type' => ['nullable', 'string', 'max:100'],
            'established_year' => ['required', 'integer', 'min:1900', 'max:' . date('Y')],
            'description' => ['nullable', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:2000'],
            'business_email' => ['nullable', 'email', 'max:100'],
            'website' => ['nullable', 'string', 'max:255'],
            'employee_count' => ['nullable', 'integer', 'min:0'],
        ]);

        $user = $request->user();
        $profile = $user->businessProfiles()->first();

        if ($profile) {
            $profile->update($validated);
        } else {
            $user->businessProfiles()->create($validated);
        }

        return Redirect::route('profile.edit')->with('status', 'business-profile-updated');
    }
}
