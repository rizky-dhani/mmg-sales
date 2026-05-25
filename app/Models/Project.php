<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasCode, HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'customer_name',
        'contact_person',
        'email',
        'phone',
        'status',
        'source',
        'priority',
        'estimated_value',
        'estimated_revenue',
        'estimated_completion_date',
        'notes',
        'customer_id',
        'converted_at',
        'last_contacted_at',
        'assigned_to',
        'position',
        'project_code',
        'created_by',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'estimated_revenue' => 'decimal:2',
        'estimated_completion_date' => 'date',
        'converted_at' => 'datetime',
        'last_contacted_at' => 'datetime',
    ];

    protected $codeColumn = 'project_code';

    protected $codePrefix = 'PRJ';

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Project $project) {
            if (is_null($project->created_by) && auth()->check()) {
                $project->created_by = auth()->id();
            }
        });
    }

    public function getAgingAttribute(): string
    {
        $end = $this->converted_at ?? now();
        $days = round($this->created_at->diffInHours($end) / 24, 1);

        return $days.' '.str('day')->plural($days);
    }

    public function contactPerson(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_person');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function collaborators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_collaborators')
            ->withPivot('added_by')
            ->withTimestamps();
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
