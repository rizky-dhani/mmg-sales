<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasCode, HasFactory, SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'customer_name',
        'type',
        'other_type',
        'tax_number',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'email',
        'phone',
        'phone_purchasing',
        'phone_finance',
        'website',
        'is_active',
        'status',
        'cd_ncd_type',
        'customer_group_id',
        'customer_code',
        'customer_acc_code',
    ];

    protected $codeColumn = 'customer_code';

    protected $codePrefix = 'CST';

    public function getDisplayNameAttribute(): string
    {
        return $this->customer_name ?? $this->name;
    }

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'customer_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'customer_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'end_customer_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'customer_id');
    }
}
