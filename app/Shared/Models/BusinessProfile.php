<?php

namespace App\Shared\Models;

use App\Modules\Intelligence\Models\BusinessCategory;
use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    /**
     * Nama tabel.
     */
    protected $table = 'business_profiles';

    /**
     * Primary key mengikuti konvensi tabel legacy.
     */
    protected $primaryKey = 'id_business_profile';

    /**
     * Primary key bertipe auto increment.
     */
    public $incrementing = true;

    /**
     * Tipe primary key.
     */
    protected $keyType = 'int';

    /**
     * Kolom yang dapat diisi.
     */
    protected $fillable = [
        'id_user',
        'id_category',
        'business_name',
        'legal_type',
        'city',
        'province',
        'postal_code',
        'address',
        'phone',
        'business_email',
        'website',
        'nib',
        'npwp',
        'employee_count',
        'established_year',
        'description',
    ];

    /**
     * Konversi tipe data.
     */
    protected function casts(): array
    {
        return [
            'id_category' => 'integer',
            'employee_count' => 'integer',
            'established_year' => 'integer',
        ];
    }

    /**
     * Relasi pemilik usaha.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Relasi kategori usaha.
     */
    public function category()
    {
        return $this->belongsTo(BusinessCategory::class, 'id_category', 'id_category');
    }
}
