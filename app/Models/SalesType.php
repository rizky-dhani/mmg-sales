<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesType extends Model
{
    use HasCode, HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    protected $codeColumn = 'code';

    protected $codePrefix = 'SLT';

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
