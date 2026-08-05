<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $leads = DB::table('leads')
            ->whereNotNull('lead_code')
            ->get(['id', 'lead_code', 'created_at']);

        $monthlyCounters = [];

        foreach ($leads as $lead) {
            $createdAt = \Carbon\Carbon::parse($lead->created_at);
            $yearMonth = $createdAt->format('Ym');

            // Initialize counter for this month
            if (! isset($monthlyCounters[$yearMonth])) {
                $monthlyCounters[$yearMonth] = 0;
            }
            $monthlyCounters[$yearMonth]++;

            $newCode = sprintf('LEAD-%s-%04d', $yearMonth, $monthlyCounters[$yearMonth]);

            DB::table('leads')
                ->where('id', $lead->id)
                ->update(['lead_code' => $newCode]);
        }

        // Reset code_sequences for LEAD partitions to continue from max used
        foreach ($monthlyCounters as $partition => $maxSeq) {
            DB::table('code_sequences')
                ->updateOrInsert(
                    ['prefix' => 'LEAD', 'partition' => $partition],
                    ['sequence_value' => $maxSeq, 'updated_at' => now()]
                );
        }
    }

    public function down(): void
    {
        // Cannot fully reverse without original codes backup
        // This is a one-way migration for format unification
    }
};
