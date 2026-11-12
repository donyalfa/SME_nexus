<?php

namespace App\Modules\Intelligence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecommendationStep extends Model
{
    /**
     * Satu langkah action plan pada rekomendasi master.
     */
    protected $table = 'autopsy_recommendation_steps';

    protected $fillable = [
        'id_recommendation',
        'step_order',
        'step',
    ];

    protected function casts(): array
    {
        return [
            'step_order' => 'integer',
        ];
    }

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class, 'id_recommendation');
    }
}
