<?php

namespace App\Modules\Intelligence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisSymptom extends Model
{
    /**
     * Gejala yang terdeteksi pada satu analisis Business Autopsy.
     */
    protected $table = 'intelligence_analysis_symptoms';

    protected $fillable = [
        'id_analysis',
        'id_symptom',
        'code',
        'name',
        'metric_key',
    ];

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class, 'id_analysis');
    }

    public function symptom(): BelongsTo
    {
        return $this->belongsTo(Symptom::class, 'id_symptom');
    }
}
