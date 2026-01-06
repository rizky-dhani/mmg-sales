<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
