<?php

namespace App\Modules\Collaboration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Swipe extends Model
{
    protected $table = 'swipes';

    protected $fillable = [
        'swiper_company_id',
        'swiped_company_id',
        'direction',
    ];

    public function swiperCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'swiper_company_id');
    }

    public function swipedCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'swiped_company_id');
    }
}
