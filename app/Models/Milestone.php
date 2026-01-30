<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'weight',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'weight' => 'integer',
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_milestone')
            ->using(ProjectMilestone::class)
            ->withPivot(['is_completed', 'completed_at', 'notes'])
            ->withTimestamps();
    }
}
