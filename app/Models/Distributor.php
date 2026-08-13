<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Distributor extends Model
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
        'address',
        'city',
        'phone',
        'email',
        'description',
        'is_active',
    ];

    protected $codeColumn = 'code';

    protected $codePrefix = 'DST';

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
