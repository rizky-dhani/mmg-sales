<?php

namespace App\Observers;

use App\Models\Activity;

class ActivityObserver
{
    /**
     * Handle the Activity "created" event.
     */
    public function created(Activity $activity): void
    {
        $lead = $activity->lead;

        if (! $lead) {
            return;
        }

        // Update the last contacted timestamp
        $lead->last_contacted_at = $activity->performed_at ?? now();

        // Automated Status Transitions
        if ($lead->status === 'new') {
            // New -> Contacted
            $lead->status = 'contacted';
        } elseif ($activity->outcome === 'Not Interested') {
            // Hard Stop -> Lost
            $lead->status = 'lost';
            $lead->converted_at = now();
        } elseif ($lead->status === 'contacted' && in_array(strtolower($activity->type), ['presentation', 'demo', 'meeting'])) {
            // Contacted -> Qualified
            $lead->status = 'qualified';
        } elseif (in_array($lead->status, ['contacted', 'qualified']) && (str_contains(strtolower($activity->subject), 'proposal') || str_contains(strtolower($activity->subject), 'quote'))) {
            // Contacted/Qualified -> Proposal (if a proposal is sent)
            $lead->status = 'proposal';
        }

        $lead->save();
    }

    /**
     * Handle the Activity "updated" event.
     */
    public function updated(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "deleted" event.
     */
    public function deleted(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "restored" event.
     */
    public function restored(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "force deleted" event.
     */
    public function forceDeleted(Activity $activity): void
    {
        //
    }
}
