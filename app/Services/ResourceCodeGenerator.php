<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ResourceCodeGenerator
{
    public function generate(string $prefix, ?string $table = null, ?string $column = null): string
    {
        $sequence = $this->getNextSequence($prefix, null, $table, $column);

        return sprintf('MMG-%s-%06d', strtoupper($prefix), $sequence);
    }

    public function generateForOrder(?int $year = null): string
    {
        $year = $year ?? now()->year;
        $sequence = $this->getNextSequence('ORD', (string) $year);

        return sprintf('MMG-ORD-%d-%06d', $year, $sequence);
    }

    public function generateForLead(?int $year = null): string
    {
        $year = $year ?? now()->year;
        $sequence = $this->getNextSequence('LEAD', (string) $year);

        return sprintf('LEAD-%d-%06d', $year, $sequence);
    }

    public function generateForActivity(?int $year = null): string
    {
        $year = $year ?? now()->year;
        $sequence = $this->getNextSequence('ACT', (string) $year);

        return sprintf('ACT-%d-%06d', $year, $sequence);
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

            $nextValue = 1;

            if ($sequence) {
                $nextValue = $sequence->sequence_value + 1;
            } elseif ($table && $column) {
                $maxCode = DB::table($table)
                    ->where($column, 'like', "%-{$prefix}-%")
                    ->max($column);

                if ($maxCode) {
                    preg_match('/'.preg_quote($prefix, '/').'-(\d+)$/', $maxCode, $matches);
                    if ($matches) {
                        $nextValue = (int) $matches[1] + 1;
                    }
                }
            }

            if ($sequence) {
                DB::table('code_sequences')
                    ->where('id', $sequence->id)
                    ->update(['sequence_value' => $nextValue, 'updated_at' => now()]);
            } else {
                DB::table('code_sequences')->insert([
                    'prefix' => $prefix,
                    'partition' => $partition ?? '',
                    'sequence_value' => $nextValue,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $nextValue;
        });
    }
}
