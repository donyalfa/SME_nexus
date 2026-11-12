<?php

namespace App\Modules\Decision\Models;

use App\Modules\Intelligence\Models\Analysis;
use App\Modules\Intelligence\Models\AnalysisFinding;
use App\Shared\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionPlan extends Model
{
    protected $table = 'decision_action_plans';

    public const STATUS_TODO = 'todo';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';

    protected $fillable = [
        'id_user',
        'id_analysis',
        'id_finding',
        'assigned_to',
        'task',
        'description',
        'due_date',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id_user');
    }

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class, 'id_analysis');
    }

    public function finding(): BelongsTo
    {
        return $this->belongsTo(AnalysisFinding::class, 'id_finding');
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    public function isOverdue(): bool
    {
        return ! $this->isCompleted()
            && $this->due_date !== null
            && $this->due_date->lt(today());
    }
}
