<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'status',
        'source',
        'priority',
        'estimated_value',
        'notes',
        'company_id',
        'converted_at',
        'last_contacted_at',
        'assigned_to',
        'position',
    ];

    public function getAgingAttribute(): string
    {
        $end = $this->converted_at ?? now();
        $days = round($this->created_at->diffInHours($end) / 24, 1);

        return $days.' '.str('day')->plural($days);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class)->orderBy('performed_at', 'desc');
    }

    public function latestActivity(): HasOne
    {
        return $this->hasOne(Activity::class)->latestOfMany('performed_at');
    }
}