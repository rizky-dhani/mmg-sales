<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'companies';

    protected $fillable = [
        'facility_name',
        'facility_type',
        'classification',
        'tax_number',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'email',
        'phone',
        'website',
        'credit_limit',
        'payment_terms_days',
        'is_active',
        'assigned_to',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'company_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'company_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'end_company_id');
    }
}