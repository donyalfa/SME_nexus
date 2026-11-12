<?php

namespace App\Modules\Intelligence\Models;

use Illuminate\Database\Eloquent\Model;
use App\Shared\Models\BusinessProfile;

class BusinessCategory extends Model
{
    protected $table = 'business_categories';

    protected $primaryKey = 'id_category';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'category_name',
        'deskripsi',
    ];

    public function smeProfiles()
    {
        return $this->hasMany(SmeProfile::class, 'id_category', 'id_category');
    }

    public function businessProfiles()
    {
        return $this->hasMany(BusinessProfile::class, 'id_category', 'id_category');
    }
}
