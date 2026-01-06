<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'segment_id',
        'description',
    ];

    public function segment(): BelongsTo
    {
        return $this->belongsTo(Segment::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
