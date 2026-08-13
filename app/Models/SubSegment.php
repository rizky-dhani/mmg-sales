<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class SubSegment extends Model
{
    use HasCode, HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

    protected $fillable = [
        'name',
        'code',
        'sub_segment_code',
        'segment_id',
        'description',
    ];

    protected $codeColumn = 'sub_segment_code';

    protected $codePrefix = 'SSG';

    public function segment(): BelongsTo
    {
        return $this->belongsTo(Segment::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
