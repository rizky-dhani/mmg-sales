<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'customer_name',
        'contact_person',
        'email',
        'phone',
        'status',
        'source',
        'priority',
        'confidence_level',
        'expected_closing_date',
        'financial_goal',
        'estimated_value',
        'estimated_revenue',
        'estimated_completion_date',
        'notes',
        'customer_id',
        'converted_at',
        'last_contacted_at',
        'assigned_to',
        'position',
    ];

    protected $casts = [
        'confidence_level' => 'integer',
        'expected_closing_date' => 'date',
        'financial_goal' => 'decimal:2',
        'estimated_value' => 'decimal:2',
        'estimated_revenue' => 'decimal:2',
        'estimated_completion_date' => 'date',
        'converted_at' => 'datetime',
        'last_contacted_at' => 'datetime',
    ];

    public function milestones(): BelongsToMany
    {
        return $this->belongsToMany(Milestone::class, 'project_milestone')
            ->using(ProjectMilestone::class)
            ->withPivot(['is_completed', 'completed_at', 'notes'])
            ->withTimestamps();
    }

    public function calculateConfidenceLevel(): int
    {
        return (int) $this->milestones()
            ->wherePivot('is_completed', true)
            ->sum('weight');
    }

    public function updateConfidenceLevel(): void
    {
        $this->updateQuietly([
            'confidence_level' => $this->calculateConfidenceLevel(),
        ]);
    }

    public function getAgingAttribute(): string
    {
        $end = $this->converted_at ?? now();
        $days = round($this->created_at->diffInHours($end) / 24, 1);

        return $days.' '.str('day')->plural($days);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'project_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'project_id')->orderBy('performed_at', 'desc');
    }

    public function latestActivity(): HasOne
    {
        return $this->hasOne(Activity::class, 'project_id')->latestOfMany('performed_at');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'project_product')->withTimestamps();
    }
}
