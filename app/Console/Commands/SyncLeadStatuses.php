<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Illuminate\Console\Command;

class SyncLeadStatuses extends Command
{
    protected $signature = 'leads:sync-statuses {--dry-run : Preview changes without updating}';

    protected $description = 'Replay activity/order status transitions for all leads with mismatched statuses';

    public function handle(): int
    {
        $leads = Lead::with('activities', 'orders')
            ->whereIn('status', ['new', 'contacted', 'qualified', 'proposal'])
            ->whereNull('deleted_at')
            ->get();

        $updated = 0;
        $skipped = 0;

        foreach ($leads as $lead) {
            $newStatus = $this->resolveStatus($lead);

            if ($newStatus === $lead->status) {
                $skipped++;
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("  {$lead->lead_code}: {$lead->status} → {$newStatus}");
            } else {
                $lead->update(['status' => $newStatus]);
            }

            $updated++;
        }

        $this->info($this->option('dry-run')
            ? "Dry run: {$updated} would change, {$skipped} unchanged."
            : "Done: {$updated} updated, {$skipped} unchanged.");

        return Command::SUCCESS;
    }

    private function resolveStatus(Lead $lead): string
    {
        // Order always wins — lead is won
        if ($lead->orders()->exists()) {
            return 'won';
        }

        $activities = $lead->activities()->orderBy('performed_at')->get();

        if ($activities->isEmpty()) {
            return $lead->status;
        }

        $status = 'new';

        foreach ($activities as $activity) {
            // Hard stop
            $outcome = strtolower($activity->outcome ?? '');
            if ($activity->outcome === 'Not Interested' || in_array($outcome, ['tidak tertarik', 'batal', 'gagal'])) {
                return 'lost';
            }

            // new → contacted (any activity)
            if ($status === 'new') {
                $status = 'contacted';
            }

            // contacted → qualified (meeting/presentation/demo — str_contains for "In-person Meeting", "Online Meeting")
            if ($status === 'contacted') {
                $type = strtolower($activity->type ?? '');
                if (str_contains($type, 'presentation') || str_contains($type, 'demo') || str_contains($type, 'meeting')) {
                    $status = 'qualified';
                }
            }

            // contacted/qualified → proposal (subject match)
            if (in_array($status, ['contacted', 'qualified'])) {
                $subject = strtolower($activity->subject ?? '');
                if (str_contains($subject, 'proposal') || str_contains($subject, 'quote') || str_contains($subject, 'kuotasi') || str_contains($subject, 'penawaran')) {
                    $status = 'proposal';
                }
            }
        }

        return $status;
    }
}
