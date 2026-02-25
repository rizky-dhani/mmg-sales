<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasCode, HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'first_name',
        'last_name',
        'position',
        'department',
        'email',
        'phone',
        'mobile',
        'is_primary',
        'is_billing_contact',
        'notes',
        'contact_code',
    ];

    protected $codeColumn = 'contact_code';

    protected $codePrefix = 'CON';

    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(ContactPhone::class);
    }
}
