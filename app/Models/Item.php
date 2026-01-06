<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'name',
        'internal_code',
        'principle_code',
        'principal_id',
        'description',
        'unit_price',
        'unit',
        'is_active',
    ];

    public function principal(): BelongsTo
    {
        return $this->belongsTo(Principal::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
