<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Principal extends Model
{
    use HasCode, HasFactory;

    protected $fillable = [
        'name',
        'initial',
        'description',
        'address',
        'is_active',
        'supplier_type',
        'website',
        'principal_code',
        'principal_acc_code',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $codeColumn = 'principal_code';

    protected $codePrefix = 'PRN';

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
