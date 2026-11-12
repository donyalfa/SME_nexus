<?php

namespace App\Shared\Models;

use App\Modules\Intelligence\Models\Analysis;
use App\Modules\Intelligence\Models\SmeProfile;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id_user
 * @property string $full_name
 * @property string $email
 * @property string $role
 * @property bool $is_verified
 * @property-read string $name
 * @property-read Carbon|null $email_verified_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, MustVerifyEmailTrait, Notifiable;

    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
    }

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'role',
        'is_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * Kompatibilitas dengan pemakaian $user->name.
     */
    public function getNameAttribute(): string
    {
        return (string) $this->full_name;
    }

    /**
     * Status verifikasi menggunakan kolom legacy is_verified.
     */
    public function hasVerifiedEmail(): bool
    {
        if (! config('app.email_verification_enabled')) {
            return true;
        }

        return (bool) $this->is_verified;
    }

    /**
     * Tandai email terverifikasi.
     */
    public function markEmailAsVerified(): bool
    {
        if ($this->is_verified) {
            return false;
        }

        return $this->forceFill([
            'is_verified' => true,
        ])->save();
    }

    /**
     * Kompatibilitas tampilan Breeze.
     */
    public function getEmailVerifiedAtAttribute(): ?Carbon
    {
        return $this->is_verified ? $this->updated_at : null;
    }

    /**
     * Relasi profil UMKM untuk Intelligence.
     */
    public function smeProfiles(): HasMany
    {
        return $this->hasMany(
            SmeProfile::class,
            'id_user',
            'id_user'
        );
    }

    /**
     * Relasi profil usaha hasil onboarding.
     */
    public function businessProfiles(): HasMany
    {
        return $this->hasMany(
            BusinessProfile::class,
            'id_user',
            'id_user'
        );
    }

    /**
     * Relasi riwayat analisis Intelligence.
     */
    public function intelligenceAnalyses(): HasMany
    {
        return $this->hasMany(
            Analysis::class,
            'id_user',
            'id_user'
        );
    }

    /**
     * Relasi audit dan aktivitas pengguna.
     */
    public function systemLogs(): HasMany
    {
        return $this->hasMany(
            SystemLog::class,
            'id_user',
            'id_user'
        );
    }
}
