<?php

namespace App\Modules\Collaboration\Models;

use App\Shared\Models\BusinessProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'user_id',
        'id_business_profile',
        'name',
        'industry_sector',
        'location',
        'offering_description',
        'seeking_description',
    ];

    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(
            BusinessProfile::class,
            'id_business_profile',
            'id_business_profile'
        );
    }

    public function swipesAsSwiper(): HasMany
    {
        return $this->hasMany(Swipe::class, 'swiper_company_id');
    }

    public function swipesAsSwiped(): HasMany
    {
        return $this->hasMany(Swipe::class, 'swiped_company_id');
    }
}
