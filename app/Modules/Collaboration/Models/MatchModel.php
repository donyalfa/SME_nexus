<?php

namespace App\Modules\Collaboration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchModel extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'company_a_id',
        'company_b_id',
        'matched_at',
    ];

    protected $casts = [
        'matched_at' => 'datetime',
    ];

    public function companyA(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_a_id');
    }

    public function companyB(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_b_id');
    }
}
