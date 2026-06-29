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
        'name',
        'status',
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

    protected static function boot(): void
    {
        static::creating(function (Contact $contact) {
            $customer = $contact->customer;

            if ($customer->status === 'inactive') {
                abort(403, 'Cannot create contact for inactive customer.');
            }

            if ($customer->max_contact_persons !== null) {
                $activeCount = $customer->contacts()->count();
                if ($activeCount >= $customer->max_contact_persons) {
                    abort(403, 'Maximum contact person limit reached.');
                }
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(ContactPhone::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
