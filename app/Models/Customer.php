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
        'type',
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
        'customer_group_id',
        'customer_code',
    ];

    protected $codeColumn = 'customer_code';

    protected $codePrefix = 'CST';

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

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'customer_id');
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
