<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use HasCode, HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'customer_id',
        'contact_id',
        'type',
        'subject',
        'description',
        'performed_at',
        'visit_started_at',
        'visit_ended_at',
        'location',
        'purpose',
        'expectations',
        'targets',
        'stakeholder_feedback',
        'is_worth_keeping',
        'confidence_level',
        'next_contact_date',
        'follow_up_notes',
        'meeting_link',
        'messaging_platform',
        'duration_minutes',
        'outcome',
        'activity_code',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'visit_started_at' => 'datetime',
        'visit_ended_at' => 'datetime',
        'is_worth_keeping' => 'boolean',
        'confidence_level' => 'integer',
        'next_contact_date' => 'date',
    ];

    protected $codeColumn = 'activity_code';

    protected $codePrefix = 'ACT';

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

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

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
