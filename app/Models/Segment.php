<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Segment extends Model
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
        'segment_code',
        'description',
    ];

    protected $codeColumn = 'segment_code';

    protected $codePrefix = 'SEG';

    public function subSegments(): HasMany
    {
        return $this->hasMany(SubSegment::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
