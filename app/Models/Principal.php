<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Principal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'contact_person',
        'phone',
        'email',
        'address',
        'is_active',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
