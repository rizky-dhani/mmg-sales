<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Milestone extends Model
{
    use HasCode, HasFactory;

    protected $fillable = [
        'name',
        'code',
        'weight',
        'description',
        'is_active',
    ];

    protected $codeColumn = 'code';

    protected $codePrefix = 'MIL';

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
