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
        $project = $activity->project;

        if (! $project) {
            return;
        }

        // Update the last contacted timestamp
        $project->last_contacted_at = $activity->performed_at ?? now();

        // Automated Status Transitions
        if ($project->status === 'new') {
            // New -> Contacted
            $project->status = 'contacted';
        } elseif ($activity->outcome === 'Not Interested') {
            // Hard Stop -> Lost
            $project->status = 'lost';
            $project->converted_at = now();
        } elseif ($project->status === 'contacted' && in_array(strtolower($activity->type), ['presentation', 'demo', 'meeting'])) {
            // Contacted -> Qualified
            $project->status = 'qualified';
        } elseif (in_array($project->status, ['contacted', 'qualified']) && (str_contains(strtolower($activity->subject), 'proposal') || str_contains(strtolower($activity->subject), 'quote'))) {
            // Contacted/Qualified -> Proposal (if a proposal is sent)
            $project->status = 'proposal';
        }

        $project->save();
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
