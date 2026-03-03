<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Segment extends Model
{
    use HasCode, HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    protected $codeColumn = 'code';

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
