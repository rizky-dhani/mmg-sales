<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ResourceCodeGenerator
{
    public function generate(string $prefix, ?string $table = null, ?string $column = null): string
    {
        $sequence = $this->getNextSequence($prefix, null, $table, $column);

        return sprintf('%s-%06d', strtoupper($prefix), $sequence);
    }

    public function generateForActivity(?int $year = null): string
    {
        $year = $year ?? now()->year;
        $sequence = $this->getNextSequence('ACT', (string) $year, 'activities', 'activity_code');

        return sprintf('ACT-%d-%06d', $year, $sequence);
    }

    public function generateForOrder(?int $year = null): string
    {
        $year = $year ?? now()->year;
        $sequence = $this->getNextSequence('ORD', (string) $year, 'orders', 'order_number');

        return sprintf('ORD-%d-%06d', $year, $sequence);
    }

    public function getNextSequenceValue(string $prefix, ?string $partition = null, ?string $table = null, ?string $column = null): int
    {
        return $this->getNextSequence($prefix, $partition, $table, $column);
    }

    protected function getNextSequence(string $prefix, ?string $partition = null, ?string $table = null, ?string $column = null): int
    {
        $lockKey = "code_gen_{$prefix}_{$partition}";

        return DB::transaction(function () use ($prefix, $partition, $table, $column) {
            $sequence = DB::table('code_sequences')
                ->where('prefix', $prefix)
                ->where('partition', $partition ?? '')
                ->lockForUpdate()
                ->first();

            $currentMax = 0;

            // Actual table is source of truth
            if ($table && $column) {
                $maxCode = DB::table($table)
                    ->where($column, 'like', $partition ? "{$prefix}-{$partition}-%" : "{$prefix}-%")
                    ->max($column);

                if ($maxCode) {
                    preg_match('/(\d+)$/', $maxCode, $matches);
                    if ($matches) {
                        $currentMax = (int) $matches[1];
                    }
                }
            }

            $nextValue = $currentMax + 1;

            if ($sequence) {
                DB::table('code_sequences')
                    ->where('id', $sequence->id)
                    ->update(['sequence_value' => $currentMax, 'updated_at' => now()]);
            } else {
                DB::table('code_sequences')->insert([
                    'prefix' => $prefix,
                    'partition' => $partition ?? '',
                    'sequence_value' => $currentMax,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $nextValue;
        });
    }
}
