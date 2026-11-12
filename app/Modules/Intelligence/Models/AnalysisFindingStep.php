<?php

namespace App\Modules\Intelligence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisFindingStep extends Model
{
    /**
     * Satu langkah action plan pada temuan rekomendasi hasil analisis.
     */
    protected $table = 'intelligence_analysis_finding_steps';

    protected $fillable = [
        'id_finding',
        'step_order',
        'step',
    ];

    protected function casts(): array
    {
        return [
            'step_order' => 'integer',
        ];
    }

    public function finding(): BelongsTo
    {
        return $this->belongsTo(AnalysisFinding::class, 'id_finding');
    }
}
