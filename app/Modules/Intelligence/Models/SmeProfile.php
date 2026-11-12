<?php
namespace App\Modules\Intelligence\Models;
use Illuminate\Database\Eloquent\Model;
use App\Shared\Models\User;
use App\Shared\Models\BusinessData;

class SmeProfile extends Model
{
    protected $table = 'sme_profiles';

    protected $primaryKey = 'id_profile';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'id_user',
        'id_category',
        'business_name',
        'legal_type',
        'address',
        'city',
        'established_year',
    ];

    protected function casts(): array
    {
        return [
            'established_year' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function category()
    {
        return $this->belongsTo(BusinessCategory::class, 'id_category', 'id_category');
    }

    public function businessData()
    {
        return $this->hasMany(BusinessData::class, 'id_profile', 'id_profile');
    }
}
