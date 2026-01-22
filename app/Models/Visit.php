<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'contact_id',
        'visit_type',
        'meeting_link',
        'messaging_platform',
        'visit_started_at',
        'visit_ended_at',
        'location',
        'purpose',
        'expectations',
        'targets',
        'summary_notes',
        'stakeholder_feedback',
        'is_worth_keeping',
        'confidence_level',
    ];

    protected $casts = [
        'visit_started_at' => 'datetime',
        'visit_ended_at' => 'datetime',
        'is_worth_keeping' => 'boolean',
        'confidence_level' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
