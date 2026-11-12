<?php

namespace App\Modules\Admin\Services\Intelligence;

use App\Modules\Intelligence\Models\SmeProfile;
use App\Modules\Intelligence\Repositories\SmeProfileRepository;
use App\Modules\Intelligence\Services\AuditLogger;
use Illuminate\Support\Facades\DB;

class SmeProfileService
{
    public function __construct(
        private readonly SmeProfileRepository $profiles,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters = [])
    {
        return $this->profiles->paginate($filters);
    }

    public function find(int $id): SmeProfile
    {
        return $this->profiles->find($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): SmeProfile
    {
        $profile = $this->profiles->create($data);

        $this->auditLogger->log(
            'admin.profile.created',
            'sme_profile',
            $profile->getKey(),
            [],
            $data
        );

        return $profile;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(SmeProfile $profile, array $data): SmeProfile
    {
        $oldValues = $profile->only([
            'id_user',
            'id_category',
            'business_name',
            'legal_type',
            'address',
            'city',
            'established_year',
        ]);

        $profile = $this->profiles->update($profile, $data);

        $this->auditLogger->log(
            'admin.profile.updated',
            'sme_profile',
            $profile->getKey(),
            $oldValues,
            $data
        );

        return $profile;
    }

    /**
     * Hapus permanen profil beserta seluruh data turunannya.
     * Foreign key ON DELETE CASCADE menangani penghapusan relasi.
     */
    public function delete(SmeProfile $profile): void
    {
        $oldValues = $profile->only([
            'id_user',
            'id_category',
            'business_name',
            'city',
        ]);

        $profileId = $profile->getKey();

        DB::transaction(function () use ($profile, $profileId, $oldValues): void {
            $this->profiles->delete($profile);

            $this->auditLogger->log(
                'admin.profile.deleted',
                'sme_profile',
                $profileId,
                $oldValues
            );
        });
    }
}
