<?php

namespace App\Modules\Collaboration\Services;

use App\Modules\Collaboration\Models\Company;
use App\Shared\Models\User;
use Illuminate\Support\Facades\DB;

class CollaborationProfileSyncService
{
    /**
     * Sinkronkan profil UMKM pengguna ke tabel companies.
     */
    public function syncForUser(User $user): ?Company
    {
        return DB::transaction(function () use ($user) {
            $profiles = $user->businessProfiles()
                ->with('category')
                ->get();

            if ($profiles->isEmpty()) {
                return null;
            }

            $companies = $profiles->map(function ($profile) use ($user) {
                $businessName = trim((string) $profile->business_name);

                $location = collect([
                    $profile->city,
                    $profile->province,
                ])->filter(fn ($value) => filled($value))
                  ->implode(', ');

                return Company::updateOrCreate(
                    [
                        'id_business_profile' => $profile->id_business_profile,
                    ],
                    [
                        'user_id' => $user->id_user,
                        'name' => $businessName !== ''
                            ? $businessName
                            : 'UMKM ' . $profile->id_business_profile,
                        'industry_sector' => $profile->category?->category_name,
                        'location' => $location !== '' ? $location : null,
                    ]
                );
            });

            return $companies->first();
        });
    }
}
