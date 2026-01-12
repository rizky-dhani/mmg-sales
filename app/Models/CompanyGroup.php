<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyGroup extends Model
{
    use HasFactory;

    protected $table = 'company_groups';

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'company_group_id');
    }
}