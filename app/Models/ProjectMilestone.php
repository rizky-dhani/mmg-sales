<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectMilestone extends Pivot
{
    protected $table = 'project_milestone';

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    protected static function booted(): void
    {
        static::saving(function (ProjectMilestone $pivot) {
            if ($pivot->is_completed && ! $pivot->completed_at) {
                $pivot->completed_at = now();
            } elseif (! $pivot->is_completed) {
                $pivot->completed_at = null;
            }
        });

        static::saved(function (ProjectMilestone $pivot) {
            $pivot->project->updateConfidenceLevel();
        });

        static::deleted(function (ProjectMilestone $pivot) {
            $pivot->project->updateConfidenceLevel();
        });
    }
}
