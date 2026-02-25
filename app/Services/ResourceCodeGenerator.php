<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ResourceCodeGenerator
{
    public function generate(string $prefix): string
    {
        $sequence = $this->getNextSequence($prefix);

        return sprintf('MMG-%s-%06d', strtoupper($prefix), $sequence);
    }

    public function generateForOrder(?int $year = null): string
    {
        $year = $year ?? now()->year;
        $sequence = $this->getNextSequence('ORD', (string) $year);

        return sprintf('MMG-ORD-%d-%06d', $year, $sequence);
    }

    protected function getNextSequence(string $prefix, ?string $partition = null): int
    {
        $lockKey = "code_gen_{$prefix}_{$partition}";

        return DB::transaction(function () use ($prefix, $partition) {
            $sequence = DB::table('code_sequences')
                ->where('prefix', $prefix)
                ->where('partition', $partition ?? '')
                ->lockForUpdate()
                ->first();

            if ($sequence) {
                $nextValue = $sequence->sequence_value + 1;

                DB::table('code_sequences')
                    ->where('id', $sequence->id)
                    ->update(['sequence_value' => $nextValue, 'updated_at' => now()]);

                return $nextValue;
            }

            DB::table('code_sequences')->insert([
                'prefix' => $prefix,
                'partition' => $partition ?? '',
                'sequence_value' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return 1;
        });
    }
}
